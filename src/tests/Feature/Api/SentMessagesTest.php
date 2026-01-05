<?php

namespace Tests\Feature\Api;

use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SentMessagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_paginated_sent_messages()
    {
        Message::factory()->count(3)->create(['status' => 'sent']);
        Message::factory()->create(['status' => 'pending']);

        $response = $this->getJson('/api/messages/sent?per_page=2');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'meta' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ],
                'data' => [
                    '*' => [
                        'id',
                        'phone_number',
                        'content',
                        'message_id',
                        'sent_at',
                    ]
                ]
            ]);

        $this->assertEquals(3, $response->json('meta.total'));
        $this->assertCount(2, $response->json('data'));
    }
}
