# 🚀 AI-Powered Project Management System

> Enterprise-grade project management platform with advanced AI capabilities and real-time collaboration

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## 📋 Overview

A comprehensive project management system built with Laravel 11, featuring an advanced AI assistant that learns from your decisions, automates workflows, and provides intelligent insights. The system includes complete project lifecycle management, team collaboration tools, real-time activity tracking, and powerful analytics dashboards.

### ✨ Key Features

#### 🤖 AI-Powered Features
- **Intelligent Decision Making** - AI analyzes tasks and projects, providing recommendations
- **Self-Learning System** - AI improves accuracy through feedback loops with acceptance rate tracking
- **Human-in-the-Loop** - All AI decisions require human approval (pending/accepted/rejected)
- **Smart Automation** - Automated workflows with 5 triggers and 4 action types
- **Advanced Analytics** - Real-time AI statistics with confidence scoring
- **External AI Integration** - OpenAI (GPT-4) and Claude (Anthropic) support
- **Decision Tracking** - Complete audit trail of all AI recommendations and actions

#### 📊 Project Management
- **Projects & Tasks** - Complete CRUD with status tracking (Completed, In Progress, Yet to Start, Cancelled)
- **Task Management** - Comprehensive task system with priorities, assignments, and progress tracking
- **Team Collaboration** - Multi-user assignment, role management, and team member profiles
- **Activity Tracking** - Full audit log with Spatie Activity Log integration
- **File Attachments** - Upload and manage project and task attachments with preview
- **Comments System** - Nested comments with replies on projects and tasks
- **Time Tracking** - Task time entries for accurate project hour monitoring
- **Permissions** - Granular RBAC with Spatie Permission (create, view, edit, delete)
- **Real-time Dashboard** - Live statistics, charts, and project overview

#### 📈 Analytics & Reporting
- **Projects Status Dashboard** - Real-time donut charts showing project distribution
- **AI Status Dashboard** - AI decision metrics with acceptance rates and confidence scores
- **Team Performance** - Member activity tracking with hours and task counts
- **Trend Analysis** - Month-over-month comparison for projects, tasks, and hours
- **Custom Date Ranges** - Flexible reporting with date filters
- **Export Capabilities** - PDF/Excel export for all reports

#### 🔔 Notifications & Communication
- **Activity Notifications** - Real-time updates on project and task changes
- **Email Notifications** - Automated email alerts for important events
- **Slack Integration** - Optional Slack webhook notifications
- **Comment Notifications** - Alerts when users are mentioned or replied to

#### 🔒 Security & Performance
- **Input Validation** - XSS and SQL injection protection
- **Rate Limiting** - 60 requests/minute per user
- **Multi-layer Caching** - Redis support with intelligent cache warming
- **Query Optimization** - Database views and index suggestions
- **Performance Monitoring** - Real-time metrics and slow query detection
- **Audit Logging** - Complete tracking of all system changes

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────┐
│         Frontend Layer                      │
│  Blade Templates + Bootstrap 5              │
│  ApexCharts + Feather Icons                 │
├─────────────────────────────────────────────┤
│         Controllers Layer                   │
│  ProjectController │ TaskController         │
│  DashboardController │ AI Controllers       │
│  Management Controllers (14+)               │
├─────────────────────────────────────────────┤
│         Services Layer                      │
│  AI Core Services (24)                      │
│  Analytics │ Automation │ Integration       │
│  Performance │ Security │ Notification      │
├─────────────────────────────────────────────┤
│         Models Layer                        │
│  Project │ Task │ User │ AI Models          │
│  Activity Logging │ Permissions             │
├─────────────────────────────────────────────┤
│         Database Layer (MySQL)              │
│  Projects │ Tasks │ Users                   │
│  AI Decisions │ Activity Log                │
│  Time Entries │ Comments │ Attachments      │
└─────────────────────────────────────────────┘
```

### Database Schema Highlights
- **Projects**: title, status, priority, progress, team_lead, members
- **Tasks**: title, status, priority, progress, assignees, due_date
- **AI Decisions**: decision_type, confidence_score, user_action, reasoning
- **Activity Log**: subject, causer, description, properties (Spatie)
- **Comments**: project_comments, task_comments with nested replies
- **Attachments**: project_attachments, task_attachments with file paths
- **Time Entries**: task_time_entries with duration tracking

---

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js & NPM
- Redis (recommended for caching)

### Installation

```bash
# Clone repository
git clone https://github.com/a11medma11er/Project-Management.git
cd Project-Management

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Seed AI permissions
php artisan db:seed --class=AIPermissionsSeeder

