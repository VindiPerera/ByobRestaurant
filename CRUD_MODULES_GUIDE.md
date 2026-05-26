# CRUD Modules Implementation Guide

## Overview
This document provides a complete guide to the three new CRUD modules: Customer Management, Products & Inventory, and Wastage Management.

---

## 1. Customer Management Module

### Features
- **Create**: Add new customers with name, phone number, and optional address
- **Read**: View all customers in a paginated table
- **Update**: Edit customer information
- **Delete**: Remove customer records
- **Status**: Track customer status (active/inactive)

### Database Schema
**Table**: `customers`
```
- id: bigint (primary key)
- name: string (255)
- phone_number: string (20)
- address: text (nullable)
- status: enum ('active', 'inactive') - default: 'active'
- created_at: timestamp
- updated_at: timestamp
```

### Routes
```
GET    /customers              → CustomerController@index      (List all customers)
GET    /customers/create       → CustomerController@create     (Show create form)
POST   /customers              → CustomerController@store      (Save new customer)
GET    /customers/{id}/edit    → CustomerController@edit       (Show edit form)
PUT    /customers/{id}         → CustomerController@update     (Update customer)
DELETE /customers/{id}         → CustomerController@destroy    (Delete customer)
```

### Views
- `customers-list.blade.php` - Display all customers in table format with pagination
- `customers-create.blade.php` - Form to create new customer
- `customers-edit.blade.php` - Form to edit existing customer

### Model
**File**: `app/Models/Customer.php`
- Fillable: name, phone_number, address, status
- Timestamps: created_at, updated_at

### Controller
**File**: `app/Http/Controllers/CustomerController.php`
- Complete CRUD operations
- Form validation
- Error handling
- Flash messages for success

---

## 2. Products & Inventory Module

### Features
- **Create**: Add new products with name, description, price, and quantity
- **Read**: View all products with pricing and stock information
- **Update**: Edit product details and pricing
- **Delete**: Remove product records
- **Stock Management**: Track product quantities

### Database Schema
**Table**: `products`
```
- id: bigint (primary key)
- name: string (255)
- description: text (nullable)
- price: decimal (8,2)
- quantity: integer
- status: enum ('active', 'inactive') - default: 'active'
- created_at: timestamp
- updated_at: timestamp
```

### Routes
```
GET    /products              → ProductController@index      (List all products)
GET    /products/create       → ProductController@create     (Show create form)
POST   /products              → ProductController@store      (Save new product)
GET    /inventory             → ProductController@index      (Alias for products list)
GET    /products/{id}/edit    → ProductController@edit       (Show edit form)
PUT    /products/{id}         → ProductController@update     (Update product)
DELETE /products/{id}         → ProductController@destroy    (Delete product)
```

### Views
- `products-list.blade.php` - Display all products in table with pricing
- `products-create.blade.php` - Form to create new product
- `products-edit.blade.php` - Form to edit existing product

### Model
**File**: `app/Models/Product.php`
- Fillable: name, description, price, quantity, status
- Relationships: hasMany(Wastage)
- Price casting as decimal(2)

### Controller
**File**: `app/Http/Controllers/ProductController.php`
- Full CRUD operations
- Input validation (price must be numeric, quantity must be integer)
- Relationship with wastage records
- Quantity tracking

---

## 3. Wastage Management Module

### Features
- **Create**: Record product wastage with reason and date
- **Read**: View all wastage records with product details
- **Update**: Edit wastage information
- **Delete**: Remove wastage records
- **Automatic Stock Deduction**: Automatically reduces product quantity when wastage is recorded
- **Stock Recovery**: Returns quantity to stock when wastage is deleted

### Database Schema
**Table**: `wastages`
```
- id: bigint (primary key)
- product_id: bigint (foreign key → products.id)
- quantity: integer
- reason: string (255)
- notes: text (nullable)
- date: date
- created_at: timestamp
- updated_at: timestamp
```

### Routes
```
GET    /wastage              → WastageController@index      (List all wastage)
GET    /wastages/create      → WastageController@create     (Show create form)
POST   /wastages             → WastageController@store      (Record wastage)
GET    /wastages/{id}/edit   → WastageController@edit       (Show edit form)
PUT    /wastages/{id}        → WastageController@update     (Update wastage)
DELETE /wastages/{id}        → WastageController@destroy    (Delete wastage)
```

### Views
- `wastages-list.blade.php` - Display all wastage records
- `wastages-create.blade.php` - Form to record new wastage
- `wastages-edit.blade.php` - Form to edit wastage record

### Model
**File**: `app/Models/Wastage.php`
- Fillable: product_id, quantity, reason, notes, date
- Relationships: belongsTo(Product)
- Date casting

### Controller
**File**: `app/Http/Controllers/WastageController.php`
- CRUD operations with intelligent quantity management
- Automatic stock deduction when recording wastage
- Quantity validation (cannot waste more than available)
- Handles product changes in edit
- Returns stock when wastage is deleted

