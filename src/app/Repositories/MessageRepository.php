<?php

namespace App\Repositories;

use App\Models\Message;
use App\Repositories\Contracts\MessageRepositoryInterface;
use Illuminate\Support\Collection;

class MessageRepository implements MessageRepositoryInterface
{
    public function getAllPendingMessages(): Collection
    {
        return Message::query()
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();
    }

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

    public function getSentMessages(int $perPage = 10)
    {
        return Message::query()
            ->where('status', 'sent')
            ->orderBy('sent_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): Message
    {
        return Message::findOrFail($id);
    }

    public function markAsFailed(int $id, string $reason): void
    {
        Message::where('id', $id)->update([
            'status' => 'failed',
            'failed_reason' => $reason,
        ]);
    }

}