# Build assets
npm run build

# Start server
php artisan serve
```

### Default Credentials
```
Admin: admin@admin.com / password
User: user@user.com / password
```

---

## ⚙️ Configuration

### AI System Setup

Add to `.env`:
```env
# AI Core Settings
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

### Run AI Automation

```bash
# Manual execution
php artisan ai:automate

# Schedule in app/Console/Kernel.php
$schedule->command('ai:automate')->hourly();
```

---

## 📊 Features Overview

### 1. Dashboard
**Path:** `/dashboard`
- Real-time project statistics (Active Projects, New Tasks, Total Hours)
- Projects Status donut chart (Completed, In Progress, Yet to Start, Cancelled)
- AI Status donut chart (Pending, Accepted, Rejected with confidence scores)
- Projects Overview bar chart (12-month trend)
- Upcoming tasks calendar
- Team members performance
- Recent activities feed

### 2. Project Management
**Path:** `/management/projects`
- Create/Edit projects with thumbnails and attachments
- Assign team lead and multiple team members
- Set priority (Low, Medium, High) and status tracking
- Progress monitoring with percentage bars
- Deadline management and alerts
- Project overview with tabbed interface:
  - Overview: Project details and team
  - Team: Member list with roles
  - Documents: File attachments with download
  - Tasks: Project tasks with progress tracking
  - Activities: Complete activity timeline
  - Comments: Discussion threads with replies

### 3. Task Management
**Path:** `/management/tasks`
- Full task lifecycle (New, Pending, In Progress, Completed, On Hold, Review, Cancelled)
- Priority management (Low, Medium, High)
- Multiple assignee support
- Due date tracking with overdue indicators
- Progress percentage tracking
- Task time entries for hour logging
- Subtasks and dependencies
- Comments and file attachments
- Task tags and categories

### 4. AI Control Panel
**Path:** `/admin/ai`
- **Learning System** - AI accuracy tracking and trends
- **Analytics & Reporting** - Custom reports with PDF/Excel export
- **Workflows & Automation** - Rule-based automation setup
- **Integrations** - OpenAI, Claude, Slack configuration
- **Performance Monitoring** - Cache stats and optimization
- **Security Dashboard** - Threat detection and rate limiting

### 5. Activity Tracking
- Real-time activity logs using Spatie Activity Log
- Track all changes to Projects, Tasks, Comments, Attachments
- User attribution (who did what, when)
- Filterable activity timeline
- Export activity reports

### 6. Team Collaboration
- User profiles with avatars
- Role-based permissions (Super Admin, Admin, Manager, User)
- Team member assignment to projects
- @mentions in comments
- Email and Slack notifications

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run AI tests only
php artisan test tests/Unit/AI
php artisan test tests/Feature/AI

# With coverage
php artisan test --coverage
```

**Test Coverage:**
- 16 automated tests
- Unit tests for core services
- Feature tests for controllers
- Security validation tests

---

## 📚 Documentation

Comprehensive documentation available in `/docs`:

- **[AI System Guide](docs/AI_SYSTEM_GUIDE.md)** - Complete AI features user guide
- **[Administrator Handbook](docs/ADMINISTRATOR_HANDBOOK.md)** - Setup & maintenance
- **[Developer Guide](docs/DEVELOPER_GUIDE.md)** - Development guidelines
- **[User Guide](docs/USER_GUIDE.md)** - End-user documentation
- **[API Reference](docs/API_REFERENCE.md)** - Full API documentation
- **[Database Schema](docs/database-schema.md)** - Database structure
- **[Environment Setup](docs/ENVIRONMENT_SETUP.md)** - Configuration guide
- **[AI Architecture](docs/ai-architecture-integration.md)** - AI system architecture

---

## 🚦 Getting Started Guide

### 1. First Login
1. Visit `http://localhost:8000`
2. Login with default credentials
3. Change password immediately
4. Update profile information

### 2. Create Your First Project
1. Navigate to **Projects** → **Create Project**
2. Fill in project details (title, description, priority)
3. Upload project thumbnail (optional)
4. Set deadline and start date
5. Assign team lead and members
6. Click **Create Project**

