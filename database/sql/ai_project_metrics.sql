-- AI Project Metrics View
-- Aggregates project-level data with team performance and AI-relevant insights

CREATE OR REPLACE VIEW ai_project_metrics AS
SELECT 
    -- Core Project Fields
    p.id AS project_id,
    p.title,
    p.slug,
    p.description,
    p.status,
    p.priority,
    p.deadline,
    p.start_date,
    p.progress,
    p.category,
    p.privacy,
    p.is_favorite,
    p.created_at,
    p.updated_at,
    
    -- Team Lead Info
    p.team_lead_id,
    team_lead.name AS team_lead_name,
    team_lead.email AS team_lead_email,
    
    -- Creator Info
    p.created_by,
    creator.name AS creator_name,
    
    -- Task Statistics
    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND deleted_at IS NULL) AS total_tasks,
    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'completed' AND deleted_at IS NULL) AS completed_tasks,
    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'in_progress' AND deleted_at IS NULL) AS in_progress_tasks,
    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'pending' AND deleted_at IS NULL) AS pending_tasks,
    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'blocked' AND deleted_at IS NULL) AS blocked_tasks,
    
    -- Overdue Tasks
    (SELECT COUNT(*) 
     FROM tasks 
     WHERE project_id = p.id 
     AND due_date < CURDATE() 
     AND status NOT IN ('completed', 'cancelled')
     AND deleted_at IS NULL
    ) AS overdue_tasks,
    
    -- Completion Rate
    CASE 
        WHEN (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND deleted_at IS NULL) > 0 
        THEN ROUND(
            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'completed' AND deleted_at IS NULL) * 100.0 / 
            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND deleted_at IS NULL),
            2
        )
        ELSE 0
    END AS completion_rate,
    
    -- Time Metrics
    (SELECT COALESCE(SUM(te.hours), 0)
     FROM time_entries te
     JOIN tasks t ON te.task_id = t.id
     WHERE t.project_id = p.id AND t.deleted_at IS NULL
    ) AS total_hours_logged,
    
    -- Activity Level
    (SELECT COUNT(*) 
     FROM activity_log 
     WHERE subject_type = 'App\\Models\\Project' 
     AND subject_id = p.id 
     AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ) AS activity_count_7d,
    
    (SELECT COUNT(*) 
     FROM activity_log 
     WHERE subject_type = 'App\\Models\\Project' 
     AND subject_id = p.id 
     AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ) AS activity_count_30d,
    
    -- Last Activity
    (SELECT MAX(created_at) 
     FROM activity_log 
     WHERE subject_type = 'App\\Models\\Project' 
     AND subject_id = p.id
    ) AS last_activity_at,
    
    -- Health Indicators
    CASE
        WHEN p.deadline < CURDATE() AND p.status != 'completed' THEN 'overdue'
        WHEN DATEDIFF(p.deadline, CURDATE()) <= 7 AND p.status != 'completed' THEN 'at_risk'
        WHEN (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'blocked' AND deleted_at IS NULL) > 0 THEN 'has_blockers'
        WHEN (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND due_date < CURDATE() AND status NOT IN ('completed', 'cancelled') AND deleted_at IS NULL) > 0 THEN 'has_overdue_tasks'
        ELSE 'on_track'
    END AS health_status,
    
    -- Progress vs Completion Comparison
    CASE 
        WHEN (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND deleted_at IS NULL) > 0 
        THEN ROUND(
            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'completed' AND deleted_at IS NULL) * 100.0 / 
            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND deleted_at IS NULL),
            2
        )
        ELSE 0
    END AS calculated_progress,
    
    -- AI Context Flags
    CASE
        WHEN (SELECT COUNT(*) FROM activity_log 
              WHERE subject_type = 'App\\Models\\Project' 
              AND subject_id = p.id 
              AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)) = 0
        AND p.status NOT IN ('completed', 'on_hold')
        THEN true
        ELSE false
    END AS is_stale,
    
    CASE
        WHEN (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'blocked' AND deleted_at IS NULL) > 2
        THEN true
        ELSE false
    END AS has_multiple_blockers,
    
    CASE
        WHEN (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND deleted_at IS NULL) = 0
        AND DATEDIFF(NOW(), p.created_at) > 7
        THEN true
        ELSE false
    END AS needs_tasks

FROM projects p
LEFT JOIN users team_lead ON p.team_lead_id = team_lead.id
LEFT JOIN users creator ON p.created_by = creator.id
