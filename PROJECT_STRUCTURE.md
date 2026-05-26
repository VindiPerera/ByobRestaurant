# Restaurant BYOB POS System - Complete Project Structure

## Directory Tree

```
C:\xampp\htdocs\RestaurantByob\
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php          [Authentication logic]
│   │   │   ├── DashboardController.php          [Dashboard logic]
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── TrustHosts.php
│   │   │   └── ...
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php                            [User model with role/employee relations]
│   │   ├── Role.php                            [Role model with users/modules relations]
│   │   ├── Module.php                          [Module model with roles relation]
│   │   ├── Employee.php                        [Employee model with user relation]
│   │   └── ...
│   ├── Providers/
│   │   └── ...
│   └── ...
│
├── bootstrap/
│   ├── app.php
│   ├── cache/
│   └── ...
│
├── config/
│   ├── app.php                                 [App name: "Restaurant BYOB"]
│   ├── auth.php                                [Auth guard configuration]
│   ├── database.php                            [DB connection: mysql]
│   ├── cache.php
│   ├── queue.php
│   └── ...
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_05_26_032222_create_roles_table.php
│   │   ├── 2026_05_26_032226_create_modules_table.php
│   │   ├── 2026_05_26_032226_create_permissions_table.php
│   │   ├── 2026_05_26_032227_create_employees_table.php
│   │   ├── 2026_05_26_032227_create_role_module_table.php
│   │   └── 2026_05_26_032333_add_role_to_users_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php                  [Main seeder - calls all seeders]
│   │   ├── RoleSeeder.php                      [Creates 3 roles]
│   │   ├── ModuleSeeder.php                    [Creates 8 modules]
│   │   └── UserSeeder.php                      [Creates 3 demo users]
│   └── database.sqlite
│
├── public/
│   ├── index.php                               [Application entry point]
│   ├── css/
│   ├── js/
│   └── ...
│
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   │   └── login.blade.php                 [Login form - red/white theme]
│   │   ├── dashboard/
│   │   │   └── index.blade.php                 [Main dashboard]
│   │   ├── modules/
│   │   │   ├── customers.blade.php             [Customer module placeholder]
│   │   │   ├── employees.blade.php             [Employee module placeholder]
│   │   │   ├── inventory.blade.php             [Inventory module placeholder]
│   │   │   ├── suppliers.blade.php             [Supplier module placeholder]
│   │   │   ├── wastage.blade.php               [Wastage module placeholder]
│   │   │   ├── pos.blade.php                   [POS module placeholder]
│   │   │   ├── reports.blade.php               [Reports module placeholder]
│   │   │   └── settings.blade.php              [Settings module placeholder]
│   │   ├── welcome.blade.php                   [Default welcome page]
│   │   └── ...
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── ...
│
├── routes/
│   ├── web.php                                 [All web routes - 16 routes]
│   ├── api.php                                 [API routes (empty)]
│   ├── console.php
│   └── channels.php
│
├── storage/
│   ├── app/
│   ├── framework/
│   ├── logs/
│   └── ...
│
├── tests/
│   ├── Feature/
│   ├── Unit/
│   └── ...
│
├── vendor/                                     [Composer dependencies]
│   └── ...
│
├── .env                                        [Environment configuration]
├── .env.example
├── .gitignore
├── .editorconfig
├── artisan                                     [Laravel CLI]
├── composer.json                               [Composer config]
├── composer.lock
├── package.json
├── vite.config.js
│
├── README_SETUP.md                             [Complete setup guide]
├── DB_SETUP.md                                 [Database reference]
├── SEEDER_DATA.md                              [Demo data details]
├── QUICKSTART.md                               [5-minute start guide]
├── PROJECT_STRUCTURE.md                        [This file]
└── LICENSE
```

## Key Files Description

### Controllers
```
app/Http/Controllers/
├── Auth/LoginController.php
│   ├── show()       → Display login form
│   ├── store()      → Process login
│   └── logout()     → Logout user
│
└── DashboardController.php
    └── index()      → Display dashboard with modules
```

