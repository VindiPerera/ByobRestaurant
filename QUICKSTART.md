# Restaurant BYOB POS System - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Database Setup (30 seconds)

```bash
# Create the database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS restaurant_byob;"

# Navigate to project
cd C:\xampp\htdocs\RestaurantByob

# Run migrations
php artisan migrate

# Seed demo data
php artisan db:seed
```

### Step 2: Start Development Server (10 seconds)

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

### Step 3: Open in Browser (5 seconds)

Visit: **http://127.0.0.1:8000/login**

## 📝 Demo Login Credentials

### Option 1: Admin Access
```
Email:    admin@restaurant.local
Password: password
Role:     Administrator (Full Access)
```

### Option 2: Manager Access
```
Email:    manager@restaurant.local
Password: password
Role:     Manager (Most Modules)
```

### Option 3: Cashier Access
```
Email:    cashier@restaurant.local
Password: password
Role:     Cashier (POS Only)
```

## 🎯 What's Included

✅ **Database Schema**
- 5 main tables (users, roles, modules, employees, role_module)
- Fully normalized structure
- Foreign key relationships

✅ **Authentication System**
- Login page with red/white theme
- Role-based access control
- Secure password hashing
- Session management

✅ **Dashboard**
- Welcome message
- Quick stats cards
- Module cards with icons
- Responsive design

✅ **Admin Panel**
- Role management
- Module management
- User management
- Employee information

✅ **8 Modules Ready for Development**
1. Customer Management
2. Employee Management
3. Inventory & Products
4. Supplier Management
5. Wastage Management
6. POS & Billing
7. Reports
8. Settings

## 🗂️ Key Files

| File | Purpose |
|------|---------|
| `.env` | Environment configuration (database, app settings) |
| `routes/web.php` | All application routes |
| `app/Models/User.php` | User model with relationships |
| `app/Models/Role.php` | Role model |
| `app/Models/Module.php` | Module model |
| `app/Http/Controllers/DashboardController.php` | Dashboard logic |
| `resources/views/auth/login.blade.php` | Login page UI |
| `resources/views/dashboard/index.blade.php` | Dashboard UI |
| `database/seeders/*.php` | Demo data seeders |

## 🔗 Navigation

### From Login Page
- Click **Log in** button after entering credentials
- Demo credentials provided above

### From Dashboard
- Click module cards to open that module
- Click **Logout** in top-right menu
- Click username to access user menu

### From Modules
- Click back arrow or logo to return to dashboard

## 📊 Database Structure Quick View

```
Users
├── name
├── email
├── password (hashed)
├── role_id (links to Roles)
└── status (active/inactive)

Roles
├── name (Admin, Manager, Cashier)
├── description
└── modules (many-to-many via role_module)

Modules
├── name
├── description
├── icon (Font Awesome class)
├── route (Laravel route name)
└── roles (many-to-many via role_module)

Employees
├── user_id (links to Users)
├── phone
├── address
├── city, state, postal_code
├── hire_date
└── salary

Role-Module (Junction)
├── role_id (links to Roles)
└── module_id (links to Modules)
```

## ⚙️ Common Commands

```bash
# Start development server
php artisan serve --host=127.0.0.1 --port=8000

# Create new migration
php artisan make:migration create_tablename_table

# Create new model
php artisan make:model ModelName -m

# Create new controller
php artisan make:controller ControllerName

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Reset all data
php artisan migrate:fresh --seed

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## 🎨 Customization Tips

### Change Application Name
Edit `.env`:
```
APP_NAME="Your Restaurant Name"
```

### Change Colors
Edit Tailwind classes in:
- `resources/views/auth/login.blade.php`
- `resources/views/dashboard/index.blade.php`

Primary colors:
- `#dc2626` (Red-600)
- `#991b1b` (Red-900)
- `#ffffff` (White)

### Add New Module

1. **Create Route** in `routes/web.php`:
```php
Route::get('/newmodule', [NewModuleController::class, 'index'])
    ->name('newmodule.index');
```

2. **Create Controller**:
```bash
php artisan make:controller NewModuleController
```

3. **Create View** in `resources/views/modules/newmodule.blade.php`

4. **Add to Database**:
```sql
INSERT INTO modules (name, description, icon, route) 
VALUES ('Module Name', 'Description', 'icon-name', 'newmodule.index');
```

5. **Link to Roles** in `role_module` table

## 🐛 Troubleshooting

### "Access Denied for database"
```bash
# Check database is created
mysql -u root -e "SHOW DATABASES;" | grep restaurant

# Fix connection in .env file
DB_HOST=127.0.0.1
DB_USERNAME=root
DB_PASSWORD=
```

### "Class LoginController not found"
```bash
# Clear autoload cache
composer dumpautoload

# Rebuild cache
php artisan cache:clear
```

### "Table not found"
```bash
# Run migrations again
php artisan migrate

# Or fresh start
php artisan migrate:fresh --seed
```

### Login shows blank page
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Try again
php artisan serve
```

## 📚 Documentation Files

- **README_SETUP.md** - Complete setup guide
- **DB_SETUP.md** - Database schema reference
- **SEEDER_DATA.md** - Demo data details
- **QUICKSTART.md** - This file!

## 🔐 Security Checklist

- [ ] Change all demo user passwords
- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL
- [ ] Keep Laravel updated
- [ ] Regular database backups
- [ ] Implement 2FA for admin
- [ ] Review user permissions
- [ ] Audit access logs

## 📱 Responsive Design

The dashboard is optimized for:
- ✓ Desktop (1920px+)
- ✓ Laptop (1024px+)
- ✓ Tablet (768px+)
- ✓ Mobile (320px+)

## ⏰ Performance Tips

1. Use caching for frequently accessed modules
2. Implement pagination for large datasets
3. Optimize database queries with eager loading
4. Minify CSS/JS in production
5. Use CDN for static assets

## 🎓 Next Steps

1. **Login**: Try all three demo accounts
2. **Explore**: Navigate through the dashboard
3. **Customize**: Change colors and branding
4. **Develop**: Start building module features
5. **Deploy**: Move to production

## 📞 Support Resources

- Laravel Docs: https://laravel.com/docs
- Tailwind CSS: https://tailwindcss.com
- MySQL: https://dev.mysql.com
- Font Awesome: https://fontawesome.com

## ✨ What's Next

1. **Customer Module**: Build customer CRUD
2. **Inventory Module**: Product management
3. **POS Module**: Transaction processing
4. **Reports Module**: Analytics
5. **API**: Build REST API for mobile app

## 📝 Version Info

- System: Restaurant BYOB POS v1.0.0
- Framework: Laravel 11.x
- Database: MySQL 8.0+
- PHP: 8.1+
- Release Date: May 26, 2026

---

**Ready to go! Happy coding! 🚀**

For detailed information, see:
- README_SETUP.md for full setup
- DB_SETUP.md for database details
- SEEDER_DATA.md for data reference
