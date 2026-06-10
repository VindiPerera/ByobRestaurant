<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order for Table {{ $table->table_number }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        .header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .table-badge {
            background: rgba(255,255,255,0.2);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            padding: 16px;
        }

        .product-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .product-card:active {
            transform: scale(0.95);
        }

        .product-card:hover {
            border-color: #dc2626;
            box-shadow: 0 4px 12px rgba(220,38,38,0.15);
        }

        .product-image {
            width: 100%;
            height: 80px;
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            overflow: hidden;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-name {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-price {
            font-size: 14px;
            font-weight: 900;
            color: #dc2626;
        }

        .category-pills {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 16px;
            padding-bottom: 8px;
        }

        .category-pill {
            white-space: nowrap;
            padding: 8px 16px;
            border-radius: 20px;
            border: 2px solid #e2e8f0;
            background: white;
            color: #64748b;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.2s;
        }

        .category-pill.active {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        .cart-summary {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 2px solid #e2e8f0;
            padding: 16px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .cart-summary.hidden {
            display: none;
        }

        .cart-total {
            flex: 1;
        }

        .cart-count {
            font-size: 12px;
            color: #64748b;
        }

        .cart-amount {
            font-size: 18px;
            font-weight: 900;
            color: #dc2626;
        }

        .btn-order {
            background: #dc2626;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .btn-order:hover {
            background: #b91c1c;
        }

        .btn-order:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
        }

        .cart-item {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .cart-item-details {
            flex: 1;
        }

        .cart-item-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 13px;
        }

        .cart-item-price {
            font-size: 12px;
            color: #64748b;
            margin-top: 2px;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .qty-btn {
            width: 24px;
            height: 24px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.2s;
        }

        .qty-btn:hover {
            background: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        .qty-display {
            min-width: 20px;
            text-align: center;
            font-weight: 700;
            font-size: 12px;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.55);
            backdrop-filter: blur(3px);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-box {
            background: white;
            border-radius: 16px;
            padding: 24px;
            max-width: 400px;
            width: 92%;
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
        }

        .modal-header {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 16px;
        }

        .input-field {
            width: 100%;
            padding: 10px 12px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 12px;
            outline: none;
        }

        .input-field:focus {
            border-color: #dc2626;
        }

        .success-message {
            display: none;
            background: #f0fdf4;
            border: 2px solid #16a34a;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin-top: 16px;
        }

        .success-message.show {
            display: block;
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
                gap: 10px;
                padding: 12px;
            }

            .header {
                padding: 16px;
            }

            .cart-summary {
                padding: 12px;
            }
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">{{ config('app.name') }}</div>
            <h1 style="font-size: 24px; font-weight: 900; margin: 0;">Order Now</h1>
        </div>
        <div class="table-badge">
            <i class="fas fa-chair" style="margin-right: 4px;"></i>
            Table {{ $table->table_number }}
        </div>
    </div>
</div>

<!-- Categories -->
<div class="category-pills">
    <button class="category-pill active" data-category="0" onclick="filterProducts(0, this)">
        All Items
    </button>
    @foreach($categories as $category)
        <button class="category-pill" data-category="{{ $category->id }}" onclick="filterProducts({{ $category->id }}, this)">
            {{ $category->name }}
        </button>
    @endforeach
</div>

<!-- Products Grid -->
<div class="product-grid" id="productsContainer">
    @foreach($products as $product)
        <div class="product-card" onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, '{{ $product->category_id }}')">
            <div class="product-image">
                @if($product->image)
                    <img src="/storage/{{ $product->image }}" alt="{{ $product->name }}">
                @else
                    <i class="fas fa-utensils" style="color: #dc2626; font-size: 24px;"></i>
                @endif
            </div>
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-price">Rs. {{ number_format($product->price, 2) }}</div>
        </div>
    @endforeach
</div>

<!-- Spacer for fixed footer -->
<div style="height: 80px;"></div>

<!-- Cart Summary -->
<div class="cart-summary hidden" id="cartSummary">
    <div class="cart-total">
        <div class="cart-count"><span id="cartItemCount">0</span> item<span id="itemsPlural">s</span></div>
        <div class="cart-amount">Rs. <span id="cartTotalAmount">0.00</span></div>
    </div>
    <button class="btn-order" onclick="openCheckoutModal()">
        <i class="fas fa-shopping-cart" style="margin-right: 6px;"></i>Order
    </button>
</div>

<!-- Checkout Modal -->
<div class="modal-overlay" id="checkoutModal">
    <div class="modal-box">
        <div class="modal-header">
            <i class="fas fa-clipboard-list" style="color: #dc2626; margin-right: 8px;"></i>Complete Your Order
        </div>

        <div style="margin-bottom: 16px;">
            <label style="font-size: 12px; font-weight: 600; color: #64748b; display: block; margin-bottom: 6px;">Your Name (Optional)</label>
            <input type="text" id="customerName" class="input-field" placeholder="Enter your name">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="font-size: 12px; font-weight: 600; color: #64748b; display: block; margin-bottom: 6px;">Phone Number (Optional)</label>
            <input type="tel" id="customerPhone" class="input-field" placeholder="Enter phone number">
        </div>

        <div style="background: #f8fafc; border-radius: 8px; padding: 12px; margin-bottom: 16px;">
            <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 8px;">Order Summary</div>
            <div id="checkoutItems" style="max-height: 200px; overflow-y: auto;"></div>
            <div style="border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 8px;">
                <div style="display: flex; justify-content: space-between; font-weight: 700; color: #dc2626; font-size: 14px;">
                    <span>Total:</span>
                    <span>Rs. <span id="checkoutTotal">0.00</span></span>
                </div>
            </div>
        </div>

        <button class="btn-order" style="width: 100%; margin-bottom: 8px;" onclick="submitOrder()">
            <i class="fas fa-check" style="margin-right: 6px;"></i>Place Order
        </button>
        <button style="width: 100%; padding: 10px; background: #e2e8f0; color: #374151; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;" onclick="closeCheckoutModal()">
            Cancel
        </button>

        <div class="success-message" id="successMessage">
            <i class="fas fa-check-circle" style="color: #16a34a; font-size: 32px; margin-bottom: 8px; display: block;"></i>
            <div style="font-size: 16px; font-weight: 700; color: #166534; margin-bottom: 4px;">Order Placed!</div>
            <div style="font-size: 13px; color: #166534;">Your order has been sent to the kitchen. Thank you!</div>
        </div>
    </div>
</div>

<script>
    const tableId = {{ $table->id }};
    let cart = {};
    let allProducts = {!! json_encode($products) !!};

    function addToCart(productId, productName, price, categoryId) {
        if (!cart[productId]) {
            cart[productId] = {
                id: productId,
                name: productName,
                price: price,
                quantity: 0,
                categoryId: categoryId
            };
        }
        cart[productId].quantity++;
        updateCartUI();
    }

    function removeFromCart(productId) {
        if (cart[productId]) {
            cart[productId].quantity--;
            if (cart[productId].quantity <= 0) {
                delete cart[productId];
            }
        }
        updateCartUI();
    }

    function updateCartUI() {
        const items = Object.values(cart);
        const cartSummary = document.getElementById('cartSummary');

        if (items.length === 0) {
            cartSummary.classList.add('hidden');
            return;
        }

        cartSummary.classList.remove('hidden');

        const totalItems = items.reduce((sum, item) => sum + item.quantity, 0);
        const totalAmount = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        document.getElementById('cartItemCount').textContent = totalItems;
        document.getElementById('itemsPlural').textContent = totalItems === 1 ? '' : 's';
        document.getElementById('cartTotalAmount').textContent = totalAmount.toFixed(2);

        // Update checkout modal
        updateCheckoutSummary();
    }

    function updateCheckoutSummary() {
        const items = Object.values(cart);
        const checkoutItemsHtml = items.map(item =>
            `<div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px;">
                <span>${item.name} x${item.quantity}</span>
                <span style="font-weight: 700;">Rs. ${(item.price * item.quantity).toFixed(2)}</span>
            </div>`
        ).join('');

        document.getElementById('checkoutItems').innerHTML = checkoutItemsHtml;

        const total = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        document.getElementById('checkoutTotal').textContent = total.toFixed(2);
    }

    function openCheckoutModal() {
        updateCheckoutSummary();
        document.getElementById('checkoutModal').classList.add('open');
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').classList.remove('open');
        document.getElementById('customerName').value = '';
        document.getElementById('customerPhone').value = '';
        document.getElementById('successMessage').classList.remove('show');
    }

    async function submitOrder() {
        const items = Object.values(cart);
        if (items.length === 0) {
            alert('Please add items to your order');
            return;
        }

        const orderData = {
            customer_name: document.getElementById('customerName').value || null,
            customer_phone: document.getElementById('customerPhone').value || null,
            items: items.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                kitchen_notes: null
            }))
        };

        try {
            const response = await fetch(`/table/${tableId}/order/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById('successMessage').classList.add('show');
                setTimeout(() => {
                    cart = {};
                    updateCartUI();
                    closeCheckoutModal();
                    alert(`Order #${result.order_number} placed successfully!`);
                }, 2000);
            } else {
                alert('Error: ' + (result.message || 'Failed to place order'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error placing order: ' + error.message);
        }
    }

    function filterProducts(categoryId, btn) {
        document.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const productsContainer = document.getElementById('productsContainer');
        const filtered = categoryId === 0 ? allProducts : allProducts.filter(p => p.category_id == categoryId);

        productsContainer.innerHTML = filtered.map(product => `
            <div class="product-card" onclick="addToCart(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.price}, ${product.category_id})">
                <div class="product-image">
                    ${product.image ? `<img src="/storage/${product.image}" alt="${product.name}">` : '<i class="fas fa-utensils" style="color: #dc2626; font-size: 24px;"></i>'}
                </div>
                <div class="product-name">${product.name}</div>
                <div class="product-price">Rs. ${parseFloat(product.price).toFixed(2)}</div>
            </div>
        `).join('');
    }

    // CSRF token for forms
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const meta = document.createElement('meta');
        meta.name = 'csrf-token';
        meta.content = '{{ csrf_token() }}';
        document.head.appendChild(meta);
    }
</script>

</body>
</html>
