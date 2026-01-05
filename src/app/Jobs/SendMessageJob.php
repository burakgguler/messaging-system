<?php

namespace App\Jobs;

use App\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int */
    public int $tries = 3;

    /**
     * @param int $messageId
     */
    public function __construct(
        private int $messageId
    ) {}

    /**
     * @param MessageService $messageService
     * @return void
     */
    public function handle(MessageService $messageService): void
    {
        $messageService->sendMessage($this->messageId);
    }
}
