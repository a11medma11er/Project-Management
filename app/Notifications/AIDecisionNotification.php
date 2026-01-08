<?php

namespace App\Notifications;

use App\Models\AI\AIDecision;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AIDecisionNotification extends Notification
{
    use Queueable;

    protected $decision;

    public function __construct(AIDecision $decision)
    {
        $this->decision = $decision;
    }

    /**
     * Get notification delivery channels
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get mail representation
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New AI Decision Requires Review')
            ->line("A new AI decision ({$this->decision->decision_type}) has been created.")
            ->line("Confidence: " . ($this->decision->confidence_score * 100) . "%")
            ->action('Review Decision', url('/admin/ai/decisions/' . $this->decision->id))
            ->line('Please review and approve/reject this decision.');
    }

    /**
     * Get array representation for database
     */
    public function toArray($notifiable): array
    {
        return [
            'decision_id' => $this->decision->id,
            'decision_type' => $this->decision->decision_type,
            'confidence_score' => $this->decision->confidence_score,
            'created_at' => $this->decision->created_at->toIso8601String(),
        ];
    }
}