### Models
```
app/Models/
├── User
│   ├── relationships: role(), employee()
│   ├── attributes: name, email, password, role_id, status
│   └── timestamps: created_at, updated_at
│
├── Role
│   ├── relationships: users(), modules()
│   ├── attributes: name, description
│   └── timestamps: created_at, updated_at
│
├── Module
│   ├── relationships: roles()
│   ├── attributes: name, description, icon, route
│   └── timestamps: created_at, updated_at
│
└── Employee
    ├── relationships: user()
    ├── attributes: user_id, phone, address, city, state, postal_code, hire_date, salary
    └── timestamps: created_at, updated_at
```

### Views
```
resources/views/
├── auth/
│   └── login.blade.php              [Professional login form]
│
├── dashboard/
│   └── index.blade.php              [Main dashboard - role-based]
│
├── modules/
│   ├── customers.blade.php
│   ├── employees.blade.php
│   ├── inventory.blade.php
│   ├── suppliers.blade.php
│   ├── wastage.blade.php
│   ├── pos.blade.php
│   ├── reports.blade.php
│   └── settings.blade.php
│
└── [Laravel default views]
```

### Routes
```
routes/web.php - 16 Total Routes

Authentication Routes:
├── GET  /login              → LoginController@show
├── POST /login              → LoginController@store
└── POST /logout             → LoginController@logout

Dashboard Route:
└── GET  /dashboard          → DashboardController@index

Module Routes (Protected):
├── GET  /customers          → modules.customers
├── GET  /employees          → modules.employees
├── GET  /inventory          → modules.inventory
├── GET  /suppliers          → modules.suppliers
├── GET  /wastage            → modules.wastage
├── GET  /pos                → modules.pos
├── GET  /reports            → modules.reports
└── GET  /settings           → modules.settings

Root Route:
└── GET  /                   → Redirect to dashboard
```

### Database Migrations
```
database/migrations/

Core Laravel Tables:
├── 0001_01_01_000000_create_users_table          [users table]
├── 0001_01_01_000001_create_cache_table          [cache table]
└── 0001_01_01_000002_create_jobs_table           [jobs table]

Custom Tables:
├── 2026_05_26_032222_create_roles_table          [roles table]
├── 2026_05_26_032226_create_modules_table        [modules table]
├── 2026_05_26_032226_create_permissions_table    [permissions table]
├── 2026_05_26_032227_create_employees_table      [employees table]
├── 2026_05_26_032227_create_role_module_table    [junction table]
└── 2026_05_26_032333_add_role_to_users_table     [add role_id to users]
```

### Seeders
```
database/seeders/

├── DatabaseSeeder.php           [Main entry - calls all seeders]
│   └── Calls: RoleSeeder → ModuleSeeder → UserSeeder
│
├── RoleSeeder.php               [Creates 3 roles]
│   ├── Admin
│   ├── Manager
│   └── Cashier
│
├── ModuleSeeder.php             [Creates 8 modules]
│   ├── Creates all 8 modules
│   ├── Links Admin to all modules
│   ├── Links Manager to 7 modules
│   └── Links Cashier to POS only
│
└── UserSeeder.php               [Creates 3 demo users]
    ├── admin@restaurant.local   (Admin role)
    ├── manager@restaurant.local (Manager role)
    └── cashier@restaurant.local (Cashier role)
```

### Configuration Files
```
config/
├── app.php
│   └── name: "Restaurant BYOB"
│
├── auth.php
│   └── guard: "web", provider: "users"
│
├── database.php
│   └── default: "mysql"
│   └── host: 127.0.0.1
│   └── database: restaurant_byob
│   └── username: root
│
└── [Other Laravel config files]
```

