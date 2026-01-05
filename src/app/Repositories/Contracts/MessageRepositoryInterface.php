<?php

namespace App\Repositories\Contracts;

use App\Models\Message;
use Illuminate\Support\Collection;

interface MessageRepositoryInterface
{
    public function getAllPendingMessages(): Collection;

    public function markAsSent(
        int $id,
        string $messageId,
        \DateTimeInterface $sentAt
    ): void;

    public function getSentMessages();

    public function findById(int $id): Message;

    public function markAsFailed(int $id, string $reason): void;
}
