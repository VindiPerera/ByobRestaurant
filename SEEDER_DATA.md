# Restaurant BYOB POS - Seeder Data Reference

## Overview
This document provides a complete reference of all data created by the database seeders.

## Roles Data

### Created Roles

```
ID | Name    | Description
---|---------|-------------------------------------
1  | Admin   | Administrator - Full system access
2  | Manager | Manager - Can manage inventory, employees, and reports
3  | Cashier | Cashier - Can process POS transactions
```

## Modules Data

### Created Modules

```
ID | Name                | Description                              | Icon            | Route
---|---------------------|------------------------------------------|-----------------|--------------------
1  | Customer Management | Manage customers and customer info      | users           | customers.index
2  | Employee Management | Manage employees and staff              | user-tie        | employees.index
3  | Inventory & Products| Manage products and inventory           | boxes           | inventory.index
4  | Supplier Management | Manage suppliers and purchases          | truck           | suppliers.index
5  | Wastage Management  | Track product wastage and losses        | trash-alt       | wastage.index
6  | POS & Billing       | Point of sale and billing management    | cash-register   | pos.index
7  | Reports             | Generate and view reports               | chart-bar       | reports.index
8  | Settings            | System settings and configuration       | cog             | settings.index
```

## Role-Module Access Matrix

### Admin Role Access
```
Module                  | Access
------------------------|--------
Customer Management     | ✓
Employee Management     | ✓
Inventory & Products    | ✓
Supplier Management     | ✓
Wastage Management      | ✓
POS & Billing          | ✓
Reports                | ✓
Settings               | ✓
```

### Manager Role Access
```
Module                  | Access
------------------------|--------
Customer Management     | ✓
Employee Management     | ✓
Inventory & Products    | ✓
Supplier Management     | ✓
Wastage Management      | ✓
POS & Billing          | ✗
Reports                | ✓
Settings               | ✓
```

### Cashier Role Access
```
Module                  | Access
------------------------|--------
Customer Management     | ✗
Employee Management     | ✗
Inventory & Products    | ✗
Supplier Management     | ✗
Wastage Management      | ✗
POS & Billing          | ✓
Reports                | ✗
Settings               | ✗
```

## Users Data

### Admin User
```
Field              | Value
-------------------|---------------------------
ID                 | 1
Name               | Admin User
Email              | admin@restaurant.local
Password           | password (hashed with bcrypt)
Role               | Admin (ID: 1)
Status             | active
Employee Phone     | 555-0001
Employee Address   | 123 Admin Street
Employee City      | Restaurant City
Employee State     | RC
Employee Postal    | 12345
Hire Date          | 12 months ago from today
Salary             | $5000.00 monthly
Account Created    | Today's date
```

### Manager User
```
Field              | Value
-------------------|---------------------------
ID                 | 2
Name               | Manager User
Email              | manager@restaurant.local
Password           | password (hashed with bcrypt)
Role               | Manager (ID: 2)
Status             | active
Employee Phone     | 555-0002
Employee Address   | 456 Manager Avenue
Employee City      | Restaurant City
Employee State     | RC
Employee Postal    | 12345
Hire Date          | 6 months ago from today
Salary             | $3500.00 monthly
Account Created    | Today's date
```

### Cashier User
```
Field              | Value
-------------------|---------------------------
ID                 | 3
Name               | Cashier User
Email              | cashier@restaurant.local
Password           | password (hashed with bcrypt)
Role               | Cashier (ID: 3)
Status             | active
Employee Phone     | 555-0003
Employee Address   | 789 Cashier Road
Employee City      | Restaurant City
Employee State     | RC
Employee Postal    | 12345
Hire Date          | 3 months ago from today
Salary             | $2500.00 monthly
Account Created    | Today's date
```

## SQL Data Representation

### Roles Table (INSERT statements)

```sql
INSERT INTO `roles` (`name`, `description`, `created_at`, `updated_at`) VALUES
('Admin', 'Administrator - Full system access', NOW(), NOW()),
('Manager', 'Manager - Can manage inventory, employees, and reports', NOW(), NOW()),
('Cashier', 'Cashier - Can process POS transactions', NOW(), NOW());
```

### Modules Table (INSERT statements)

```sql
INSERT INTO `modules` (`name`, `description`, `icon`, `route`, `created_at`, `updated_at`) VALUES
('Customer Management', 'Manage customers and customer information', 'users', 'customers.index', NOW(), NOW()),
('Employee Management', 'Manage employees and staff', 'user-tie', 'employees.index', NOW(), NOW()),
('Inventory & Products', 'Manage products and inventory', 'boxes', 'inventory.index', NOW(), NOW()),
('Supplier Management', 'Manage suppliers and purchases', 'truck', 'suppliers.index', NOW(), NOW()),
('Wastage Management', 'Track product wastage and losses', 'trash-alt', 'wastage.index', NOW(), NOW()),
('POS & Billing', 'Point of sale and billing management', 'cash-register', 'pos.index', NOW(), NOW()),
('Reports', 'Generate and view reports', 'chart-bar', 'reports.index', NOW(), NOW()),
('Settings', 'System settings and configuration', 'cog', 'settings.index', NOW(), NOW());
```

### Users Table (INSERT statements)

