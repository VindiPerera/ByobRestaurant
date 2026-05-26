# Product Images - Code Reference Guide

## Quick Navigation

1. **Inventory Page** → `resources/views/modules/products-list.blade.php` (Lines 31-68)
2. **POS Product Grid** → `resources/views/modules/pos.blade.php` (Lines 671-679)
3. **POS Billing Panel** → `resources/views/modules/pos.blade.php` (Lines 806-844)
4. **Backend API** → `app/Http/Controllers/PosController.php` (Lines 65-79, 137-149)

---

## Code Changes - Inventory Table

### File: `resources/views/modules/products-list.blade.php`

**Location:** Table header and body cells

```blade
<!-- NEW: Image column header -->
<th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Image</th>

<!-- NEW: Image cell in table body -->
<td class="px-6 py-4 text-sm">
    @if($product->image)
        <div class="h-12 w-12 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 group cursor-pointer relative">
            <img src="{{ asset('storage/' . $product->image) }}"
                 alt="{{ $product->name }}"
                 class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-200"
                 title="{{ $product->name }}">
            <div class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition-opacity"></div>
        </div>
    @else
        <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400 text-lg flex-shrink-0">
            <i class="fas fa-image"></i>
        </div>
    @endif
</td>
```

**CSS Classes Explanation:**
- `h-12 w-12` → 48px × 48px
- `rounded-lg` → 8px border radius
- `object-cover` → Maintain aspect ratio, fill container
- `group-hover:scale-110` → 110% zoom on hover
- `opacity-0` / `opacity-10` → Dark overlay effect
- `transition-transform` / `transition-opacity` → Smooth animations

---

## Code Changes - POS Product Grid

### File: `resources/views/modules/pos.blade.php`

**Location:** `renderProducts()` function (around line 665)

```javascript
// UPDATED: renderProducts() method
function renderProducts() {
    const container = document.getElementById('productsContainer');
    if (allProducts.length === 0) {
        container.innerHTML = '<p style="grid-column:1/-1; text-align:center; color:#94a3b8; padding:48px 0; font-size:13px;"><i class="fas fa-search" style="font-size:28px; display:block; margin-bottom:10px;"></i>No products found</p>';
        return;
    }
    container.innerHTML = allProducts.map(function(p) {
        // ↓ NEW: Image HTML generation
        let imageHtml = '';
        if (p.image) {
            imageHtml = '<img src="/storage/' + p.image + '" alt="' + escapeHtml(p.name) + '" '
                + 'style="width:100%; height:100%; object-fit:cover;">';
        } else {
            imageHtml = '<i class="fas fa-utensils" style="color:#dc2626; font-size:18px;"></i>';
        }
        // ↑ NEW

        return '<div class="product-card" onclick="addProductToOrder(' + p.id + ', \'' + escapeJs(p.name) + '\', ' + p.price + ')">'
            // ↓ UPDATED: Larger image container (80px height)
            + '<div style="height:80px; background:linear-gradient(135deg,#fef2f2,#fee2e2); border-radius:10px; display:flex; align-items:center; justify-content:center; margin-bottom:10px; overflow:hidden; position:relative;">'
            // ↑ UPDATED: height:80px (was 52px), added overflow:hidden and position:relative
            + imageHtml
            + '</div>'
            + '<p style="font-size:12px; font-weight:700; color:#0f172a; margin:0 0 4px; line-height:1.3; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">' + escapeHtml(p.name) + '</p>'
            + '<p style="font-size:14px; font-weight:900; color:#dc2626; margin:0;">Rs. ' + p.price.toFixed(2) + '</p>'
            + '</div>';
    }).join('');
}
```

**Key Changes:**
- Image height increased from 52px to 80px
- Added `overflow:hidden` to prevent image overflow
- Added `position:relative` for absolute positioning support
- Dynamic image loading based on product.image field

---

## Code Changes - POS Billing Panel

