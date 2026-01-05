<?php

namespace App\Repositories;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Support\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    /**
     * @return Collection
     */
    public function getAllPendingMessages(): Collection
    {
        return Message::query()
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();
    }

    /**
     * @param int $id
     * @param string $messageId
     * @param \DateTimeInterface $sentAt
     * @return void
     */
    public function markAsSent(
        int $id,
        string $messageId,
        \DateTimeInterface $sentAt
    ): void {
        Message::where('id', $id)->update([
            'status' => 'sent',
            'message_id' => $messageId,
            'sent_at' => $sentAt,
        ]);
    }

    /**
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSentMessages(int $perPage = 10)
    {
        return Message::query()
            ->where('status', 'sent')
            ->orderBy('sent_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * @param int $id
     * @return Message
     */
    public function findById(int $id): Message
    {
        return Message::findOrFail($id);
    }
}
