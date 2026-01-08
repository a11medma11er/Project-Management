# Administrator Handbook - AI System

## System Administration

### Installation & Setup

#### 1. Environment Configuration
```env
# Core AI Settings
AI_SYSTEM_ENABLED=true
AI_DEFAULT_PROVIDER=local

# External AI Providers (Optional)
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4
CLAUDE_API_KEY=sk-ant-...

# Integrations
SLACK_WEBHOOK_URL=https://hooks.slack.com/...
AI_WEBHOOK_URL=https://your-domain.com/webhook

# Performance
AI_CACHE_TTL=3600
CACHE_DRIVER=redis

# Security
AI_MIN_CONFIDENCE=0.7
AI_MAX_ACTIONS_PER_HOUR=100
```

#### 2. Database Migration
```bash
# Run AI migrations
php artisan migrate

# Seed AI permissions
php artisan db:seed --class=AIPermissionsSeeder
```

#### 3. Permission Setup
Required permissions:
- `access-ai-control` - View AI dashboard
- `manage-ai-decisions` - Review decisions
- `manage-ai-prompts` - Edit prompts
- `manage-ai-guardrails` - Configure safety
- `manage-ai-settings` - System settings
- `manage-ai-safety` - Safety settings
- `view-ai-insights` - View analytics
- `manage-ai-reports` - Generate reports

### User Management

#### Assigning AI Permissions
```php
// Via code
$user->givePermissionTo('access-ai-control');

// Via admin panel
Admin → Users → Edit → Permissions → AI Permissions
```

#### Role-Based Access
```php
// Create AI Admin role
$role = Role::create(['name' => 'AI Administrator']);
$role->givePermissionTo([
    'access-ai-control',
    'manage-ai-decisions',
    'manage-ai-settings',
]);

// Assign to user
$user->assignRole('AI Administrator');
```

### System Configuration

#### Guardrails Configuration
```php
// config/ai.php
'guardrails' => [
    'min_confidence' => 0.7,           // Minimum confidence for auto-execution
    'max_actions_per_hour' => 100,    // Rate limit
    'require_approval_below' => 0.8,  // Approval threshold
],
```

#### Cache Configuration
```php
'cache_ttl' => 3600,  // 1 hour
'timeout' => 30,      // API timeout in seconds
```

### Monitoring & Maintenance

#### Daily Tasks
1. **Review Decision Queue**
   - Check pending decisions
   - Monitor approval rates
   - Address stuck decisions

2. **Check System Health**
   - Performance metrics
   - Error logs
   - Rate limits

3. **Monitor Learning**
   - Accuracy trends
   - Calibration status
   - Feedback patterns

#### Weekly Tasks
1. **Generate Reports**
   - Weekly summary
   - Accuracy report
   - User engagement

2. **Cache Maintenance**
   - Clear old cache
   - Warm up frequently used data

3. **Security Review**
   - Check security logs
   - Review blocked requests
   - Update security rules

#### Monthly Tasks
1. **Performance Audit**
   - Query optimization
   - Index analysis
   - Slow operations review

2. **Learning Review**
   - Calibration adjustment
   - Confidence threshold review
   - Pattern analysis

3. **Backup & Archival**
   - Export old decisions
   - Archive feedback logs
   - Clean up old data

### Automation Management

#### CLI Commands
```bash
# Run automation manually
php artisan ai:automate

# With specific type
php artisan ai:automate --type=overdue_tasks

# Schedule in Kernel.php
$schedule->command('ai:automate')->hourly();
```

#### Automation Rules
Create rules via `/admin/ai/workflows`:
```json
{
  "trigger": "task_overdue",
  "conditions": {
    "days_overdue": 3,
    "priority": "high"
  },
  "action": "analyze_task",
  "enabled": true
}
```

### Integration Management

#### OpenAI Setup
1. Get API key from platform.openai.com
2. Add to `.env`:
   ```env
   OPENAI_API_KEY=sk-...
   OPENAI_MODEL=gpt-4
   ```
3. Test via Integrations dashboard

#### Slack Setup
1. Create Slack app
2. Enable Incoming Webhooks
3. Add webhook URL to `.env`
4. Test via Integrations dashboard

### Performance Tuning

#### Database Optimization
```sql
-- Recommended indexes
CREATE INDEX idx_ai_decisions_type ON ai_decisions(decision_type);
CREATE INDEX idx_ai_decisions_created ON ai_decisions(created_at);
CREATE INDEX idx_ai_decisions_confidence ON ai_decisions(confidence_score);
CREATE INDEX idx_ai_feedback_decision ON ai_feedback_logs(decision_id);
```

#### Cache Strategy
```bash
# Clear all AI cache
php artisan cache:forget ai_cache:*

# Warm up cache
# Via API: POST /admin/ai/performance/warm-cache
```

#### Query Optimization
- Use database views for complex queries
- Enable query caching
- Monitor slow queries via Performance dashboard

### Security Management

#### Rate Limiting
```php
// Adjust in config/ai.php
'rate_limit' => 60,  // requests per minute
```

#### Input Validation
All inputs automatically:
- Sanitized for XSS
- Checked for SQL injection
- Validated against whitelist

#### Threat Detection
Monitor via Security dashboard:
- Blocked requests
- Suspicious activities
- Failed authentications

### Troubleshooting

#### Common Issues

**1. High Memory Usage**
```bash
# Check PHP memory limit
php -i | grep memory_limit

# Increase if needed
# php.ini: memory_limit = 512M
```

**2. Slow Responses**
- Check cache hit rate
- Review slow operations
- Optimize database queries
- Consider Redis for caching

**3. Low Accuracy**
- Review calibration data
- Provide more feedback
- Check training data quality
- Adjust confidence thresholds

**4. Integration Failures**
- Verify API keys
- Check connectivity
- Review rate limits
- Check error logs

### Backup & Recovery

#### Data Backup
```bash
# Backup AI decisions
php artisan ai:export-decisions --from=2026-01-01

# Backup feedback logs
php artisan ai:export-feedback --from=2026-01-01
```

#### Restore Procedures
1. Restore database from backup
2. Re-run migrations
3. Clear cache
4. Verify data integrity

### Security Audit Checklist

- [ ] All API keys rotated quarterly
- [ ] Rate limits configured
- [ ] Input validation enabled
- [ ] Audit logging active
- [ ] Permissions properly assigned
- [ ] HTTPS enforced
- [ ] CSRF protection enabled
- [ ] Security logs reviewed weekly

### Performance Benchmarks

**Target Metrics:**
- Response time: <500ms (95th percentile)
- Cache hit rate: >80%
- Decision accuracy: >75%
- System uptime: >99.5%

**Monitoring:**
- Use Performance dashboard
- Set up alerts for anomalies
- Review metrics weekly

---

**Version:** 2.0.0
**Last Updated:** January 2026
