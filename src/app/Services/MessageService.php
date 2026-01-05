<?php

namespace App\Services;

use App\Jobs\SendMessageJob;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class MessageService
{
    /** @var int */
    private const MESSAGE_CHAR_LIMIT = 255;

    /** @var int */
    private const MESSAGE_SEND_INTERVAL_SECONDS = 5;

    /** @var int */
    private const MAX_MESSAGES_PER_INTERVAL = 2;

    /**
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(
        private MessageRepositoryInterface $messageRepository
    ) {}

    /**
     * @return int
     */
    public function dispatchPendingMessages(): int
    {
        $messages = $this->messageRepository->getAllPendingMessages();

        if ($messages->isEmpty()) {
            return 0;
        }

        $dispatchedCount = 0;

        $chunks = $messages->chunk(self::MAX_MESSAGES_PER_INTERVAL);

        foreach ($chunks as $index => $chunk) {
            foreach ($chunk as $message) {
                if (strlen($message->content) > self::MESSAGE_CHAR_LIMIT) {
                    continue;
                }

                SendMessageJob::dispatch($message->id)
                    ->delay(now()->addSeconds(
                        $index * self::MESSAGE_SEND_INTERVAL_SECONDS
                    ));

                $dispatchedCount++;
            }
        }

        return $dispatchedCount;
    }

    /**
     * @param int $messageId
     * @return void
     */
    public function sendMessage(int $messageId): void
    {
        $message = $this->messageRepository->findById($messageId);

        if ($message->status !== 'pending') {
            return;
        }

        $response = Http::post(config('services.webhook.url'), [
            'phone' => $message->phone_number,
            'content' => $message->content,
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Webhook request failed');
        }

        $data = $response->json();
        $sentAt = now();
        $messageId = $data['messageId'] ?? null;

        $this->messageRepository->markAsSent(
            $message->id,
            $messageId,
            $sentAt
        );

        Cache::put(
            "message:{$messageId}",
            [
                'sent_at' => $sentAt->toDateTimeString(),
            ],
            now()->addDay()
        );
    }

    public function markAsFailed(int $messageId, Throwable $exception): void
    {
        $this->messageRepository->markAsFailed(
            $messageId,
            $exception->getMessage()
        );
    }

    public function getSentMessages(int $perPage = 10)
    {
        return $this->messageRepository->getSentMessages($perPage);
    }

}