### 3. Add Tasks to Project
1. Open the project overview page
2. Go to **Tasks** tab
3. Click **Add Task** button
4. Fill in task details with priority and due date
5. Assign team members
6. Set initial progress
7. Save task

### 4. Track Progress
1. Update task status as work progresses
2. Log time entries for accurate hour tracking
3. Add comments for team communication
4. Upload relevant files and attachments
5. Monitor dashboard for overall progress

### 5. Enable AI Features (Optional)
1. Configure `.env` with AI provider keys
2. Run AI permissions seeder
3. Access **Admin** → **AI Control**
4. Enable AI system
5. Configure automation workflows

---

## 🔧 Advanced Configuration

### Custom AI Provider
```php
// config/ai.php
'providers' => [
    'custom' => [
        'enabled' => true,
        'api_key' => env('CUSTOM_AI_API_KEY'),
        'endpoint' => 'https://api.custom-ai.com',
    ],
],
```

### Notification Channels
```php
// config/services.php
'slack' => [
    'webhook_url' => env('SLACK_WEBHOOK_URL'),
],
```

### File Upload Limits
```php
// config/filesystems.php
'upload_max_size' => 10240, // 10MB in KB
'allowed_extensions' => ['jpg', 'png', 'pdf', 'docx'],
```

---

## 🐛 Troubleshooting

### Common Issues

**Issue: Charts not displaying**
```bash
npm run build
php artisan optimize:clear
```

**Issue: File upload fails**
```bash
php artisan storage:link
chmod -R 775 storage
```

**Issue: AI features not working**
```bash
# Check AI configuration
php artisan config:clear
php artisan cache:clear

# Verify API keys in .env
# Run AI seeder
php artisan db:seed --class=AIPermissionsSeeder
```

**Issue: Slow dashboard loading**
```bash
# Enable Redis caching
# Update CACHE_DRIVER=redis in .env
composer require predis/predis

# Clear and warm cache
php artisan cache:clear
php artisan optimize
```

---

## 🎯 Project Stats

| Metric | Count |
|--------|-------|
| **Development Status** | Production Ready ✅ |
| **Total Files** | 100+ |
| **Lines of Code** | ~20,000+ |
| **Database Tables** | 25+ |
| **Models** | 15+ |
| **Controllers** | 20+ |
| **Services** | 24 |
| **Migrations** | 30+ |
| **Views** | 50+ |
| **API Endpoints** | 50+ |
| **Tests** | 16 |
| **Documentation** | 10 guides |
| **Features** | 75+ |

### Recent Updates (January 2026)
- ✅ Real-time dashboard with live statistics
- ✅ Projects Status chart with actual data
- ✅ AI Status dashboard integration
- ✅ Complete task management with tabs
- ✅ Nested comments system with replies
- ✅ File attachment upload/download
- ✅ Activity log integration for all models
- ✅ Team member performance tracking
- ✅ Time entry logging and reporting
- ✅ Trend analysis (month-over-month)

---

## 🛠️ Tech Stack

### Backend
- **Framework:** Laravel 11
- **PHP Version:** 8.2+
- **Database:** MySQL 8.0
- **Cache:** Redis (optional)
- **Permissions:** Spatie Permission (RBAC)
- **Activity Log:** Spatie Activity Log
- **Testing:** PHPUnit, Pest

### Frontend
- **Template Engine:** Blade
- **CSS Framework:** Bootstrap 5
- **Charts:** ApexCharts (Donut, Bar, Line)
- **Icons:** Feather Icons, Remix Icons
- **HTTP Client:** Axios
- **Date Picker:** Flatpickr
- **File Upload:** Native HTML5 with preview
- **Modal:** Bootstrap Modals

### AI & Integrations
- **AI Providers:** OpenAI (GPT-4), Claude (Anthropic), Local fallback
- **Notifications:** Laravel Mail, Slack Webhooks
- **File Storage:** Laravel Storage (local/public disk)
- **Webhooks:** Custom webhook support
- **Cron Jobs:** Laravel Task Scheduler

### Development Tools
- **Composer:** Dependency management
- **NPM:** Frontend asset management
- **Vite:** Asset bundling and compilation
- **Git:** Version control

---

## 🔐 Security Features

- ✅ Input sanitization (XSS protection)
- ✅ SQL injection prevention
- ✅ CSRF protection (Laravel default)
- ✅ Rate limiting (60 req/min)
- ✅ Permission-based access (RBAC)
- ✅ Audit logging (all actions tracked)
- ✅ Threat detection & monitoring
- ✅ Secure API authentication

