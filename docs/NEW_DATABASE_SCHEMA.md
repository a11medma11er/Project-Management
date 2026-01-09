# Database Schema Documentation

## Overview

The database uses MySQL 8.0+ with InnoDB engine and utf8mb4_unicode_ci character set. The schema is designed for performance, data integrity, and scalability.

## Entity Relationship Overview

```
Users ──< Projects (created_by)
Users ──< Projects (team_lead_id)
Users ──<many-to-many>─ Projects (project_members)
Users ──<many-to-many>─ Tasks (task_user)
Users ──< Tasks (created_by)
Users ──< AIDecisions (reviewed_by)
Users ──< AIPrompts (created_by)

Projects ──< Tasks
Projects ──< ProjectMembers
Projects ──< ProjectAttachments
Projects ──< ProjectComments
Projects ──< AIDecisions

Tasks ──< TaskAttachments
Tasks ──< TaskComments
Tasks ──< TaskSubTasks
Tasks ──< TaskTags
Tasks ──< TaskTimeEntries
Tasks ──< AIDecisions

Comments ──< Comments (parent_id - nested)
```

---

## Core Tables

### users

User accounts and authentication.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| name | varchar(255) | NO | - | User full name |
| email | varchar(255) | NO | - | User email (unique) |
| email_verified_at | timestamp | YES | NULL | Email verification timestamp |
| password | varchar(255) | NO | - | Hashed password (bcrypt) |
| avatar | varchar(255) | YES | NULL | Avatar file path |
| remember_token | varchar(100) | YES | NULL | Remember me token |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (email)

**Relationships**:
- Has many projects (created_by)
- Has many projects (team_lead_id)
- Has many tasks (created_by)
- Belongs to many projects (project_members)
- Belongs to many tasks (task_user)
- Has many roles (via model_has_roles)
- Has many permissions (via model_has_permissions)

---

## Project Management Tables

### projects

Project entities with status tracking.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| title | varchar(255) | NO | - | Project title |
| slug | varchar(255) | NO | - | URL-friendly slug (unique) |
| thumbnail | varchar(255) | YES | NULL | Thumbnail image path |
| description | text | NO | - | Project description |
| priority | enum | NO | 'Medium' | High, Medium, Low |
| status | enum | NO | 'Inprogress' | Inprogress, Completed, On Hold |
| privacy | enum | NO | 'Team' | Private, Team, Public |
| category | varchar(255) | YES | NULL | Project category |
| skills | json | YES | NULL | Required skills array |
| deadline | date | NO | - | Project deadline |
| start_date | date | YES | NULL | Project start date |
| progress | tinyint(3) unsigned | NO | 0 | Progress percentage (0-100) |
| is_favorite | tinyint(1) | NO | 0 | Favorite flag |
| team_lead_id | bigint(20) unsigned | YES | NULL | Foreign key to users |
| created_by | bigint(20) unsigned | NO | - | Foreign key to users |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (slug)
- KEY (status)
- KEY (priority)
- KEY (deadline)
- KEY (created_by)
- FOREIGN KEY (team_lead_id) REFERENCES users(id) ON DELETE SET NULL
- FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE

**Enum Values**:
- priority: 'High', 'Medium', 'Low'
- status: 'Inprogress', 'Completed', 'On Hold'
- privacy: 'Private', 'Team', 'Public'

**Relationships**:
- Belongs to User (creator via created_by)
- Belongs to User (team lead via team_lead_id)
- Has many Tasks
- Has many ProjectMembers
- Has many ProjectAttachments
- Has many ProjectComments
- Has many AIDecisions

**Business Rules**:
- Slug auto-generated from title on creation
- Progress auto-calculated based on task completion
- Skills stored as JSON array

---

### project_members

Many-to-many relationship between projects and users.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| project_id | bigint(20) unsigned | NO | - | Foreign key to projects |
| user_id | bigint(20) unsigned | NO | - | Foreign key to users |
| role | varchar(255) | YES | NULL | Member role (e.g., "Developer") |
| joined_at | timestamp | NO | CURRENT_TIMESTAMP | Join timestamp |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (project_id, user_id)
- KEY (project_id)
- KEY (user_id)
- FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

