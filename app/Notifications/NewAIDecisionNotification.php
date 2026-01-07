<?php

namespace App\Notifications;

use App\Models\AI\AIDecision;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAIDecisionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $decision;

    /**
     * Create a new notification instance.
     */
    public function __construct(AIDecision $decision)
    {
        $this->decision = $decision;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New AI Decision Requires Review')
                    ->line('A new AI decision has been generated and requires your review.')
                    ->line('Recommendation: ' . $this->decision->recommendation)
                    ->line('Confidence: ' . round($this->decision->confidence_score * 100, 2) . '%')
                    ->action('Review Decision', route('ai.decisions.show', $this->decision->id))
                    ->line('Please review and take appropriate action.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'decision_id' => $this->decision->id,
            'decision_type' => $this->decision->decision_type,
            'recommendation' => $this->decision->recommendation,
            'confidence_score' => $this->decision->confidence_score,
            'task_id' => $this->decision->task_id,
            'project_id' => $this->decision->project_id,
            'created_at' => $this->decision->created_at->toIso8601String(),
        ];
    }
}
