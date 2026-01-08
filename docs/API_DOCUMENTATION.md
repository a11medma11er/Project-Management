# 📡 API Documentation - AI System

## Authentication & Authorization

All API endpoints require authentication via Laravel Sanctum/Session.

### Required Permissions

| Permission | Description |
|----------|-------------|
| `view-ai-decisions` | View AI decisions |
| `approve-ai-actions` | Approve/reject decisions |
| `manage-ai-prompts` | Manage prompt templates |
| `manage-ai-settings` | Manage AI settings |
| `access-ai-control` | Access AI control panel |
| `view-ai-analytics` | View analytics & insights |

---

## Base URL

```
https://your-domain.com/ai
```

---

## AI Decisions

### List Decisions

```http
GET /ai/decisions
```

#### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | string | Filter by status: `pending`, `accepted`, `rejected`, `modified` |
| `type` | string | Filter by decision type |
| `date_from` | date | Start date (Y-m-d) |
| `date_to` | date | End date (Y-m-d) |
| `per_page` | integer | Items per page (default: 15) |

#### Response

```json
{
  "data": [
    {
      "id": 1,
      "decision_type": "priority_change",
      "entity_type": "task",
      "entity_id": 42,
      "recommendation": "Increase priority to High",
      "reasoning": {
        "factors": ["Task is blocking others", "Deadline approaching"],
        "impact": "High",
        "urgency": "High"
      },
      "confidence_score": 0.85,
      "alternatives": [
        {
          "action": "Extend deadline instead",
          "impact": "Medium",
          "confidence": 0.72
        }
      ],
      "user_action": "pending",
      "guardrail_violations": 0,
      "created_at": "2026-01-08T10:30:00Z",
      "updated_at": "2026-01-08T10:30:00Z"
    }
  ],
  "links": {
    "first": "http://example.com/ai/decisions?page=1",
    "last": "http://example.com/ai/decisions?page=5",
    "prev": null,
    "next": "http://example.com/ai/decisions?page=2"
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 5,
    "per_page": 15,
    "to": 15,
    "total": 73
  }
}
```

---

### Get Single Decision

```http
GET /ai/decisions/{id}
```

#### Response

```json
{
  "id": 1,
  "decision_type": "priority_change",
  "entity_type": "task",
  "entity_id": 42,
  "task": {
    "id": 42,
    "title": "Database Optimization",
    "status": "in_progress",
    "priority": "medium"
  },
  "recommendation": "Increase priority to High",
  "reasoning": {
    "factors": ["Task is blocking others", "Deadline approaching"],
    "impact": "High",
    "urgency": "High",
    "data_used": {
      "dependencies_count": 5,
      "days_until_deadline": 2,
      "completion_percentage": 45
    }
  },
  "confidence_score": 0.85,
  "alternatives": [
    {
      "action": "Extend deadline instead",
      "impact": "Medium",
      "confidence": 0.72,
      "pros": ["Less pressure", "Better quality"],
      "cons": ["Delays dependent tasks"]
    }
  ],
  "user_action": "pending",
  "user_comment": null,
  "reviewed_by": null,
  "reviewed_at": null,
  "executed_at": null,
  "execution_result": null,
  "guardrail_violations": 0,
  "guardrail_check": {
    "passed": true,
    "violations": [],
    "total_violations": 0,
    "highest_severity": null
  },
  "created_at": "2026-01-08T10:30:00Z",
  "updated_at": "2026-01-08T10:30:00Z"
}
```

---

### Take Action on Decision

```http
POST /ai/decisions/{id}/action
```

#### Request Body

```json
{
  "action": "accept",
  "comment": "Approved based on analysis"
}
```

#### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `action` | string | Yes | `accept`, `reject`, `modify`, `defer` |
| `comment` | string | No | User comment |
| `modified_data` | object | Conditional | Required if action is `modify` |

#### Response

```json
{
  "success": true,
  "message": "Decision accepted and executed successfully",
  "decision": {
    "id": 1,
    "user_action": "accepted",
    "executed_at": "2026-01-08T11:00:00Z",
    "execution_result": {
      "status": "success",
      "changes_applied": {
        "task_id": 42,
        "field": "priority",
        "old_value": "medium",
        "new_value": "high"
      }
    }
  }
}
```

---

## AI Prompts

### List Prompts

```http
GET /ai/prompts
```

#### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `category` | string | Filter by category |
| `is_active` | boolean | Filter active/inactive |
| `search` | string | Search in name/description |

#### Response

```json
{
  "data": [
    {
      "id": 1,
      "name": "task_priority_analysis",
      "description": "Analyzes task priority based on context",
      "category": "task_analysis",
      "template": "...",
      "variables": ["task_id", "project_context"],
      "version": 2,
      "is_active": true,
      "usage_count": 145,
      "last_used_at": "2026-01-08T09:15:00Z"
    }
  ]
}
```

---

### Create Prompt

```http
POST /ai/prompts
```

#### Request Body