### Environment File
```
.env
├── APP_NAME=Restaurant BYOB
├── APP_ENV=local
├── APP_DEBUG=true
├── DB_CONNECTION=mysql
├── DB_HOST=127.0.0.1
├── DB_PORT=3306
├── DB_DATABASE=restaurant_byob
├── DB_USERNAME=root
├── DB_PASSWORD=
└── [Other environment variables]
```

## Database Structure Diagram

```
┌─────────────────────────────────────────────────────────┐
│                     DATABASE SCHEMA                      │
└─────────────────────────────────────────────────────────┘

┌──────────────┐         ┌──────────────┐
│    USERS     │         │    ROLES     │
├──────────────┤         ├──────────────┤
│ id (PK)      │────┐    │ id (PK)      │
│ name         │    │    │ name         │────┐
│ email        │    │    │ description  │    │
│ password     │    ├───>│              │    │
│ role_id (FK) │    │    │ created_at   │    │
│ status       │    │    │ updated_at   │    │
│ created_at   │    │    └──────────────┘    │
│ updated_at   │    │                        │
└──────────────┘    │    ┌──────────────┐    │
       │            │    │   MODULES    │    │
       │            │    ├──────────────┤    │
       │            │    │ id (PK)      │    │
       │            │    │ name         │    │
       │            │    │ description  │    │
       │            │    │ icon         │    │
       │            │    │ route        │    │
       │            │    │ created_at   │    │
       │            │    │ updated_at   │    │
       │            │    └──────────────┘    │
       │            │           ▲            │
       │            │           │            │
       │            │    ┌──────────────────┐│
       │            │    │  ROLE_MODULE     ││
       │            │    ├──────────────────┤│
       │            │    │ id (PK)          ││
       │            │    │ role_id (FK)─────┘│
       │            │    │ module_id (FK)───┘
       │            │    │ created_at       │
       │            │    │ updated_at       │
       │            │    └──────────────────┘
       │            │
       │    ┌──────────────┐
       │    │  EMPLOYEES   │
       │    ├──────────────┤
       │    │ id (PK)      │
       │    │ user_id (FK) │<────┘
       │    │ phone        │
       │    │ address      │
       │    │ city         │
       │    │ state        │
       │    │ postal_code  │
       │    │ hire_date    │
       │    │ salary       │
       │    │ created_at   │
       │    │ updated_at   │
       │    └──────────────┘
       │
       └────────────────────────────────────┐
                                            │
                           Many-to-One Relationship
                                            ▼
```

## File Count Summary

| Category | Count |
|----------|-------|
| Controllers | 2 |
| Models | 4 |
| Migrations | 9 |
| Seeders | 3 |
| Views | 10+ |
| Routes | 16 |
| Documentation Files | 4 |
| Total Tables | 8 |
| Total Modules | 8 |
| Demo Users | 3 |

## Important Paths

```
Project Root:  C:\xampp\htdocs\RestaurantByob\
Database:      restaurant_byob (MySQL)
.env:          C:\xampp\htdocs\RestaurantByob\.env
Migrations:    C:\xampp\htdocs\RestaurantByob\database\migrations\
Seeders:       C:\xampp\htdocs\RestaurantByob\database\seeders\
Views:         C:\xampp\htdocs\RestaurantByob\resources\views\
Controllers:   C:\xampp\htdocs\RestaurantByob\app\Http\Controllers\
Models:        C:\xampp\htdocs\RestaurantByob\app\Models\
Routes:        C:\xampp\htdocs\RestaurantByob\routes\web.php
Config:        C:\xampp\htdocs\RestaurantByob\config\
```

## Quick Command Reference

```bash
# Navigate to project
cd C:\xampp\htdocs\RestaurantByob

# Start development server
php artisan serve --host=127.0.0.1 --port=8000

# Create migrations
php artisan make:migration create_table_name

# Create controllers
php artisan make:controller ControllerName

# Create models
php artisan make:model ModelName -m

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Fresh start
php artisan migrate:fresh --seed
```

---

This structure provides a solid foundation for building a complete restaurant management POS system with Laravel.
