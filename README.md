# 🚀 Project Management System

A comprehensive Project Management System built with Laravel 12, featuring advanced Role-Based Access Control (RBAC) and complete project lifecycle management.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red)
![PHP](https://img.shields.io/badge/PHP-8.2-blue)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Modules](#modules)
- [Security](#security)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

---

## ✨ Features

### 🔐 Role-Based Access Control (RBAC)
- **Dynamic Roles Management:** Create, edit, and delete custom roles
- **Granular Permissions:** 20+ permissions across 5 modules
- **Permission Assignment:** Flexible permission-to-role mapping
- **User Role Management:** Assign multiple roles to users
- **Protected Operations:** Super Admin protection, self-deletion prevention

### 📊 Projects Management
- **Complete CRUD Operations:** Create, Read, Update, Delete projects
- **Rich Project Details:**
  - Title, Description (CKEditor), Thumbnail
  - Priority (High/Medium/Low), Status (Inprogress/Completed/On Hold)
  - Privacy (Public/Team/Private)
  - Deadlines, Start Dates, Progress Tracking
  - Skills Tags, Categories
- **Team Management:**
  - Assign Team Lead
  - Add/Remove Team Members
  - Role-based member management
- **Favorite Projects:** Quick access to important projects
- **Advanced Search & Filters:** By status, priority, keywords
- **File Attachments:** Upload and manage project files
- **Dynamic UI:** Fully responsive Blade templates with Bootstrap 5

### 👥 User Management
- **User CRUD:** Full user lifecycle management
- **Avatar Upload:** Profile picture management
- **Role Assignment:** Multi-role support per user
- **Security Features:**
  - Password change functionality
  - Self-deletion prevention
  - Super Admin protection

### 🎨 Modern UI/UX
- **Bootstrap 5 Integration:** Clean, responsive design
- **CKEditor Integration:** Rich text editing for descriptions
- **Dynamic Avatars:** User initials fallback
- **AJAX Features:** Favorite toggle without page reload
- **Pagination:** Clean navigation for large datasets

---

## 🛠️ Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Database:** MySQL (configurable)
- **Frontend:** Blade Templates, Bootstrap 5
- **Rich Text:** CKEditor
- **Permissions:** Spatie Laravel Permission
- **Authentication:** Laravel Breeze/UI
- **File Storage:** Laravel Storage (local/s3)

---

## 📦 Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL 5.7+ / MariaDB 10.3+
- Node.js & NPM (for frontend assets)

### Steps

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/Project-Management.git
cd Project-Management
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install Node dependencies**
```bash
npm install
npm run build
```

4. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database**
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=root
DB_PASSWORD=
```

6. **Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
```

7. **Create storage link**
```bash
php artisan storage:link
```

8. **Start development server**
```bash
php artisan serve
```

Visit: `http://127.0.0.1:8000`

---

## ⚙️ Configuration

### Initial Admin Setup

Run the provided script to assign Super Admin role:
```bash
php assign_super_admin.php
```

Or manually via tinker:
```bash
php artisan tinker
```
```php
$user = User::where('email', 'admin@example.com')->first();
$user->assignRole('Super Admin');
```

### Permissions Structure

| Module | Permissions |
|--------|-------------|
| Users | view-users, create-users, edit-users, delete-users |
| Roles | view-roles, create-roles, edit-roles, delete-roles |
| Permissions | view-permissions, create-permissions, edit-permissions, delete-permissions |
| Projects | view-projects, create-projects, edit-projects, delete-projects |

---

## 📖 Usage

### Access Management Panel

Navigate to: `http://127.0.0.1:8000/management/`

Available sections:
- **Users:** `/management/users`
- **Roles:** `/management/roles`
- **Permissions:** `/management/permissions`
- **Projects:** `/management/projects`

### Creating a Project

1. Navigate to Projects → Add New
2. Fill in project details:
   - Basic info (Title, Description)
   - Settings (Priority, Status, Privacy)
   - Timeline (Deadline, Start Date)
   - Team (Lead, Members)
   - Files (Thumbnail, Attachments)
3. Click "Create Project"

### Managing Team Members

1. On Create/Edit page, click "Invite Members"
2. Select users from the modal
3. Search functionality available for large teams
4. Members displayed with avatars

### Editing Projects

1. Click project card or "..." menu → Edit
2. All fields pre-populated
3. Update as needed
4. Changes saved immediately

### Deleting Projects

1. Click "..." menu → Remove
2. Confirm deletion in modal
3. Cascading delete:
   - Project record
   - Team associations
   - Uploaded files (thumbnail, attachments)

---

## 📚 Modules

### Projects Module

**Model: `App\Models\Project`**

**Relationships:**
- `teamLead()` - BelongsTo User (team leader)
- `creator()` - BelongsTo User (project creator)
- `members()` - BelongsToMany User (team members)
- `attachments()` - HasMany ProjectAttachment
- `comments()` - HasMany ProjectComment

**Scopes:**
- `scopeStatus($status)` - Filter by status
- `scopePriority($priority)` - Filter by priority
- `scopeFavorites()` - Only favorite projects
- `scopeSearch($term)` - Search in title/description

**Routes:**
```php
Route::prefix('management')->group(function () {
    Route::middleware('can:view-projects')->group(function () {
        Route::post('projects/{project}/toggle-favorite', 'toggleFavorite');
        Route::resource('projects', ProjectController::class);
    });
});
```

### Users Module

**Protected Operations:**
- Self-deletion blocked
- Super Admin deletion blocked
- Password optional on update

### Roles & Permissions

**Seeded Roles:**
- Super Admin (all permissions)
- Admin (all except permissions management)
- Manager (view users, all projects)
- User (view projects only)

---

## 🔒 Security

### Features
- CSRF Protection on all forms
- XSS Prevention in Blade templates
- SQL Injection protection via Eloquent ORM
- Role-based authorization (`@can` directives)
- File upload validation (type, size)
- Request validation (Form Requests)

### Best Practices
- Never commit `.env` file
- Use environment variables for sensitive data
- Regular security updates
- Strong password policies
- Two-factor authentication (recommended)

---

## 🧪 Testing

### Manual Testing Summary
✅ All CRUD operations tested and working
✅ Role-based access verified
✅ File uploads functional
✅ Search and filters operational
✅ Team management working
✅ Favorite toggle via AJAX

### Test Coverage
- Project creation with all fields
- Project editing and updates
- Project deletion with cascading
- Team member assignment
- Permission-based access control

**Full Test Report:** See `final_testing_report.md`

---

## 🐛 Troubleshooting

### Common Issues

**1. 500 Error after Create/Update/Delete**
- **Cause:** Incorrect route names
- **Solution:** All routes use `management.projects.*` namespace
- **Fixed in:** Latest version

**2. Avatar not displaying**
- **Cause:** Storage link not created
- **Solution:** Run `php artisan storage:link`

**3. Permissions not working**
- **Cause:** User not assigned a role
- **Solution:** Assign role via Users management or tinker

**4. CKEditor not loading**
- **Cause:** Missing assets
- **Solution:** Run `npm install && npm run build`

**5. Favorite button not working**
- **Cause:** Missing JavaScript or route conflict
- **Solution:** JavaScript added in latest version, route order fixed

### Logs Location
```bash
storage/logs/laravel.log
```

---

## 📝 Recent Updates

### Version 1.0.0 (Latest)

**Projects Management - Complete Implementation**
- ✅ Full CRUD operations
- ✅ Team management with roles
- ✅ Advanced search and filtering
- ✅ Favorite projects functionality
- ✅ File attachments support
- ✅ Rich text descriptions (CKEditor)
- ✅ Progress tracking
- ✅ Privacy settings

**Bug Fixes:**
- Fixed route naming conflicts
- Fixed 500 error on redirects
- Added missing JavaScript for favorites
- Added validation for all fields
- Fixed avatar display issues

**UI/UX Improvements:**
- Dynamic Blade templates (from 1400+ to 312 lines)
- Responsive design
- Avatar fallbacks
- Clean pagination
- Search with debounce

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/YourFeature`)
3. Commit your changes (`git commit -m 'Add YourFeature'`)
4. Push to the branch (`git push origin feature/YourFeature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

---

## 👨‍💻 Author

**Ahmed Mmaher**

---

## 🙏 Acknowledgments

- Laravel Framework
- Spatie Permission Package
- Bootstrap Team
- CKEditor Team
- All Contributors

---

**Built with ❤️ using Laravel**
