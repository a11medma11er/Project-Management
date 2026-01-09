# AI-Powered Project Management System

## Overview

A comprehensive enterprise-grade project management platform built with Laravel 12 and PHP 8.2, featuring an advanced AI decision-making system, real-time collaboration tools, complete project lifecycle management, and powerful analytics dashboards.

## Problem Statement

Modern project management requires balancing multiple priorities, tracking team productivity, and making data-driven decisions quickly. Traditional systems lack intelligent automation and fail to provide actionable insights from historical data. This platform solves these challenges through:

- Intelligent task analysis and priority recommendations
- Self-learning AI that improves from user feedback
- Complete audit trails for accountability
- Real-time collaboration and activity tracking
- Comprehensive reporting and analytics

## Target Audience

### Primary Users
- **Project Managers**: Manage projects, assign tasks, track progress, review AI recommendations
- **Team Leaders**: Oversee team activities, balance workload, coordinate deliverables
- **Team Members**: Complete tasks, log time, collaborate through comments
- **Executives**: View analytics, monitor performance, make strategic decisions

### Secondary Users
- **Administrators**: Manage users, roles, permissions, and system configuration
- **AI Operators**: Configure AI settings, manage prompts, review decision accuracy

## Core Features

### Project Management
- Complete CRUD operations for projects with status tracking
- Project statuses: Inprogress, Completed, On Hold
- Priority levels: High, Medium, Low
- Privacy settings: Private, Team, Public
- Team lead assignment and member management
- Progress calculation based on task completion
- Favorite project marking
- File attachments with upload management
- Nested comments with replies
- Activity logging with Spatie Activity Log