### File: `resources/views/modules/pos.blade.php`

**Location:** `renderBill()` function (around line 806-844)

```javascript
// UPDATED: renderBill() method - order items section
document.getElementById('billItems').innerHTML = currentOrder.items.map(function(item) {
    const noteHtml = item.kitchen_notes
        ? '<p style="font-size:10px; color:#f59e0b; margin:2px 0 0;"><i class="fas fa-note-sticky" style="margin-right:3px;"></i>' + escapeHtml(item.kitchen_notes) + '</p>'
        : '';
    const removeBtn = item.id
        ? '<button onclick="removeItem(' + item.id + ')" style="font-size:10px; color:#ef4444; background:none; border:none; cursor:pointer; padding:0; margin-top:3px;"><i class="fas fa-trash"></i> Remove</button>'
        : '';
    const decBtn = item.id
        ? '<button class="qty-btn" onclick="decreaseQty(' + item.id + ')">−</button>'
        : '<button class="qty-btn" style="opacity:0.4;" disabled>−</button>';
    const incBtn = item.id
        ? '<button class="qty-btn" onclick="increaseQty(' + item.id + ')">+</button>'
        : '<button class="qty-btn" style="opacity:0.4;" disabled>+</button>';

    // ↓ NEW: Thumbnail image HTML
    let thumbHtml = '';
    if (item.image) {
        thumbHtml = '<div style="width:48px; height:48px; border-radius:8px; overflow:hidden; flex-shrink:0; background:#f1f5f9; margin-right:10px;">'
            + '<img src="/storage/' + item.image + '" alt="' + escapeHtml(item.product_name) + '" '
            + 'style="width:100%; height:100%; object-fit:cover;">'
            + '</div>';
    }
    // ↑ NEW

    // ↓ UPDATED: Add thumbHtml and adjust layout
    return '<div class="bill-item" style="align-items:flex-start;">'
        + thumbHtml  // ← NEW
        + '<div style="flex:1; min-width:0;">'
        + '<p style="font-size:13px; font-weight:700; color:#0f172a; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">' + escapeHtml(item.product_name) + '</p>'
        + '<p style="font-size:11px; color:#94a3b8; margin:2px 0 0;">Rs. ' + item.unit_price.toFixed(2) + ' each</p>'
        + noteHtml
        + '</div>'
        + '<div style="display:flex; align-items:center; gap:5px; flex-shrink:0;">'
        + decBtn
        + '<span style="min-width:22px; text-align:center; font-size:13px; font-weight:800; color:#0f172a;">' + item.quantity + '</span>'
        + incBtn
        + '</div>'
        + '<div style="min-width:72px; text-align:right; flex-shrink:0;">'
        + '<p style="font-size:13px; font-weight:800; color:#0f172a; margin:0;">Rs. ' + item.subtotal.toFixed(2) + '</p>'
        + removeBtn
        + '</div>'
        + '</div>';
    // ↑ UPDATED
}).join('');
```

**Key Changes:**
- Added image thumbnail logic (48px × 48px)
- Thumbnail appears before product information
- `flex-shrink:0` prevents image from shrinking
- `margin-right:10px` creates spacing between image and text
- `align-items:flex-start` aligns items properly with image

---

## Backend Changes

### File: `app/Http/Controllers/PosController.php`

#### Change 1: `getProducts()` method (Lines 65-79)

```php
// UPDATED: getProducts() response mapping
$products = $query->get()->map(function ($product) {
    return [
        'id' => $product->id,
        'name' => $product->name,
        'price' => (float) ($product->selling_price ?? $product->price),
        'cost_price' => (float) ($product->cost_price ?? 0),
        'category_id' => $product->category_id,
        'barcode' => $product->barcode,
        'is_unlimited_stock' => $product->is_unlimited_stock,
        'quantity' => $product->quantity,
        'image' => $product->image,  // ← NEW FIELD
    ];
});
```

