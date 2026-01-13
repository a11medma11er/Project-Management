<?php

namespace App\Enums;

enum TaskStatus: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case ON_HOLD = 'on_hold';
    case REVIEW = 'review';
    case CANCELLED = 'cancelled';
    case BLOCKED = 'blocked';

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match($this) {
            self::NEW => 'New',
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::ON_HOLD => 'On Hold',
            self::REVIEW => 'Review',
            self::CANCELLED => 'Cancelled',
            self::BLOCKED => 'Blocked',
        };
    }

    /**
     * Get Bootstrap color class
     */
    public function color(): string
    {
        return match($this) {
            self::NEW => 'info',
            self::PENDING => 'warning',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::ON_HOLD => 'secondary',
            self::REVIEW => 'info',
            self::CANCELLED => 'danger',
            self::BLOCKED => 'danger',
        };
    }

    /**
     * Get badge CSS classes
     */
    public function badgeClass(): string
    {
        $color = $this->color();
        return "badge bg-{$color}-subtle text-{$color} text-uppercase";
    }

    /**
     * Check if status is terminal (cannot transition from)
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::CANCELLED]);
    }

    /**
     * Check if status is active (task is being worked on)
     */
    public function isActive(): bool
    {
        return in_array($this, [self::IN_PROGRESS, self::PENDING]);
    }

    /**
     * Get all possible values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get all cases with labels
     */
    public static function options(): array
    {
        return array_map(
            fn(self $status) => [
                'value' => $status->value,
                'label' => $status->label(),
                'color' => $status->color(),
            ],
            self::cases()
        );
    }
}