### Task Management
- Comprehensive task lifecycle management
- Task statuses: New, Pending, Inprogress, Completed
- Priority levels: High, Medium, Low
- Multi-user assignment with role specification
- Due date tracking with overdue detection
- Auto-generated task numbers (e.g., #VLZ0001)
- Kanban board integration with drag-drop
- Subtasks with completion tracking
- Time entry logging
- File attachments
- Tags for categorization
- Comments with nested replies
- Soft delete for recovery

### AI-Powered Features
- **Task Analysis**: AI analyzes task complexity, dependencies, and urgency
- **Priority Recommendations**: Suggests priority changes based on patterns
- **Project Breakdown**: Decomposes large projects into manageable tasks
- **Decision Tracking**: Complete audit trail of all AI recommendations
- **Confidence Scoring**: Each recommendation includes confidence level (0.0-1.0)
- **Human-in-the-Loop**: All AI decisions require manual approval
- **User Action Tracking**: pending, accepted, rejected, modified states
- **Feedback Loop**: System learns from user acceptances and rejections
- **Guardrails**: Safety rules to prevent inappropriate recommendations
- **Multiple Providers**: Support for OpenAI (GPT-4) and Claude (Anthropic)

### Permissions & Security
- Role-based access control (RBAC) with Spatie Permission
- Granular permissions for projects, tasks, users, roles, activity logs
- AI-specific permissions:
  - `access-ai-control`: Access AI control panel
  - `view-ai-decisions`: View AI recommendations
  - `approve-ai-actions`: Accept/reject AI decisions
  - `manage-ai-prompts`: Configure AI prompt templates
  - `manage-ai-settings`: Configure AI system settings
  - `manage-ai-safety`: Configure guardrails and safety rules
  - `test-ai-prompts`: Test prompt templates
  - `view-ai-analytics`: View AI performance metrics
- Rate limiting: 60 requests/minute per user
- XSS and SQL injection protection
- Input validation on all forms

### Analytics & Reporting
- Real-time dashboard with key metrics
- Project status distribution (donut charts)
- Monthly trend analysis
- Team member performance tracking
- Task completion rates
- Time tracking summaries
- AI decision acceptance rates
- Confidence score trends
- Custom date range filtering

### Notifications
- Real-time activity notifications
- Email notifications for important events
- Optional Slack webhook integration
- Comment mention notifications

## Technology Stack

### Backend
- **Framework**: Laravel 12.0
- **PHP**: 8.2+
- **Database**: MySQL 8.0+
- **Authentication**: Laravel Sanctum + Laravel UI
- **Permissions**: Spatie Permission 6.24
- **Activity Logging**: Spatie Activity Log 4.10
- **Caching**: Redis (optional, file-based fallback)
- **Queue**: Database driver
- **Testing**: PHPUnit 11.0, PestPHP 3.0

### Frontend
- **UI Framework**: Bootstrap 5.3.6
- **Template**: Velzon 4.3.0 Admin Dashboard
- **Charts**: ApexCharts 4.7.0, Chart.js 4.4.9
- **Icons**: Feather Icons 4.29.2
- **Calendar**: FullCalendar 6.1.15
- **Drag & Drop**: Sortable.js 1.15.6
- **Date Picker**: Flatpickr 4.6.13
- **Rich Text Editor**: CKEditor 5, Quill 1.3.7
- **File Upload**: Filepond 4.30.4
- **Notifications**: SweetAlert2 11.22.0, Toastify.js 1.12.0

### Development Tools
- **Build Tool**: Vite 5.0.12
- **Package Manager**: npm
- **Code Quality**: Laravel Pint 1.13
- **Testing**: Mockery 1.6, Faker 1.23

## Architecture Overview

```
┌─────────────────────────────────────────────┐
│         Presentation Layer                  │
│  Blade Templates + Bootstrap 5              │
│  JavaScript + ApexCharts                    │
├─────────────────────────────────────────────┤
│         Application Layer                   │
│  Controllers (Web Routes)                   │
│  - DashboardController                      │
│  - ProjectController                        │
│  - TaskController                           │
│  - KanbanController                         │
│  - AI Controllers (15)                      │
│  - Management Controllers (7)               │
├─────────────────────────────────────────────┤
│         Business Logic Layer                │
│  Services                                   │
│  - AI Services (20+)                        │
│  - ActivityService                          │
│  - KanbanService                            │
│  - TaskStatusService                        │
├─────────────────────────────────────────────┤
│         Data Access Layer                   │
│  Models (Eloquent ORM)                      │
│  - Project, Task, User                      │
│  - AI Models (Decision, Prompt, etc.)      │
│  - Activity Logs                            │
├─────────────────────────────────────────────┤
│         Database Layer                      │
│  MySQL 8.0+                                 │
│  - Tables (20+)                             │
│  - Indexes for Performance                  │
│  - Database Views for AI                    │
│  Redis (Caching)                            │
└─────────────────────────────────────────────┘
```

### Design Patterns
- **MVC (Model-View-Controller)**: Clean separation of concerns
- **Service Layer Pattern**: Business logic in dedicated services
- **Repository Pattern**: Implicit through Eloquent ORM
- **Observer Pattern**: Model events for activity logging
- **Strategy Pattern**: AI guardrail rules
- **Factory Pattern**: Database factories for testing

## System Requirements

### Minimum Requirements
- PHP 8.2 or higher
- Composer 2.x
- MySQL 8.0 or higher
- Node.js 18.x or higher
- npm 9.x or higher
- 2GB RAM
- 1GB free disk space

### Recommended Requirements
- PHP 8.3
- MySQL 8.0+ with InnoDB engine
- Redis 6.x or higher (for caching)
- 4GB RAM
- 5GB free disk space
- SSL certificate for production

### Required PHP Extensions
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- XML
- Ctype
- JSON
- BCMath
- Fileinfo
- GD or Imagick

## Installation

### 1. Clone Repository
```bash
git clone https://github.com/a11medma11er/Project-Management.git
cd Project-Management
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration

Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Run migrations:
```bash
php artisan migrate
```

### 5. Seed Database
```bash
# Seed with default data
php artisan db:seed

# Seed AI permissions
php artisan db:seed --class=AIPermissionsSeeder
```

### 6. Storage Setup
```bash
# Create symbolic link for public storage
php artisan storage:link

# Set permissions
chmod -R 775 storage bootstrap/cache
```

### 7. Build Frontend Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Start Development Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## Default Credentials

After seeding, use these credentials:

```
Admin Account:
Email: admin@admin.com
Password: password

User Account:
Email: user@user.com
Password: password
```

## Configuration

### AI System Configuration

Add to `.env`:
```env
# Enable/Disable AI System
AI_SYSTEM_ENABLED=true

# AI Provider (local, openai, claude)
AI_DEFAULT_PROVIDER=local

# OpenAI Configuration (Optional)
OPENAI_API_KEY=your-openai-api-key
OPENAI_MODEL=gpt-4
OPENAI_MAX_TOKENS=1000
OPENAI_TIMEOUT=30

# Claude Configuration (Optional)
CLAUDE_API_KEY=your-claude-api-key
CLAUDE_MODEL=claude-3-sonnet-20240229
CLAUDE_MAX_TOKENS=1000

# AI Guardrails
AI_MIN_CONFIDENCE=0.7
AI_MAX_ACTIONS_PER_HOUR=100
AI_REQUIRE_APPROVAL_BELOW=0.8

# Performance
AI_CACHE_TTL=3600
AI_CACHE_DRIVER=redis
AI_ENABLE_QUERY_CACHE=true
```

### Cache Configuration

For Redis caching:
```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Queue Configuration

For background jobs:
```env
QUEUE_CONNECTION=database
```

Run queue worker:
```bash
php artisan queue:work
```

### Email Configuration

For notifications:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Slack Integration (Optional)

For Slack notifications:
```env
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
```

## Development Workflow

### Running Tests
```bash
# Run PHPUnit tests
php artisan test

# Run specific test
php artisan test --filter=ProjectTest

# Run Pest tests
./vendor/bin/pest
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Check code without fixing
./vendor/bin/pint --test
```

### Database Management
```bash
# Fresh migration with seeding
php artisan migrate:fresh --seed

# Rollback last migration
php artisan migrate:rollback

# View migration status
php artisan migrate:status
```

### Cache Management
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan optimize
```

## Project Structure

```
project-management/
├── app/
│   ├── Console/Commands/          # Artisan commands
│   ├── Contracts/                 # Interfaces
│   ├── DTO/                       # Data Transfer Objects
│   ├── Enums/                     # Enumerations
│   ├── Http/
│   │   ├── Controllers/           # HTTP controllers
│   │   │   ├── Admin/AI/         # AI system controllers
│   │   │   └── Management/       # Management controllers
│   │   ├── Middleware/            # HTTP middleware
│   │   └── Requests/              # Form requests
│   ├── Models/                    # Eloquent models
│   │   └── AI/                   # AI-related models
│   ├── Notifications/             # Notification classes
│   ├── Providers/                 # Service providers
│   ├── Rules/                     # Validation rules
│   └── Services/                  # Business logic services
│       └── AI/                   # AI services
├── config/                        # Configuration files
├── database/
│   ├── factories/                # Model factories
│   ├── migrations/               # Database migrations
│   ├── seeders/                  # Database seeders
│   └── sql/                      # SQL views
├── docs/                          # Documentation
├── public/                        # Public assets
├── resources/
│   ├── js/                       # JavaScript files
│   ├── scss/                     # SCSS stylesheets
│   └── views/                    # Blade templates
├── routes/
│   ├── web.php                   # Web routes
│   ├── api.php                   # API routes
│   ├── ai.php                    # AI routes
│   └── channels.php              # Broadcast channels
├── storage/                       # Storage directory
├── tests/                         # Tests
└── vendor/                        # Composer dependencies
```

## Key Directories Explained

### app/Services/AI/
Contains all AI-related business logic:
- AIAnalysisService: Task and project analysis
- AIDecisionEngine: Decision-making logic
- AIContextBuilder: Context building for AI
- AIGuardrailService: Safety rules enforcement
- AIMetricsService: Performance metrics
- And 15+ more services

### database/migrations/
Database schema definitions:
- User and authentication tables
- Projects and tasks tables
- AI decision tracking tables
- Activity logs
- Comments, attachments, time entries

### resources/views/
Blade templates organized by feature:
- admin/: Admin panel views
- management/: CRUD views
- apps-: Application views
- dashboard-projects.blade.php: Main dashboard

## Troubleshooting

### Common Issues

**Issue**: Permission denied errors
```bash
# Solution: Fix storage permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

**Issue**: Class not found errors
```bash
# Solution: Regenerate autoload files
composer dump-autoload
```

**Issue**: Database connection failed
```bash
# Solution: Verify database credentials in .env
# Ensure MySQL service is running
sudo systemctl status mysql
```

**Issue**: Node module errors
```bash
# Solution: Reinstall dependencies
rm -rf node_modules package-lock.json
npm install
```

**Issue**: Mixed content errors (HTTP/HTTPS)
```bash
# Solution: Add to .env
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com
```

## Performance Optimization

### Production Optimization
```bash
# Optimize configuration loading
php artisan config:cache

# Optimize route loading
php artisan route:cache

# Optimize view compilation
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev

# Build production assets
npm run build
```

### Database Optimization
- Ensure indexes are properly set (migrations include index definitions)
- Use database views for complex AI queries
- Enable query caching for AI context building
- Monitor slow queries with `DB::enableQueryLog()`

### Caching Strategy
- Use Redis for session and cache storage in production
- Cache AI context data (TTL: 1 hour)
- Cache dashboard statistics (TTL: 10 minutes)
- Implement cache warming for frequently accessed data

## Contributing

### Development Guidelines
1. Follow PSR-12 coding standards
2. Write tests for new features
3. Update documentation
4. Use meaningful commit messages
5. Create pull requests for review

### Code Style
```bash
# Format code before committing
./vendor/bin/pint
```

### Testing Requirements
- Unit tests for services
- Feature tests for controllers
- Integration tests for AI workflows
- Minimum 70% code coverage

## License

This project is licensed under the MIT License. See [LICENSE](LICENSE) file for details.

## Support

For issues, questions, or contributions:
- GitHub Issues: [github.com/a11medma11er/Project-Management/issues](https://github.com/a11medma11er/Project-Management/issues)
- Documentation: See `docs/` directory
- Email: support@yourdomain.com

## Acknowledgments

- Laravel Framework
- Spatie for Activity Log and Permissions packages
- Velzon Admin Template by Themesbrand
- All open-source contributors

---

**Version**: 1.0.0  
**Last Updated**: January 2026  
**Status**: Production Ready