```json
{
  "name": "custom_analysis",
  "description": "Custom analysis template",
  "category": "custom",
  "template": "Analyze {{entity}} with focus on {{focus_area}}",
  "variables": ["entity", "focus_area"],
  "is_active": true
}
```

#### Response

```json
{
  "success": true,
  "message": "Prompt template created successfully",
  "prompt": {
    "id": 10,
    "name": "custom_analysis",
    "...": "..."
  }
}
```

---

## AI Insights

### Get System Insights

```http
GET /ai/insights
```

#### Response

```json
{
  "insights": {
    "health_score": 94.5,
    "system_status": "healthy",
    "metrics": {
      "total_decisions": 1247,
      "pending_count": 12,
      "acceptance_rate": 87.2,
      "avg_confidence": 0.823
    }
  },
  "trends": {
    "acceptance_trend": [0.85, 0.86, 0.88, 0.87, 0.89],
    "confidence_trend": [0.80, 0.81, 0.82, 0.83, 0.82],
    "volume_trend": [45, 52, 48, 61, 55]
  },
  "recent_decisions": [...]
}
```

---

### Get Performance Metrics

```http
GET /ai/insights/performance
```

#### Response

```json
{
  "success": true,
  "metrics": {
    "response_time": {
      "avg": 1.2,
      "min": 0.5,
      "max": 3.5,
      "unit": "seconds"
    },
    "accuracy": {
      "prediction_accuracy": 0.91,
      "user_agreement_rate": 0.87
    },
    "throughput": {
      "decisions_per_day": 42,
      "decisions_per_hour": 1.75
    }
  }
}
```

---

### Get System Health

```http
GET /ai/insights/health
```

#### Response

```json
{
  "success": true,
  "health_score": 94.5,
  "status": "healthy",
  "metrics": {
    "cache_hit_rate": 0.95,
    "error_rate": 0.02,
    "avg_processing_time": 1.2,
    "queue_length": 3
  },
  "issues": []
}
```

---

## AI Guardrails

### Get Guardrail Settings

```http
GET /ai/guardrails
```

#### Response

```json
{
  "rules": {
    "no_data_deletion": true,
    "no_critical_changes": true,
    "no_mass_changes": true,
    "no_unverified_actions": true
  },
  "thresholds": {
    "mass_change_limit": 5,
    "min_confidence_score": 0.7
  },
  "statistics": {
    "total_checks": 1247,
    "total_violations": 34,
    "violation_rate": 2.7,
    "rules_count": 4
  }
}
```

---

### Update Guardrail Rule

```http
POST /ai/guardrails/rule/update
```

#### Request Body

```json
{
  "rule": "no_mass_changes",
  "enabled": false
}
```

#### Response

```json
{
  "success": true,
  "message": "Guardrail rule updated successfully",
  "rule": "no_mass_changes",
  "enabled": false
}
```

---

### Update Threshold

```http
POST /ai/guardrails/threshold/update
```

#### Request Body

```json
{
  "key": "mass_change_limit",
  "value": 10
}
```

#### Response

```json
{
  "success": true,
  "message": "Threshold updated successfully",
  "key": "mass_change_limit",
  "value": 10
}
```

---

## AI Advanced Features

### Generate Development Plan

```http
POST /ai/features/development-plan
```

#### Request Body

```json
{
  "project_id": 5,
  "requirements": "Need Arabic support and mobile compatibility"
}
```

#### Response

```json
{
  "success": true,
  "plan": {
    "overview": {
      "title": "Development Plan for: E-commerce Platform",
      "summary": "AI-generated comprehensive development plan",
      "estimated_duration": 14,
      "complexity": "Medium",
      "confidence": 0.85
    },
    "phases": [
      {
        "name": "Phase 1: Planning & Analysis",
        "duration": "1-2 weeks",
        "tasks": [
          "Requirements gathering",
          "Technical feasibility study",
          "Resource allocation",
          "Risk assessment"
        ],
        "deliverables": [
          "Requirements document",
          "Technical specification",
          "Project plan"
        ]
      }
    ],
    "timeline": {
      "start_date": "2026-01-08",
      "estimated_end_date": "2026-04-22",
      "total_weeks": 14,
      "milestones": [
        {"week": 2, "milestone": "Requirements finalized"},
        {"week": 5, "milestone": "Design approved"},
        {"week": 10, "milestone": "Development complete"},
        {"week": 12, "milestone": "Testing complete"},
        {"week": 14, "milestone": "Production deployment"}
      ]
    },
    "resources": {
      "team_requirements": {
        "Backend developers": 2,
        "Frontend developers": 2,
        "UI/UX designer": 1,
        "QA engineer": 1,
        "DevOps engineer": 1
      },
      "current_team": 3,
      "additional_resources_needed": 4
    },
    "risks": [
      {
        "risk": "Insufficient team size",
        "severity": "High",
        "mitigation": "Consider hiring additional team members"
      }
    ],
    "recommendations": [
      "Implement daily standups",
      "Use AI-powered task prioritization",
      "Set up automated testing early"
    ]
  },
  "project": "E-commerce Platform"
}
```

