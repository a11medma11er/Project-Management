<?php

namespace App\Services\AI;

use App\Models\AI\AIDecision;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AIDecisionNotification;

class AINotificationService
{
    /**
     * Send notification for new AI decision
     */
    public function notifyNewDecision(AIDecision $decision): void
    {
        try {
            // Get assigned user or project owner
            $user = $this->getRelevantUser($decision);
            
            if ($user) {
                // Send in-app notification
                $user->notify(new AIDecisionNotification($decision));
                
                Log::info('AI decision notification sent', [
                    'decision_id' => $decision->id,
                    'user_id' => $user->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send AI notification', [
                'decision_id' => $decision->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send browser push notification
     */
    public function sendBrowserNotification(User $user, string $title, string $body): void
    {
        // This would integrate with a push notification service
        // For now, we'll log it
        Log::info('Browser notification', [
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
        ]);
    }

    /**
     * Send email notification
     */
    public function sendEmailNotification(User $user, AIDecision $decision): void
    {
        // Laravel's mail system would handle this
        try {
            $user->notify(new AIDecisionNotification($decision));
        } catch (\Exception $e) {
            Log::error('Email notification failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get relevant user for decision
     */
    protected function getRelevantUser(AIDecision $decision): ?User
    {
        // Get entity owner/assignee
        $entity = $decision->entity;
        
        if ($entity && isset($entity->assigned_to)) {
            return User::find($entity->assigned_to);
        }
        
        if ($entity && isset($entity->created_by)) {
            return User::find($entity->created_by);
        }
        
        return null;
    }

    /**
     * Notify about high-priority decisions
     */
    public function notifyHighPriority(AIDecision $decision): void
    {
        if ($decision->confidence_score >= 0.9) {
            // Get all admins
            $admins = User::permission('manage-ai-settings')->get();
            
            foreach ($admins as $admin) {
                $this->sendBrowserNotification(
                    $admin,
                    'High Confidence AI Decision',
                    "A new AI decision with {$decision->confidence_score}% confidence requires review."
                );
            }
        }
    }

    /**
     * Send bulk notifications
     */
    public function sendBulkNotifications(array $users, string $message): void
    {
        foreach ($users as $user) {
            try {
                $this->sendBrowserNotification($user, 'AI System Update', $message);
            } catch (\Exception $e) {
                Log::error('Bulk notification failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Get notification preferences
     */
    public function getPreferences(User $user): array
    {
        // Would typically fetch from user settings
        return [
            'email' => true,
            'browser' => true,
            'slack' => false,
        ];
    }
}
