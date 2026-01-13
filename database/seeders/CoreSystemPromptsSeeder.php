<?php

namespace Database\Seeders;

use App\Models\AI\AIPrompt;
use App\Models\AI\PromptCategory;
use Illuminate\Database\Seeder;

/**
 * ╔══════════════════════════════════════════════════════════════════════════════╗
 * ║                    CORE SYSTEM PROMPTS SEEDER                                 ║
 * ║                                                                               ║
 * ║  This file contains the 12 core AI prompts that power the entire AI system.  ║
 * ║  Each prompt is PROTECTED and cannot be deleted from the UI.                 ║
 * ║                                                                               ║
 * ║  ⚠️  WARNING: Editing these prompts incorrectly may break AI features!       ║
 * ║                                                                               ║
 * ║  SECTIONS:                                                                    ║
 * ║  1. AI Features Page (3 prompts) - Development Plan, Breakdown, Studies      ║
 * ║  2. Task Details Page (4 prompts) - Analysis, Risk, Assignment, Deadline     ║
 * ║  3. AI Workflows Page (5 prompts) - Batch Operations + Workload Balance      ║
 * ╚══════════════════════════════════════════════════════════════════════════════╝
 */
class CoreSystemPromptsSeeder extends Seeder
{
    public function run(): void
    {
        $corePrompts = [
            
            // ╔══════════════════════════════════════════════════════════════════════╗
            // ║  SECTION 1: AI FEATURES PAGE (3 prompts)                             ║
            // ║  Location: AI Assistant → AI Features                                ║
            // ╚══════════════════════════════════════════════════════════════════════╝

            [
                'name' => 'ai_feature_development_plan',
                'category' => 'project-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 AI Features → Development Plan Generator → "Generate Plan" button

🎯 PURPOSE: Creates comprehensive project development plans with phases, timeline, and resources.

📤 EXPECTED RESPONSE FORMAT: The AI should return a structured plan. The parser accepts any JSON format with these possible keys:
- overview (object with title, summary, estimated_duration)
- phases (array of phase objects)
- timeline (object with milestones)
- resources (object)
- risks (array)
- recommendations (array)

⚠️ EDITING TIPS:
- Keep the {{variables}} placeholders intact
- Response can be JSON or structured text
- Do NOT remove the project context variables',
                'template' => <<<'EOT'
Generate a comprehensive development plan for the following project:

**Project:** {{project_title}}
**Description:** {{project_description}}
**Budget:** {{budget}}
**Deadline:** {{deadline}}
**Team Size:** {{team_size}}
**Existing Tasks:** {{existing_tasks}}
**Progress:** {{progress}}%

Provide a structured development plan including:

1. **Executive Summary**
   - Project overview
   - Estimated timeline
   - Complexity assessment

2. **Development Phases**
   - Phase 1: Planning & Analysis (tasks, duration, deliverables)
   - Phase 2: Design (tasks, duration, deliverables)
   - Phase 3: Development (tasks, duration, deliverables)
   - Phase 4: Testing & QA (tasks, duration, deliverables)
   - Phase 5: Deployment (tasks, duration, deliverables)

3. **Timeline & Milestones**
   - Start date and estimated completion
   - Key milestones with dates
   - Critical path items

4. **Resource Requirements**
   - Team composition needed
   - Additional resources required
   - Budget allocation breakdown

5. **Risk Assessment**
   - Identified risks with severity
   - Mitigation strategies
   - Contingency plans

6. **Recommendations**
   - Best practices for this project
   - Optimization suggestions
   - Success criteria
EOT
            ],

            [
                'name' => 'ai_feature_project_breakdown',
                'category' => 'project-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 AI Features → Project Breakdown → "Generate Breakdown" button

🎯 PURPOSE: Breaks down a project into categorized tasks with estimates.

📤 EXPECTED RESPONSE FORMAT: JSON with:
- total_estimated_tasks (number)
- categories (object with category names as keys, each containing tasks array)

Example:
{
  "total_estimated_tasks": 25,
  "categories": {
    "Planning": { "tasks": [...], "estimated_duration": "2 weeks" },
    "Development": { "tasks": [...], "estimated_duration": "4 weeks" }
  }
}

⚠️ EDITING TIPS:
- Response MUST include task counts
- Categories should be logical phases
- Keep {{project_title}} and {{project_description}} variables',
                'template' => <<<'EOT'
Create a comprehensive task breakdown for the following project:

**Project Title:** {{project_title}}
**Project Description:** {{project_description}}
**Budget:** {{budget}}
**Deadline:** {{deadline}}

Break down the project into specific, actionable tasks:

1. **Planning Tasks**
   - List 3-4 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

2. **Design Tasks**
   - List 4-6 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

3. **Development Tasks**
   - List 6-10 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

4. **Testing & QA Tasks**
   - List 4-5 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

5. **Deployment Tasks**
   - List 3-4 specific tasks
   - Estimated duration for each
   - Priority level
   - Dependencies

6. **Summary**
   - Total estimated tasks
   - Total estimated duration
   - Critical path
   - Resource requirements
EOT
            ],

            [
                'name' => 'ai_feature_feasibility_study',
                'category' => 'project-analysis',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 AI Features → AI Studies Generator → "Feasibility Study" / "Technical Study" / "Risk Study" buttons

🎯 PURPOSE: Generates professional feasibility, technical, or risk studies.

📤 EXPECTED RESPONSE FORMAT: JSON with "analysis" object containing:
- executive_summary
- technical_feasibility / financial_feasibility / operational_feasibility
- recommendations (array)
- conclusion

⚠️ EDITING TIPS:
- {{study_type}} determines which study type (feasibility/technical/risk)
- Response should be comprehensive and professional
- Keep all project context variables intact',
                'template' => <<<'EOT'
Generate a comprehensive {{study_type}} for the following project:

**Project Title:** {{project_title}}
**Project Description:** {{project_description}}
**Budget:** {{budget}}
**Deadline:** {{deadline}}
**Team Size:** {{team_size}}
**Current Progress:** {{progress}}%
**Existing Tasks:** {{existing_tasks}}

Provide a thorough analysis covering:

1. **Executive Summary**
   - Key findings overview
   - Critical success factors
   - Overall assessment

2. **Detailed Analysis**
   - Technical considerations
   - Financial implications
   - Operational requirements
   - Schedule feasibility

3. **Risk Assessment**
   - Identified risks with severity
   - Mitigation strategies
   - Contingency plans

4. **Recommendations**
   - Key action items
   - Priority changes if needed
   - Resource optimization suggestions

5. **Conclusion**
   - Final assessment
   - Go/No-Go recommendation
   - Next steps
EOT
            ],

            // ╔══════════════════════════════════════════════════════════════════════╗
            // ║  SECTION 2: TASK DETAILS PAGE - AI ASSISTANT (4 prompts)             ║
            // ║  Location: Any Task → Task Details → AI Assistant Card               ║
            // ╚══════════════════════════════════════════════════════════════════════╝

            [
                'name' => 'ai_feature_task_analysis',
                'category' => 'task-management',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 Task Details → AI Assistant → "Analysis" button

🎯 PURPOSE: Provides comprehensive analysis of a single task including complexity, risks, and recommendations.

📤 EXPECTED RESPONSE FORMAT: Any JSON structure is accepted. Common keys:
- task_summary, complexity, estimated_effort
- critical_issues (array)
- recommendations (array)
- risk_assessment (object)

⚠️ EDITING TIPS:
- Keep {{task_title}}, {{task_description}}, {{priority}}, {{due_date}} variables
- Response should be actionable and specific
- Any valid JSON format works - parser is flexible',
                'template' => <<<'EOT'
Analyze the following task comprehensively:

**Task Title:** {{task_title}}
**Description:** {{task_description}}
**Priority:** {{priority}}
**Assigned To:** {{assigned_to}}
**Due Date:** {{due_date}}

Provide detailed analysis:

1. **Complexity Analysis**
   - Overall complexity level (Low/Medium/High/Very High)
   - Technical complexity factors
   - Business complexity factors

2. **Effort Estimation**
   - Estimated hours/days required
   - Breakdown by sub-components
   - Buffer recommendations

3. **Risk Assessment**
   - Identified risks
   - Impact level for each
   - Mitigation strategies

4. **Dependencies**
   - Related tasks or blockers
   - Required resources
   - Prerequisites

5. **Recommendations**
   - Approach suggestions
   - Priority adjustments if needed
   - Resource allocation advice
EOT
            ],

            [
                'name' => 'ai_decision_risk_assessment',
                'category' => 'task-management',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 Task Details → AI Assistant → "Risk Assessment" button

🎯 PURPOSE: Identifies and assesses risks for a single task.

📤 EXPECTED RESPONSE FORMAT: Any JSON with risk information. Parser looks for:
- risk_level or urgency_level
- identified_risks or risks (array)
- mitigation or recommendations
- impact or impact_assessment

⚠️ EDITING TIPS:
- Focus on risk identification
- Include severity levels (Low/Medium/High/Critical)
- Keep all {{variable}} placeholders',
                'template' => <<<'EOT'
Analyze risks for this task:

**Task:** {{task_title}}
**Description:** {{task_description}}
**Priority:** {{priority}}
**Due Date:** {{due_date}}

Provide:
1. **Risk Level**: Low/Medium/High/Critical
2. **Identified Risks**: List 2-4 specific risks
3. **Mitigation**: Recommended actions for each risk
4. **Impact Assessment**: What happens if risks materialize
5. **Timeline Risks**: Deadline-related concerns
EOT
            ],

            [
                'name' => 'ai_decision_assignment_suggestion',
                'category' => 'task-management',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 Task Details → AI Assistant → "Suggest Assignment" button

🎯 PURPOSE: Recommends the best team member to assign to a task.

📤 EXPECTED RESPONSE FORMAT: Any JSON with assignment suggestion. Parser looks for:
- recommended_assignee or assignee
- user_id or id
- reason or reasoning
- alternative (optional second choice)

⚠️ EDITING TIPS:
- {{available_users}} contains team members with workload
- Suggest based on skills, workload, and availability
- Always include a reason for the recommendation',
                'template' => <<<'EOT'
Suggest the best assignee for this task:

**Task:** {{task_title}}
**Description:** {{task_description}}
**Priority:** {{priority}}

**Available Team:**
{{available_users}}

Provide:
1. **Recommended Assignee**: Name and ID
2. **Reason**: Why this person is the best fit
3. **Alternative**: Second choice if primary unavailable
4. **Workload Consideration**: How this affects their current load
EOT
            ],

            [
                'name' => 'ai_decision_deadline_estimation',
                'category' => 'task-management',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 Task Details → AI Assistant → "Estimate Deadline" button

🎯 PURPOSE: Estimates a realistic deadline for task completion.

📤 EXPECTED RESPONSE FORMAT: Any JSON with deadline info. Parser looks for:
- estimated_duration or duration
- recommended_deadline or deadline
- confidence or confidence_level
- factors (what influenced the estimate)

⚠️ EDITING TIPS:
- Consider task complexity and current workload
- Provide specific dates when possible
- Include confidence level (Low/Medium/High)',
                'template' => <<<'EOT'
Estimate a realistic deadline for this task:

**Task:** {{task_title}}
**Description:** {{task_description}}
**Priority:** {{priority}}
**Current Due Date:** {{due_date}}

Provide:
1. **Estimated Duration**: Days/hours needed
2. **Recommended Deadline**: Specific date
3. **Confidence Level**: Low/Medium/High
4. **Factors Considered**: What influenced the estimate
5. **Buffer Recommendation**: Extra time for unexpected issues
EOT
            ],

            // ╔══════════════════════════════════════════════════════════════════════╗
            // ║  SECTION 3: AI WORKFLOWS PAGE - RUN AUTOMATION (5 prompts)           ║
            // ║  Location: AI Assistant → Workflows → Run Automation Section         ║
            // ╚══════════════════════════════════════════════════════════════════════╝

            [
                'name' => 'ai_automation_priority_batch',
                'category' => 'ai-automation',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 Workflows → Run Automation → "Priority Adjustments" button

🎯 PURPOSE: Analyzes multiple tasks and suggests priority changes.

📤 EXPECTED RESPONSE FORMAT: Text-based format with Task IDs. Parser searches for:
- "Task [ID]: CHANGE priority to [Level]" or "Task [ID]: KEEP priority"
- Keywords: change, keep, urgent, high, escalate

Example response:
Task 366: CHANGE priority to Urgent - deadline is tomorrow
Task 388: KEEP priority High - already appropriate

⚠️ CRITICAL EDITING RULES:
- MUST include "Task [ID]:" format for parser to detect
- MUST include action keywords (CHANGE/KEEP/urgent/high)
- Mention EVERY task in {{tasks_list}}
- Do NOT use JSON format for this prompt',
                'template' => <<<'EOT'
Analyze ALL {{tasks_count}} tasks for priority adjustments:

{{tasks_list}}

Date: {{analysis_date}}

**CRITICAL: You MUST analyze and respond for EVERY task in the list above.**

For each task, respond with one line in this format:
Task [ID]: [KEEP/CHANGE] priority [to Level] - [brief reason]

Examples:
Task 366: CHANGE priority to Urgent - deadline is tomorrow
Task 388: KEEP priority High - already appropriate for workload
Task 292: CHANGE priority to High - blocking other tasks

Priority levels: low, medium, high, urgent

**Summary:**
- Total analyzed: {{tasks_count}}
- Changes recommended: [number]
EOT
            ],

            [
                'name' => 'ai_automation_assignment_batch',
                'category' => 'ai-automation',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 Workflows → Run Automation → "Assignment Suggestions" button

🎯 PURPOSE: Suggests team member assignments for multiple unassigned tasks.

📤 EXPECTED RESPONSE FORMAT: Text-based format with Task IDs. Parser searches for:
- "Task [ID]: Assign to [Name] (ID:[number])"
- Keywords: assign, delegate, allocate

Example response:
Task 105: Assign to John Doe (ID:5) - low workload
Task 206: Assign to Jane Smith (ID:3) - matching skills

⚠️ CRITICAL EDITING RULES:
- MUST include "Task [ID]:" format
- MUST include "Assign to" keyword
- Include User ID in format (ID:X)
- Assign EVERY task in the list',
                'template' => <<<'EOT'
**CRITICAL: You MUST provide an assignment for EVERY task below.**

{{tasks_list}}

Date: {{analysis_date}}

For each task, respond with:
Task [ID]: Assign to [User Name] (ID:[User ID]) - [brief reason]

Example:
Task 105: Assign to John Doe (ID:5) - low workload, matching skills
Task 206: Assign to Jane Smith (ID:3) - available capacity

Rules:
- Assign EVERY task in the list
- Prioritize users with fewer active tasks
- Use exact User IDs from the list
EOT
            ],

            [
                'name' => 'ai_automation_deadline_batch',
                'category' => 'ai-automation',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 Workflows → Run Automation → "Deadline Extensions" button

🎯 PURPOSE: Analyzes overdue/at-risk tasks and suggests deadline extensions.

📤 EXPECTED RESPONSE FORMAT: Text-based format with Task IDs. Parser searches for:
- "Task [ID]: EXTEND deadline by X days" or "Task [ID]: KEEP deadline"
- Keywords: extend, deadline, postpone, delay

Example response:
Task 204: EXTEND deadline by 3 days - complexity requires more time
Task 305: KEEP deadline - on track for completion

⚠️ CRITICAL EDITING RULES:
- MUST include "Task [ID]:" format
- MUST include EXTEND or KEEP keyword
- Respond for EVERY task
- Specify number of days for extensions',
                'template' => <<<'EOT'
**CRITICAL: You MUST analyze EVERY task below and provide a recommendation.**

{{tasks_list}}

Date: {{analysis_date}}

For each task, respond with:
Task [ID]: [EXTEND/KEEP] deadline [by X days] - [brief reason]

Examples:
Task 204: EXTEND deadline by 3 days - complexity requires more time
Task 305: KEEP deadline - on track for completion
Task 406: EXTEND deadline by 1 day - minor delays

Rules:
- Respond for EVERY task in the list
- EXTEND if task is at risk or overdue
- KEEP if deadline is achievable
EOT
            ],

            [
                'name' => 'ai_automation_project_health_batch',
                'category' => 'ai-automation',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 Workflows → Run Automation → "Project Health" button

🎯 PURPOSE: Assesses health status for multiple projects.

📤 EXPECTED RESPONSE FORMAT: Text-based format with Project IDs. Parser searches for:
- "Project [ID]: HEALTHY/AT_RISK/CRITICAL"
- Keywords: healthy, at_risk, at risk, critical, investigate

Example response:
Project 1: CRITICAL - 5 overdue tasks, 20% progress only
Project 2: HEALTHY - on track, no overdue tasks

⚠️ CRITICAL EDITING RULES:
- MUST include "Project [ID]:" format
- MUST include status keyword (HEALTHY/AT_RISK/CRITICAL)
- Assess EVERY project in the list',
                'template' => <<<'EOT'
**CRITICAL: You MUST assess EVERY project below.**

{{projects_list}}

Date: {{analysis_date}}

For each project, respond with:
Project [ID]: [HEALTHY/AT_RISK/CRITICAL] - [brief assessment]

Examples:
Project 1: CRITICAL - 5 overdue tasks, 20% progress only
Project 2: HEALTHY - on track, no overdue tasks
Project 3: AT_RISK - 2 overdue tasks, team understaffed

Rules:
- Assess EVERY project in the list
- HEALTHY = on track, no issues
- AT_RISK = minor issues, needs attention  
- CRITICAL = major issues, immediate action needed
EOT
            ],

            [
                'name' => 'ai_workload_balance',
                'category' => 'ai-automation',
                'type' => 'system',
                'is_system' => true,
                'description' => '📍 Workflows → Workload Balance → "Analyze Workload" button

🎯 PURPOSE: Analyzes overloaded team members and suggests task redistribution.

📤 EXPECTED RESPONSE FORMAT: JSON with specific structure:
{
  "recommendations": [
    {
      "user_id": 5,
      "user_name": "John Doe",
      "recommendation": "Redistribute 3 tasks",
      "severity": "high",
      "tasks_to_redistribute": 3
    }
  ],
  "summary": {
    "total_analyzed": 5,
    "critical_cases": 2
  }
}

⚠️ CRITICAL EDITING RULES:
- Response MUST be valid JSON
- MUST include "recommendations" array
- Each item needs user_id and recommendation
- Keep the JSON structure intact',
                'template' => <<<'EOT'
Analyze overloaded team members and provide smart workload management recommendations:

**Overloaded Team Members (JSON):**
{{users_json}}

**Context:**
- Total overloaded users: {{users_count}}
- Workload threshold: {{threshold}} active tasks
- Analysis date: {{analysis_date}}

**Instructions:**
For each overloaded user, consider:
1. **Workload Metrics**: Total tasks, overdue tasks, urgent tasks
2. **Task Complexity**: Difficulty and time requirements
3. **Performance Impact**: How overload affects quality
4. **Team Dynamics**: Available capacity in team

**Required Output Format:**
Return a JSON object with this EXACT structure:

```json
{
  "recommendations": [
    {
      "user_id": (integer),
      "user_name": "string",
      "active_tasks": (integer),
      "recommendation": "Detailed, actionable recommendation",
      "severity": "low|medium|high|critical",
      "analysis": {
        "overdue_tasks": (integer),
        "urgent_tasks": (integer),
        "workload_score": (float 0-10),
        "burnout_risk": "low|medium|high"
      },
      "suggested_actions": ["action 1", "action 2"],
      "tasks_to_redistribute": (integer)
    }
  ],
  "summary": {
    "total_analyzed": (integer),
    "critical_cases": (integer),
    "team_health_score": (float 0-10)
  }
}
```
EOT
            ],

        ];

        // ════════════════════════════════════════════════════════════════════════
        // SEEDER LOGIC - DO NOT MODIFY BELOW THIS LINE
        // ════════════════════════════════════════════════════════════════════════

        echo "\nSeeding 12 core system prompts...\n";

        foreach ($corePrompts as $promptData) {
            $category = PromptCategory::firstOrCreate(
                ['slug' => $promptData['category']],
                [
                    'name' => ucwords(str_replace('-', ' ', $promptData['category'])),
                    'description' => 'System category for ' . $promptData['category']
                ]
            );

            AIPrompt::updateOrCreate(
                ['name' => $promptData['name']],
                [
                    'category_id' => $category->id,
                    'type' => $promptData['type'],
                    'is_system' => $promptData['is_system'],
                    'description' => $promptData['description'],
                    'template' => $promptData['template'],
                    'created_by' => 1, // System Admin
                ]
            );

            echo "✓ {$promptData['name']}\n";
        }

        echo "\n🎉 Successfully seeded 12 core system prompts!\n";
        echo "⚠️  These prompts are protected and cannot be deleted (but can be edited).\n";
    }
}
