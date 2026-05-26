# Restaurant BYOB POS System - Complete Setup Guide

## Project Overview

This is a comprehensive Laravel-based Restaurant Management POS (Point of Sale) System for BYOB (Bring Your Own Bottle) restaurants. The system includes role-based access control, module management, and a professional red and white themed UI.

## Tech Stack

- **Framework**: Laravel (Latest)
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, Tailwind CSS, Font Awesome Icons
- **Authentication**: Laravel Built-in Authentication
- **Server**: PHP 8.1+

## Features Implemented

### 1. ✅ Database Setup
- MySQL database `restaurant_byob` created and configured
- 9 migrations executed successfully
- All tables properly structured with foreign key relationships

### 2. ✅ User Role Management
Three comprehensive roles with different permission levels:
- **Admin**: Full system access to all modules
- **Manager**: Access to most modules except POS
- **Cashier**: Limited access - only POS & Billing

### 3. ✅ Module System
8 main modules available:
1. **Customer Management** - Manage customer information and profiles
2. **Employee Management** - Staff management and HR functions
3. **Inventory & Products** - Stock and product management
4. **Supplier Management** - Vendor and purchase management
5. **Wastage Management** - Track product loss and waste
6. **POS & Billing** - Point of sale and transaction processing
7. **Reports** - Analytics and reporting
8. **Settings** - System configuration and preferences

### 4. ✅ Authentication System
- Custom login page with red and white branding (Piznek restaurant theme)
- Email/password authentication
- Remember me functionality
- Logout capability
- Session management

### 5. ✅ Dashboard
- Role-based welcome message
- Quick stat cards (Total Sales, Active Orders, Inventory Items, Active Users)
- Module cards with icons and descriptions
- Responsive design (mobile, tablet, desktop)
- Sidebar navigation on desktop
- Mobile-friendly navigation

### 6. ✅ Professional UI
- Red (#dc2626) and white color scheme
- Gradient effects for depth
- Smooth hover transitions
- Responsive grid layouts
- Font Awesome icon integration
- Modern shadow and rounded border effects

## Installation Instructions

### 1. Prerequisites
- PHP 8.1 or higher
- MySQL 8.0 or higher
- Composer
- XAMPP/WAMP with PHP & MySQL

### 2. Environment Setup

```bash
# Navigate to project directory
cd C:\xampp\htdocs\RestaurantByob

# Set application name
APP_NAME="Restaurant BYOB"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurant_byob
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Database Setup

```bash
# Create database (if not exists)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS restaurant_byob;"

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed
```

### 4. Start Development Server

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### 5. Access Application

- **URL**: http://127.0.0.1:8000
- **Login Page**: http://127.0.0.1:8000/login
- **Dashboard**: http://127.0.0.1:8000/dashboard (after login)

## Demo Credentials

### Admin Account
```
Email: admin@restaurant.local
Password: password
Role: Admin - Full system access
```

### Manager Account
```
Email: manager@restaurant.local
Password: password
Role: Manager - All except POS
```

### Cashier Account
```
Email: cashier@restaurant.local
Password: password
Role: Cashier - POS only
```

## Project Structure

```
RestaurantByob/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php
│   │   │   └── DashboardController.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Module.php
│   │   └── Employee.php
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 2026_05_26_032222_create_roles_table.php
│   │   ├── 2026_05_26_032226_create_modules_table.php
│   │   ├── 2026_05_26_032227_create_employees_table.php
│   │   ├── 2026_05_26_032227_create_role_module_table.php
│   │   └── 2026_05_26_032333_add_role_to_users_table.php
│   └── seeders/
│       ├── RoleSeeder.php
│       ├── ModuleSeeder.php
│       ├── UserSeeder.php
│       └── DatabaseSeeder.php
│
├── resources/
│   └── views/
│       ├── auth/
│       │   └── login.blade.php
│       ├── dashboard/
│       │   └── index.blade.php
│       └── modules/
│           ├── customers.blade.php
│           ├── employees.blade.php
│           ├── inventory.blade.php
│           ├── suppliers.blade.php
│           ├── wastage.blade.php
│           ├── pos.blade.php
│           ├── reports.blade.php
│           └── settings.blade.php
│
├── routes/
│   └── web.php
│
├── config/
│   ├── app.php
│   ├── database.php
│   └── auth.php
│
└── .env (environment configuration)
```

## Database Schema

### Users Table
- Stores user authentication information
- Links to Role (many-to-one relationship)
- Links to Employee (one-to-one relationship)

### Roles Table
- Defines 3 roles: Admin, Manager, Cashier
- Many-to-many relationship with Modules

### Modules Table
- Defines 8 system modules
- Stores icon class and route name
- Many-to-many relationship with Roles

### Employees Table
- Extended employee information
- One-to-one relationship with Users

### Role-Module Table
- Junction table for many-to-many relationship
- Controls which modules each role can access

## API Routes

### Authentication Routes
```
GET    /login                 - Show login form
POST   /login                 - Process login
POST   /logout                - Logout user
```

### Dashboard Routes
```
GET    /dashboard             - Display dashboard (protected)
```

### Module Routes (Protected)
```
GET    /customers             - Customer Management
GET    /employees             - Employee Management
GET    /inventory             - Inventory & Products
GET    /suppliers             - Supplier Management
GET    /wastage               - Wastage Management
GET    /pos                   - POS & Billing
GET    /reports               - Reports
GET    /settings              - Settings
```

## Key Features

### Role-Based Access Control (RBAC)
- Each user is assigned a specific role
- Roles determine which modules are accessible
- Admin has unrestricted access
- Manager has almost full access
- Cashier has limited access to POS only

### Module System
- Modular architecture for easy feature addition
- Each module can have its own controller, views, and routes
- Modules are database-driven (can be enabled/disabled per role)

### Professional UI/UX
- Clean, modern design with red and white branding
- Responsive layout (mobile-first approach)
- Smooth animations and transitions
- Intuitive navigation
- Quick access to frequently used features

### Security Features
- Password hashing with bcrypt
- CSRF protection
- Session management
- SQL injection prevention (Laravel Eloquent ORM)
- XSS protection

## Customization Guide

### Adding New Users
```bash
php artisan tinker
User::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => bcrypt('password'),
    'role_id' => 1, // Admin
    'status' => 'active'
]);
```

### Adding New Modules
1. Create a new module record in the database
2. Create a controller in `app/Http/Controllers/`
3. Create views in `resources/views/modules/`
4. Add routes in `routes/web.php`
5. Link module to roles in `role_module` table

### Changing Colors
Edit the Tailwind classes in:
- `resources/views/auth/login.blade.php`
- `resources/views/dashboard/index.blade.php`

Color scheme used:
- Primary Red: `#dc2626` (red-600)
- Dark Red: `#991b1b` (red-900)
- Light Red: `#fecaca` (red-200)
- White: `#ffffff`