**Business Rules**:
- User cannot be added twice to same project (unique constraint)
- Role is optional descriptive field

---

### project_attachments

File attachments for projects.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| project_id | bigint(20) unsigned | NO | - | Foreign key to projects |
| file_name | varchar(255) | NO | - | Original filename |
| file_path | varchar(255) | NO | - | Storage path |
| file_type | varchar(255) | YES | NULL | MIME type |
| file_size | bigint(20) | YES | NULL | File size in bytes |
| uploaded_by | bigint(20) unsigned | NO | - | Foreign key to users |
| created_at | timestamp | YES | NULL | Upload timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (project_id)
- FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
- FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE

---

### project_comments

Comments on projects with nested reply support.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| project_id | bigint(20) unsigned | NO | - | Foreign key to projects |
| user_id | bigint(20) unsigned | NO | - | Foreign key to users |
| parent_id | bigint(20) unsigned | YES | NULL | Parent comment ID (for replies) |
| comment | text | NO | - | Comment content |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (project_id)
- KEY (parent_id)
- FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (parent_id) REFERENCES project_comments(id) ON DELETE CASCADE

**Business Rules**:
- parent_id = NULL for top-level comments
- parent_id references another comment for replies
- Supports unlimited nesting depth

---

## Task Management Tables

### tasks

