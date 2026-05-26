# Restaurant BYOB POS System - Database Setup

## Overview
This document describes the database structure, seeders, and user roles for the Restaurant BYOB POS system.

## Database Configuration
- **Database Name**: `restaurant_byob`
- **Database Type**: MySQL
- **Host**: 127.0.0.1
- **Port**: 3306
- **Username**: root
- **Password**: (empty)

## Database Tables

### 1. users
Stores user authentication and basic information
- `id` - Primary key
- `name` - User's full name
- `email` - User's email (unique)
- `password` - Hashed password
- `role_id` - Foreign key to roles table
- `status` - Active/Inactive status
- `email_verified_at` - Email verification timestamp
- `remember_token` - Remember me token
- `created_at`, `updated_at` - Timestamps

### 2. roles
Defines user roles and permissions
- `id` - Primary key
- `name` - Role name (Admin, Manager, Cashier)
- `description` - Role description
- `created_at`, `updated_at` - Timestamps

### 3. modules
Defines system modules available in the application
- `id` - Primary key
- `name` - Module name
- `description` - Module description
- `icon` - Font Awesome icon class
- `route` - Route name for the module
- `created_at`, `updated_at` - Timestamps

### 4. role_module
Junction table for role-module relationship (many-to-many)
- `id` - Primary key
- `role_id` - Foreign key to roles table
- `module_id` - Foreign key to modules table
- `created_at`, `updated_at` - Timestamps

### 5. employees
Extended employee information
- `id` - Primary key
- `user_id` - Foreign key to users table (unique)
- `phone` - Employee phone number
- `address` - Employee address
- `city` - City
- `state` - State
- `postal_code` - Postal code
- `hire_date` - Date hired
- `salary` - Salary amount
- `created_at`, `updated_at` - Timestamps

## Roles and Permissions

### Admin Role
**Description**: Administrator - Full system access
**Available Modules**:
- Customer Management
- Employee Management
- Inventory & Products
- Supplier Management
- Wastage Management
- POS & Billing
- Reports
- Settings

### Manager Role
**Description**: Manager - Can manage inventory, employees, and reports
**Available Modules**:
- Customer Management
- Employee Management
- Inventory & Products
- Supplier Management
- Reports
- Settings

### Cashier Role
**Description**: Cashier - Can process POS transactions
**Available Modules**:
- POS & Billing

## Module Definitions

| Module Name | Icon | Description | Route |
|-------------|------|-------------|-------|
| Customer Management | users | Manage customers and customer information | customers.index |
| Employee Management | user-tie | Manage employees and staff | employees.index |
| Inventory & Products | boxes | Manage products and inventory | inventory.index |
| Supplier Management | truck | Manage suppliers and purchases | suppliers.index |
| Wastage Management | trash-alt | Track product wastage and losses | wastage.index |
| POS & Billing | cash-register | Point of sale and billing management | pos.index |
| Reports | chart-bar | Generate and view reports | reports.index |
| Settings | cog | System settings and configuration | settings.index |

## Demo User Credentials

### Admin User
- **Email**: admin@restaurant.local
- **Password**: password
- **Role**: Admin
- **Phone**: 555-0001
- **Hire Date**: 12 months ago

### Manager User
- **Email**: manager@restaurant.local
- **Password**: password
- **Role**: Manager
- **Phone**: 555-0002
- **Hire Date**: 6 months ago

### Cashier User
- **Email**: cashier@restaurant.local
- **Password**: password
- **Role**: Cashier
- **Phone**: 555-0003
- **Hire Date**: 3 months ago

## Seeders

### RoleSeeder
Creates three roles in the database:
- Admin
- Manager
- Cashier

### ModuleSeeder
Creates eight modules and assigns them to roles:
- Admin has access to all modules
- Manager has access to all modules except POS & Billing
- Cashier has access only to POS & Billing

### UserSeeder
Creates three test users (one for each role) with employee information

## Running Seeders

### Run All Seeders
```bash
php artisan db:seed
```

### Run Specific Seeder
```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=ModuleSeeder
php artisan db:seed --class=UserSeeder
```

## Database Relationships

```
Users
├── has One Role (1:1)
├── has One Employee (1:1)
└── has Many Sessions (1:N)

Roles
├── has Many Users (1:N)
└── belongs to Many Modules (M:M via role_module)

Modules
├── belongs to Many Roles (M:M via role_module)
└── Stored with icon and route information

Employees
└── belongs to User (M:1)
    └── contains extended employee information
```

## Migration Files

The following migrations were created and executed in order:

1. `0001_01_01_000000_create_users_table` - Creates users table
2. `0001_01_01_000001_create_cache_table` - Creates cache table
3. `0001_01_01_000002_create_jobs_table` - Creates jobs table
4. `2026_05_26_032222_create_roles_table` - Creates roles table
5. `2026_05_26_032226_create_modules_table` - Creates modules table
6. `2026_05_26_032226_create_permissions_table` - Creates permissions table
7. `2026_05_26_032227_create_employees_table` - Creates employees table
8. `2026_05_26_032227_create_role_module_table` - Creates role_module junction table
9. `2026_05_26_032333_add_role_to_users_table` - Adds role_id foreign key to users table

## Module Features to Implement

### Customer Management Module
- Customer registration and profile management
- Customer contact information
- Purchase history tracking
- Loyalty program integration

### Employee Management Module
- Employee profile management
- Salary and compensation tracking
- Schedule management
- Performance tracking

### Inventory & Product Management Module
- Product catalog management
- Stock level tracking
- Product categories
- Low stock alerts

### Supplier Management Module
- Supplier information management
- Purchase order management
- Supplier ratings and reviews
- Payment tracking

### Wastage Management Module
- Track product wastage
- Wastage categories
- Cost analysis
- Reporting on waste reduction

### POS & Billing Management Module
- Order management
- Payment processing
- Receipt generation
- Sales tracking

### Reports Module
- Sales reports
- Inventory reports
- Employee performance reports
- Financial reports

### Settings Module
- System configuration
- User management
- Role management
- Application preferences
