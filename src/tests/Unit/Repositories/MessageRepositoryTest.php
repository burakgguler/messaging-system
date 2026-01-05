<?php

namespace Tests\Unit\Repositories;

use App\Models\Message;
use App\Repositories\MessageRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MessageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private MessageRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new MessageRepository();
    }

    #[Test]
    public function it_marks_message_as_sent()
    {
        $message = Message::factory()->create([
            'status' => 'pending',
        ]);

        $sentAt = now();
        $externalMessageId = 'uuid-123';

        $this->repository->markAsSent(
            $message->id,
            $externalMessageId,
            $sentAt
        );

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'status' => 'sent',
            'message_id' => $externalMessageId,
        ]);
    }

    #[Test]
    public function it_returns_only_sent_messages()
    {
        Message::factory()->create(['status' => 'pending']);
        Message::factory()->count(2)->create(['status' => 'sent']);

        $sentMessages = $this->repository->getSentMessages();

        $this->assertCount(2, $sentMessages);
        $this->assertTrue(
            $sentMessages->every(fn ($message) => $message->status === 'sent')
        );
    }
}
