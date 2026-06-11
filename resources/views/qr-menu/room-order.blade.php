<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Order for Room {{ $room->room_number }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

        .header {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .room-badge {
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

        .product-card:active { transform: scale(0.95); }
        .product-card:hover {
            border-color: #7c3aed;
            box-shadow: 0 4px 12px rgba(124,58,237,0.15);
        }

        .product-image {
            width: 100%;
            height: 80px;
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            overflow: hidden;
        }

        .product-image img { width: 100%; height: 100%; object-fit: cover; }

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

        .product-price { font-size: 14px; font-weight: 900; color: #7c3aed; }

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

        .category-pill.active { background: #7c3aed; color: white; border-color: #7c3aed; }

        .cart-summary {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            background: white;
            border-top: 2px solid #e2e8f0;
            padding: 16px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .cart-summary.hidden { display: none; }
        .cart-total { flex: 1; }
        .cart-count { font-size: 12px; color: #64748b; }
        .cart-amount { font-size: 18px; font-weight: 900; color: #7c3aed; }

        .btn-order {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
        }

        .btn-order:hover { background: #6d28d9; }
        .btn-order:disabled { background: #cbd5e1; cursor: not-allowed; }

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

        .modal-overlay.open { display: flex; }

        .modal-box {
            background: white;
            border-radius: 16px;
            padding: 24px;
            max-width: 400px;
            width: 92%;
            box-shadow: 0 24px 64px rgba(0,0,0,0.2);
        }

        .modal-header { font-size: 18px; font-weight: 800; color: #0f172a; margin-bottom: 16px; }

        .success-message {
            display: none;
            background: #f0fdf4;
            border: 2px solid #16a34a;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            margin-top: 16px;
        }

        .success-message.show { display: block; }

        @media (max-width: 480px) {
            .product-grid { grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; padding: 12px; }
            .header { padding: 16px; }
            .cart-summary { padding: 12px; }
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
        <div class="room-badge">
            <i class="fas fa-door-open" style="margin-right: 4px;"></i>
            Room {{ $room->room_number }}
        </div>
    </div>
</div>

@if(!$hasActiveBooking)
    <!-- Room not currently open -->
    <div style="max-width: 480px; margin: 64px auto; padding: 32px; text-align: center;">
        <i class="fas fa-clock" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
        <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Room not open for ordering</h2>
        <p style="font-size: 14px; color: #64748b;">
            This room does not have an active booking yet. Please contact reception to start your booking,
            then scan again to order.
        </p>
    </div>
@else
    <!-- Categories -->
    <div class="category-pills">
        <button class="category-pill active" data-category="0" onclick="filterProducts(0, this)">All Items</button>
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
                        <i class="fas fa-utensils" style="color: #7c3aed; font-size: 24px;"></i>
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
                <i class="fas fa-clipboard-list" style="color: #7c3aed; margin-right: 8px;"></i>Confirm Your Order
            </div>

            <div style="background: #f8fafc; border-radius: 8px; padding: 12px; margin-bottom: 16px;">
                <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 8px;">Order Summary — Room {{ $room->room_number }}</div>
                <div id="checkoutItems" style="max-height: 200px; overflow-y: auto;"></div>
                <div style="border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 8px;">
                    <div style="display: flex; justify-content: space-between; font-weight: 700; color: #7c3aed; font-size: 14px;">
                        <span>Total:</span>
                        <span>Rs. <span id="checkoutTotal">0.00</span></span>
                    </div>
                </div>
                <p style="font-size: 11px; color: #94a3b8; margin-top: 8px;">These items will be added to your room bill.</p>
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
                <div style="font-size: 13px; color: #166534;">Your order was added to your room bill and sent to the kitchen.</div>
            </div>
        </div>
    </div>

    <script>
        const roomId = {{ $room->id }};
        let cart = {};
        let allProducts = {!! json_encode($products) !!};

        function addToCart(productId, productName, price, categoryId) {
            if (!cart[productId]) {
                cart[productId] = { id: productId, name: productName, price: price, quantity: 0, categoryId: categoryId };
            }
            cart[productId].quantity++;
            updateCartUI();
        }

        function updateCartUI() {
            const items = Object.values(cart);
            const cartSummary = document.getElementById('cartSummary');
            if (items.length === 0) { cartSummary.classList.add('hidden'); return; }
            cartSummary.classList.remove('hidden');

            const totalItems = items.reduce((sum, item) => sum + item.quantity, 0);
            const totalAmount = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            document.getElementById('cartItemCount').textContent = totalItems;
            document.getElementById('itemsPlural').textContent = totalItems === 1 ? '' : 's';
            document.getElementById('cartTotalAmount').textContent = totalAmount.toFixed(2);
            updateCheckoutSummary();
        }

        function updateCheckoutSummary() {
            const items = Object.values(cart);
            document.getElementById('checkoutItems').innerHTML = items.map(item =>
                `<div style="display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px;">
                    <span>${item.name} x${item.quantity}</span>
                    <span style="font-weight: 700;">Rs. ${(item.price * item.quantity).toFixed(2)}</span>
                </div>`
            ).join('');
            const total = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            document.getElementById('checkoutTotal').textContent = total.toFixed(2);
        }

        function openCheckoutModal() {
            updateCheckoutSummary();
            document.getElementById('checkoutModal').classList.add('open');
        }

        function closeCheckoutModal() {
            document.getElementById('checkoutModal').classList.remove('open');
            document.getElementById('successMessage').classList.remove('show');
        }

        async function submitOrder() {
            const items = Object.values(cart);
            if (items.length === 0) { alert('Please add items to your order'); return; }

            const orderData = {
                items: items.map(item => ({ product_id: item.id, quantity: item.quantity, kitchen_notes: null }))
            };

            try {
                const response = await fetch(`/room/${roomId}/order/submit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
                        alert(`Order #${result.order_number} added to Room ${result.room_number} bill!`);
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
                        ${product.image ? `<img src="/storage/${product.image}" alt="${product.name}">` : '<i class="fas fa-utensils" style="color: #7c3aed; font-size: 24px;"></i>'}
                    </div>
                    <div class="product-name">${product.name}</div>
                    <div class="product-price">Rs. ${parseFloat(product.price).toFixed(2)}</div>
                </div>
            `).join('');
        }
    </script>
@endif

</body>
</html>
