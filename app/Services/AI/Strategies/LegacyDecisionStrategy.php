<?php

namespace App\Services\AI\Strategies;

class LegacyDecisionStrategy implements DecisionStrategyInterface
{
    /**
     * Perform detailed task analysis using legacy rule-based logic
     */
    public function analyzeTask(array $context, ?string $decisionType = null): array
    {
        $taskContext = $context['task_context'];
        $aiSignals = $taskContext['ai_signals'];
        $timeline = $taskContext['timeline'];
        
        $requiresAction = false;
        $recommendation = '';
        $reasoning = [];
        $confidence = 0.0;
        $alternatives = [];

        // Check for overdue tasks (needs_attention)
        if ($aiSignals['needs_attention']) {
            $requiresAction = true;
            $daysOverdue = abs($timeline['days_overdue']);
            
            $recommendation = "Escalate task priority - {$daysOverdue} days overdue";
            $reasoning[] = "Task is {$daysOverdue} days past due date";
            $reasoning[] = "Current status: {$taskContext['task']['status']}";
            $reasoning[] = "Urgency level: {$timeline['urgency_level']}";
            
            $confidence = min(0.95, 0.7 + ($daysOverdue * 0.05));
            
            $alternatives = [
                [
                    'action' => 'Extend deadline',
                    'impact' => 'Low',
                    'description' => 'Request deadline extension from stakeholders'
                ],
                [
                    'action' => 'Reassign task',
                    'impact' => 'Medium',
                    'description' => 'Assign to available team member'
                ],
                [
                    'action' => 'Cancel task',
                    'impact' => 'High',
                    'description' => 'Mark as cancelled if no longer needed'
                ]
            ];
        }
        // Check for stale tasks
        elseif ($aiSignals['stale_task']) {
            $requiresAction = true;
            
            $recommendation = "Request status update - No activity for 7+ days";
            $reasoning[] = "No activity recorded in the last 7 days";
            $reasoning[] = "Task created: {$timeline['created_at']}";
            $reasoning[] = "Engagement metrics indicate low priority";
            
            $confidence = 0.75;
            
            $alternatives = [
                [
                    'action' => 'Send reminder',
                    'impact' => 'Low',
                    'description' => 'Notify assigned team member'
                ],
                [
                    'action' => 'Schedule review',
                    'impact' => 'Medium',
                    'description' => 'Add to next team meeting agenda'
                ]
            ];
        }
        // Check for low engagement
        elseif ($aiSignals['low_engagement']) {
            $requiresAction = true;
            
            $recommendation = "Increase collaboration - Add team members or comments";
            $reasoning[] = "Zero comments despite being created >3 days ago";
            $reasoning[] = "Low engagement may indicate unclear requirements";
            
            $confidence = 0.65;
            
            $alternatives = [
                [
                    'action' => 'Add collaborators',
                    'impact' => 'Low',
                    'description' => 'Invite relevant team members'
                ],
                [
                    'action' => 'Clarify requirements',
                    'impact' => 'Medium',
                    'description' => 'Request detailed specifications'
                ]
            ];
        }
        // Check for blocked tasks
        elseif ($aiSignals['is_blocked']) {
            $requiresAction = true;
            
            $recommendation = "Resolve blocker - Task marked as blocked";
            $reasoning[] = "Task status is currently 'blocked'";
            $reasoning[] = "Requires immediate intervention";
            
            $confidence = 0.85;
            
            $alternatives = [
                [
                    'action' => 'Identify blocker',
                    'impact' => 'High',
                    'description' => 'Document blocking issue'
                ],
                [
                    'action' => 'Escalate to management',
                    'impact' => 'High',
                    'description' => 'Request management intervention'
                ]
            ];
        }

        return [
            'requires_action' => $requiresAction,
            'recommendation' => $recommendation,
            'reasoning' => $reasoning,
            'confidence' => $confidence,
            'alternatives' => $alternatives,
        ];
    }

    /**
     * Perform detailed project analysis using legacy rule-based logic
     */
    public function analyzeProject(array $context): array
    {
        $projectContext = $context['project_context'];
        $health = $projectContext['health'];
        $tasks = $projectContext['tasks'];
        $progress = $projectContext['progress'];
        
        $requiresAction = false;
        $recommendation = '';
        $reasoning = [];
        $confidence = 0.0;
        $alternatives = [];

        // Check health status
        if ($health['status'] === 'overdue') {
            $requiresAction = true;
            
            $recommendation = "Project deadline review required - Currently overdue";
            $reasoning[] = "Project deadline has passed";
            $reasoning[] = "Completion rate: {$progress['completion_rate']}%";
            $reasoning[] = "Remaining tasks: " . ($tasks['total'] - $tasks['completed']);
            
            $confidence = 0.90;
            
            $alternatives = [
                [
                    'action' => 'Extend deadline',
                    'impact' => 'High',
                    'description' => 'Request official deadline extension'
                ],
                [
                    'action' => 'Reduce scope',
                    'impact' => 'High',
                    'description' => 'Move non-critical tasks to next phase'
                ],
                [
                    'action' => 'Add resources',
                    'impact' => 'High',
                    'description' => 'Assign additional team members'
                ]
            ];
        }
        elseif ($health['status'] === 'at_risk') {
            $requiresAction = true;
            
            $recommendation = "Risk mitigation needed - Deadline approaching";
            $reasoning[] = "Project deadline is within 7 days";
            $reasoning[] = "Current progress: {$progress['calculated_progress']}%";
            
            $confidence = 0.80;
            
            $alternatives = [
                [
                    'action' => 'Accelerate progress',
                    'impact' => 'Medium',
                    'description' => 'Focus on critical path tasks'
                ],
                [
                    'action' => 'Daily standups',
                    'impact' => 'Low',
                    'description' => 'Increase communication frequency'
                ]
            ];
        }
        elseif ($health['has_multiple_blockers']) {
            $requiresAction = true;
            
            $recommendation = "Blocker resolution required - Multiple blocked tasks";
            $reasoning[] = "More than 2 tasks are currently blocked";
            $reasoning[] = "Blocked tasks: {$tasks['blocked']}";
            
            $confidence = 0.85;
            
            $alternatives = [
                [
                    'action' => 'Blocker workshop',
                    'impact' => 'High',
                    'description' => 'Dedicated session to resolve blockers'
                ],
                [
                    'action' => 'Prioritize unblocking',
                    'impact' => 'Medium',
                    'description' => 'Make blocker resolution top priority'
                ]
            ];
        }
        elseif ($health['is_stale']) {
            $requiresAction = true;
            
            $recommendation = "Activity review needed - Low activity for 14+ days";
            $reasoning[] = "No recorded activity in the last 14 days";
            $reasoning[] = "Project status: {$projectContext['project']['status']}";
            
            $confidence = 0.70;
            
            $alternatives = [
                [
                    'action' => 'Status meeting',
                    'impact' => 'Low',
                    'description' => 'Schedule project review meeting'
                ],
                [
                    'action' => 'Put on hold',
                    'impact' => 'Medium',
                    'description' => 'Officially mark project as on-hold'
                ]
            ];
        }

        return [
            'requires_action' => $requiresAction,
            'recommendation' => $recommendation,
            'reasoning' => $reasoning,
            'confidence' => $confidence,
            'alternatives' => $alternatives,
        ];
    }
}
