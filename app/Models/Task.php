<?php

namespace App\Models;

use DateInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'original_due_date',
        'due_date',
        'link',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'immutable_date',
            'original_due_date' => 'immutable_date',
            'completed' => 'boolean',
            'completed_date' => 'immutable_date',
        ];
    }

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function next(): HasOne
    {
        return $this->hasOne(Task::class, 'parent_task_id');
    }

    public function hasParent(): bool
    {
        return $this->parent_task_id !== null;
    }

    public function hasNext(): bool
    {
        return $this->next()->exists();
    }

    public function complete(): void
    {
        if ($this->recurring() && ! $this->hasNext()) {
            $next = $this->replicate();
            $expression = $this->parseEveryExpression();
            $next->original_due_date = $next->due_date = $this->original_due_date
                ->add(new DateInterval(substr($expression['expression'], 1)));
            $next->iteration = $this->iteration + 1;
            $next->parent_task_id = $this->id;
            $next->save();
        }
        $this->completed = true;
        $this->completed_date = now();
        $this->save();
    }

    public function revert(): void
    {
        $this->completed = false;
        $this->completed_date = null;
        $this->save();
    }

    public function shortTitle(): string
    {
        $expression = $this->parseEveryExpression();
        if ($expression === null) {
            return $this->title;
        }

        return trim(str_replace($expression['expression'], '', $this->title));
    }

    public function recurring(): bool
    {
        return is_array($expression = $this->parseEveryExpression()) && $expression['expression'];
    }

    public function repeatPhrase(): string
    {
        $expression = $this->parseEveryExpression();

        if ($expression === null) {
            return '';
        }

        return sprintf(
            'every %s%s',
            $expression['interval'] === 1 ? ' ' : $this->ordinal($expression['interval']).' ', $expression['unit']
        );
    }

    private function parseEveryExpression(): ?array
    {
        if (! preg_match('/@P(\d+)([DWMY])\b/i', $this->title, $matches)) {
            return null;
        }

        $interval = isset($matches[1]) && $matches[1] !== ''
            ? (int) $matches[1]
            : 1;

        $unitMap = [
            'D' => 'day',
            'W' => 'week',
            'M' => 'month',
            'Y' => 'year',
        ];

        $unitCode = strtoupper($matches[2]);

        return [
            'expression' => $matches[0],
            'interval' => $interval,
            'unit' => $unitMap[$unitCode],
        ];
    }

    public function ordinal(int $number): string
    {
        $suffixes = ['th', 'st', 'nd', 'rd'];
        if (($number % 100) >= 11 && ($number % 100) <= 13) {
            return $number.'th';
        }

        return $number.($suffixes[$number % 10] ?? 'th');
    }
}