#### Change 2: `getOrder()` method (Lines 137-149)

```php
// UPDATED: getOrder() items mapping
'items' => $order->items->map(function ($item) {
    return [
        'id' => $item->id,
        'product_id' => $item->product_id,
        'product_name' => $item->product_name,
        'unit_price' => (float) $item->unit_price,
        'quantity' => $item->quantity,
        'subtotal' => (float) $item->subtotal,
        'kitchen_notes' => $item->kitchen_notes,
        'is_bar_item' => $item->is_bar_item,
        'image' => $item->product?->image,  // ← NEW FIELD (with null-safe operator)
    ];
}),
```

---

## Image Path Format

### Storage Path
```
Database column: products.image
Value example: "products/fried-rice-1234567890.jpg"

Access in Blade:
{{ asset('storage/' . $product->image) }}
→ https://yoursite.com/storage/products/fried-rice-1234567890.jpg

Access in JavaScript:
/storage/products/fried-rice-1234567890.jpg
```

### Storage Directory Structure
```
storage/
├── app/
│   └── public/
│       └── products/
│           ├── fried-rice-1234567890.jpg
│           ├── biryani-2345678901.jpg
│           ├── chocolate-cake-3456789012.jpg
│           └── ...
└── public/ (symlink to app/public)
```

---

## CSS Classes Used

| Class | Purpose | Example |
|-------|---------|---------|
| `h-12 w-12` | 48px square | Inventory thumbnails |
| `height:80px` | 80px height | POS product grid |
| `rounded-lg` | 8px border radius | All images |
| `object-fit:cover` | Fill container aspect-ratio | Image sizing |
| `object-cover` | Same as above | Image sizing |
| `group-hover:scale-110` | 110% zoom on hover | Inventory hover |
| `overflow:hidden` | Clip overflow content | Image containers |
| `flex-shrink:0` | Prevent flex shrinking | Thumbnails in bill |
| `opacity-0` / `opacity-10` | Transparency | Overlay effects |
| `transition-transform` | Smooth zoom | Hover animations |
| `transition-opacity` | Smooth fade | Overlay animations |

---

## JavaScript Functions Modified

### `renderProducts()` - ~30 lines added
- Generates image HTML for each product
- Falls back to utensils icon if no image
- Injects image into product card

### `renderBill()` - ~20 lines added
- Generates thumbnail HTML for each order item
- Creates 48px image containers
- Positions thumbnails left of product info

---

## API Response Examples

### Products Endpoint: `/pos/products`

**Before:**
```json
[
  {
    "id": 1,
    "name": "Fried Rice",
    "price": 800,
    "category_id": 2,
    "quantity": 45
  }
]
```

**After:**
```json
[
  {
    "id": 1,
    "name": "Fried Rice",
    "price": 800,
    "category_id": 2,
    "quantity": 45,
    "image": "products/fried-rice-1234567890.jpg"  // ← NEW
  }
]
```

### Order Endpoint: `/pos/order/{id}`

**Before:**
```json
{
  "items": [
    {
      "id": 1,
      "product_name": "Fried Rice",
      "quantity": 2,
      "subtotal": 1600
    }
  ]
}
```

**After:**
```json
{
  "items": [
    {
      "id": 1,
      "product_name": "Fried Rice",
      "quantity": 2,
      "subtotal": 1600,
      "image": "products/fried-rice-1234567890.jpg"  // ← NEW
    }
  ]
}
```

---

## Testing Checklist

- [ ] Inventory page displays product images in table
- [ ] Placeholder icon shows for products without images
- [ ] Hover effect zooms and darkens on inventory images
- [ ] POS product grid displays larger images (80px)
- [ ] POS billing panel shows 48px thumbnails next to items
- [ ] Images load correctly from `/storage/products/` path
- [ ] No broken image icons appear
- [ ] All product operations work (add, remove, edit)
- [ ] Mobile responsive display works
- [ ] Performance is acceptable (no lag)