## Troubleshooting

### Database Connection Error
- Ensure MySQL is running
- Check database credentials in `.env`
- Run `php artisan migrate` to create tables

### Login Not Working
- Verify seeders were run: `php artisan db:seed`
- Check email addresses match demo credentials
- Ensure password is "password" (case-sensitive)

### Module Not Showing in Dashboard
- Check `role_module` table has entries for that role
- Verify module route exists in `routes/web.php`
- Ensure user's role has access in `role_module` table

### Views Not Found
- Clear cache: `php artisan view:clear`
- Clear config: `php artisan config:clear`
- Rebuild cache: `php artisan cache:clear`

## Next Steps

### For Module Development
1. Create database migrations for module-specific tables
2. Create Eloquent models
3. Create controllers with CRUD operations
4. Design views with consistent styling
5. Add routes

### For Feature Enhancement
1. Implement customer CRUD operations
2. Add inventory management features
3. Implement POS transaction processing
4. Create reporting system
5. Add user management interface

### For Production
1. Set `APP_DEBUG=false`
2. Generate secure `APP_KEY`
3. Configure proper database backups
4. Implement error logging
5. Set up SSL/HTTPS
6. Configure automated deployments

## Support & Maintenance

### Common Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Reset migrations
php artisan migrate:reset

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Create backup
mysqldump -u root restaurant_byob > backup.sql
```

## File Locations Summary

- **Login View**: `resources/views/auth/login.blade.php`
- **Dashboard**: `resources/views/dashboard/index.blade.php`
- **Controllers**: `app/Http/Controllers/`
- **Models**: `app/Models/`
- **Routes**: `routes/web.php`
- **Migrations**: `database/migrations/`
- **Seeders**: `database/seeders/`
- **Database Config**: `.env`

## Version Information

- **Laravel Version**: 11.x
- **PHP Version**: 8.1+
- **MySQL Version**: 8.0+
- **System Version**: 1.0.0
- **Setup Date**: May 26, 2026

## Additional Notes

- All demo accounts use the password "password"
- The system is configured for local development
- Replace demo users with real employees before production
- Consider implementing 2FA for admin accounts
- Regular database backups are recommended

## Contact & Documentation

For detailed information about specific features, refer to:
- `DB_SETUP.md` - Database structure and seeders
- `routes/web.php` - All available routes
- `database/seeders/` - Data population scripts

---

**Restaurant BYOB POS System - Ready for Development!**
