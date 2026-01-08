# AI System API Reference

## Base URL
```
/admin/ai
```

## Authentication
All endpoints require authentication and appropriate permissions.

```php
// Headers
Authorization: Bearer {token}
X-CSRF-TOKEN: {csrf_token}
```

---

## AI Control

### Get Dashboard
```http
GET /admin/ai/control
```

**Response:**
```json
{
  "ai_enabled": true,
  "recent_decisions": [],
  "stats": {
    "total_decisions": 150,
    "accuracy_rate": 78.5,
    "pending_reviews": 5
  }
}
```

---

## Decisions

### List Decisions
```http
GET /admin/ai/decisions?type={type}&status={status}&page={page}
```

**Parameters:**
- `type` (optional): decision_type filter
- `status` (optional): pending|accepted|rejected
- `page` (optional): page number

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "decision_type": "priority_adjustment",
      "confidence_score": 0.85,
      "reasoning": "Task approaching deadline",
      "user_action": null,
      "created_at": "2026-01-08T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 50
  }
}
```

### Accept Decision
```http
POST /admin/ai/decisions/{id}/accept
```

**Body:**
```json
{
  "feedback": "Accurate recommendation"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Decision accepted"
}
```

### Reject Decision
```http
POST /admin/ai/decisions/{id}/reject
```

**Body:**
```json
{
  "feedback": "Not applicable in this case"
}
```

---

## Learning

### Get Learning Data
```http
GET /admin/ai/learning/data?days={days}
```

**Parameters:**
- `days` (optional): Number of days (default: 30)

**Response:**
```json
{
  "accuracy_rate": 78.5,
  "total_decisions": 150,
  "accepted": 118,
  "rejected": 32,
  "trend": [
    {"date": "2026-01-01", "accuracy": 75},
    {"date": "2026-01-02", "accuracy": 76}
  ]
}
```

---

## Reports

### Generate Report
```http
POST /admin/ai/reports/generate
```

**Body:**
```json
{
  "template": "decision_summary",
  "date_from": "2026-01-01",
  "date_to": "2026-01-31"
}
```

**Response:**
```json
{
  "success": true,
  "report": {
    "summary": {
      "total_decisions": 150,
      "avg_confidence": 0.82
    },
    "details": []
  }
}
```

### Export to PDF
```http
POST /admin/ai/reports/export/pdf
```

**Body:**
```json
{
  "template": "decision_summary",
  "date_from": "2026-01-01",
  "date_to": "2026-01-31"
}
```

**Response:** PDF file download

### Export to Excel
```http
POST /admin/ai/reports/export/excel
```

---

## Workflows

### Run Automation
```http
POST /admin/ai/workflows/run
```

**Response:**
```json
{
  "success": true,
  "results": {
    "tasks_analyzed": 25,
    "decisions_created": 8,
    "auto_executed": 2,
    "errors": []
  }
}
```

### Create Rule
```http
POST /admin/ai/workflows/create-rule
```

**Body:**
```json
{
  "trigger": "task_overdue",
  "conditions": {
    "days_overdue": 3
  },
  "action": "analyze_task",
  "enabled": true
}
```

### Schedule Analysis
```http
POST /admin/ai/workflows/schedule
```

**Body:**
```json
{
  "type": "project_health",
  "params": {},
  "run_at": "2026-01-10T10:00:00"
}
```

---

## Integrations

### Test AI Provider
```http
POST /admin/ai/integrations/test-provider
```

**Body:**
```json
{
  "provider": "openai",
  "prompt": "Analyze this task"
}
```

**Response:**
```json
{
  "success": true,
  "result": {
    "provider": "openai",
    "response": "Task analysis result...",
    "model": "gpt-4"
  }
}
```

### Test Webhook
```http
POST /admin/ai/integrations/test-webhook
```

**Body:**
```json
{
  "event": "test.event",
  "data": {"key": "value"}
}
```

### Test Slack
```http
POST /admin/ai/integrations/test-slack
```

**Body:**
```json
{
  "message": "Test notification",
  "channel": "#ai-notifications"
}
```

---

## Performance

### Get System Metrics
```http
GET /admin/ai/performance/system-metrics
```

**Response:**
```json
{
  "success": true,
  "metrics": {
    "memory_usage": 128.5,
    "memory_peak": 256.2,
    "cpu_load": [0.5, 0.6, 0.7]
  }
}
```

### Clear Cache
```http
POST /admin/ai/performance/clear-cache
```

**Body:**
```json
{
  "pattern": "*"
}
```

### Warm Cache
```http
POST /admin/ai/performance/warm-cache
```

**Response:**
```json
{
  "success": true,
  "warmed": ["metrics", "decisions", "analytics"]
}
```

---

## Security

### Validate Input
```http
POST /admin/ai/security/validate-input
```

**Body:**
```json
{
  "data": "User input to validate"
}
```

**Response:**
```json
{
  "success": true,
  "sanitized": "User input to validate",
  "suspicious": false
}
```

### Check Rate Limit
```http
GET /admin/ai/security/check-rate-limit?user_id={id}&action={action}
```

**Response:**
```json
{
  "success": true,
  "allowed": true
}
```

---

## Error Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden (Permission denied) |
| 404 | Not Found |
| 429 | Rate Limit Exceeded |
| 500 | Internal Server Error |

---

## Rate Limits

- **Default:** 60 requests/minute per user
- **AI Analysis:** 20 requests/minute
- **Reports:** 10 requests/minute

---

**Version:** 2.0.0
**Last Updated:** January 2026