### Key Logic
1. **Store**: Validates quantity <= product.quantity, then decrements product stock
2. **Update**: Adjusts previous wastage quantity, handles product changes
3. **Destroy**: Returns wasted quantity back to product stock

---

## File Structure

### Models Created
```
app/Models/
├── Customer.php
├── Product.php
└── Wastage.php
```

### Controllers Created
```
app/Http/Controllers/
├── CustomerController.php
├── ProductController.php
└── WastageController.php
```

### Migrations Created
```
database/migrations/
├── 2026_05_26_042404_create_customers_table.php
├── 2026_05_26_042408_create_products_table.php
└── 2026_05_26_042416_create_wastages_table.php
```

### Views Created
```
resources/views/modules/
├── customers-list.blade.php
├── customers-create.blade.php
├── customers-edit.blade.php
├── products-list.blade.php
├── products-create.blade.php
├── products-edit.blade.php
├── wastages-list.blade.php
├── wastages-create.blade.php
└── wastages-edit.blade.php
```

---

## Usage Instructions

### Customer Management
1. Navigate to Dashboard → Click "Customer Management" card
2. Click "Add Customer" button
3. Fill in customer details (Name, Phone Number, optional Address)
4. Select status (Active/Inactive)
5. Click "Save Customer"
6. Edit: Click "Edit" button on any customer row
7. Delete: Click "Delete" button with confirmation

### Products & Inventory
1. Navigate to Dashboard → Click "Inventory & Products" card
2. Click "Add Product" button
3. Enter product details (Name, Description, Price, Quantity)
4. Select status (Active/Inactive)
5. Click "Save Product"
6. Edit: Click "Edit" button on any product row
7. Delete: Click "Delete" button with confirmation

### Wastage Management
1. Navigate to Dashboard → Click "Wastage Management" card
2. Click "Record Wastage" button
3. Select product from dropdown (shows available quantity)
4. Enter quantity wasted (must be ≤ available quantity)
5. Enter reason (e.g., Expired, Damaged, Spoiled)
6. Select date of wastage
7. Add optional notes
8. Click "Record Wastage"
   - Product quantity automatically decreases
9. Edit: Click "Edit" to modify wastage details
10. Delete: Click "Delete" to remove record and restore quantity

---

## Validation Rules

### Customer
- Name: required, max 255 characters
- Phone Number: required, max 20 characters
- Address: optional, max 500 characters
- Status: required, must be 'active' or 'inactive'

### Product
- Name: required, max 255 characters
- Description: optional, max 1000 characters
- Price: required, numeric, min 0
- Quantity: required, integer, min 0
- Status: required, must be 'active' or 'inactive'

### Wastage
- Product: required, must exist in products table
- Quantity: required, integer, min 1, must be ≤ available quantity
- Reason: required, max 255 characters
- Notes: optional, max 500 characters
- Date: required, must be valid date

---

## Key Features

### Sidebar Navigation
- All three modules are accessible from the sidebar on each module page
- Current module is highlighted in red
- Quick navigation between modules

### Dashboard Integration
- Modules are displayed as cards on the dashboard
- Click any card to enter that module
- Full CRUD functionality in each module

### User Feedback
- Success messages displayed after create/update/delete
- Form errors shown inline
- Confirmation dialog before deletion

### Responsive Design
- Mobile-friendly table layout
- Responsive forms
- Proper spacing and typography

---

## Database Relationships

```
Products (1) ──── (Many) Wastages
```
- Each product can have multiple wastage records
- Wastage belongs to a product
- Deleting a product cascades delete to its wastage records

---

## Testing the Modules

1. **Add a Product**
   - Go to Inventory & Products
   - Add: "Tomato Sauce", Price: 5.99, Qty: 100, Status: Active

2. **Record Wastage**
   - Go to Wastage Management
   - Select "Tomato Sauce"
   - Record 5 units wasted (Reason: "Expired")
   - Verify product quantity reduces from 100 to 95

3. **Edit Wastage**
   - Edit the wastage record to 10 units
   - Verify product quantity is now 90 (100 - 10)

4. **Delete Wastage**
   - Delete the wastage record
   - Verify product quantity returns to 100

5. **Add Customers**
   - Add several customers with different statuses
   - Test pagination (10 per page)
   - Test edit and delete functionality

---

## Future Enhancements

Potential improvements:
- Search and filter functionality
- Bulk operations (delete multiple records)
- Export to CSV/Excel
- Wastage reports and analytics
- Product images
- Customer purchase history
- Inventory alerts (low stock)
- Audit logs for tracking changes

---

## Support

All modules follow Laravel best practices:
- Resource routing
- Model relationships
- Form validation
- CSRF protection
- Pagination
- Error handling

Each module is fully functional and ready for production use.
