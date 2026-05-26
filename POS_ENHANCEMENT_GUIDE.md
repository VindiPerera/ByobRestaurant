# 🍽️ Restaurant POS Enhancement Guide

## New Features Overview

Your Restaurant BYOB POS system has been enhanced with powerful new billing and table management features. This guide walks you through each feature and how to use it.

---

## 📋 Table of Contents

1. [Getting Started](#getting-started)
2. [Table Status & Management](#table-status--management)
3. [Customer Details](#customer-details)
4. [Waiter Bill Preview](#waiter-bill-preview)
5. [Live Billing](#live-billing)
6. [Final Invoice & Payment](#final-invoice--payment)
7. [Kitchen & Bar Operations](#kitchen--bar-operations)
8. [Advanced Workflows](#advanced-workflows)

---

## Getting Started

### Access the POS System

1. Open your browser and go to: `http://localhost:8000/pos`
2. Log in with your credentials:
   - **Admin**: `admin@restaurant.local` / `password`
   - **Manager**: `manager@restaurant.local` / `password`
   - **Cashier**: `cashier@restaurant.local` / `password`

The POS interface has three main sections:
- **Left Panel**: Table cards (select a table to start)
- **Middle Panel**: Menu items and product search
- **Right Panel**: Order bill and controls

---

## Table Status & Management

### Understanding Table Colors

The table cards use color coding to show status:

| Color | Status | Meaning |
|-------|--------|---------|
| 🟢 Green | Available | Table ready for new order |
| 🔴 Red | Occupied | Table has active unpaid order |
| 🟡 Yellow | Reserved | Table is reserved |
| ⚫ Gray | Cleaning | Table is being cleaned |

### Viewing Table Details

When a table is occupied, the table card shows:
- **Table number** (e.g., "Table 1")
- **Table name** (e.g., "Standard")
- **Capacity** (e.g., "Cap: 4")
- **Item count** (e.g., "📦 3" = 3 items in order)
- **Occupied time** (e.g., "12:34:56") - how long table has been occupied

### Selecting a Table

1. Click on an **available** (green) table to start a new order
   - Order is automatically created
   - Table status changes to **occupied** (red)
   - Customer section appears

2. Click on an **occupied** (red) table to manage existing order
   - Existing order loads
   - Customer details shown
   - Can add/remove items or proceed to payment

### Filter Tables

Use the filter buttons at the top:
- **All**: Show all tables
- **Main**: Show only main section tables (1-12)
- **VIP**: Show only VIP section tables (13-14)

---

## Customer Details

### Adding Customer Information

When you select a table, a **Customer Details** section appears below the order header:

1. **Customer Name** field
   - Optional - for walk-ins, leave blank
   - Type customer name (e.g., "John Smith")

2. **Phone Number** field
   - Optional - customer contact
   - Type phone number (e.g., "+94771234567")

3. **Save** button
   - Saves customer info with the order
   - This data appears on Waiter Bill and Final Invoice

### When to Use

- **Dine-in customers**: Collect name for personalization
- **Delivery orders**: Phone number essential for driver
- **Catering**: Customer name for record-keeping
- **Walk-ins**: Leave empty if unknown

### Data Persistence

Customer details are saved with the order:
- If you **hold** the order, details are preserved
- When you **resume** the order, details are pre-filled
- When you **print bills**, customer details are included

---

## Waiter Bill Preview

The **Waiter Bill** is a preview of all items and totals before final payment. Perfect for showing the customer before they pay.

### How to Use

1. **Add items to the order** (minimum required)
   - Click products from the menu
   - Adjust quantities using +/- buttons
   - Add kitchen notes if needed

2. **Click "Waiter Bill" button**
   - Located in the order controls section
   - Modal opens showing:
     - **Order Number** (e.g., "ORD-ABC12345")
     - **Table Number** (e.g., "Table 3")
     - **Customer Name** (if entered)
     - **All items** with quantities and individual prices
     - **Subtotal** (sum of all items)
     - **Tax (10%)** (calculated automatically)
     - **Total** (grand total including tax)

3. **Show to Customer**
   - Present the preview on your device
   - Customer reviews items and amounts
   - No payment yet - just confirmation

4. **Proceed to Payment**
   - Click "Generate Final Bill" in the modal
   - Payment modal opens (see next section)
   - OR click Back to add more items

### Waiter Bill vs Final Invoice

| Feature | Waiter Bill | Final Invoice |
|---------|-------------|---------------|
| **Purpose** | Preview before payment | Receipt after payment |
| **Includes** | Items, subtotal, tax, total | All above + payment method, change |
| **Printing** | Manual - only in modal | Auto-print after payment |
| **Payment Info** | None shown | Shows how much paid & change |
| **Paid Badge** | No | Yes - "✓ PAID ✓" |

---

## Live Billing

**Live Billing** automatically prints the bill each time an item is added, updated, or removed. Perfect for busy service where you want real-time kitchen and bar communication.

### Enabling Live Billing

1. **Select a table** and start order
2. **Click "Live Bill" button**
   - Button color changes from gray to **purple**
   - Visual indicator starts **pulsing**
   - Text changes to "Live Bill ON"

### What Happens When Enabled

When you **add an item**:
- Item is added to the order
- Bill is automatically printed to your printer
- Kitchen/bar gets immediate notice of new items

When you **increase quantity**:
- Quantity updates
- Updated bill prints automatically
- Shows new total items

When you **remove an item**:
- Item deleted from order
- Updated bill prints immediately
- Shows revised totals

### Disabling Live Billing

1. Click "Live Bill" button again
   - Button color changes back to gray
   - Text changes to "Live Bill"
   - Pulsing indicator stops

From now on, items won't auto-print. You'll need to manually click KOT/BOT to print.

### Live Bill Format

The live bill is a compact **thermal receipt** (80mm width):
- Order ID
- Table number
- All current items with quantities
- Subtotal, tax, and total
- Current timestamp

### Use Cases

- **Busy dinner service**: Kitchen knows exactly what to cook immediately
- **Large parties**: Real-time item tracking
- **Bar orders**: Bartender sees drinks instantly
- **Order modifications**: Updated bills show quantity changes
- **Multi-table tracking**: Each table's bill is independent

### Pro Tip

Use Live Billing during peak hours but disable it during quiet times to reduce paper waste.

---

## Final Invoice & Payment

The **Final Invoice** is the official receipt after payment is confirmed. It's printed automatically and includes all payment details.

### Starting Payment

1. **Ensure order has items** (obviously!)
2. **Click "Pay" button** OR "Generate Final Bill" from Waiter Bill
3. **Payment Modal opens**

### Payment Method Selection

Choose how the customer is paying:

- **💵 Cash**: Physical money payment
- **💳 Card**: Credit/debit card
- **🏦 Bank**: Bank transfer (check, wire, etc.)
- **➕ Mixed**: Combination of above

### Entering Amount Paid

1. **Amount Paid field**
   - Enter the amount the customer gave
   - System auto-calculates change
   - Change amount updates in real-time below

2. **Example**
   - Total: Rs. 1,500.00
   - Customer gives: Rs. 2,000.00
   - Change: Rs. 500.00

### Confirming Payment

1. Click **"Confirm Payment"** button
2. System processes payment:
   - Order status becomes "completed"
   - Table status changes to "available" (green)
   - Final invoice modal opens

### Final Invoice Details

The invoice modal shows (thermal format):

```
═══════════════════════════════════════
           INVOICE
═══════════════════════════════════════
Restaurant BYOB - POS System
───────────────────────────────────────
Order #: ORD-ABC12345
Table: 3
Customer: John Smith
Phone: +94771234567
Date: 26/05/2026 14:35:00
───────────────────────────────────────
Item Name          Qty    Price
Biryani            2      Rs. 500.00
Fish Curry         1      Rs. 350.00
Drinks             3      Rs. 200.00
───────────────────────────────────────
Subtotal:               Rs. 1,050.00
Tax (10%):              Rs.   105.00
Total:                  Rs. 1,155.00
───────────────────────────────────────
Payment Method: CASH
Amount Paid:            Rs. 2,000.00
Change:                 Rs.   845.00
═══════════════════════════════════════
              ✓ PAID ✓
═══════════════════════════════════════
       Thank you for your order!
═══════════════════════════════════════
```

### Auto-Print Invoice

- **Automatically prints** to your thermal printer
- **Print dialog appears** for manual selection
- After print, invoice modal **closes automatically**
- **Order is marked paid** and table is freed

### After Payment

After payment is confirmed:
- Table card returns to **green** (available)
- Order details are cleared from bill panel
- New orders can be created on that table
- Order history is saved in database

---

## Kitchen & Bar Operations

### KOT (Kitchen Order Ticket)

1. **When to print**: When food items are confirmed
2. **How to print**: Click "KOT" button
3. **What's shown**:
   - Order number
   - Table number
   - All kitchen items with quantities
   - Kitchen notes for special requests
   - NOT drink/bar items

4. **Print**: Click "Print" button in modal
   - Sends to kitchen printer
   - Marks `kot_printed_at` timestamp
   - Can print multiple times if needed

### BOT (Bar Order Ticket)

1. **When to print**: When drink items are ready
2. **How to print**: Click "BOT" button
3. **What's shown**:
   - Order number
   - Table number
   - All bar items (drinks) with quantities
   - Kitchen notes for special instructions
   - NOT food items

4. **Print**: Click "Print" button in modal
   - Sends to bar printer
   - Marks `bot_printed_at` timestamp
   - Can print multiple times

### KOT vs BOT Comparison

| Feature | KOT | BOT |
|---------|-----|-----|
| **Items** | Food only | Drinks/Bar only |
| **Recipient** | Kitchen | Bar/Bartender |
| **When** | Before cooking | When drinks ordered |
| **Table** | Shows table number | Shows table number |
| **Quantity** | All items | Only bar items |

---

## Advanced Workflows

### Workflow 1: Standard Dine-In Service

```
1. Customer arrives → Select table (Green → Red)
2. Enter customer name (optional)
3. Customer orders → Add items
4. Click "Waiter Bill" → Show customer
5. Customer confirms → "Generate Final Bill"
6. Select payment method → Enter amount
7. Click "Confirm Payment"
8. Invoice auto-prints
9. Table freed (Red → Green)
10. Next customer can use table
```

### Workflow 2: Live Billing with Kitchen

```
1. Select table → Disable Live Bill
2. Customer orders items → Add one by one
3. After each item: Click "KOT" → Print to kitchen
4. Kitchen starts cooking
5. Customer keeps adding items
6. Each KOT shows only latest items
7. After all items ordered → Payment
8. Keep invoice for records
```

### Workflow 3: Large Party with Hold

```
1. Select table, enable Live Bill
2. Items auto-print as added
3. Customers keep ordering → Auto-print continues
4. Mid-order: Click "Hold" → Items held, table freed
5. Later: Click "Held" counter → Resume order
6. Add more items → Continue auto-printing
7. Complete order → Payment → Invoice
```

### Workflow 4: Delivery Order

```
1. Select "Delivery" from order type
2. Enter customer name: "John Smith"
3. Enter phone: "+94771234567"
4. Add items
5. Click "Waiter Bill" → Confirm with customer
6. Proceed to payment
7. Invoice prints with delivery address phone number
8. Driver uses invoice for delivery
```

### Workflow 5: Split Bills (Multiple Customers, One Table)

Currently, one order = one bill. For split bills:

**Option A: Hold Orders Method**
```
1. Customer 1 orders → Add items
2. Click "Hold" → Save order
3. Select same table → New order created
4. Customer 2 orders → Add items
5. Pay Customer 2 first → Invoice prints
6. Click "Held" → Resume Customer 1's order
7. Pay Customer 1 → Invoice prints
```

**Option B: Calculate Manually**
```
1. Mix both customers' items
2. Show Waiter Bill → Calculate split
3. Process payment for first customer
4. After invoice → Refund unused amount
5. Create new order for second customer
```

---

## Troubleshooting

### Issue: "Live Bill" won't toggle

**Solution:**
- Ensure you have an active order (table selected)
- Refresh the page
- Check if browser allows printing

### Issue: Invoice doesn't auto-print

**Solution:**
- Check if printer is connected and online
- Browser may be blocking pop-ups - allow them
- Check browser print settings
- Manually click "Print" in print dialog

### Issue: Customer details not saving

**Solution:**
- Ensure you clicked "Save" button (blue)
- Check if order is still active (hasn't been completed)
- Refresh page if needed

### Issue: Live Bill printing too much paper

**Solution:**
- Click "Live Bill" to toggle OFF
- Only use during busy service
- Use KOT/BOT buttons manually instead for selective printing

### Issue: Wrong table selected

**Solution:**
- Click correct table (you can switch anytime)
- Old items stay in old order, new items go to new table
- To recover items: Click "Held" button to see paused orders

---

## Tips & Best Practices

### 🎯 For Efficient Service

1. **Pre-enter customer name** - Saves time, personalizes service
2. **Use Live Billing** - Kitchen reacts instantly to new orders
3. **Print KOT immediately** - Don't wait until all items ordered
4. **Group items** - Add all drinks together, then food
5. **Check Waiter Bill** - Confirm totals before payment

### 💰 For Accurate Billing

1. **Verify totals** in Waiter Bill before payment
2. **Double-check discount** if applied
3. **Confirm payment method** matches customer intention
4. **Keep invoice** for records (system also saves)
5. **Check change calculation** before handing money back

### 🖨️ For Printing

1. **Check printer is online** before service starts
2. **Stock thermal paper** (80mm width for receipts)
3. **Test KOT/BOT prints** before busy hours
4. **Keep invoice backup** if printer fails
5. **Print invoice** even if customer doesn't want it (for records)

### 👥 For Customer Experience

1. **Greet customer** and get name upfront
2. **Show Waiter Bill** instead of just saying total
3. **Offer to review items** from bill before payment
4. **Calculate change clearly** before handing it
5. **Thank customer** - they're helping you pay bills! 😊

---

## Keyboard Shortcuts

While the POS interface is primarily mouse-driven, you can use Tab key to navigate:

- **Tab**: Move between fields
- **Enter**: Confirm action (Pay, Save, etc.)
- **Esc**: Close modals (back to main POS)

---

## Support & Issues

If you encounter issues:

1. **Check server is running**: `php artisan serve`
2. **Check database is migrated**: `php artisan migrate`
3. **Clear browser cache**: Ctrl+Shift+Delete
4. **Restart browser**: Close and reopen POS
5. **Check Laravel logs**: `/storage/logs/laravel.log`

---

## Version Info

- **Version**: 1.1.0 (Enhanced)
- **Release Date**: May 26, 2026
- **Compatibility**: Laravel 11, PHP 8.1+, MySQL 8.0+
- **Browser**: Modern browsers (Chrome, Firefox, Safari, Edge)

---

**Happy serving! 🍽️**

For technical support or feature requests, contact: jaanclaude.lk@gmail.com