---

### Breakdown Project

```http
POST /ai/features/breakdown-project
```

#### Request Body

```json
{
  "project_id": 5,
  "granularity": "medium"
}
```

#### Parameters

| Parameter | Type | Options | Description |
|-----------|------|---------|-------------|
| `project_id` | integer | | Project ID |
| `granularity` | string | `low`, `medium`, `high` | Detail level |

#### Response

```json
{
  "success": true,
  "breakdown": {
    "project_id": 5,
    "project_title": "E-commerce Platform",
    "granularity": "medium",
    "total_estimated_tasks": 23,
    "categories": {
      "Planning": {
        "tasks": [
          "Define project scope and objectives",
          "Create detailed requirements document",
          "Establish project timeline",
          "Identify key stakeholders"
        ],
        "estimated_duration": "2 weeks",
        "priority": "Critical"
      },
      "Design": {
        "tasks": [
          "Create system architecture diagram",
          "Design database schema",
          "Create UI/UX mockups",
          "Define API endpoints"
        ],
        "estimated_duration": "2 weeks",
        "priority": "High"
      }
    },
    "generated_at": "2026-01-08T12:00:00Z"
  },
  "project": "E-commerce Platform"
}
```

---

### Create Study

```http
POST /ai/features/create-study
```

#### Request Body

```json
{
  "project_id": 5,
  "study_type": "feasibility"
}
```

#### Parameters

| Parameter | Type | Options | Description |
|-----------|------|---------|-------------|
| `project_id` | integer | | Project ID |
| `study_type` | string | `feasibility`, `technical`, `risk` | Study type |

#### Response

```json
{
  "success": true,
  "study": {
    "type": "feasibility",
    "project": "E-commerce Platform",
    "analysis": {
      "executive_summary": "Project appears technically and financially feasible",
      "technical_feasibility": "High - existing technology stack is suitable",
      "financial_feasibility": "Medium - budget allocation needs review",
      "operational_feasibility": "High - team has necessary skillset",
      "schedule_feasibility": "Medium - timeline is ambitious but achievable",
      "conclusion": "Project is recommended to proceed with contingency planning"
    },
    "generated_at": "2026-01-08T12:15:00Z"
  }
}
```

---

### Analyze Task

```http
POST /ai/features/analyze-task
```

#### Request Body

```json
{
  "task_id": 42
}
```

#### Response

```json
{
  "success": true,
  "analysis": {
    "task": "Database Optimization",
    "estimated_effort": "8-16 hours",
    "complexity": "Medium-High",
    "dependencies": {
      "blocking": [],
      "blocked_by": []
    },
    "recommendations": [
      "Set a due date for better time management",
      "Consider breaking down this high-priority task"
    ]
  }
}
```

---

## Error Responses

### Standard Error Format

```json
{
  "success": false,
  "message": "Error message here",
  "errors": {
    "field": ["Validation error message"]
  }
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| `200` | Success |
| `201` | Created |
| `400` | Bad Request |
| `401` | Unauthorized |
| `403` | Forbidden (insufficient permissions) |
| `404` | Not Found |
| `422` | Validation Error |
| `500` | Server Error |

---

## Rate Limiting

- **Limit:** 60 requests per minute per user
- **Headers:**
  - `X-RateLimit-Limit`: Total requests allowed
  - `X-RateLimit-Remaining`: Remaining requests
  - `X-RateLimit-Reset`: Timestamp when limit resets

---

## Webhooks (Future Feature)

Coming soon: Real-time notifications for AI events.

---

## Code Examples

### JavaScript (Axios)

```javascript
// Get decisions
const response = await axios.get('/ai/decisions', {
  params: {
    status: 'pending',
    per_page: 10
  }
});

// Accept decision
await axios.post(`/ai/decisions/${decisionId}/action`, {
  action: 'accept',
  comment: 'Approved'
}, {
  headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  }
});
```

### PHP (Guzzle)

```php
use GuzzleHttp\Client;

$client = new Client(['base_uri' => 'https://your-domain.com']);

// Get insights
$response = $client->get('/ai/insights', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
    ]
]);

$insights = json_decode($response->getBody(), true);
```

### cURL

```bash
# Get decisions
curl -X GET "https://your-domain.com/ai/decisions?status=pending" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"

# Create development plan
curl -X POST "https://your-domain.com/ai/features/development-plan" \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: YOUR_TOKEN" \
  -d '{
    "project_id": 5,
    "requirements": "Need Arabic support"
  }'
```

---

## Postman Collection

Download our Postman collection for easy testing:
[AI_API.postman_collection.json](./postman/AI_API.postman_collection.json)

---

## Changelog

### v2.0.0 (January 2026)
- Initial AI system release
- Decisions API
- Prompts management
- Insights & Analytics
- Guardrails configuration
- Advanced features (Dev Plan, Breakdown, Studies)

---

**Support:** api-support@projectmanagement.com  
**Documentation:** https://docs.projectmanagement.com/ai  
**Last Updated:** January 8, 2026
