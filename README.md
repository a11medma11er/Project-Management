# Project Management System 🚀

A comprehensive Laravel-based project management system with advanced task management, role-based access control (RBAC), and AI-ready architecture.

## 🌟 Features

### 📊 **Tasks Management**
- **Kanban Board** - Visual task management with drag-and-drop
- **List View** - Detailed task listing with filters and search
- **Task CRUD** - Complete create, read, update, delete operations
- **Sub-tasks** - Break down complex tasks
- **Comments System** - Nested comments with replies
- **File Attachments** - Secure file uploads with type validation
- **Time Tracking** - Log time entries for tasks
- **User Assignment** - Assign multiple users to tasks
- **Tags** - Organize tasks with custom tags
- **Priority Levels** - High, Medium, Low
- **Status Tracking** - New, Pending, In Progress, Completed

### 📁 **Projects Management**
- Full CRUD operations for projects
- Project overview and statistics
- Team member assignment
- Favorite projects
- Project filtering and search

### 👥 **User Management**
- User CRUD operations
- Avatar upload
- Role assignment
- Profile management
- Self-deletion protection

### 🔐 **Security & Permissions**
- **Role-Based Access Control (RBAC)** using Spatie Laravel-Permission
- 4 Default Roles: Super Admin, Admin, Manager, User
- Granular permissions across 5 modules
- **Security Features:**
  - File upload type validation (prevents malicious uploads)
  - CSRF protection
  - Input validation and sanitization
  - Authorization checks on all endpoints
  - Comment length limits
  - Date validation

### 🎨 **UI/UX**
- Modern, responsive design
- Statistics dashboards
- Real-time search and filters
- Pagination support
- Avatar with fallback initials
- Status and priority badges

---

## 🛠️ Technology Stack

- **Framework:** Laravel 11.x
- **PHP:** 8.2+
- **Database:** MySQL
- **Frontend:** Blade Templates, Bootstrap 5
- **Permissions:** Spatie Laravel-Permission
- **Icons:** Remix Icon

---

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL
- Node.js & NPM

### Steps

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/Project-Management.git
cd Project-Management
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database**
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Seed database**
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

7. **Build assets**
```bash
npm run dev
# or for production
npm run build
```

8. **Assign Super Admin role**
```bash
php assign_super_admin.php
```
Then login with your user credentials.

9. **Start the server**
```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

## 🗂️ Database Schema

Navigate to: `http://127.0.0.1:8000/management/`

Available sections:
- **Users:** `/management/users`
- **Roles:** `/management/roles`
- **Permissions:** `/management/permissions`
- **Projects:** `/management/projects`
- **Tasks:** `/management/tasks` (List View)
- **Tasks Kanban:** `/management/tasks/kanban` (Kanban Board)

### Creating a Task

1. Navigate to Tasks → Create Task
2. Fill in task details:
   - **Basic Info:** Title (required), Description
   - **Project:** Link to existing project (optional)
   - **Timeline:** Due Date (required)
   - **Classification:** Status, Priority
   - **Team:** Assign multiple users
   - **Organization:** Add tags
