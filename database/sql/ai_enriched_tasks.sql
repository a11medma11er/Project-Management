-- AI Enriched Tasks View
-- Provides comprehensive task data enriched with relationships, metrics, and AI-relevant context

CREATE OR REPLACE VIEW ai_enriched_tasks AS
SELECT 
    -- Core Task Fields
    t.id AS task_id,
    t.task_number,
    t.title,
    t.description,
    t.status,
    t.priority,
    t.due_date,
    t.client_name,
    t.created_at,
    t.updated_at,
    
    -- Project Context
    t.project_id,
    p.title AS project_title,
    p.status AS project_status,
    p.deadline AS project_deadline,
    
    -- Creator Info
    t.created_by,
    creator.name AS creator_name,
    creator.email AS creator_email,
    
    -- Task Metrics
    DATEDIFF(t.due_date, CURDATE()) AS days_until_due,
    CASE 
        WHEN t.due_date < CURDATE() AND t.status NOT IN ('completed', 'cancelled') THEN DATEDIFF(CURDATE(), t.due_date)
        ELSE 0
    END AS days_overdue,
    
    -- Urgency Calculation
    CASE
        WHEN t.due_date < CURDATE() AND t.status NOT IN ('completed', 'cancelled') THEN
            CASE
                WHEN DATEDIFF(CURDATE(), t.due_date) > 7 THEN 'critical'
                WHEN DATEDIFF(CURDATE(), t.due_date) > 3 THEN 'high'
                WHEN DATEDIFF(CURDATE(), t.due_date) > 0 THEN 'medium'
                ELSE 'normal'
            END
        WHEN DATEDIFF(t.due_date, CURDATE()) <= 2 THEN 'high'
        WHEN DATEDIFF(t.due_date, CURDATE()) <= 7 THEN 'medium'
        ELSE 'normal'
    END AS urgency_level,
    
    -- Relationship Counts
    (SELECT COUNT(*) FROM task_comments WHERE task_id = t.id) AS comment_count,
    (SELECT COUNT(*) FROM attachments WHERE task_id = t.id) AS attachment_count,
    (SELECT COUNT(*) FROM time_entries WHERE task_id = t.id) AS time_entry_count,
    (SELECT COALESCE(SUM(hours), 0) FROM time_entries WHERE task_id = t.id) AS total_hours_logged,
    
    -- Activity Metrics
    (SELECT COUNT(*) 
     FROM activity_log 
     WHERE subject_type = 'App\\Models\\Task' 
     AND subject_id = t.id 
     AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ) AS activity_count_7d,
    
    (SELECT MAX(created_at) 
     FROM activity_log 
     WHERE subject_type = 'App\\Models\\Task' 
     AND subject_id = t.id
    ) AS last_activity_at,
    
    -- Completion Status
    CASE 
        WHEN t.status IN ('completed', 'cancelled') THEN true
        ELSE false
    END AS is_completed,
    
    -- Blocking Analysis
    CASE
        WHEN t.status = 'blocked' THEN true
        ELSE false
    END AS is_blocked,
    
    -- AI Context Flags
    CASE
        WHEN t.due_date < CURDATE() AND t.status NOT IN ('completed', 'cancelled') THEN true
        ELSE false
    END AS needs_attention,
    
    CASE
        WHEN (SELECT COUNT(*) FROM task_comments WHERE task_id = t.id) = 0 
        AND DATEDIFF(NOW(), t.created_at) > 3 THEN true
        ELSE false
    END AS low_engagement,
    
    CASE
        WHEN (SELECT COUNT(*) FROM activity_log 
              WHERE subject_type = 'App\\Models\\Task' 
              AND subject_id = t.id 
              AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) = 0
        AND t.status NOT IN ('completed', 'cancelled')
        THEN true
        ELSE false
    END AS stale_task

FROM tasks t
LEFT JOIN projects p ON t.project_id = p.id
LEFT JOIN users creator ON t.created_by = creator.id
WHERE t.deleted_at IS NULL