Task entities with status and priority tracking.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| task_number | varchar(255) | NO | - | Auto-generated (#VLZ0001) |
| title | varchar(255) | NO | - | Task title |
| description | text | YES | NULL | Task description |
| project_id | bigint(20) unsigned | YES | NULL | Foreign key to projects |
| client_name | varchar(255) | YES | NULL | Client name |
| due_date | date | NO | - | Due date |
| status | enum | NO | 'New' | New, Pending, Inprogress, Completed |
| priority | enum | NO | 'Medium' | High, Medium, Low |
| progress | tinyint(3) unsigned | NO | 0 | Progress percentage (0-100) |
| position | int(11) | YES | NULL | Position in Kanban board |
| kanban_status | varchar(255) | YES | NULL | Kanban board status |
| created_by | bigint(20) unsigned | YES | NULL | Foreign key to users |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |
| deleted_at | timestamp | YES | NULL | Soft delete timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (task_number)
- KEY (project_id)
- KEY (status)
- KEY (priority)
- KEY (due_date)
- KEY (created_by)
- KEY (kanban_status, position)
- FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL
- FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL

**Enum Values**:
- status: 'New', 'Pending', 'Inprogress', 'Completed'
- priority: 'High', 'Medium', 'Low'

**Relationships**:
- Belongs to Project
- Belongs to User (creator via created_by)
- Belongs to many Users (assignees via task_user)
- Has many TaskAttachments
- Has many TaskComments
- Has many TaskSubTasks
- Has many TaskTags
- Has many TaskTimeEntries
- Has many AIDecisions

**Business Rules**:
- task_number auto-generated: #VLZ + zero-padded ID
- kanban_status separate from regular status for board management
- Soft deletes enabled for recovery
- On save/delete, triggers project progress recalculation

---

### task_user

Many-to-many relationship between tasks and users (task assignees).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| task_id | bigint(20) unsigned | NO | - | Foreign key to tasks |
| user_id | bigint(20) unsigned | NO | - | Foreign key to users |
| role | varchar(255) | YES | NULL | User role on task |
| created_at | timestamp | YES | NULL | Assignment timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (task_id, user_id)
- FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

---

### task_attachments

File attachments for tasks.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| task_id | bigint(20) unsigned | NO | - | Foreign key to tasks |
| file_name | varchar(255) | NO | - | Original filename |
| file_path | varchar(255) | NO | - | Storage path |
| file_type | varchar(255) | YES | NULL | MIME type |
| file_size | bigint(20) | YES | NULL | File size in bytes |
| uploaded_by | bigint(20) unsigned | NO | - | Foreign key to users |
| created_at | timestamp | YES | NULL | Upload timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (task_id)
- FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
- FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE

---

### task_comments

Comments on tasks with nested reply support.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| task_id | bigint(20) unsigned | NO | - | Foreign key to tasks |
| user_id | bigint(20) unsigned | NO | - | Foreign key to users |
| parent_id | bigint(20) unsigned | YES | NULL | Parent comment ID (for replies) |
| comment | text | NO | - | Comment content |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (task_id)
- KEY (parent_id)
- FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY (parent_id) REFERENCES task_comments(id) ON DELETE CASCADE

---

### task_sub_tasks

Subtasks for tracking task breakdown.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| task_id | bigint(20) unsigned | NO | - | Foreign key to tasks |
| title | varchar(255) | NO | - | Subtask title |
| is_completed | tinyint(1) | NO | 0 | Completion flag |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (task_id)
- FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE

---

### task_tags

Tags for task categorization.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| task_id | bigint(20) unsigned | NO | - | Foreign key to tasks |
| tag_name | varchar(255) | NO | - | Tag name |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (task_id)
- KEY (tag_name)
- FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE

---

### task_time_entries

Time tracking entries for tasks.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| task_id | bigint(20) unsigned | NO | - | Foreign key to tasks |
| user_id | bigint(20) unsigned | NO | - | Foreign key to users |
| date | date | NO | - | Date of work |
| duration_minutes | int(11) | NO | - | Work duration in minutes |
| idle_minutes | int(11) | NO | 0 | Idle time in minutes |
| task_title | varchar(255) | YES | NULL | Task title snapshot |
| created_at | timestamp | YES | NULL | Entry timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (task_id)
- KEY (user_id)
- KEY (date)
- FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
- FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

**Business Rules**:
- duration_minutes is the actual working time
- idle_minutes tracks non-productive time
- Used for dashboard statistics and reporting

---

## AI System Tables

### ai_decisions

AI recommendations and user actions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| task_id | bigint(20) unsigned | YES | NULL | Foreign key to tasks |
| project_id | bigint(20) unsigned | YES | NULL | Foreign key to projects |
| decision_type | varchar(50) | NO | - | Type of decision |
| ai_response | json | NO | - | Full AI response data |
| suggested_actions | json | NO | - | Specific actionable items |
| confidence_score | decimal(3,2) | NO | - | Confidence (0.00-1.00) |
| reasoning | text | NO | - | AI explanation |
| user_action | enum | NO | 'pending' | pending, accepted, rejected, modified |
| user_feedback | text | YES | NULL | User's reason for action |
| reviewed_by | bigint(20) unsigned | YES | NULL | Foreign key to users |
| reviewed_at | timestamp | YES | NULL | Review timestamp |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |
| deleted_at | timestamp | YES | NULL | Soft delete timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (task_id, decision_type)
- KEY (project_id, decision_type)
- KEY (user_action)
- KEY (created_at)
- KEY (confidence_score)
- FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
- FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
- FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL

**Enum Values**:
- user_action: 'pending', 'accepted', 'rejected', 'modified'

**Decision Types**:
- task_analysis
- priority_change
- project_breakdown
- workload_balance
- deadline_suggestion

**Relationships**:
- Belongs to Task (optional)
- Belongs to Project (optional)
- Belongs to User (reviewer)
- Has many AIAuditLogs

---

### ai_prompts

AI prompt templates for different operations.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| name | varchar(100) | NO | - | Prompt identifier (unique) |
| type | enum | NO | - | system, user, assistant |
| template | text | NO | - | Prompt template with variables |
| version | varchar(20) | NO | '1.0.0' | Semantic version |
| variables | json | YES | NULL | Available variables |
| description | text | YES | NULL | Prompt description |
| is_active | tinyint(1) | NO | 1 | Active flag |
| usage_count | int(11) | NO | 0 | Usage counter |
| created_by | bigint(20) unsigned | NO | - | Foreign key to users |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (name)
- KEY (name, version)
- KEY (is_active)
- KEY (type)
- FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE

**Enum Values**:
- type: 'system', 'user', 'assistant'

**Variables Format**:
```json
{
  "task_title": "string",
  "project_name": "string",
  "due_date": "date"
}
```

---

### ai_settings

AI system configuration settings.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| key | varchar(100) | NO | - | Setting key (unique) |
| value | text | YES | NULL | Setting value |
| type | varchar(50) | NO | 'string' | Data type |
| description | text | YES | NULL | Setting description |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (key)

**Common Settings**:
- ai_enabled: boolean
- default_provider: string (openai, claude, local)
- min_confidence: decimal
- max_actions_per_hour: integer

---

### ai_audit_logs

Audit trail for AI operations.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| decision_id | bigint(20) unsigned | YES | NULL | Foreign key to ai_decisions |
| action | varchar(100) | NO | - | Action performed |
| actor_id | bigint(20) unsigned | YES | NULL | Foreign key to users |
| details | json | YES | NULL | Action details |
| created_at | timestamp | YES | NULL | Action timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (decision_id)
- KEY (actor_id)
- KEY (created_at)
- FOREIGN KEY (decision_id) REFERENCES ai_decisions(id) ON DELETE CASCADE
- FOREIGN KEY (actor_id) REFERENCES users(id) ON DELETE SET NULL

---

## Permission Tables (Spatie)

### roles

User roles.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| name | varchar(255) | NO | - | Role name |
| guard_name | varchar(255) | NO | - | Guard name (web) |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (name, guard_name)

**Default Roles**:
- Super Admin
- Admin
- Project Manager
- Team Member
- User

---

### permissions

System permissions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| name | varchar(255) | NO | - | Permission name |
| guard_name | varchar(255) | NO | - | Guard name (web) |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (name, guard_name)

**Permission Categories**:
- Project Management: view-projects, create-projects, edit-projects, delete-projects
- Task Management: view-tasks, create-tasks, edit-tasks, delete-tasks
- User Management: view-users, create-users, edit-users, delete-users
- Role Management: view-roles, create-roles, edit-roles, delete-roles
- Permission Management: view-permissions, create-permissions, edit-permissions, delete-permissions
- Activity Logs: view-activity-logs, manage-activity-logs
- AI System: access-ai-control, view-ai-decisions, approve-ai-actions, manage-ai-prompts, test-ai-prompts, manage-ai-settings, manage-ai-safety, view-ai-analytics

---

### role_has_permissions

Many-to-many between roles and permissions.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| permission_id | bigint(20) unsigned | NO | - | Foreign key to permissions |
| role_id | bigint(20) unsigned | NO | - | Foreign key to roles |

**Indexes**:
- PRIMARY KEY (permission_id, role_id)
- FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
- FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE

---

### model_has_roles

Assign roles to users (polymorphic).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| role_id | bigint(20) unsigned | NO | - | Foreign key to roles |
| model_type | varchar(255) | NO | - | Model class (App\Models\User) |
| model_id | bigint(20) unsigned | NO | - | Model ID (user_id) |

**Indexes**:
- PRIMARY KEY (role_id, model_id, model_type)
- KEY (model_id, model_type)
- FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE

---

### model_has_permissions

Assign permissions directly to users (polymorphic).

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| permission_id | bigint(20) unsigned | NO | - | Foreign key to permissions |
| model_type | varchar(255) | NO | - | Model class (App\Models\User) |
| model_id | bigint(20) unsigned | NO | - | Model ID (user_id) |

**Indexes**:
- PRIMARY KEY (permission_id, model_id, model_type)
- KEY (model_id, model_type)
- FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE

---

## Activity Logging Table (Spatie)

### activity_log

Complete audit trail of all system activities.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| log_name | varchar(255) | YES | NULL | Log channel name |
| description | text | NO | - | Activity description |
| subject_type | varchar(255) | YES | NULL | Model class (polymorphic) |
| subject_id | bigint(20) unsigned | YES | NULL | Model ID |
| causer_type | varchar(255) | YES | NULL | Actor model class |
| causer_id | bigint(20) unsigned | YES | NULL | Actor model ID |
| properties | json | YES | NULL | Activity properties |
| event | varchar(255) | YES | NULL | Event name |
| batch_uuid | char(36) | YES | NULL | Batch identifier |
| created_at | timestamp | YES | NULL | Activity timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (subject_type, subject_id)
- KEY (causer_type, causer_id)
- KEY (log_name)
- KEY (batch_uuid)
- KEY (created_at)

**Common Events**:
- created
- updated
- deleted
- restored

**Properties Format**:
```json
{
  "attributes": {"status": "Completed"},
  "old": {"status": "Inprogress"}
}
```

---

## Authentication Tables

### password_resets

Password reset tokens.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| email | varchar(255) | NO | - | User email |
| token | varchar(255) | NO | - | Reset token |
| created_at | timestamp | YES | NULL | Token creation time |

**Indexes**:
- KEY (email)

---

### personal_access_tokens

Laravel Sanctum API tokens.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | bigint(20) unsigned | NO | AUTO_INCREMENT | Primary key |
| tokenable_type | varchar(255) | NO | - | Model class |
| tokenable_id | bigint(20) unsigned | NO | - | Model ID |
| name | varchar(255) | NO | - | Token name |
| token | varchar(64) | NO | - | Hashed token |
| abilities | text | YES | NULL | Token abilities |
| last_used_at | timestamp | YES | NULL | Last usage time |
| expires_at | timestamp | YES | NULL | Expiration time |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (token)
- KEY (tokenable_type, tokenable_id)

---

### sessions

Session storage.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | varchar(255) | NO | - | Session ID |
| user_id | bigint(20) unsigned | YES | NULL | Foreign key to users |
| ip_address | varchar(45) | YES | NULL | IP address |
| user_agent | text | YES | NULL | Browser user agent |
| payload | longtext | NO | - | Session data |
| last_activity | int(11) | NO | - | Unix timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (user_id)
- KEY (last_activity)

---

### notifications

Database notification storage.

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| id | char(36) | NO | - | UUID primary key |
| type | varchar(255) | NO | - | Notification class |
| notifiable_type | varchar(255) | NO | - | Model class |
| notifiable_id | bigint(20) unsigned | NO | - | Model ID |
| data | text | NO | - | Notification data (JSON) |
| read_at | timestamp | YES | NULL | Read timestamp |
| created_at | timestamp | YES | NULL | Creation timestamp |
| updated_at | timestamp | YES | NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- KEY (notifiable_type, notifiable_id)

---

## Database Views (Performance Optimization)

### ai_enriched_tasks (View)

Optimized view for AI analysis with pre-computed metrics.

**Columns**:
- task_id
- task_title
- task_status
- task_priority
- project_id
- project_title
- assignee_count
- comment_count
- time_logged_minutes
- days_until_due
- is_overdue
- completion_percentage

**Purpose**: Reduce query complexity for AI context building

---

### ai_project_metrics (View)

Aggregated project metrics for AI analysis.

**Columns**:
- project_id
- project_title
- total_tasks
- completed_tasks
- in_progress_tasks
- overdue_tasks
- total_hours_logged
- average_task_completion_time
- team_size

**Purpose**: Quick access to project-level statistics

---

## Constraints and Rules

### Foreign Key Constraints

**CASCADE ON DELETE**:
- All project relationships (when project deleted, remove members, attachments, comments)
- All task relationships (when task deleted, remove attachments, comments, time entries, etc.)
- User to role/permission assignments

**SET NULL ON DELETE**:
- Project team_lead_id (project keeps existing if leader deleted)
- Task created_by (task keeps existing if creator deleted)
- AI decision reviewed_by

### Unique Constraints

- users.email
- projects.slug
- tasks.task_number
- project_members (project_id, user_id) - prevent duplicate membership
- ai_prompts.name
- ai_settings.key
- roles (name, guard_name)
- permissions (name, guard_name)

### Default Values

- projects.priority = 'Medium'
- projects.status = 'Inprogress'
- projects.privacy = 'Team'
- projects.progress = 0
- projects.is_favorite = false
- tasks.status = 'New'
- tasks.priority = 'Medium'
- tasks.progress = 0
- task_time_entries.idle_minutes = 0
- ai_prompts.version = '1.0.0'
- ai_prompts.is_active = true
- ai_prompts.usage_count = 0
- ai_settings.type = 'string'
- ai_decisions.user_action = 'pending'

---

## Performance Indexes

### Critical Indexes

**For Dashboard Queries**:
- projects (status, created_at)
- tasks (status, due_date, created_at)
- task_time_entries (date, user_id)

**For Filtering**:
- projects (priority, status, deadline)
- tasks (status, priority, project_id)

**For AI Operations**:
- ai_decisions (user_action, confidence_score)
- ai_decisions (task_id, decision_type)
- ai_prompts (is_active, name)

**For Activity Logs**:
- activity_log (subject_type, subject_id)
- activity_log (causer_type, causer_id)
- activity_log (created_at)

**For Kanban Board**:
- tasks (kanban_status, position)

---

## Data Types and Storage

### JSON Columns

Used for flexible, schema-less data:
- projects.skills: Array of required skills
- ai_decisions.ai_response: Full AI response
- ai_decisions.suggested_actions: Actionable items array
- ai_prompts.variables: Available template variables
- ai_audit_logs.details: Action details
- activity_log.properties: Activity properties

### ENUM Columns

Used for fixed value sets:
- projects.priority: 'High', 'Medium', 'Low'
- projects.status: 'Inprogress', 'Completed', 'On Hold'
- projects.privacy: 'Private', 'Team', 'Public'
- tasks.status: 'New', 'Pending', 'Inprogress', 'Completed'
- tasks.priority: 'High', 'Medium', 'Low'
- ai_decisions.user_action: 'pending', 'accepted', 'rejected', 'modified'
- ai_prompts.type: 'system', 'user', 'assistant'

### Timestamp Columns

All tables use Laravel's convention:
- created_at: Automatically set on creation
- updated_at: Automatically updated on modification
- deleted_at: Soft delete timestamp (where applicable)

---

## Soft Deletes

Tables with soft delete support:
- tasks
- ai_decisions

Soft deleted records remain in database but are excluded from normal queries. Can be restored if needed.

---

## Character Set and Collation

- **Character Set**: utf8mb4
- **Collation**: utf8mb4_unicode_ci
- Supports full Unicode including emojis
- Case-insensitive string comparisons

---

## Storage Engine

- **Engine**: InnoDB
- **Benefits**:
  - ACID compliance
  - Foreign key constraints
  - Row-level locking
  - Crash recovery
  - Better for concurrent writes

---

## Backup and Maintenance

### Recommended Practices

1. **Regular Backups**:
   - Daily full backups
   - Transaction log backups (hourly)
   - Test restore procedures

2. **Index Maintenance**:
   - Analyze tables monthly
   - Optimize tables quarterly
   - Monitor slow queries

3. **Data Cleanup**:
   - Archive old activity logs (older than 1 year)
   - Purge soft-deleted records (older than 30 days)
   - Clean expired sessions

4. **Performance Monitoring**:
   - Query execution time
   - Table sizes
   - Index usage
   - Lock contention

---

## Migration Management

All schema changes are version-controlled through Laravel migrations in:
`database/migrations/`

### Running Migrations

```bash
# Run all pending migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migration (WARNING: drops all tables)
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

---

## Conclusion

This database schema provides:
- **Data Integrity**: Foreign key constraints ensure referential integrity
- **Performance**: Strategic indexes for common queries
- **Scalability**: Proper normalization with denormalization where needed
- **Flexibility**: JSON columns for schema-less data
- **Auditability**: Complete activity logging
- **Security**: Proper permissions and role management

The schema supports the full feature set of the project management system while maintaining performance and data quality.