3. Click "Create Task"
4. Task gets auto-generated number (e.g., #VLZ0001)

### Using Kanban Board

1. Navigate to Tasks → Kanban Board (`/management/tasks/kanban`)
2. View tasks organized in columns by status:
   - **New** - Newly created tasks
   - **Pending** - Tasks waiting to start
   - **In Progress** - Currently active tasks
   - **Completed** - Finished tasks
3. Click any task card to view full details
4. Use for quick visual overview of project status

### Managing Task Workflow

**Adding Comments:**
1. Open task details page
2. Scroll to Comments tab
3. Type your comment
4. Click "Add Comment"
5. Supports nested replies

**Uploading Attachments:**
1. Open task details page
2. Go to Attachments tab
3. Click "Upload File"
4. Select file (max 10MB)
5. Allowed types: pdf, doc, docx, xls, xlsx, images, zip

**Tracking Time:**
1. Open task details page
2. Go to Time Entries tab
3. Enter date and duration
4. Log time spent on task

### Core Tables
- `users` - User accounts
- `roles` - User roles
- `permissions` - System permissions
- `projects` - Projects
- `tasks` - Tasks with soft deletes
- `task_user` - Task assignments (pivot)
- `task_comments` - Task comments with nested replies
- `task_attachments` - File attachments
- `task_tags` - Task tags
- `task_sub_tasks` - Sub-tasks
- `task_time_entries` - Time tracking

---

## 🔑| Module | Permissions |
|--------|-------------|
| Users | view-users, create-users, edit-users, delete-users |
| Roles | view-roles, create-roles, edit-roles, delete-roles |
| Permissions | view-permissions, create-permissions, edit-permissions, delete-permissions |
| Projects | view-projects, create-projects, edit-projects, delete-projects |
| **Tasks** | **view-tasks, create-tasks, edit-tasks, delete-tasks** |

---

## 📚 Usage

### Creating a Task

1. Navigate to **Tasks → Create Task**
2. Fill in task details:
   - Title (required)
   - Description
   - Project (optional)
   - Due Date (required)
   - Status & Priority
   - Assign users
   - Add tags
3. Click **Create Task**

### Using Kanban Board

1. Navigate to **Tasks → Kanban Board**
2. View tasks organized by status
3. Drag and drop to change status (if enabled)
4. Click on any task to view details

### Managing Permissions

1. Navigate to **Management → Roles**
2. Create/Edit a role
3. Assign permissions
4. Assign role to users in **Management → Users**

---

## 🔒 Security Features

### Implemented Security Measures

1. **File Upload Protection**
   - Restricted file types: pdf, doc, docx, xls, xlsx, png, jpg, jpeg, gif, zip, rar, txt
   - Max file size: 10MB
   - Prevents .php, .exe, and malicious file uploads

2. **Authorization**
   - Permission checks on all CRUD operations
   - Route middleware protection
   - Form request authorization

3. **Input Validation**
   - Comment max length: 1000 characters
   - Description max length: 5000 characters
   - Date validation (no past dates for task creation)
   - Time entry validation (max 24 hours, no future dates)

4. **Data Integrity**
   - Database transactions (recommended for critical operations)
   - Soft deletes for tasks
   - Race condition prevention in task number generation
   - Database indexes for performance

5. **CSRF Protection**
   - All forms include CSRF tokens
   - Protected against cross-site request forgery

---

## 🐛 Known Issues & Solutions

### Issue: View not found
**Solution:** Clear view cache
```bash
php artisan view:clear
php artisan config:clear
```

### Issue: Permission denied
**Solution:** Run database seeder and assign role
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
php assign_super_admin.php
```

### Issue: File upload fails
**Solution:** Check storage permissions
```bash
php artisan storage:link
chmod -R 775 storage
```

---

## 🚀 Performance Optimization

1. **Database Indexes**
   - Indexes on foreign keys
   - Composite index on `status` and `due_date`
   - Migration included for all indexes

2. **Eager Loading**
   - Relationships are eager loaded to prevent N+1 queries
   - Example: `Task::with(['project', 'assignedUsers'])`

3. **Caching** (Future)
   - Redis support ready
   - Query result caching planned

---

## 🔄 API Endpoints

### Tasks Management
```
GET    /management/tasks              - List all tasks
GET    /management/tasks/kanban       - Kanban board view
GET    /management/tasks/create       - Create form
POST   /management/tasks              - Store task
GET    /management/tasks/{id}         - Show task
GET    /management/tasks/{id}/edit    - Edit form
PUT    /management/tasks/{id}         - Update task
DELETE /management/tasks/{id}         - Delete task (soft)

POST   /management/tasks/{id}/comments         - Add comment
POST   /management/tasks/{id}/attachments      - Upload file
POST   /management/tasks/{id}/sub-tasks        - Add sub-task
PATCH  /management/tasks/sub-tasks/{id}/toggle - Toggle sub-task
POST   /management/tasks/{id}/time-entries     - Log time
```

---

## 🧪 Testing

### Run Tests (Coming Soon)
```bash
php artisan test
```

### Manual Testing Checklist
- [ ] Create task
- [ ] Edit task
- [ ] Delete task
- [ ] Upload attachment (test .php rejection)
- [ ] Add comment
- [ ] Toggle sub-task
- [ ] Log time entry
- [ ] Check permissions
- [ ] Test Kanban board

---

## 📈 Future Enhancements

- [ ] **AI Features**
  - AI Task Assistant
  - AI Analytics
  - Smart task recommendations
  
- [ ] **Notifications**
  - Email notifications
  - In-app notifications
  - Task reminders

- [ ] **Reports**
  - Task completion reports
  - Time tracking reports
  - User productivity analytics

- [ ] **Integrations**
  - Slack integration
  - Google Calendar sync
  - Email integration

---

## 👨‍💻 Development

### Code Quality Standards
- PSR-12 coding standards
- Laravel best practices
- Comprehensive validation
- Security-first approach

### Git Workflow
```bash
# Feature branch
git checkout -b feature/new-feature

# Commit
git add .
git commit -m "feat: add new feature"

# Push
git push origin feature/new-feature
```

---

## 📝 Changelog

### Version 1.0.0 (2026-01-07)
- ✅ Complete Tasks Management System
- ✅ Kanban Board implementation
- ✅ Security fixes (14 critical fixes)
- ✅ Delete functionality
- ✅ File validation
- ✅ Database indexes
- ✅ Full RBAC system
- ✅ Projects Management
- ✅ User Management

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---


## 🙏 Acknowledgments

- Laravel Framework
- Spatie Laravel-Permission
- Bootstrap
- RemixIcon


---

**Made with ❤️ using Laravel**
