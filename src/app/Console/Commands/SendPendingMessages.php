<?php

namespace App\Console\Commands;

use App\Services\MessageService;
use Illuminate\Console\Command;

class SendPendingMessages extends Command
{
    protected $signature = 'messages:send';

    protected $description = 'Dispatch pending messages';

    public function handle(MessageService $messageService): int
    {
        $count = $messageService->dispatchPendingMessages();

        if ($count === 0) {
            $this->warn('No pending messages found.');
            return Command::SUCCESS;
        }

        $this->info(sprintf(
            '%d pending message(s) dispatched successfully.',
            $count
        ));

        return Command::SUCCESS;
    }
}
