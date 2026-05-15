<?php

namespace App\Models;

use DateInterval;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

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
        'countdown',
        'link',
        'description',
        'icon',
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
        $title = $expression === null
            ? $this->title
            : trim(str_replace($expression['expression'], '', $this->title));

        if ($this->countdown && $this->original_due_date && $this->due_date) {
            $years = $this->original_due_date->diffInYears($this->due_date);
            $title .= " ({$years})";
        }

        return $title;
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

    public function countdownPhrase(): string
    {
        $diff = (int) floor(Carbon::now()->startOfDay()->diffInDays($this->due_date));

        return match ($diff) {
            0 => 'Today',
            1 => 'Tomorrow',
            default => sprintf('in %d days', $diff),
        };
    }

    public function countdownColor(): string
    {
        $colors = [
            '#E67E22', // orange
            '#3498DB', // blue
            '#E74C3C', // red
            '#27AE60', // green

            '#9B59B6', // violet
            '#F1C40F', // yellow
            '#D33682', // pink
            '#1ABC9C', // turquoise

            '#8E6E53', // brown
            '#607D8B', // blue gray
            '#7CB342', // lime green
            '#3F51B5', // indigo

            '#C0392B', // dark red
            '#8E44AD', // purple
            '#16A085', // teal
            '#D35400', // dark orange
        ];

        return $colors[hexdec(substr(md5($this->title), 0, 1))];
    }

    public static function icons(): array
    {
        return [
            'academic-cap',
            'archive-box',
            'banknotes',
            'bell',
            'briefcase',
            'cake',
            'calendar',
            'camera',
            'face-smile',
            'film',
            'flag',
            'gift',
            'globe-europe-africa',
            'key',
            'light-bulb',
            'sparkles',
            'star',
            'sun',
            'ticket',
            'trophy',
            'user-group',
            'wrench',
        ];
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

    private function ordinal(int $number): string
    {
        $suffixes = ['th', 'st', 'nd', 'rd'];
        if (($number % 100) >= 11 && ($number % 100) <= 13) {
            return $number.'th';
        }

        return $number.($suffixes[$number % 10] ?? 'th');
    }
}
