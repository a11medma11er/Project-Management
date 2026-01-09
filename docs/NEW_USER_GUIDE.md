# User Guide - AI-Powered Project Management System

## Table of Contents

1. [Introduction](#introduction)
2. [Getting Started](#getting-started)
3. [Dashboard Overview](#dashboard-overview)
4. [Project Management](#project-management)
5. [Task Management](#task-management)
6. [Kanban Board](#kanban-board)
7. [Team Collaboration](#team-collaboration)
8. [Time Tracking](#time-tracking)
9. [Activity Tracking](#activity-tracking)
10. [AI Features](#ai-features)
11. [User Profile Management](#user-profile-management)
12. [Troubleshooting](#troubleshooting)
13. [Best Practices](#best-practices)
14. [FAQ](#faq)

---

## Introduction

### What is This System?

The AI-Powered Project Management System is a comprehensive platform designed to help teams manage projects, tasks, and collaborate effectively. It combines traditional project management features with intelligent AI capabilities that learn from your decisions and provide actionable recommendations.

### Who is This Guide For?

This guide is designed for end users of the system:
- Project Managers
- Team Leaders
- Team Members
- Executives monitoring project progress

### System Overview

Key capabilities:
- Create and manage projects with team assignments
- Track tasks with multiple status levels and priorities
- Visualize work using Kanban boards
- Log time spent on tasks
- Collaborate through comments and file attachments
- Receive AI-powered recommendations
- Monitor team performance through analytics

---

## Getting Started

### Accessing the System

1. Open your web browser
2. Navigate to your organization's system URL (e.g., https://pm.yourcompany.com)
3. You will see the login screen

### First-Time Login

**Step 1**: Enter your credentials
- Email address provided by your administrator
- Temporary password (you will be asked to change it)

**Step 2**: Verify your email (if required)
- Check your email inbox
- Click the verification link

**Step 3**: Update your profile
- Add a profile picture
- Update your personal information
- Change your password

### Navigating the Interface

The system uses a consistent layout across all pages:

**Top Navigation Bar**:
- Logo and Home link (left side)
- Search bar (center)
- Profile menu (right side)

**Side Navigation Menu**:
- Dashboard
- Projects
- Tasks
- Kanban Board
- Activity Logs
- User Management (admin only)
- AI Control Panel (authorized users only)

**Main Content Area**:
- Page content changes based on your selection
- Breadcrumb navigation at top
- Action buttons (Create, Edit, Delete, etc.)

### User Roles

Your access level determines what you can see and do:

**Super Admin**:
- Full system access
- User management
- AI configuration
- System settings

**Admin**:
- Project and task management
- Team management
- Reports and analytics

**Project Manager**:
- Create and manage projects
- Assign tasks
- View team performance
- Review AI recommendations

**Team Member**:
- View assigned projects
- Manage assigned tasks
- Log time
- Add comments

**User** (Basic):
- View own tasks
- Update task status
- Log time

---

## Dashboard Overview

The Dashboard is your command center, providing an at-a-glance view of your work.

### Dashboard Sections

**1. Statistics Cards** (Top Row)

Three key metrics with trend indicators:

- **Active Projects**: Number of projects currently in progress
  - Green arrow ↑: Increase from last month
  - Red arrow ↓: Decrease from last month
  - Percentage shows change

- **New Tasks**: Tasks created this month
  - Shows growth or decline trend
  - Helps gauge workload changes

- **Total Hours**: Time logged this month
  - Displays hours and minutes
  - Trend compared to previous month

**2. Project Overview** (Charts Section)

- **Donut Chart**: Project status distribution
  - Blue: Active projects
  - Green: Completed projects
  - Orange: On Hold projects
  - Click segments to filter

- **Area Chart**: Monthly project trends
  - Shows project creation over last 12 months
  - Hover to see exact numbers
  - Helps identify busy periods

**3. Upcoming Tasks** (Center Column)

Lists tasks due soon:
- Task title and project name
- Due date (highlighted if overdue)
- Assigned team members (avatars)
- Priority level (badge color)
- Click to open task details

**4. Active Projects** (Right Column)

Recent projects you're working on:
- Project thumbnail
- Progress bar (percentage complete)
- Team lead information
- Deadline date
- Quick actions (view, edit)

**5. Your Tasks** (Bottom Left)

Your assigned tasks:
- Task number (e.g., #VLZ0001)
- Status badge
- Priority indicator
- Due date with overdue warning
- Click to view/edit

**6. Team Members** (Bottom Right)

Team performance overview:
- Member name and avatar
- Tasks assigned
- Hours logged
- Activity status
- Click to see detailed profile

### Filtering Dashboard Data

**Date Range Filter**:
1. Click the date picker in top-right
2. Select "This Month", "Last Month", "This Quarter", or "Custom Range"
3. Dashboard updates automatically

**Task Status Filter**:
1. Use dropdown to filter by status
2. Options: All, New, Pending, In Progress, Completed

**Project Status Filter**:
1. Select from: All, Active, Completed, On Hold
2. Charts and lists update immediately

### Refreshing Data

- Dashboard auto-refreshes every 5 minutes
- Click "Refresh" button for manual update
- New data appears with subtle animation

---

## Project Management

Projects are the top-level containers for organizing work.

### Viewing Projects

**Project List View**:

1. Click "Projects" in side menu
2. You see all projects you have access to
3. Each project card shows:
   - Project thumbnail
   - Title and description
   - Status badge (In Progress, Completed, On Hold)
   - Priority (High, Medium, Low)
   - Progress percentage with bar
   - Team lead avatar
   - Deadline date
   - Star icon (favorite marker)

**Filtering Projects**:
- Use search box to find by name
- Filter by status dropdown
- Filter by priority dropdown
- Toggle "Show Only Favorites" switch

**Sorting Projects**:
- Click column headers to sort
- Options: Name, Status, Priority, Progress, Deadline

### Creating a New Project

**Step 1**: Click "Create Project" button (top-right)

**Step 2**: Fill in Basic Information
- **Project Title**: Clear, descriptive name
- **Description**: Detailed project objectives and scope
- **Category**: Select from dropdown (Web Development, Marketing, Research, etc.)

**Step 3**: Set Project Details
- **Priority**: High, Medium, or Low
- **Status**: In Progress, Completed, or On Hold
- **Privacy**: 
  - Private: Only you
  - Team: Project members only
  - Public: All users
- **Start Date**: When project begins
- **Deadline**: Target completion date

**Step 4**: Upload Thumbnail (Optional)
- Click "Choose File"
- Select image (JPG, PNG, max 2MB)
- Thumbnail appears in project list

**Step 5**: Assign Team Lead
- Search and select user
- Team lead has special permissions

**Step 6**: Add Team Members
- Click "Add Member" button
- Search for users
- Select multiple members
- Assign roles (optional): Developer, Designer, QA, etc.

**Step 7**: Add Required Skills (Optional)
- Type skill name
- Press Enter to add
- Example: PHP, JavaScript, Project Management
- Used for team matching

**Step 8**: Upload Attachments (Optional)
- Click "Upload Files"
- Select multiple files (max 64MB each)
- Supported: Documents, images, PDFs, spreadsheets
- Files appear in project overview

**Step 9**: Click "Create Project"
- Project is created immediately
- You're redirected to project overview page
- Team members receive notifications

### Editing a Project

1. Go to project overview page
2. Click "Edit Project" button
3. Modify any fields
4. Click "Save Changes"
5. Changes logged in activity feed

**Note**: Changes to team members trigger notifications

### Managing Project Members

**Adding Members**:
1. Click "Manage Team" in project view
2. Click "Add Member"
3. Search for user
4. Assign role
5. Click "Add"

**Removing Members**:
1. Find member in team list
2. Click "Remove" button
3. Confirm removal
4. Member loses access to project

**Changing Member Roles**:
1. Click role dropdown next to member
2. Select new role
3. Changes save automatically

### Project Progress Tracking

Progress is calculated automatically:
- Based on completed vs. total tasks
- Updates in real-time as tasks change
- Displayed as percentage and progress bar

**Manual Override** (if needed):
1. Click progress bar
2. Enter custom percentage
3. Save changes
4. Automatic calculation resumes with next task update

### Adding Project Comments

Comments enable team discussion:

**Adding a Comment**:
1. Scroll to Comments section
2. Type your comment in text box
3. Click "Post Comment"
4. Comment appears immediately

**Replying to Comments**:
1. Click "Reply" under comment
2. Type your response
3. Click "Post Reply"
4. Creates nested conversation

**Mentioning Users**:
- Type @ followed by name
- Select user from dropdown
- User receives notification

**Editing Comments**:
1. Click three dots (⋮) on your comment
2. Select "Edit"
3. Modify text
4. Click "Save"

**Deleting Comments**:
1. Click three dots (⋮)
2. Select "Delete"
3. Confirm deletion

### Project Attachments

**Viewing Attachments**:
- All attachments appear in Attachments section
- Shows file name, type, size, uploader, date
- Click file name to download

**Adding More Attachments**:
1. Click "Add Files" button
2. Select files
3. Files upload with progress bar
4. Appear in list when complete

**Deleting Attachments**:
1. Click trash icon next to file
2. Confirm deletion
3. File removed from project

### Marking Project as Favorite

- Click star icon on project card
- Star becomes filled (gold)
- Project appears in "Favorites" filter
- Click again to unfavorite

### Viewing Project Activity

Activity log shows all project changes:
- Task created/updated
- Members added/removed
- Status changes
- Comments added
- Attachments uploaded

**To View**:
1. Go to project overview
2. Scroll to Activity section
3. See chronological list of events
4. Click event to see details

---

## Task Management

Tasks are the individual work items within projects.

### Viewing Tasks

**Task List View**:
1. Click "Tasks" in main menu
2. See all tasks you have access to
3. Table shows:
   - Task number (#VLZ0001)
   - Title
   - Project name
   - Status
   - Priority
   - Assigned members
   - Due date
   - Progress

**Task Detail View**:
1. Click any task in list
2. Opens full task details page
3. Shows all task information

### Creating a New Task

**Step 1**: Click "Create Task" button

**Step 2**: Basic Information
- **Task Title**: Clear, actionable description
- **Task Description**: Detailed requirements and acceptance criteria
- **Project**: Select project (or leave blank for standalone task)

**Step 3**: Task Properties
- **Status**: New, Pending, In Progress, or Completed
- **Priority**: High, Medium, or Low
- **Due Date**: Target completion date
- **Progress**: 0-100% (optional)

**Step 4**: Assignment
- **Assign To**: Select one or more team members
- **Role**: Specify their role on this task (optional)
- Example: Frontend Developer, Code Reviewer

**Step 5**: Additional Fields (Optional)
- **Client Name**: If task is for specific client
- **Tags**: Add labels for categorization (comma-separated)

**Step 6**: Attachments (Optional)
- Upload relevant files
- Specifications, mockups, documents

**Step 7**: Click "Create Task"
- Task gets auto-generated number (#VLZ####)
- Assigned members get notified
- Task appears in list

### Editing Tasks

1. Open task detail page
2. Click "Edit Task"
3. Modify fields
4. Click "Save"
5. Changes logged in activity

### Updating Task Status

**Method 1 - From Task List**:
1. Click status badge on task
2. Select new status from dropdown
3. Status updates immediately

**Method 2 - From Task Detail**:
1. Open task
2. Change status dropdown
3. Click "Update"

**Method 3 - Drag and Drop (Kanban)**:
- See Kanban Board section

### Updating Task Progress

**Manual Update**:
1. Open task
2. Enter progress percentage (0-100)
3. Progress bar updates

**Subtask-Based Progress**:
- Progress calculated from completed subtasks
- If 3 of 6 subtasks done, progress = 50%
- Updates automatically

### Managing Subtasks

Subtasks break down tasks into smaller steps.

**Adding Subtasks**:
1. Open task detail page
2. Scroll to Subtasks section
3. Click "Add Subtask"
4. Enter subtask title
5. Click "Add"
6. Subtask appears in list

**Completing Subtasks**:
1. Click checkbox next to subtask
2. Subtask marked as complete
3. Progress recalculates
4. Checkmark appears

**Editing Subtasks**:
1. Click subtask text
2. Modify title
3. Press Enter to save

**Deleting Subtasks**:
1. Hover over subtask
2. Click delete icon (×)
3. Subtask removed

### Task Comments

Same functionality as project comments:
- Add comments
- Reply to comments
- Mention users with @
- Edit and delete own comments

### Task Attachments

Upload and manage files related to task:
- Screenshots
- Specifications
- Test results
- Documentation

**Process**:
1. Click "Add Attachment"
2. Select files
3. Files upload
4. Download or delete as needed

### Task Tags

Organize tasks with tags:

**Adding Tags**:
1. Edit task
2. Enter tags (comma-separated)
3. Example: "bug, critical, customer-facing"
4. Save task

**Using Tags**:
- Filter task list by tag
- Search for tasks with specific tag
- Group related tasks

### Task Time Tracking

See [Time Tracking](#time-tracking) section

---

## Kanban Board

The Kanban board provides a visual way to manage tasks.

### Accessing Kanban Board

1. Click "Kanban Board" in main menu
2. See board with columns

### Board Columns

Default columns:
- **To Do**: New tasks, not started
- **In Progress**: Currently being worked on
- **In Review**: Awaiting review or approval
- **Completed**: Finished tasks

### Adding Tasks to Board

**Method 1 - Create New Task**:
1. Click "+" in column header
2. Fill in task details
3. Task appears in that column

**Method 2 - Add Existing Task**:
1. Click "Add Existing Tasks"
2. Search for tasks not on board
3. Select tasks
4. Choose target column
5. Click "Add"

### Moving Tasks

**Drag and Drop**:
1. Click and hold task card
2. Drag to target column
3. Release to drop
4. Task status updates automatically
5. Position saves

**Reordering Within Column**:
- Drag task up or down in same column
- Sets priority order
- Team sees same order

### Task Cards

Each card shows:
- Task number
- Title
- Priority indicator (colored dot)
- Assigned member avatars
- Due date
- Comments count
- Attachments count

### Filtering Kanban Board

**Filter Options**:
- By Project: Show tasks from specific project only
- By Assigned User: Show only your tasks or specific member
- By Priority: Show only High, Medium, or Low priority

**To Filter**:
1. Click filter icon
2. Select filter criteria
3. Board updates
4. Click "Clear Filters" to reset

### Removing Tasks from Board

1. Click task card
2. Click "Remove from Board"
3. Task removed from Kanban but not deleted
4. Task remains in task list

---

## Team Collaboration

### Assigning Work

**Assigning Projects**:
- Add members when creating project
- Or use "Manage Team" in project view

**Assigning Tasks**:
- Select assignees when creating task
- Or edit task and add assignees
- Multiple people can be assigned

### Communication

**Comments**:
- Use comments for discussions
- Keep conversations in context
- Reply to specific comments

**Mentions**:
- @mention users to notify them
- Use for questions or assignments
- User gets notification

**Notifications**:
- Email notifications for important events
- In-app notification bell
- Click to see recent notifications

### File Sharing

**Uploading Files**:
- Attach to projects or tasks
- Files visible to all project members
- Include context in comments

**Downloading Files**:
- Click file name to download
- Or click download icon

### Activity Transparency

- All actions logged
- Team sees changes in real-time
- Complete audit trail maintained

---

## Time Tracking

Track time spent on tasks for accurate reporting.

### Logging Time

**Step 1**: Open task detail page

**Step 2**: Scroll to Time Entries section

**Step 3**: Click "Log Time"

**Step 4**: Fill in details:
- **Date**: Date you worked
- **Duration**: Hours and minutes
- **Idle Time**: Time away from work (optional)
- **Notes**: What you accomplished (optional)

**Step 5**: Click "Save"
- Time entry added to task
- Appears in time log
- Included in reports

### Viewing Your Time Logs

**Personal Time Log**:
1. Go to Profile
2. Click "My Time Logs"
3. See all your time entries
4. Filter by date range
5. Export to Excel

**Project Time Logs**:
1. Go to project overview
2. See total time logged
3. Breakdown by member
4. Breakdown by task

### Editing Time Entries

1. Find time entry in list
2. Click "Edit"
3. Modify fields
4. Save changes

### Deleting Time Entries

1. Click "Delete" on time entry
2. Confirm deletion
3. Entry removed

### Time Reports

**Dashboard Statistics**:
- Total hours this month
- Trend vs. last month

**Team Member Performance**:
- Hours logged per member
- Tasks completed
- Activity level

---

## Activity Tracking

The system logs all actions for transparency and audit purposes.

### Activity Log

**Accessing Activity Log**:
1. Click "Activity Logs" in menu (if you have permission)
2. See chronological list of activities

**Activity Types**:
- Project created/updated/deleted
- Task created/updated/deleted/completed
- User added/removed from project
- Comment posted
- File uploaded
- Status changed
- Time logged

**Activity Details**:
- What changed
- Who made the change
- When it happened
- Old value vs. new value

### Filtering Activities

**Filter Options**:
- By Date Range
- By User (who did it)
- By Subject Type (projects, tasks, etc.)
- By Event Type (created, updated, deleted)

**To Filter**:
1. Select filters
2. Click "Apply"
3. List updates

### Viewing Activity Details

1. Click activity in list
2. See full details:
   - User who performed action
   - Timestamp
   - Changes made
   - Before and after values

### Activity Analytics

**Activity Dashboard** (Admin only):
- Most active users
- Activity by hour/day
- Popular actions
- System usage metrics

---

## AI Features

The AI system analyzes your work patterns and provides intelligent recommendations.

### AI Recommendations

**What AI Analyzes**:
- Task complexity and dependencies
- Historical completion patterns
- Team workload distribution
- Project deadlines and risks

**Types of Recommendations**:
1. **Priority Changes**: Suggests increasing/decreasing task priority
2. **Workload Balancing**: Recommends reassigning tasks
3. **Deadline Adjustments**: Flags unrealistic timelines
4. **Task Breakdown**: Suggests splitting complex tasks

### Reviewing AI Decisions

**Accessing AI Decisions**:
1. Click notification when AI makes recommendation
2. Or go to "AI Control" in menu
3. Click "Decisions" tab

**Decision Card Shows**:
- Recommendation type
- Confidence score (0-100%)
- Reasoning explanation
- Suggested action
- Affected task/project
- Alternative options

**Taking Action**:

**Option 1 - Accept**:
1. Review recommendation
2. Click "Accept"
3. Changes applied automatically
4. AI learns from acceptance

**Option 2 - Reject**:
1. Click "Reject"
2. Provide reason (optional)
3. No changes made
4. AI learns from rejection

**Option 3 - Modify**:
1. Click "Modify"
2. Adjust recommendation
3. Apply modified version
4. AI learns preferred approach

### AI Confidence Scores

**Understanding Scores**:
- 90-100%: Very High Confidence (strong patterns)
- 70-89%: High Confidence (clear indicators)
- 50-69%: Medium Confidence (mixed signals)
- Below 50%: Low Confidence (insufficient data)

**System Behavior**:
- Recommendations below 70% require review
- High confidence decisions may auto-apply (if configured)
- System learns from your feedback

### AI Learning

**How AI Improves**:
- Tracks acceptance/rejection rates
- Identifies patterns in your decisions
- Adjusts confidence scoring
- Refines recommendations over time

**Viewing AI Performance**:
1. Go to AI Control Panel
2. Click "Analytics"
3. See:
   - Acceptance rate trends
   - Confidence score distribution
   - Most successful recommendation types
   - Learning progress

### Disabling AI Features

If you don't want AI recommendations:
1. Go to Profile
2. Click "Settings"
3. Toggle "AI Recommendations" off
4. No more AI suggestions for you
5. (Admin can disable system-wide)

---

## User Profile Management

Manage your personal information and preferences.

### Accessing Your Profile

1. Click your avatar (top-right corner)
2. Select "Profile" from dropdown
3. Or click "Profile" in side menu

### Updating Profile Information

**Personal Information**:
- Name
- Email address
- Phone number (if applicable)

**To Update**:
1. Click "Edit Profile"
2. Modify fields
3. Click "Save"

### Changing Profile Picture

1. Go to Profile page
2. Click on current avatar
3. Click "Upload New Photo"
4. Select image file (JPG, PNG)
5. Crop if desired
6. Click "Save"
7. New photo appears everywhere

### Changing Password

1. Go to Profile
2. Click "Security" tab
3. Enter current password
4. Enter new password
5. Confirm new password
6. Click "Update Password"

**Password Requirements**:
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number

### Notification Preferences

1. Go to Profile
2. Click "Notifications" tab
3. Toggle preferences:
   - Email notifications
   - Browser notifications
   - Slack notifications (if integrated)
   - Notification types (comments, mentions, assignments, etc.)
4. Changes save automatically

### Viewing Your Activity

**Your Activity History**:
1. Go to Profile
2. Click "Activity" tab
3. See your recent actions
4. Filter by date

**Your Statistics**:
- Projects you're involved in
- Tasks assigned to you
- Tasks completed
- Hours logged
- Comments posted

---

## Troubleshooting

### Common Issues and Solutions

#### Cannot Login

**Problem**: Login page shows error

**Solutions**:
1. Verify email and password are correct
2. Check Caps Lock is off
3. Try "Forgot Password" link
4. Contact admin if account is locked

#### Tasks Not Appearing

**Problem**: Tasks missing from list

**Solutions**:
1. Check if filters are applied
2. Verify you're assigned to the tasks
3. Check if tasks are in different project
4. Refresh page (Ctrl+F5)

#### Cannot Upload Files

**Problem**: File upload fails

**Solutions**:
1. Check file size (max 64MB)
2. Verify file type is allowed
3. Check internet connection
4. Try different browser
5. Contact admin if persists

#### Dashboard Not Loading

**Problem**: Dashboard shows loading spinner

**Solutions**:
1. Refresh page
2. Clear browser cache
3. Check internet connection
4. Try different browser
5. Contact IT support

#### Changes Not Saving

**Problem**: Updates don't persist

**Solutions**:
1. Ensure form validation passes
2. Check for error messages
3. Verify required fields filled
4. Try again after page refresh
5. Check if session expired

#### Notifications Not Working

**Problem**: Not receiving notifications

**Solutions**:
1. Check notification settings in Profile
2. Verify email address is correct
3. Check spam/junk folder
4. Allow browser notifications
5. Ask admin to check system settings

---

## Best Practices

### Project Management Best Practices

1. **Clear Project Scope**
   - Write detailed project descriptions
   - Define clear objectives
   - Set realistic deadlines

2. **Regular Updates**
   - Update project status weekly
   - Keep progress accurate
   - Communicate blockers early

3. **Team Communication**
   - Use comments for discussions
   - @mention team members
   - Document decisions

4. **File Organization**
   - Use clear file names
   - Add context in comments
   - Keep attachments updated

### Task Management Best Practices

1. **Clear Task Titles**
   - Use action verbs
   - Be specific
   - Keep it concise
   - Good: "Design user login page"
   - Bad: "UI stuff"

2. **Detailed Descriptions**
   - Include acceptance criteria
   - List requirements
   - Reference related tasks
   - Add examples if helpful

3. **Proper Assignment**
   - Assign to appropriate skill level
   - Don't overload team members
   - Balance workload

4. **Realistic Deadlines**
   - Consider task complexity
   - Account for dependencies
   - Buffer for unexpected issues

5. **Regular Status Updates**
   - Update status when it changes
   - Log time consistently
   - Comment on progress

### Collaboration Best Practices

1. **Effective Communication**
   - Be clear and concise
   - Stay on topic
   - Be respectful
   - Respond promptly

2. **File Sharing**
   - Use descriptive names
   - Version files if needed
   - Remove obsolete files

3. **Time Tracking**
   - Log time daily
   - Be honest and accurate
   - Include notes on what you did

4. **Comment Usage**
   - Keep comments relevant
   - Use @mentions appropriately
   - Don't spam notifications

---

## FAQ

### General Questions

**Q: Can I work on multiple projects?**
A: Yes, you can be a member of multiple projects simultaneously.

**Q: How do I know what to work on?**
A: Check your Dashboard for assigned tasks, or view Tasks list filtered by "Assigned to Me".

**Q: Can I create my own projects?**
A: Depends on your role. Project Managers and Admins can create projects. Contact your admin for access.

**Q: What happens to deleted tasks?**
A: Tasks are soft-deleted and can be recovered by an admin for 30 days.

### Project Questions

**Q: Who can see my project?**
A: Depends on privacy setting:
- Private: Only you
- Team: Project members only
- Public: All system users

**Q: Can I change the project team lead?**
A: Yes, edit the project and select a new team lead (if you have permission).

**Q: How is project progress calculated?**
A: Automatically based on completed vs. total tasks. You can override manually if needed.

### Task Questions

**Q: Can a task belong to multiple projects?**
A: No, each task belongs to one project (or no project for standalone tasks).

**Q: What's the difference between status and kanban status?**
A: Regular status is for overall task lifecycle. Kanban status is specific to board columns and may differ.

**Q: Can I assign a task to multiple people?**
A: Yes, tasks can have multiple assignees.

**Q: How do I mark a task as blocked?**
A: Add a comment explaining the blocker, and optionally change status to "Pending".

### Time Tracking Questions

**Q: Can I edit time entries after submission?**
A: Yes, you can edit your own time entries.

**Q: Is time tracking mandatory?**
A: Depends on your organization's policy. Check with your manager.

**Q: Can I log time for someone else?**
A: No, each user logs their own time. Admins can make adjustments if needed.

### AI Questions

**Q: Is AI always right?**
A: No, AI provides recommendations based on patterns. You make final decisions.

**Q: Can I disable AI recommendations?**
A: Yes, in your profile settings.

**Q: Does rejecting AI recommendations affect my evaluation?**
A: No, your judgment is valued. AI learns from your decisions.

**Q: How does AI learn?**
A: From collective team decisions over time. It identifies patterns in acceptances and rejections.

### Technical Questions

**Q: Which browsers are supported?**
A: Modern versions of Chrome, Firefox, Safari, and Edge.

**Q: Can I use the system on mobile?**
A: Yes, the interface is responsive but some features work best on desktop.

**Q: Is my data secure?**
A: Yes, all data is encrypted and backed up regularly.

**Q: Can I export my data?**
A: Yes, you can export task lists, time logs, and reports to Excel.

---

## Getting Help

If you need assistance:

1. **Check this User Guide**: Most questions are answered here

2. **Contact Your Team Lead**: For project-specific questions

3. **Contact System Administrator**: For access or technical issues

4. **Email Support**: support@yourcompany.com

5. **Submit Feedback**: Use the feedback form in Profile menu

---

**Document Version**: 1.0
**Last Updated**: January 2026
**System Version**: 1.0.0
