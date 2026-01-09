# API Documentation

**Project**: AI-Powered Project Management System  
**Version**: 1.0.0  
**Last Updated**: January 9, 2026  
**Base URL**: `https://your-domain.com`

---

## Table of Contents

1. [Introduction](#introduction)
2. [Authentication](#authentication)
3. [General Information](#general-information)
4. [Dashboard Endpoints](#dashboard-endpoints)
5. [User Management](#user-management)
6. [Profile Management](#profile-management)
7. [Project Management](#project-management)
8. [Task Management](#task-management)
9. [Kanban Board](#kanban-board)
10. [Time Tracking](#time-tracking)
11. [Activity Log](#activity-log)
12. [AI System](#ai-system)
13. [Role & Permission Management](#role--permission-management)
14. [Error Responses](#error-responses)

---

## Introduction

This document describes all available HTTP endpoints in the AI-Powered Project Management System. All endpoints are web-based and use Laravel's session-based authentication with CSRF protection.

### Important Notes

- **API Type**: Web Routes (Session-based, not REST API)
- **Authentication**: Laravel Sanctum with session cookies
- **CSRF Protection**: Required for all POST, PUT, PATCH, DELETE requests
- **Content Type**: `application/x-www-form-urlencoded` or `multipart/form-data` for file uploads
- **Response Format**: HTML views (not JSON) for most endpoints

### REST API Endpoints

Currently, the system has **minimal REST API** support:

```http
GET /api/user
```

Returns authenticated user information (JSON).

---

## Authentication

### Authentication Method

The system uses **Laravel Sanctum** with session-based authentication.

#### Login

```http
POST /login
```

**Authentication**: None (public)

**Request Body**:
```
email: string (required, valid email)
password: string (required)
remember: boolean (optional)
```

**Response**: Redirect to dashboard on success

**Errors**:
- `422 Unprocessable Entity`: Invalid credentials
- `429 Too Many Requests`: Rate limit exceeded

---

#### Logout

```http
POST /logout
```

**Authentication**: Required

**Response**: Redirect to login page

---

#### Register

```http
POST /register
```

**Authentication**: None (public)

**Request Body**:
```
name: string (required, max: 255)
email: string (required, unique, valid email)
password: string (required, min: 8, confirmed)
password_confirmation: string (required)
```

**Response**: Redirect to dashboard on success

---

## General Information

### Rate Limiting

Profile updates are rate-limited to **10 requests per minute**.

### File Upload Limits

- **Project Thumbnails**: 5MB max, formats: jpeg, png, jpg, gif, webp
- **Project Attachments**: 10MB max per file
- **Task Attachments**: 5MB max, formats: pdf, doc, docx, zip, png, jpg, jpeg
- **Maximum Files**: 5 files per task

### Pagination

Most list endpoints support pagination with the following query parameters:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 10, max: 100)

---

## Dashboard Endpoints

### Get Dashboard

```http
GET /
GET /dashboard
```

**Authentication**: Required

**Description**: Main dashboard with statistics, charts, and recent activities

**Response**: HTML view with:
- Total projects, tasks, team members
- Project completion chart
- Upcoming tasks
- Active projects list
- Team member list
- AI decision statistics

---

### Get Dashboard Projects Data

```http
GET /dashboard-projects
```

**Authentication**: Required

**Description**: Fetch project data for dashboard charts (AJAX endpoint)

**Response**: JSON
```json
{
  "labels": ["January", "February", "..."],
  "completed": [10, 15, 20],
  "in_progress": [5, 8, 12],
  "on_hold": [2, 1, 3]
}
```

---

### Filter Dashboard Tasks

```http
GET /dashboard/tasks/filter
```

**Authentication**: Required

**Query Parameters**:
- `project_id`: Filter by project (optional)
- `status`: Filter by status (optional)
- `priority`: Filter by priority (optional)

**Response**: HTML partial with filtered tasks

---

### Get Upcoming Tasks

```http
GET /dashboard/tasks/upcoming
```

**Authentication**: Required

**Query Parameters**:
- `days`: Number of days ahead (default: 7)

**Response**: HTML partial with upcoming tasks

---

### Sort Team Members

```http
GET /dashboard/team/sort
```

**Authentication**: Required

**Query Parameters**:
- `sort_by`: `tasks` | `projects` | `name` (default: name)
- `order`: `asc` | `desc` (default: asc)

**Response**: HTML partial with sorted team members

---

## User Management

### List Users

```http
GET /users
```

**Authentication**: Required  
**Permission**: `view-users`

**Query Parameters**:
- `search`: Search by name or email
- `role`: Filter by role
- `status`: Filter by status

**Response**: HTML view with users list

---

### Create User

```http
GET /users/create
```

**Authentication**: Required  
**Permission**: `create-users`

**Response**: HTML form for creating user

---

### Store User

```http
POST /users
```

**Authentication**: Required  
**Permission**: `create-users`

**Request Body**:
```
name: string (required, max: 255)
email: string (required, unique, valid email)
password: string (required, min: 8, confirmed)
password_confirmation: string (required)
phone: string (optional, max: 20)
address: string (optional, max: 500)
country: string (optional, max: 100)
city: string (optional, max: 100)
state: string (optional, max: 100)
zip_code: string (optional, max: 20)
role: string (required, exists in roles)
profile_picture: file (optional, image, max: 2MB)
```

**Response**: Redirect to users list with success message

**Errors**:
- `422 Unprocessable Entity`: Validation errors
- `403 Forbidden`: Insufficient permissions

---

### View User

```http
GET /users/{id}
```

**Authentication**: Required  
**Permission**: `view-users`

**Response**: HTML view with user details

---

### Edit User

```http
GET /users/{id}/edit
```

**Authentication**: Required  
**Permission**: `edit-users`

**Response**: HTML form for editing user

---

### Update User

```http
PUT /users/{id}
PATCH /users/{id}
```

**Authentication**: Required  
**Permission**: `edit-users`

**Request Body**: Same as Store User (password optional)

**Response**: Redirect to user view with success message

---

### Delete User

```http
DELETE /users/{id}
```

**Authentication**: Required  
**Permission**: `delete-users`

**Response**: Redirect to users list with success message

---

## Profile Management

### View Profile

```http
GET /profile
```

**Authentication**: Required

**Response**: HTML view with user profile

---

### Update Profile

```http
PATCH /profile
```

**Authentication**: Required  
**Rate Limit**: 10 requests/minute

**Request Body**:
```
name: string (optional, max: 255)
email: string (optional, unique, valid email)
phone: string (optional, max: 20)
address: string (optional, max: 500)
bio: string (optional, max: 1000)
profile_picture: file (optional, image, max: 2MB)
```

**Response**: Redirect back with success message

---

### Delete Profile

```http
DELETE /profile
```

**Authentication**: Required

**Response**: Redirect to home with success message

---

## Project Management

### List Projects

```http
GET /projects
```

**Authentication**: Required  
**Permission**: `view-projects`

**Query Parameters**:
- `search`: Search by title
- `status`: Filter by status (Inprogress, Completed, On Hold)
- `priority`: Filter by priority (High, Medium, Low)
- `team_lead`: Filter by team lead

**Response**: HTML view with projects list

---

### Create Project

```http
GET /projects/create
```

**Authentication**: Required  
**Permission**: `create-projects`

**Response**: HTML form for creating project

---

### Store Project

```http
POST /projects
```

**Authentication**: Required  
**Permission**: `create-projects`

**Request Body**:
```
title: string (required, max: 255)
thumbnail: file (optional, image, max: 5MB, formats: jpeg,png,jpg,gif,webp)
description: string (required)
priority: enum (required, values: High|Medium|Low)
status: enum (required, values: Inprogress|Completed|On Hold)
privacy: enum (optional, values: Private|Team|Public)
category: string (optional, max: 100)
skills: string (optional, comma-separated)
deadline: date (required, >= today)
start_date: date (optional, <= deadline)
team_lead_id: integer (optional, exists in users)
members: array (optional, each exists in users)
attachments: array (optional, each file max 10MB)
```

**Response**: Redirect to project view with success message

**Validation Errors** (422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["Project title is required."],
    "description": ["Project description is required."],
    "deadline": ["Deadline must be today or a future date."]
  }
}
```

---

### View Project

```http
GET /projects/{id}
```

**Authentication**: Required  
**Permission**: `view-projects`

**Response**: HTML view with project details, tasks, members, comments

---

### Edit Project

```http
GET /projects/{id}/edit
```

**Authentication**: Required  
**Permission**: `edit-projects`

**Response**: HTML form for editing project

---

### Update Project

```http
PUT /projects/{id}
PATCH /projects/{id}
```

**Authentication**: Required  
**Permission**: `edit-projects`

**Request Body**: Same as Store Project

**Response**: Redirect to project view with success message

---

### Delete Project

```http
DELETE /projects/{id}
```

**Authentication**: Required  
**Permission**: `delete-projects`

**Response**: Redirect to projects list with success message

---

### Add Project Comment

```http
POST /projects/{id}/comments
```

**Authentication**: Required  
**Permission**: `view-projects`

**Request Body**:
```
comment: string (required, max: 2000)
parent_id: integer (optional, for replies, exists in project_comments)
```

**Response**: Redirect back with success message

---

### Delete Project Comment

```http
DELETE /projects/{project_id}/comments/{comment_id}
```

**Authentication**: Required  
**Permission**: Owner or `delete-projects`

**Response**: Redirect back with success message

---

### Upload Project Attachment

```http
POST /projects/{id}/attachments
```

**Authentication**: Required  
**Permission**: `edit-projects`

**Request Body**:
```
attachment: file (required, max: 10MB)
description: string (optional, max: 255)
```

**Response**: Redirect back with success message

---

### Download Project Attachment

```http
GET /projects/{project_id}/attachments/{attachment_id}/download
```

**Authentication**: Required  
**Permission**: `view-projects`

**Response**: File download

---

### Delete Project Attachment

```http
DELETE /projects/{project_id}/attachments/{attachment_id}
```

**Authentication**: Required  
**Permission**: `edit-projects`

**Response**: Redirect back with success message

---

### Toggle Project Favorite

```http
POST /projects/{id}/favorite
```

**Authentication**: Required

**Response**: JSON
```json
{
  "success": true,
  "is_favorite": true
}
```

---

## Task Management

### List Tasks

```http
GET /tasks
```

**Authentication**: Required  
**Permission**: `view-tasks`

**Query Parameters**:
- `search`: Search by title or task number
- `status`: Filter by status (New, Pending, Inprogress, Completed)
- `priority`: Filter by priority (High, Medium, Low)
- `project_id`: Filter by project
- `assigned_to`: Filter by assigned user

**Response**: HTML view with tasks list

---

### Create Task

```http
GET /tasks/create
```

**Authentication**: Required  
**Permission**: `create-tasks`

**Response**: HTML form for creating task

---

### Store Task

```http
POST /tasks
```

**Authentication**: Required  
**Permission**: `create-tasks`

**Request Body**:
```
title: string (required, max: 255)
description: string (required)
project_id: integer (optional, exists in projects)
client_name: string (optional, max: 255)
due_date: date (required, >= today)
status: enum (required, values: New|Pending|Inprogress|Completed)
priority: enum (required, values: High|Medium|Low)
assigned_users: array (optional, max: 10 users)
assigned_users.*: integer (exists in users)
tags: array (optional, max: 10 tags)
tags.*: string (max: 50)
attachments: array (optional, max: 5 files)
attachments.*: file (max: 5MB, formats: pdf,doc,docx,zip,png,jpg,jpeg)
sub_tasks: array (optional, max: 20 subtasks)
sub_tasks.*: string (max: 255)
```

**Response**: Redirect to task view with success message

**Validation Errors** (422):
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."],
    "due_date": ["Due date must be today or a future date."],
    "assigned_users": ["You can assign a maximum of 10 users."]
  }
}
```

**Note**: Task number is auto-generated in format `#VLZ####`

---

### View Task

```http
GET /tasks/{id}
```

**Authentication**: Required  
**Permission**: `view-tasks`

**Response**: HTML view with task details, subtasks, comments, time entries

---

### Edit Task

```http
GET /tasks/{id}/edit
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Response**: HTML form for editing task

---

### Update Task

```http
PUT /tasks/{id}
PATCH /tasks/{id}
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Request Body**: Same as Store Task

**Response**: Redirect to task view with success message

---

### Delete Task

```http
DELETE /tasks/{id}
```

**Authentication**: Required  
**Permission**: `delete-tasks`

**Response**: Redirect to tasks list with success message

---

### Update Task Status

```http
POST /tasks/{id}/status
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Request Body**:
```
status: enum (required, values: New|Pending|Inprogress|Completed)
```

**Response**: JSON
```json
{
  "success": true,
  "status": "Inprogress"
}
```

---

### Update Task Progress

```http
POST /tasks/{id}/progress
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Request Body**:
```
progress: integer (required, min: 0, max: 100)
```

**Response**: JSON
```json
{
  "success": true,
  "progress": 75
}
```

---

### Add Task Comment

```http
POST /tasks/{id}/comments
```

**Authentication**: Required  
**Permission**: `view-tasks`

**Request Body**:
```
comment: string (required, max: 2000)
parent_id: integer (optional, for replies)
```

**Response**: Redirect back with success message

---

### Add Task SubTask

```http
POST /tasks/{id}/subtasks
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Request Body**:
```
title: string (required, max: 255)
```

**Response**: JSON
```json
{
  "success": true,
  "subtask": {
    "id": 123,
    "title": "Subtask title",
    "completed": false
  }
}
```

---

### Complete Task SubTask

```http
POST /tasks/{task_id}/subtasks/{subtask_id}/complete
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Response**: JSON
```json
{
  "success": true,
  "completed": true,
  "task_progress": 50
}
```

---

### Delete Task SubTask

```http
DELETE /tasks/{task_id}/subtasks/{subtask_id}
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Response**: JSON
```json
{
  "success": true,
  "task_progress": 33
}
```

---

## Kanban Board

### View Kanban Board

```http
GET /kanban
```

**Authentication**: Required  
**Permission**: `view-tasks`

**Query Parameters**:
- `project_id`: Filter by project
- `assigned_to`: Filter by assigned user
- `priority`: Filter by priority

**Response**: HTML view with Kanban board (4 columns: To Do, In Progress, In Review, Completed)

---

### Add Task to Kanban

```http
POST /kanban/add-task
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Request Body**:
```
task_id: integer (required, exists in tasks)
kanban_status: enum (required, values: To Do|In Progress|In Review|Completed)
position: integer (optional)
```

**Response**: JSON
```json
{
  "success": true,
  "message": "Task added to Kanban board"
}
```

---

### Update Kanban Task Position

```http
POST /kanban/update-position
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Request Body**:
```
task_id: integer (required, exists in tasks)
kanban_status: enum (required, values: To Do|In Progress|In Review|Completed)
position: integer (required, >= 0)
```

**Response**: JSON
```json
{
  "success": true,
  "message": "Task position updated"
}
```

**Note**: This endpoint is called when dragging/dropping tasks on the Kanban board

---

### Remove Task from Kanban

```http
POST /kanban/remove-task
```

**Authentication**: Required  
**Permission**: `edit-tasks`

**Request Body**:
```
task_id: integer (required, exists in tasks)
```

**Response**: JSON
```json
{
  "success": true,
  "message": "Task removed from Kanban board"
}
```

---

## Time Tracking

### List Time Entries

```http
GET /time-entries
```

**Authentication**: Required

**Query Parameters**:
- `task_id`: Filter by task
- `user_id`: Filter by user
- `start_date`: Filter by start date
- `end_date`: Filter by end date

**Response**: HTML view with time entries list

---

### Store Time Entry

```http
POST /time-entries
```

**Authentication**: Required

**Request Body**:
```
task_id: integer (required, exists in tasks)
date: date (required, <= today)
start_time: time (required, format: HH:MM)
end_time: time (required, after: start_time)
hours: decimal (auto-calculated)
idle_time: decimal (optional, default: 0)
notes: string (optional, max: 500)
```

**Response**: Redirect back with success message

**Validation**:
```json
{
  "errors": {
    "date": ["Date cannot be in the future."],
    "end_time": ["End time must be after start time."],
    "idle_time": ["Idle time cannot exceed total hours."]
  }
}
```

---

### Update Time Entry

```http
PUT /time-entries/{id}
PATCH /time-entries/{id}
```

**Authentication**: Required  
**Permission**: Owner or Admin

**Request Body**: Same as Store Time Entry

**Response**: Redirect back with success message

---

### Delete Time Entry

```http
DELETE /time-entries/{id}
```

**Authentication**: Required  
**Permission**: Owner or Admin

**Response**: Redirect back with success message

---

### Get Time Reports

```http
GET /time-reports
```

**Authentication**: Required

**Query Parameters**:
- `user_id`: Filter by user (Admin only)
- `project_id`: Filter by project
- `start_date`: Report start date
- `end_date`: Report end date
- `group_by`: `user` | `project` | `task` | `date`

**Response**: HTML view with time reports and charts

---

## Activity Log

### List Activities

```http
GET /activity-logs
```

**Authentication**: Required  
**Permission**: `view-activity-logs`

**Query Parameters**:
- `user_id`: Filter by user
- `subject_type`: Filter by model type (Project, Task, User)
- `description`: Filter by event (created, updated, deleted)
- `start_date`: Filter by start date
- `end_date`: Filter by end date

**Response**: HTML view with activity logs

---

### View Activity Details

```http
GET /activity-logs/{id}
```

**Authentication**: Required  
**Permission**: `view-activity-logs`

**Response**: HTML view with activity details including before/after values

---

### Get Activity Analytics

```http
GET /activity-logs/analytics
```

**Authentication**: Required  
**Permission**: `view-activity-logs`

**Query Parameters**:
- `period`: `day` | `week` | `month` | `year`

**Response**: JSON
```json
{
  "total_activities": 1250,
  "by_type": {
    "created": 450,
    "updated": 650,
    "deleted": 150
  },
  "by_model": {
    "Project": 300,
    "Task": 700,
    "User": 250
  },
  "timeline": [
    {"date": "2026-01-01", "count": 45},
    {"date": "2026-01-02", "count": 52}
  ]
}
```

---

### Cleanup Old Activities

```http
POST /activity-logs/cleanup
```

**Authentication**: Required  
**Permission**: `delete-activity-logs`

**Request Body**:
```
days: integer (required, min: 30)
```

**Response**: JSON
```json
{
  "success": true,
  "deleted_count": 1500,
  "message": "Successfully deleted activities older than 30 days"
}
```

---

## AI System

The AI system has extensive endpoints for managing AI features. All AI routes require authentication and specific permissions.

### AI Control Panel

```http
GET /ai
GET /ai/dashboard
```

**Authentication**: Required  
**Permission**: `view-ai-features`

**Response**: HTML view with AI system overview, statistics, and settings

---

### AI Settings

#### View AI Settings

```http
GET /ai/settings
```

**Authentication**: Required  
**Permission**: `manage-ai-settings`

**Response**: HTML view with AI configuration settings

---

#### Update AI Settings

```http
POST /ai/settings
```

**Authentication**: Required  
**Permission**: `manage-ai-settings`

**Request Body**:
```
ai_enabled: boolean (required)
provider: enum (required, values: openai|claude|local)
model: string (required)
temperature: decimal (optional, min: 0, max: 2)
max_tokens: integer (optional, min: 1, max: 4096)
auto_apply_suggestions: boolean (optional)
confidence_threshold: decimal (optional, min: 0, max: 1)
learning_enabled: boolean (optional)
```

**Response**: Redirect back with success message

---

### AI Prompts

#### List Prompts

```http
GET /ai/prompts
```

**Authentication**: Required  
**Permission**: `manage-ai-prompts`

**Response**: HTML view with AI prompts list

---

#### Create Prompt

```http
POST /ai/prompts
```

**Authentication**: Required  
**Permission**: `manage-ai-prompts`

**Request Body**:
```
name: string (required, max: 255)
type: enum (required, values: task_analysis|priority_change|project_breakdown|task_suggestion|risk_assessment)
template: string (required, max: 5000)
variables: array (optional)
is_active: boolean (required)
```

**Response**: Redirect back with success message

---

#### Update Prompt

```http
PUT /ai/prompts/{id}
```

**Authentication**: Required  
**Permission**: `manage-ai-prompts`

**Request Body**: Same as Create Prompt

**Response**: Redirect back with success message

---

#### Delete Prompt

```http
DELETE /ai/prompts/{id}
```

**Authentication**: Required  
**Permission**: `manage-ai-prompts`

**Response**: Redirect back with success message

---

#### Test Prompt

```http
POST /ai/prompts/{id}/test
```

**Authentication**: Required  
**Permission**: `manage-ai-prompts`

**Request Body**:
```
test_data: object (required, variables for template)
```

**Response**: JSON
```json
{
  "success": true,
  "prompt": "Generated prompt with variables",
  "estimated_tokens": 150
}
```

---

### AI Decisions

#### View Decisions

```http
GET /ai/decisions
```

**Authentication**: Required  
**Permission**: `view-ai-decisions`

**Query Parameters**:
- `type`: Filter by decision type
- `status`: Filter by user action (pending, accepted, rejected, modified)
- `confidence_min`: Minimum confidence score
- `start_date`: Filter by start date

**Response**: HTML view with AI decisions list

---

#### View Decision Details

```http
GET /ai/decisions/{id}
```

**Authentication**: Required  
**Permission**: `view-ai-decisions`

**Response**: HTML view with decision details, reasoning, and context

---

### AI Decision Review

#### Review Decision

```http
POST /ai/decisions/{id}/review
```

**Authentication**: Required  
**Permission**: `review-ai-decisions`

**Request Body**:
```
action: enum (required, values: accept|reject|modify)
feedback: string (optional, max: 1000)
modified_data: object (required if action=modify)
```

**Response**: Redirect back with success message

**Note**: User actions provide feedback to improve AI learning

---

#### Bulk Accept Decisions

```http
POST /ai/decisions/bulk-accept
```

**Authentication**: Required  
**Permission**: `review-ai-decisions`

**Request Body**:
```
decision_ids: array (required, min: 1, max: 50)
decision_ids.*: integer (exists in ai_decisions)
```

**Response**: JSON
```json
{
  "success": true,
  "accepted_count": 15,
  "failed_count": 0
}
```

---

#### Bulk Reject Decisions

```http
POST /ai/decisions/bulk-reject
```

**Authentication**: Required  
**Permission**: `review-ai-decisions`

**Request Body**: Same as Bulk Accept

**Response**: Similar to Bulk Accept

---

### AI Learning

#### View Learning Data

```http
GET /ai/learning
```

**Authentication**: Required  
**Permission**: `manage-ai-learning`

**Response**: HTML view with AI learning metrics and feedback data

---

#### Reset Learning Data

```http
POST /ai/learning/reset
```

**Authentication**: Required  
**Permission**: `manage-ai-learning`

**Response**: Redirect back with success message

**Warning**: This action is irreversible

---

### AI Reporting

#### View AI Reports

```http
GET /ai/reports
```

**Authentication**: Required  
**Permission**: `view-ai-reports`

**Query Parameters**:
- `period`: `day` | `week` | `month` | `year`
- `metric`: `accuracy` | `acceptance_rate` | `confidence` | `usage`

**Response**: HTML view with AI performance reports and charts

---

#### Export AI Report (PDF)

```http
GET /ai/reports/export/pdf
```

**Authentication**: Required  
**Permission**: `export-ai-reports`

**Query Parameters**: Same as View AI Reports

**Response**: PDF file download

---

#### Export AI Report (Excel)

```http
GET /ai/reports/export/excel
```

**Authentication**: Required  
**Permission**: `export-ai-reports`

**Query Parameters**: Same as View AI Reports

**Response**: Excel file download

---

### AI Analytics

#### View AI Analytics

```http
GET /ai/analytics
```

**Authentication**: Required  
**Permission**: `view-ai-analytics`

**Response**: HTML view with comprehensive AI analytics dashboard

---

#### Get AI Metrics (API)

```http
GET /ai/analytics/metrics
```

**Authentication**: Required  
**Permission**: `view-ai-analytics`

**Query Parameters**:
- `start_date`: Start date (required)
- `end_date`: End date (required)
- `group_by`: `day` | `week` | `month`

**Response**: JSON
```json
{
  "total_decisions": 1500,
  "accepted": 1200,
  "rejected": 200,
  "modified": 100,
  "acceptance_rate": 0.80,
  "average_confidence": 0.85,
  "by_type": {
    "task_analysis": 500,
    "priority_change": 400,
    "project_breakdown": 300
  },
  "timeline": [
    {"date": "2026-01-01", "decisions": 45, "acceptance_rate": 0.82}
  ]
}
```

---

### AI Workflows

#### List Workflows

```http
GET /ai/workflows
```

**Authentication**: Required  
**Permission**: `manage-ai-workflows`

**Response**: HTML view with AI automation workflows

---

#### Create Workflow

```http
POST /ai/workflows
```

**Authentication**: Required  
**Permission**: `manage-ai-workflows`

**Request Body**:
```
name: string (required, max: 255)
trigger: enum (required, values: task_created|project_updated|deadline_approaching)
conditions: array (required)
actions: array (required)
is_active: boolean (required)
```

**Response**: Redirect back with success message

---

### AI Integrations

#### List Integrations

```http
GET /ai/integrations
```

**Authentication**: Required  
**Permission**: `manage-ai-integrations`

**Response**: HTML view with AI provider integrations

---

#### Test Integration

```http
POST /ai/integrations/{provider}/test
```

**Authentication**: Required  
**Permission**: `manage-ai-integrations`

**Path Parameters**:
- `provider`: openai | claude | local

**Response**: JSON
```json
{
  "success": true,
  "provider": "openai",
  "status": "connected",
  "latency_ms": 250
}
```

---

### AI Performance

#### View Performance Metrics

```http
GET /ai/performance
```

**Authentication**: Required  
**Permission**: `view-ai-performance`

**Response**: HTML view with AI system performance metrics

---

#### Clear AI Cache

```http
POST /ai/performance/clear-cache
```

**Authentication**: Required  
**Permission**: `manage-ai-performance`

**Response**: JSON
```json
{
  "success": true,
  "cache_cleared": true,
  "items_removed": 1500
}
```

---

#### Optimize AI Models

```http
POST /ai/performance/optimize
```

**Authentication**: Required  
**Permission**: `manage-ai-performance`

**Response**: JSON
```json
{
  "success": true,
  "optimization_complete": true,
  "models_optimized": 5
}
```

---

### AI Security

#### View Security Settings

```http
GET /ai/security
```

**Authentication**: Required  
**Permission**: `manage-ai-security`

**Response**: HTML view with AI security settings

---

#### Update Rate Limits

```http
POST /ai/security/rate-limits
```

**Authentication**: Required  
**Permission**: `manage-ai-security`

**Request Body**:
```
requests_per_minute: integer (required, min: 1, max: 1000)
requests_per_hour: integer (required, min: 10, max: 10000)
```

**Response**: Redirect back with success message

---

### AI Safety & Guardrails

#### View Guardrails

```http
GET /ai/safety
```

**Authentication**: Required  
**Permission**: `manage-ai-safety`

**Response**: HTML view with AI safety guardrails configuration

---

#### Update Guardrails

```http
POST /ai/safety/guardrails
```

**Authentication**: Required  
**Permission**: `manage-ai-safety`

**Request Body**:
```
max_confidence_override: boolean (required)
require_human_review: boolean (required)
sensitive_data_filter: boolean (required)
content_moderation: boolean (required)
```

**Response**: Redirect back with success message

---

### AI Features

#### Request Task Analysis

```http
POST /ai/features/analyze-task
```

**Authentication**: Required  
**Permission**: `use-ai-features`

**Request Body**:
```
task_id: integer (required, exists in tasks)
analysis_type: enum (optional, values: complexity|priority|breakdown|timeline)
```

**Response**: JSON
```json
{
  "success": true,
  "decision_id": 123,
  "analysis": {
    "type": "task_analysis",
    "confidence": 0.87,
    "recommendations": [
      "Break down into 3 subtasks",
      "Estimated 8 hours completion time",
      "Assign to senior developer"
    ],
    "reasoning": "Based on similar completed tasks..."
  }
}
```

---

#### Request Project Breakdown

```http
POST /ai/features/breakdown-project
```

**Authentication**: Required  
**Permission**: `use-ai-features`

**Request Body**:
```
project_id: integer (required, exists in projects)
detail_level: enum (optional, values: high|medium|low, default: medium)
```

**Response**: JSON
```json
{
  "success": true,
  "decision_id": 124,
  "breakdown": {
    "suggested_tasks": [
      {"title": "Setup development environment", "priority": "High", "estimated_hours": 4},
      {"title": "Design database schema", "priority": "High", "estimated_hours": 6}
    ],
    "milestones": [
      {"name": "Phase 1: Planning", "duration_days": 5},
      {"name": "Phase 2: Development", "duration_days": 20}
    ],
    "confidence": 0.82,
    "reasoning": "Based on project description and similar projects..."
  }
}
```

---

#### Request Priority Study

```http
POST /ai/features/study-priority
```

**Authentication**: Required  
**Permission**: `use-ai-features`

**Request Body**:
```
task_id: integer (required, exists in tasks)
context: object (optional, additional context)
```

**Response**: JSON
```json
{
  "success": true,
  "decision_id": 125,
  "priority_analysis": {
    "suggested_priority": "High",
    "current_priority": "Medium",
    "confidence": 0.91,
    "factors": [
      "Blocking 3 other tasks",
      "Deadline in 2 days",
      "Critical path item"
    ],
    "reasoning": "This task is on the critical path..."
  }
}
```

---

## Role & Permission Management

### List Roles

```http
GET /roles
```

**Authentication**: Required  
**Permission**: `view-roles`

**Response**: HTML view with roles list

---

### Create Role

```http
POST /roles
```

**Authentication**: Required  
**Permission**: `create-roles`

**Request Body**:
```
name: string (required, unique, max: 255)
permissions: array (required, min: 1)
permissions.*: integer (exists in permissions)
```

**Response**: Redirect back with success message

---

### Update Role

```http
PUT /roles/{id}
```

**Authentication**: Required  
**Permission**: `edit-roles`

**Request Body**: Same as Create Role

**Response**: Redirect back with success message

---

### Delete Role

```http
DELETE /roles/{id}
```

**Authentication**: Required  
**Permission**: `delete-roles`

**Response**: Redirect back with success message

**Note**: Cannot delete default roles or roles assigned to users

---

### List Permissions

```http
GET /permissions
```

**Authentication**: Required  
**Permission**: `view-permissions`

**Response**: HTML view with all available permissions

---

## Error Responses

### Standard Error Format

All errors follow Laravel's standard error response format.

#### Validation Error (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required.",
      "The email must be a valid email address."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

---

#### Authentication Error (401)

```json
{
  "message": "Unauthenticated."
}
```

**Response**: Redirect to login page

---

#### Authorization Error (403)

```json
{
  "message": "This action is unauthorized."
}
```

---

#### Not Found Error (404)

```json
{
  "message": "Resource not found."
}
```

---

#### Rate Limit Error (429)

```json
{
  "message": "Too Many Requests."
}
```

**Headers**:
```
X-RateLimit-Limit: 10
X-RateLimit-Remaining: 0
Retry-After: 60
```

---

#### Server Error (500)

```json
{
  "message": "Server Error"
}
```

**Note**: Detailed error messages are only shown in development mode

---

## CSRF Protection

All POST, PUT, PATCH, and DELETE requests require a CSRF token.

### Getting CSRF Token

The CSRF token is automatically included in forms using `@csrf` Blade directive.

For AJAX requests, include the token in the request header:

```javascript
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

Or include in the meta tag:

```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

---

## Development & Testing Routes

The following routes are only available in development mode:

### Test Context

```http
GET /context-test
```

**Environment**: Development only  
**Authentication**: Required  
**Permission**: `view-users`

**Description**: Test endpoint for debugging context and session data

**Response**: HTML view with current context information

---

## Notes

1. **Session-Based Authentication**: All endpoints use Laravel's session-based authentication, not JWT or Bearer tokens
2. **CSRF Protection**: Required for all state-changing requests (POST, PUT, PATCH, DELETE)
3. **Rate Limiting**: Profile updates limited to 10/minute
4. **File Uploads**: Use `multipart/form-data` content type
5. **Soft Deletes**: Tasks and AI decisions use soft deletes and can be restored
6. **Activity Logging**: All CRUD operations are automatically logged via Spatie Activity Log
7. **Permissions**: All management endpoints check user permissions via Spatie Permission package
8. **AI Features**: Require specific AI permissions and can be disabled globally in settings

---

## Future API Development

Currently, the system has minimal REST API endpoints (only `/api/user`). Future development may include:

- RESTful API for mobile applications
- GraphQL API endpoint
- Webhook support for integrations
- OAuth2 authentication for API access
- OpenAPI (Swagger) specification

---

**Document Version**: 1.0.0  
**Last Updated**: January 9, 2026  
**Maintained By**: Development Team

For questions or clarifications, please contact the development team.
