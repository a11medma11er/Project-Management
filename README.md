# Project Management System

A comprehensive **SaaS-based Project Management System** with advanced **Role-Based Access Control (RBAC)** using Laravel 12 and Spatie Permission package.

## 🚀 Features

### ✅ Complete RBAC System
- **Users Management**: Full CRUD operations with role assignment
- **Roles Management**: Create, edit, delete roles with permission assignment
- **Permissions Management**: Granular permission control
- **4 Pre-defined Roles**: Super Admin, Admin, Manager, User
- **20 Permissions**: Covering Users, Roles, Permissions, Projects, and Tasks

### 🔒 Security Features
- Permission-based access control on all routes
- Super Admin protection (cannot be deleted or modified)
- Self-deletion prevention
- Server-side validation with Form Requests
- CSRF protection
- Password hashing (bcrypt)

### 🎨 User Interface
- Modern Bootstrap 5 responsive design
- Permission-based sidebar navigation
- Avatar upload support
- Success/Error notifications
- Pagination for large datasets
- Grouped permissions by module

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL/MariaDB
- Laravel 12
- Node.js & NPM (for frontend assets)

## 🛠️ Installation

### 1. Clone the repository
```bash
git clone https://github.com/yourusername/Project-Management.git
cd Project-Management
```

### 2. Install dependencies
```bash
composer install
npm install && npm run build
```

### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database configuration
Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_management
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run migrations and seeders
```bash
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 6. Create storage link
```bash
php artisan storage:link
```

### 7. Assign Super Admin role
```bash
php artisan tinker
```
Then run:
```php
$user = App\Models\User::first();
$user->assignRole('Super Admin');
exit
```

### 8. Start the development server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 👤 Default Login Credentials

- **Email**: `admin@themesbrand.com`
- **Password**: `12345678`

## 📁 Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Management/
│   │       ├── UserManagementController.php
│   │       ├── RoleController.php
│   │       └── PermissionController.php
│   └── Requests/
│       ├── StoreUserRequest.php
│       └── UpdateUserRequest.php
├── Models/
│   └── User.php
database/
├── migrations/
└── seeders/
    └── RolesAndPermissionsSeeder.php
resources/
└── views/
    └── management/
        ├── users/
        ├── roles/
        └── permissions/
```

## 🔐 Permissions List

### Users Management
- `view-users`
- `create-users`
- `edit-users`
- `delete-users`

### Roles Management
- `view-roles`
- `create-roles`
- `edit-roles`
- `delete-roles`

### Permissions Management
- `view-permissions`
- `create-permissions`
- `edit-permissions`
- `delete-permissions`

### Projects Management (Future)
- `view-projects`
- `create-projects`
- `edit-projects`
- `delete-projects`

### Tasks Management (Future)
- `view-tasks`
- `create-tasks`
- `edit-tasks`
- `delete-tasks`

## 🧪 Testing

Access the management panel at: `/management/users`

**Test Cases:**
- ✅ Create user with/without avatar
- ✅ Assign multiple roles to user
- ✅ Update user information
- ✅ Delete user (with protection checks)
- ✅ Create/Edit/Delete roles
- ✅ Manage permissions

## 🐛 Troubleshooting

### Management section not visible
```bash
php artisan tinker
$user = User::first();
$user->assignRole('Super Admin');
```

### Clear cache
```bash
php artisan optimize:clear
php artisan config:cache
```

### Storage link issue
```bash
php artisan storage:link
```

## 📦 Packages Used

- [Laravel 12](https://laravel.com)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) - RBAC system
- [Bootstrap 5](https://getbootstrap.com) - UI Framework

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👨‍💻 Author

Built with ❤️ by Ahmed

## 🔄 Changelog

### Version 1.0.0 (2026-01-07)
- ✅ Complete RBAC system implementation
- ✅ Users, Roles, and Permissions management
- ✅ 6 bug fixes and optimizations
- ✅ Security enhancements
- ✅ Responsive UI with permission-based navigation

---

**Note**: This is a demonstration project showcasing Laravel RBAC implementation with best practices.