---

## 📈 Performance

- **Average Response Time:** <500ms
- **Cache Hit Rate:** ~85%
- **Supported Users:** 100+ concurrent
- **Database:** Optimized with indexes, relationships, and eager loading
- **Caching:** Multi-layer (Redis + file) with query caching
- **File Uploads:** Efficient storage with public disk access
- **Real-time Updates:** Dashboard auto-refresh and live statistics
- **Query Optimization:** N+1 prevention with eager loading
- **Asset Compilation:** Vite for fast builds and hot reloading

---

## 🎨 UI/UX Features

- **Responsive Design:** Mobile, tablet, and desktop support
- **Dark/Light Mode:** Theme switching (if enabled)
- **Interactive Charts:** Hover tooltips and animations
- **Smooth Animations:** CSS transitions and loading states
- **Avatar Groups:** Multiple user display with overflow badges
- **Badge Colors:** Status-based color coding
- **Progress Bars:** Visual progress indicators
- **Empty States:** Friendly messages for no data
- **Toast Notifications:** Success/error feedback
- **Confirmation Modals:** Safe delete actions

---

## 📱 User Roles & Permissions

### Super Admin
- Full system access
- User management
- AI control panel
- System settings
- All project/task operations

### Admin
- Project management
- Task management
- Team management
- Reports and analytics
- Limited AI access

### Manager
- Assigned project management
- Task creation and assignment
- Team member coordination
- Project reports

### User
- View assigned projects/tasks
- Update task status and progress
- Add comments and attachments
- Log time entries
- View personal dashboard

---

## 🗺️ Roadmap

### Completed ✅
- [x] Core project and task management
- [x] AI decision system with learning
- [x] Real-time dashboard with statistics
- [x] Activity logging for all models
- [x] Comments system with nested replies
- [x] File attachment management
- [x] Time tracking and reporting
- [x] Team collaboration features
- [x] Trend analysis and charts
- [x] Role-based permissions

### Planned 🚀
- [ ] Kanban board view
- [ ] Gantt chart for project timeline
- [ ] Real-time WebSocket notifications
- [ ] Mobile app (iOS/Android)
- [ ] Calendar integration (Google, Outlook)
- [ ] Advanced reporting with custom filters
- [ ] Resource allocation optimizer
- [ ] Budget tracking and cost analysis
- [ ] Invoice generation
- [ ] Client portal access

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Coding Standards
- Follow PSR-12 coding style
- Write meaningful commit messages
- Add tests for new features
- Update documentation
- Keep methods focused and small

---

## 📧 Support

For issues, questions, or suggestions:
- **GitHub Issues:** [Report a bug](https://github.com/a11medma11er/Project-Management/issues)
- **Email:** support@yourproject.com
- **Documentation:** Check `/docs` folder

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Author

**Ahmed Maher**
- GitHub: [@a11medma11er](https://github.com/a11medma11er)
- Email: ahmed.maher@example.com

---

## 🙏 Acknowledgments

- **Laravel Framework** - Robust PHP framework
- **Spatie** - Permissions and Activity Log packages
- **OpenAI & Anthropic** - AI integration support
- **Bootstrap Team** - UI framework
- **ApexCharts** - Beautiful chart library
- **Feather Icons** - Clean icon set
- **All Contributors** - Community support

---

## 📸 Screenshots

### Dashboard
![Dashboard Overview](docs/screenshots/dashboard.png)
- Real-time statistics cards
- Project status donut chart
- AI status monitoring
- Team performance

### Project Overview
![Project Page](docs/screenshots/project-overview.png)
- Project details with team members
- Tabbed interface (Team, Documents, Tasks, Activities, Comments)
- File attachments
- Activity timeline

### Task Management
![Tasks Page](docs/screenshots/tasks.png)
- Task list with filtering
- Priority and status badges
- Progress tracking
- Multiple assignees

### AI Control Panel
![AI Dashboard](docs/screenshots/ai-control.png)
- Decision tracking
- Learning analytics
- Automation workflows
- Performance metrics

---

**Version:** 2.5.0 | **Last Updated:** January 9, 2026

---

<div align="center">
  <p>Made with ❤️ using Laravel</p>
  <p>⭐ Star this repo if you find it helpful!</p>
</div>