```sql
INSERT INTO `users` (`name`, `email`, `password`, `role_id`, `status`, `created_at`, `updated_at`) VALUES
('Admin User', 'admin@restaurant.local', '$2y$12$...hashed_password...', 1, 'active', NOW(), NOW()),
('Manager User', 'manager@restaurant.local', '$2y$12$...hashed_password...', 2, 'active', NOW(), NOW()),
('Cashier User', 'cashier@restaurant.local', '$2y$12$...hashed_password...', 3, 'active', NOW(), NOW());
```

### Employees Table (INSERT statements)

```sql
INSERT INTO `employees` (`user_id`, `phone`, `address`, `city`, `state`, `postal_code`, `hire_date`, `salary`, `created_at`, `updated_at`) VALUES
(1, '555-0001', '123 Admin Street', 'Restaurant City', 'RC', '12345', DATE_SUB(CURDATE(), INTERVAL 12 MONTH), 5000.00, NOW(), NOW()),
(2, '555-0002', '456 Manager Avenue', 'Restaurant City', 'RC', '12345', DATE_SUB(CURDATE(), INTERVAL 6 MONTH), 3500.00, NOW(), NOW()),
(3, '555-0003', '789 Cashier Road', 'Restaurant City', 'RC', '12345', DATE_SUB(CURDATE(), INTERVAL 3 MONTH), 2500.00, NOW(), NOW());
```

### Role-Module Table (INSERT statements)

```sql
INSERT INTO `role_module` (`role_id`, `module_id`, `created_at`, `updated_at`) VALUES
-- Admin (Role 1) has access to all modules
(1, 1, NOW(), NOW()),
(1, 2, NOW(), NOW()),
(1, 3, NOW(), NOW()),
(1, 4, NOW(), NOW()),
(1, 5, NOW(), NOW()),
(1, 6, NOW(), NOW()),
(1, 7, NOW(), NOW()),
(1, 8, NOW(), NOW()),

-- Manager (Role 2) has access to all except POS
(2, 1, NOW(), NOW()),
(2, 2, NOW(), NOW()),
(2, 3, NOW(), NOW()),
(2, 4, NOW(), NOW()),
(2, 5, NOW(), NOW()),
(2, 7, NOW(), NOW()),
(2, 8, NOW(), NOW()),

-- Cashier (Role 3) only has access to POS
(3, 6, NOW(), NOW());
```

## Seeder Execution Order

The seeders are executed in this order by `DatabaseSeeder.php`:

1. **RoleSeeder** - Creates the 3 roles
2. **ModuleSeeder** - Creates the 8 modules and sets up role-module relationships
3. **UserSeeder** - Creates the 3 test users and their employee records

## Verification Queries

### Verify Roles were created
```sql
SELECT * FROM roles;
-- Should return 3 rows
```

### Verify Modules were created
```sql
SELECT * FROM modules;
-- Should return 8 rows
```

### Verify Users were created
```sql
SELECT u.id, u.name, u.email, r.name as role FROM users u 
JOIN roles r ON u.role_id = r.id;
-- Should return 3 rows with correct role assignments
```

### Verify Employee Data
```sql
SELECT u.name, e.phone, e.hire_date, e.salary 
FROM employees e 
JOIN users u ON e.user_id = u.id;
-- Should return 3 rows with correct employee information
```

### Verify Role-Module Assignments
```sql
SELECT r.name as role, COUNT(m.id) as module_count 
FROM roles r 
LEFT JOIN role_module rm ON r.id = rm.role_id 
LEFT JOIN modules m ON rm.module_id = m.id 
GROUP BY r.id, r.name;
-- Should return:
-- Admin: 8 modules
-- Manager: 7 modules
-- Cashier: 1 module
```

### Get all modules for a specific role (e.g., Cashier)
```sql
SELECT DISTINCT m.* FROM modules m
JOIN role_module rm ON m.id = rm.module_id
JOIN roles r ON rm.role_id = r.id
WHERE r.name = 'Cashier'
ORDER BY m.id;
-- Should return 1 row: POS & Billing
```

## Data Statistics

### Total Counts
- Roles: 3
- Modules: 8
- Users: 3
- Employees: 3
- Role-Module Relationships: 16

### Role Distribution
- Admin: 1 user
- Manager: 1 user
- Cashier: 1 user

### Module Accessibility
- All Modules (8): Admin
- 7 Modules: Manager
- 1 Module: Cashier

## Notes for Development

1. **Password Hashing**: All demo user passwords are "password" but stored as bcrypt hashes
2. **Email Addresses**: Use `.local` domain for demo accounts (easily identifiable)
3. **Hire Dates**: Set relative to current date for realistic data
4. **Phone Numbers**: Follow 555-XXXX format (reserved for fictional use)
5. **Salary**: Reflects role hierarchy (Admin > Manager > Cashier)

## Resetting Data

To reset all data and re-seed:

```bash
# Option 1: Fresh migration with seed
php artisan migrate:fresh --seed

# Option 2: Reset migrations then seed
php artisan migrate:reset
php artisan migrate
php artisan db:seed

# Option 3: Run just the seeders on existing database
php artisan db:seed
```

## Important Security Notes

- Demo accounts should NEVER be used in production
- Change all demo user passwords immediately before going live
- Consider using different email domains for production
- Implement proper authentication (2FA, OAuth, etc.) for production
- Regularly rotate access credentials
- Keep employee salary data confidential and secure

---

**Seeder Data Reference - Complete Database Population Guide**
