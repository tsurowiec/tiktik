<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'app:advance-countdowns', description: 'Create next iterations for passed recurring countdowns')]
class AdvanceCountdowns extends Command
{
    public function handle(): void
    {
        $countdowns = Task::where('countdown', true)
            ->where('due_date', '<', today())
            ->whereDoesntHave('next')
            ->get();

        foreach ($countdowns as $countdown) {
            $next = $countdown->complete();
            if ($next) {
                $this->line("Advanced: {$countdown->shortTitle()} to {$next->due_date->toDateString()}");
            } else {
                $this->line("Completed: {$countdown->shortTitle()}");
            }

        }

        $this->info("Done. Advanced {$countdowns->count()} countdown(s).");
    }

    private function parseExpression(string $title): string
    {
        preg_match('/@P(\d+)([DWMY])\b/i', $title, $matches);

        return $matches[0];
    }
}
