# Restaurant POS API Documentation

## Overview

This document provides technical API specifications for the enhanced Restaurant BYOB POS system. All endpoints require authentication via Laravel's built-in authentication middleware.

**Base URL**: `http://localhost:8000/api` or `http://localhost:8000/pos`

**Authentication**: Laravel Session (Cookie-based) - Users must be logged in

---

## Table of Contents

1. [Authentication](#authentication)
2. [Orders API](#orders-api)
3. [Items API](#items-api)
4. [Billing API](#billing-api)
5. [Tables API](#tables-api)
6. [Error Handling](#error-handling)
7. [Data Types](#data-types)

---

## Authentication

All endpoints require the user to be authenticated via Laravel's session authentication.

### Login
```bash
POST /login
Content-Type: application/json

{
  "email": "admin@restaurant.local",
  "password": "password"
}
```

**Response**: Redirects to authenticated dashboard with session cookie

### CSRF Token
All POST/PUT/DELETE requests require a valid CSRF token:

```bash
-H "X-CSRF-TOKEN: {{ csrf_token() }}"
```

---

## Orders API

### Create Order

Creates a new order and marks the table as occupied.

**Endpoint:**
```
POST /pos/order
```

**Request Headers:**
```
Content-Type: application/json
X-CSRF-TOKEN: {token}
```

**Request Body:**
```json
{
  "table_id": 1,
  "customer_name": "John Doe",
  "customer_phone": "0771234567",
  "order_type": "dine_in",
  "waiter_name": "Ali"
}
```

**Parameters:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `table_id` | integer | yes | Restaurant table ID |
| `customer_name` | string | no | Customer name |
| `customer_phone` | string | no | Customer phone number |
| `order_type` | enum | yes | One of: `dine_in`, `takeaway`, `delivery`, `vip_room` |
| `waiter_name` | string | no | Name of waiter (defaults to logged-in user) |

**Response:**
```json
{
  "success": true,
  "order_id": 42,
  "order_number": "ORD-ABC12XYZ"
}
```

**Status Code**: 200 (Success) | 422 (Validation Error)

---

### Get Order

Retrieves complete order details including items and totals.

**Endpoint:**
```
GET /pos/order/{order_id}
```

**Response:**
```json
{
  "id": 42,
  "order_number": "ORD-ABC12XYZ",
  "table_id": 1,
  "table_number": "3",
  "order_type": "dine_in",
  "status": "pending",
  "customer_name": "John Doe",
  "customer_phone": "0771234567",
  "live_bill_enabled": false,
  "waiter_bill_printed_at": null,
  "subtotal": 1050.00,
  "discount_amount": 0.00,
  "tax_amount": 105.00,
  "total": 1155.00,
  "items": [
    {
      "id": 1,
      "product_id": 5,
      "product_name": "Biryani",
      "unit_price": 500.00,
      "quantity": 2,
      "subtotal": 1000.00,
      "kitchen_notes": "Extra spicy",
      "is_bar_item": false
    }
  ]
}
```

**Status Code**: 200 (Success) | 404 (Not Found)

---

### Update Customer Details

Updates customer name and phone on an order.

**Endpoint:**
```
POST /pos/order/{order_id}/customer
```

**Request Body:**
```json
{
  "customer_name": "Jane Smith",
  "customer_phone": "0779876543"
}
```

**Parameters:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `customer_name` | string | no | Updated customer name |
| `customer_phone` | string | no | Updated phone number |

**Response:**
```json
{
  "success": true,
  "message": "Customer details updated"
}
```

**Status Code**: 200 (Success) | 422 (Validation Error)

---

### Hold Order

Pauses an order without completing it. Table becomes available but order is saved.

**Endpoint:**
```
POST /pos/order/{order_id}/hold
```

**Response:**
```json
{
  "success": true,
  "message": "Order held"
}
```

**Status Code**: 200 (Success)

---

### Complete Order (Payment)

Finalizes order, processes payment, and marks table as available.

**Endpoint:**
```
POST /pos/order/{order_id}/complete
```

**Request Body:**
```json
{
  "payment_method": "cash",
  "amount_paid": 2000.00,
  "discount_type": "percentage",
  "discount_value": 10
}
```

**Parameters:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `payment_method` | enum | yes | One of: `cash`, `card`, `bank_transfer`, `mixed` |
| `amount_paid` | decimal | yes | Total amount customer paid |
| `discount_type` | enum | no | Type of discount: `percentage` or `fixed` |
| `discount_value` | decimal | no | Discount amount or percentage |

**Response:**
```json
{
  "success": true,
  "total": 1155.00,
  "change": 845.00,
  "message": "Order completed"
}
```

**Status Code**: 200 (Success) | 422 (Validation Error)

---

### Close Table

Closes an empty order (no items) and frees the table.

**Endpoint:**
```
POST /pos/order/{order_id}/close-table
```

**Response:**
```json
{
  "success": true,
  "message": "Table closed"
}
```

**Error Response:**
```json
{
  "error": "Cannot close table with active order"
}
```

**Status Code**: 200 (Success) | 400 (Bad Request)

---

## Items API

### Add Item to Order

Adds a product to an order.

**Endpoint:**
```
POST /pos/order/{order_id}/item
```

**Request Body:**
```json
{
  "product_id": 5,
  "quantity": 2,
  "kitchen_notes": "Extra spicy, no onions",
  "is_bar_item": false
}
```

**Parameters:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `product_id` | integer | yes | Product ID |
| `quantity` | integer | yes | Quantity (minimum 1) |
| `kitchen_notes` | string | no | Special instructions |
| `is_bar_item` | boolean | no | Whether item is bar/drink |

**Response:**
```json
{
  "success": true,
  "item_id": 42,
  "message": "Biryani added to order"
}
```

**Status Code**: 200 (Success) | 422 (Validation Error)

---

### Update Item

Updates quantity and notes for an item.

**Endpoint:**
```
PUT /pos/order/{order_id}/item/{item_id}
```

**Request Body:**
```json
{
  "quantity": 3,
  "kitchen_notes": "Make it mild"
}
```

**Parameters:**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `quantity` | integer | yes | New quantity (minimum 1) |
| `kitchen_notes` | string | no | Updated instructions |

**Response:**
```json
{
  "success": true,
  "message": "Item updated"
}
```

**Status Code**: 200 (Success) | 422 (Validation Error)

---

### Remove Item

Deletes an item from order.

**Endpoint:**
```
DELETE /pos/order/{order_id}/item/{item_id}
```

**Response:**
```json
{
  "success": true,
  "message": "Item removed"
}
```

**Status Code**: 200 (Success) | 404 (Not Found)

---

## Billing API

### Generate Waiter Bill

Creates a preview bill for customer review before payment.

**Endpoint:**
```
POST /pos/order/{order_id}/waiter-bill
```

**Response:**
```json
{
  "success": true,
  "order_number": "ORD-ABC12XYZ",
  "table_number": "3",
  "customer_name": "John Doe",
  "customer_phone": "0771234567",
  "subtotal": 1050.00,
  "tax_amount": 105.00,
  "total": 1155.00,
  "items": [
    {
      "product_name": "Biryani",
      "quantity": 2,
      "unit_price": 500.00,
      "subtotal": 1000.00,
      "kitchen_notes": "Extra spicy"
    }
  ]
}
```

**Status Code**: 200 (Success) | 404 (Not Found)

---

### Toggle Live Bill

Enables or disables automatic bill printing on item changes.

**Endpoint:**
```
POST /pos/order/{order_id}/live-bill
```

**Response:**
```json
{
  "success": true,
  "live_bill_enabled": true,
  "message": "Live bill enabled"
}
```

**Status Code**: 200 (Success)

---

## Kitchen & Bar API

### Print KOT (Kitchen Order Ticket)

Generates and marks KOT as printed.

**Endpoint:**
```
POST /pos/order/{order_id}/kot
```

**Response:**
```json
{
  "success": true,
  "order_number": "ORD-ABC12XYZ",
  "items": [
    {
      "product_name": "Biryani",
      "quantity": 2,
      "kitchen_notes": "Extra spicy"
    }
  ]
}
```

**Note**: KOT includes only non-bar items (`is_bar_item: false`)

**Status Code**: 200 (Success)

---

### Print BOT (Bar Order Ticket)

Generates and marks BOT as printed.

**Endpoint:**
```
POST /pos/order/{order_id}/bot
```

**Response:**
```json
{
  "success": true,
  "order_number": "ORD-ABC12XYZ",
  "items": [
    {
      "product_name": "Mojito",
      "quantity": 3,
      "kitchen_notes": "Light sugar"
    }
  ]
}
```

**Note**: BOT includes only bar items (`is_bar_item: true`)

**Status Code**: 200 (Success)

---

## Tables API

### Get All Tables

Retrieves list of all tables with their status and active orders.

**Endpoint:**
```
GET /pos/tables
```

**Response:**
```json
[
  {
    "id": 1,
    "table_number": "1",
    "name": "Table 1",
    "capacity": 4,
    "status": "available",
    "section": "main",
    "occupied_at": null,
    "has_order": false,
    "order_id": null,
    "order_items_count": 0
  },
  {
    "id": 3,
    "table_number": "3",
    "name": "Table 3",
    "capacity": 4,
    "status": "occupied",
    "section": "main",
    "occupied_at": "2026-05-26T14:35:00",
    "has_order": true,
    "order_id": 42,
    "order_items_count": 3
  }
]
```

**Status Code**: 200 (Success)

---

### Get Table Orders

Retrieves all orders for a specific table.

**Endpoint:**
```
GET /pos/table/{table_id}/orders
```

**Response:**
```json
[
  {
    "id": 42,
    "order_number": "ORD-ABC12XYZ",
    "status": "completed",
    "customer_name": "John Doe",
    "items_count": 3,
    "subtotal": 1050.00,
    "total": 1155.00,
    "created_at": "2026-05-26T14:30:00"
  }
]
```

**Status Code**: 200 (Success)

---

### Get Products

Searches products with optional filtering.

**Endpoint:**
```
GET /pos/products
```

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `search` | string | Search by name or barcode |
| `category_id` | integer | Filter by category ID |

**Example:**
```
GET /pos/products?search=biryani&category_id=2
```

**Response:**
```json
[
  {
    "id": 5,
    "name": "Biryani",
    "price": 500.00,
    "cost_price": 200.00,
    "category_id": 2,
    "barcode": "BYRN-001",
    "is_unlimited_stock": true,
    "quantity": 0
  }
]
```

**Status Code**: 200 (Success)

---

### Get Held Orders

Retrieves all paused orders.

**Endpoint:**
```
GET /pos/held-orders
```

**Response:**
```json
[
  {
    "id": 40,
    "order_number": "ORD-OLD12345",
    "table_number": "5",
    "items_count": 2,
    "total": 950.00
  }
]
```

**Status Code**: 200 (Success)

---

## Error Handling

### Standard Error Response

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "table_id": ["The table_id field is required."],
    "order_type": ["The order_type must be one of: dine_in, takeaway, delivery, vip_room."]
  }
}
```

### HTTP Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200 | Success | Order created successfully |
| 400 | Bad Request | Cannot close table with active order |
| 404 | Not Found | Order ID doesn't exist |
| 422 | Validation Error | Missing required field |
| 500 | Server Error | Database connection failed |

---

## Data Types

### Order Status Enum

```php
enum('pending', 'confirmed', 'completed', 'cancelled', 'hold')
```

- `pending`: Order created, items being added
- `confirmed`: Order confirmed, ready for kitchen
- `completed`: Payment received, order finished
- `cancelled`: Order cancelled without payment
- `hold`: Order paused, to be resumed later

### Table Status Enum

```php
enum('available', 'occupied', 'reserved', 'cleaning')
```

- `available`: Table ready for new customer
- `occupied`: Table has active order
- `reserved`: Table reserved for later
- `cleaning`: Table being cleaned

### Order Type Enum

```php
enum('dine_in', 'takeaway', 'delivery', 'vip_room')
```

### Payment Method Enum

```php
enum('cash', 'card', 'bank_transfer', 'mixed')
```

---

## Monetary Fields

All monetary values are stored as `decimal(12,2)`:

- `subtotal` - Sum of all item prices
- `discount_amount` - Discount applied
- `tax_amount` - Calculated as subtotal × 10%
- `total` - subtotal - discount + tax
- `amount_paid` - Amount customer paid
- `change_amount` - amount_paid - total
- `unit_price` - Price per item
- `selling_price` - Retail price of product
- `cost_price` - Cost to restaurant

**Example**:
```
Subtotal: 1000.00
Discount: 50.00
Subtotal after discount: 950.00
Tax (10%): 95.00
Total: 1045.00
```

---

## Timestamps

All timestamp fields are in ISO 8601 format (UTC):

```
"2026-05-26T14:35:00Z"
```

Fields:
- `created_at` - When order/item was created
- `updated_at` - Last update to order/item
- `occupied_at` - When table was occupied
- `kot_printed_at` - When KOT was printed
- `bot_printed_at` - When BOT was printed
- `waiter_bill_printed_at` - When waiter bill was printed
- `printed_at` - When final invoice was printed

---

## Rate Limiting

Currently: **No rate limiting** (suitable for single-location restaurant)

For multi-location or high-volume scenarios, implement rate limiting:
```php
// In routes/web.php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/pos/order', ...);
});
```

---

## CORS (Cross-Origin Resource Sharing)

Currently: **Not enabled** (frontend and backend on same server)

If deploying frontend separately, enable CORS in `config/cors.php`:
```php
'allowed_origins' => ['https://yourdomain.com'],
```

---

## Webhooks

Not implemented in current version. Future enhancement for:
- Order status changes
- Payment confirmations
- Kitchen updates
- Real-time table status

---

## Debugging

### Enable API Logging

Add to `.env`:
```
LOG_CHANNEL=stack
APP_DEBUG=true
```

### View Request Logs

```bash
tail -f storage/logs/laravel.log
```

### Use Postman Collection

[Import provided Postman collection for API testing]

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | May 2026 | Initial POS system |
| 1.1.0 | May 26, 2026 | Added customer fields, waiter bill, live billing |

---

## Support

For API issues or questions:
- **Email**: jaanclaude.lk@gmail.com
- **Docs**: Check README.md for setup instructions
- **Issues**: Create issue on GitHub or contact support

---

**Last Updated**: May 26, 2026
**API Version**: 1.1.0
