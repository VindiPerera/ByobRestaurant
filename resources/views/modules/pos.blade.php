<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POS & Billing - Restaurant BYOB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .pos-grid { display: grid; grid-template-columns: 280px 1fr 360px; gap: 0; height: 100vh; }
        @media (max-width: 1400px) { .pos-grid { grid-template-columns: 1fr; } }
        .tables-panel { background: white; border-right: 1px solid #e5e7eb; overflow-y: auto; }
        .menu-panel { background: white; overflow-y: auto; }
        .bill-panel { background: #fafafa; border-left: 1px solid #e5e7eb; overflow-y: auto; display: flex; flex-direction: column; }
        .table-card { cursor: pointer; border: 2px solid transparent; transition: all 0.2s; }
        .table-card:hover { border-color: #dc2626; transform: translateY(-2px); }
        .table-card.active { border-color: #dc2626; background: #fef2f2; }
        .table-card.available { background: #f0fdf4; border: 2px solid #22c55e; }
        .table-card.occupied { background: #fef2f2; border: 2px solid #dc2626; }
        .table-card.reserved { background: #fef3c7; border: 2px solid #f59e0b; }
        .table-card.cleaning { background: #f3f4f6; border: 2px solid #6b7280; }
        .category-pill { @apply px-4 py-2 rounded-full text-sm font-medium cursor-pointer transition-all; border: 2px solid #e5e7eb; }
        .category-pill.active { @apply bg-red-600 text-white border-red-600; }
        .product-card { @apply p-4 border border-gray-200 rounded-lg cursor-pointer transition-all hover:shadow-md hover:border-red-600; }
        .bill-item { @apply py-3 border-b border-gray-200 flex items-center justify-between text-sm; }
        .btn-primary { @apply bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors; }
        .btn-secondary { @apply bg-gray-200 hover:bg-gray-300 text-gray-900 px-4 py-2 rounded-lg font-medium transition-colors; }
        .modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center; }
        .modal.open { display: flex; }
        .modal-content { background: white; border-radius: 12px; padding: 24px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="pos-grid">
        <!-- TABLES PANEL -->
        <div class="tables-panel">
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4 z-10">
                <h2 class="font-bold text-lg text-gray-900 mb-4">Tables</h2>
                <div class="flex gap-2 mb-4">
                    <button onclick="filterTables('all')" class="filter-btn active text-xs font-medium px-3 py-1 rounded border border-gray-300 cursor-pointer" data-filter="all">All</button>
                    <button onclick="filterTables('main')" class="filter-btn text-xs font-medium px-3 py-1 rounded border border-gray-300 cursor-pointer" data-filter="main">Main</button>
                    <button onclick="filterTables('vip')" class="filter-btn text-xs font-medium px-3 py-1 rounded border border-gray-300 cursor-pointer" data-filter="vip">VIP</button>
                </div>
            </div>

            <div class="p-4 space-y-3" id="tablesContainer">
                <!-- Tables loaded via JS -->
            </div>
        </div>

        <!-- MENU PANEL -->
        <div class="menu-panel">
            <div class="sticky top-0 bg-white border-b border-gray-200 p-4 z-10">
                <div class="flex gap-3 mb-4">
                    <div class="flex-1">
                        <input type="text" id="searchInput" placeholder="Search by name or barcode..." class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <select id="orderTypeSelect" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium bg-white">
                        <option value="dine_in">Dine In</option>
                        <option value="takeaway">Takeaway</option>
                        <option value="delivery">Delivery</option>
                        <option value="vip_room">VIP Room</option>
                    </select>
                </div>

                <div class="flex gap-2 pb-4 border-b border-gray-200 overflow-x-auto" id="categoriesContainer">
                    <button class="category-pill active" data-category="0">All</button>
                </div>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-3 gap-3" id="productsContainer">
                    <!-- Products loaded via JS -->
                </div>
            </div>
        </div>

        <!-- BILL PANEL -->
        <div class="bill-panel">
            <div class="bg-white border-b border-gray-200 p-4 sticky top-0">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-gray-900">Order</h3>
                    <button onclick="loadHeldOrders()" class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded hover:bg-amber-200">
                        <i class="fas fa-pause mr-1"></i><span id="heldCount">0</span> Held
                    </button>
                </div>
                <div class="text-sm text-gray-600">
                    <div id="selectedTable" class="font-medium text-gray-900">No table selected</div>
                    <div id="orderType" class="text-xs text-gray-500">-</div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="p-4" id="billItems">
                    <p class="text-center text-gray-500 text-sm py-8">Select items to add to order</p>
                </div>
            </div>

            <div class="bg-white border-t border-gray-200 p-4 space-y-3">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span id="subtotalDisplay" class="font-medium">LKR 0.00</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <label class="text-xs">Discount:</label>
                        <div class="flex gap-2">
                            <select id="discountType" class="text-xs border border-gray-300 rounded px-2 py-1">
                                <option value="">None</option>
                                <option value="percentage">%</option>
                                <option value="fixed">Fixed</option>
                            </select>
                            <input type="number" id="discountValue" placeholder="0" class="w-20 text-xs border border-gray-300 rounded px-2 py-1" min="0">
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <span>Tax (10%):</span>
                        <span id="taxDisplay" class="font-medium">LKR 0.00</span>
                    </div>

                    <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-2 text-red-600">
                        <span>Total:</span>
                        <span id="totalDisplay">LKR 0.00</span>
                    </div>
                </div>

                <div id="orderControls" class="space-y-2">
                    <button onclick="completeOrder()" class="w-full btn-primary">
                        <i class="fas fa-credit-card mr-2"></i>Pay
                    </button>
                    <div class="flex gap-2">
                        <button onclick="holdCurrentOrder()" class="flex-1 btn-secondary">
                            <i class="fas fa-pause mr-1"></i>Hold
                        </button>
                        <button onclick="printKot()" class="flex-1 btn-secondary">
                            <i class="fas fa-print mr-1"></i>KOT
                        </button>
                        <button onclick="printBot()" class="flex-1 btn-secondary">
                            <i class="fas fa-wine-glass mr-1"></i>BOT
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Payment</h2>
                <button onclick="closeModal('paymentModal')" class="text-gray-500 hover:text-gray-700 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div class="flex gap-2 mb-4">
                    <button class="payment-method-btn flex-1 py-3 border-2 border-gray-300 rounded-lg font-medium cursor-pointer text-sm" data-method="cash">
                        <i class="fas fa-money-bill-wave mr-2"></i>Cash
                    </button>
                    <button class="payment-method-btn flex-1 py-3 border-2 border-gray-300 rounded-lg font-medium cursor-pointer text-sm" data-method="card">
                        <i class="fas fa-credit-card mr-2"></i>Card
                    </button>
                    <button class="payment-method-btn flex-1 py-3 border-2 border-gray-300 rounded-lg font-medium cursor-pointer text-sm" data-method="bank_transfer">
                        <i class="fas fa-university mr-2"></i>Bank
                    </button>
                    <button class="payment-method-btn flex-1 py-3 border-2 border-gray-300 rounded-lg font-medium cursor-pointer text-sm" data-method="mixed">
                        <i class="fas fa-plus-circle mr-2"></i>Mixed
                    </button>
                </div>

                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Total Amount</p>
                    <p class="text-3xl font-bold text-red-600" id="paymentTotal">LKR 0.00</p>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-900">Amount Paid</label>
                    <input type="number" id="amountPaid" class="w-full px-4 py-2 border border-gray-300 rounded-lg mt-1 text-lg" placeholder="0" step="0.01" min="0">
                </div>

                <div class="bg-green-50 p-3 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Change</p>
                    <p class="text-2xl font-bold text-green-600" id="changeDisplay">LKR 0.00</p>
                </div>

                <div class="flex gap-2">
                    <button onclick="closeModal('paymentModal')" class="flex-1 btn-secondary">Cancel</button>
                    <button id="confirmPayBtn" onclick="confirmPayment()" class="flex-1 btn-primary">Confirm Payment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- KOT MODAL -->
    <div id="kotModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Kitchen Order Ticket</h2>
                <button onclick="closeModal('kotModal')" class="text-gray-500 hover:text-gray-700 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm font-medium text-gray-900" id="kotOrderNumber">Order #</p>
                </div>

                <div id="kotItems" class="bg-white border border-gray-300 p-4 rounded-lg space-y-3 max-h-64 overflow-y-auto">
                    <!-- KOT items loaded via JS -->
                </div>

                <div class="flex gap-2">
                    <button onclick="closeModal('kotModal')" class="flex-1 btn-secondary">Close</button>
                    <button onclick="printKotReceipt()" class="flex-1 btn-primary">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- BOT MODAL -->
    <div id="botModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Bar Order Ticket</h2>
                <button onclick="closeModal('botModal')" class="text-gray-500 hover:text-gray-700 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-sm font-medium text-gray-900" id="botOrderNumber">Order #</p>
                </div>

                <div id="botItems" class="bg-white border border-gray-300 p-4 rounded-lg space-y-3 max-h-64 overflow-y-auto">
                    <!-- BOT items loaded via JS -->
                </div>

                <div class="flex gap-2">
                    <button onclick="closeModal('botModal')" class="flex-1 btn-secondary">Close</button>
                    <button onclick="printBotReceipt()" class="flex-1 btn-primary">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- HELD ORDERS MODAL -->
    <div id="heldOrdersModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Held Orders</h2>
                <button onclick="closeModal('heldOrdersModal')" class="text-gray-500 hover:text-gray-700 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="heldOrdersList" class="space-y-3 max-h-96 overflow-y-auto">
                <!-- Held orders loaded via JS -->
            </div>
        </div>
    </div>

    <script>
        let currentOrder = null;
        let currentTable = null;
        let allTables = [];
        let allProducts = [];
        let allCategories = @json($categories);
        let selectedPaymentMethod = 'cash';

        async function initPos() {
            await loadTables();
            await loadCategories();
            await loadProducts();
            loadHeldOrders();
            setupEventListeners();
        }

        async function loadTables() {
            const res = await fetch('{{ route("pos.tables") }}');
            allTables = await res.json();
            renderTables();
        }

        async function loadProducts(search = '', categoryId = 0) {
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (categoryId > 0) params.append('category_id', categoryId);

            const res = await fetch(`{{ route("pos.products") }}?${params}`);
            allProducts = await res.json();
            renderProducts();
        }

        function loadCategories() {
            const container = document.getElementById('categoriesContainer');
            container.innerHTML = '<button class="category-pill active" data-category="0">All</button>';

            allCategories.forEach(cat => {
                const btn = document.createElement('button');
                btn.className = 'category-pill';
                btn.textContent = cat.name;
                btn.setAttribute('data-category', cat.id);
                btn.onclick = () => selectCategory(cat.id);
                container.appendChild(btn);
            });
        }

        function renderTables() {
            const container = document.getElementById('tablesContainer');
            const filter = document.querySelector('.filter-btn.active')?.getAttribute('data-filter') || 'all';

            container.innerHTML = allTables
                .filter(t => filter === 'all' || t.section === filter)
                .map(table => `
                    <div class="table-card ${table.status} p-3 rounded-lg text-center ${currentTable?.id === table.id ? 'active' : ''}"
                         onclick="selectTable(${table.id})">
                        <div class="font-bold text-lg text-gray-900">${table.table_number}</div>
                        <div class="text-xs text-gray-600">Cap: ${table.capacity}</div>
                        ${table.has_order ? `<div class="text-xs font-medium text-red-600 mt-1">📦 ${table.order_items_count}</div>` : ''}
                    </div>
                `).join('');
        }

        function renderProducts() {
            const container = document.getElementById('productsContainer');
            if (allProducts.length === 0) {
                container.innerHTML = '<p class="col-span-3 text-center text-gray-500 py-8">No products found</p>';
                return;
            }

            container.innerHTML = allProducts.map(product => `
                <div class="product-card" onclick="addProductToOrder(${product.id}, '${product.name}', ${product.price})">
                    <div class="h-20 bg-gray-100 rounded-lg flex items-center justify-center mb-2">
                        <i class="fas fa-utensils text-gray-400 text-2xl"></i>
                    </div>
                    <p class="font-medium text-gray-900 text-sm line-clamp-2">${product.name}</p>
                    <p class="text-red-600 font-bold text-lg mt-1">LKR ${product.price.toFixed(2)}</p>
                </div>
            `).join('');
        }

        async function selectTable(tableId) {
            const table = allTables.find(t => t.id === tableId);
            if (!table) return;

            currentTable = table;

            if (table.has_order) {
                currentOrder = { id: table.order_id };
                const res = await fetch(`{{ route("pos.order.show", ":id") }}`.replace(':id', table.order_id));
                const order = await res.json();
                currentOrder = order;
            } else {
                const res = await fetch('{{ route("pos.order.create") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        table_id: tableId,
                        order_type: document.getElementById('orderTypeSelect').value
                    })
                });
                const data = await res.json();
                currentOrder = { id: data.order_id, items: [], subtotal: 0, tax_amount: 0, total: 0, discount_amount: 0 };
            }

            renderTableView();
            renderBill();
            renderTables();
        }

        function renderTableView() {
            document.getElementById('selectedTable').textContent = `Table ${currentTable.table_number} - ${currentTable.name}`;
            document.getElementById('orderType').textContent = 'Dine In Order';
        }

        async function addProductToOrder(productId, productName, price) {
            if (!currentOrder) {
                alert('Please select a table first');
                return;
            }

            const res = await fetch(`{{ route("pos.item.add", ":id") }}`.replace(':id', currentOrder.id), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            });

            const data = await res.json();
            if (data.success) {
                await loadCurrentOrder();
                renderBill();
            }
        }

        async function loadCurrentOrder() {
            if (!currentOrder?.id) return;
            const res = await fetch(`{{ route("pos.order.show", ":id") }}`.replace(':id', currentOrder.id));
            currentOrder = await res.json();
        }

        function renderBill() {
            if (!currentOrder) {
                document.getElementById('billItems').innerHTML = '<p class="text-center text-gray-500 text-sm py-8">Select items to add to order</p>';
                return;
            }

            const itemsHtml = currentOrder.items.map(item => `
                <div class="bill-item">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">${item.product_name}</p>
                        <p class="text-xs text-gray-500">LKR ${item.unit_price.toFixed(2)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="decreaseQty(${item.id})" class="w-6 h-6 border border-gray-300 rounded text-xs hover:bg-gray-100">-</button>
                        <span class="w-6 text-center text-sm font-medium">${item.quantity}</span>
                        <button onclick="increaseQty(${item.id})" class="w-6 h-6 border border-gray-300 rounded text-xs hover:bg-gray-100">+</button>
                        <button onclick="removeItem(${item.id})" class="text-red-600 hover:text-red-700 text-sm ml-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="w-20 text-right">
                        <p class="font-medium text-gray-900">LKR ${item.subtotal.toFixed(2)}</p>
                    </div>
                </div>
            `).join('');

            const subtotal = currentOrder.subtotal || 0;
            const discount = currentOrder.discount_amount || 0;
            const tax = currentOrder.tax_amount || 0;
            const total = currentOrder.total || 0;

            document.getElementById('billItems').innerHTML = itemsHtml || '<p class="text-center text-gray-500 text-sm py-8">No items in order</p>';
            document.getElementById('subtotalDisplay').textContent = `LKR ${subtotal.toFixed(2)}`;
            document.getElementById('taxDisplay').textContent = `LKR ${tax.toFixed(2)}`;
            document.getElementById('totalDisplay').textContent = `LKR ${total.toFixed(2)}`;
            document.getElementById('paymentTotal').textContent = `LKR ${total.toFixed(2)}`;
        }

        async function increaseQty(itemId) {
            const item = currentOrder.items.find(i => i.id === itemId);
            if (!item) return;

            const res = await fetch(`{{ route("pos.item.update", [":id", ":item"]) }}`.replace(':id', currentOrder.id).replace(':item', itemId), {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ quantity: item.quantity + 1 })
            });

            if ((await res.json()).success) {
                await loadCurrentOrder();
                renderBill();
            }
        }

        async function decreaseQty(itemId) {
            const item = currentOrder.items.find(i => i.id === itemId);
            if (!item || item.quantity <= 1) return;

            const res = await fetch(`{{ route("pos.item.update", [":id", ":item"]) }}`.replace(':id', currentOrder.id).replace(':item', itemId), {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ quantity: item.quantity - 1 })
            });

            if ((await res.json()).success) {
                await loadCurrentOrder();
                renderBill();
            }
        }

        async function removeItem(itemId) {
            const res = await fetch(`{{ route("pos.item.remove", [":id", ":item"]) }}`.replace(':id', currentOrder.id).replace(':item', itemId), {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            if ((await res.json()).success) {
                await loadCurrentOrder();
                renderBill();
            }
        }

        async function completeOrder() {
            if (!currentOrder?.id || currentOrder.items.length === 0) {
                alert('Add items to order first');
                return;
            }
            openModal('paymentModal');
        }

        async function confirmPayment() {
            const paymentMethod = selectedPaymentMethod;
            const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
            const discountType = document.getElementById('discountType').value;
            const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;

            if (amountPaid === 0) {
                alert('Enter amount paid');
                return;
            }

            const res = await fetch(`{{ route("pos.order.complete", ":id") }}`.replace(':id', currentOrder.id), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    payment_method: paymentMethod,
                    amount_paid: amountPaid,
                    discount_type: discountType || null,
                    discount_value: discountValue || 0
                })
            });

            const data = await res.json();
            if (data.success) {
                alert(`Payment Complete!\nTotal: LKR ${data.total.toFixed(2)}\nChange: LKR ${data.change.toFixed(2)}`);
                closeModal('paymentModal');
                currentOrder = null;
                currentTable = null;
                await loadTables();
                renderTables();
                document.getElementById('billItems').innerHTML = '<p class="text-center text-gray-500 text-sm py-8">Select items to add to order</p>';
                document.getElementById('selectedTable').textContent = 'No table selected';
            }
        }

        async function printKot() {
            if (!currentOrder?.id) return;
            const res = await fetch(`{{ route("pos.order.kot", ":id") }}`.replace(':id', currentOrder.id), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            const data = await res.json();
            document.getElementById('kotOrderNumber').textContent = `Order #${data.order_number}`;
            document.getElementById('kotItems').innerHTML = data.items.map(item => `
                <div class="border-b pb-2">
                    <p class="font-medium text-gray-900">${item.product_name} x${item.quantity}</p>
                    ${item.kitchen_notes ? `<p class="text-xs text-gray-600 mt-1">Note: ${item.kitchen_notes}</p>` : ''}
                </div>
            `).join('');

            openModal('kotModal');
        }

        async function printBot() {
            if (!currentOrder?.id) return;
            const res = await fetch(`{{ route("pos.order.bot", ":id") }}`.replace(':id', currentOrder.id), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            const data = await res.json();
            document.getElementById('botOrderNumber').textContent = `Order #${data.order_number}`;
            document.getElementById('botItems').innerHTML = data.items.map(item => `
                <div class="border-b pb-2">
                    <p class="font-medium text-gray-900">${item.product_name} x${item.quantity}</p>
                    ${item.kitchen_notes ? `<p class="text-xs text-gray-600 mt-1">Note: ${item.kitchen_notes}</p>` : ''}
                </div>
            `).join('');

            openModal('botModal');
        }

        async function holdCurrentOrder() {
            if (!currentOrder?.id) return;
            const res = await fetch(`{{ route("pos.order.hold", ":id") }}`.replace(':id', currentOrder.id), {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            if ((await res.json()).success) {
                currentOrder = null;
                currentTable = null;
                await loadTables();
                renderTables();
                document.getElementById('billItems').innerHTML = '<p class="text-center text-gray-500 text-sm py-8">Select items to add to order</p>';
                document.getElementById('selectedTable').textContent = 'No table selected';
                loadHeldOrders();
            }
        }

        async function loadHeldOrders() {
            const res = await fetch('{{ route("pos.held") }}');
            const orders = await res.json();
            document.getElementById('heldCount').textContent = orders.length;

            const list = document.getElementById('heldOrdersList');
            if (orders.length === 0) {
                list.innerHTML = '<p class="text-center text-gray-500 py-8">No held orders</p>';
                return;
            }

            list.innerHTML = orders.map(order => `
                <div class="p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50" onclick="resumeOrder(${order.id})">
                    <p class="font-medium text-gray-900">${order.order_number}</p>
                    <p class="text-sm text-gray-600">Table: ${order.table_number || 'N/A'} | Items: ${order.items_count}</p>
                    <p class="text-sm font-medium text-red-600 mt-1">LKR ${order.total.toFixed(2)}</p>
                </div>
            `).join('');
        }

        async function resumeOrder(orderId) {
            currentOrder = { id: orderId };
            const res = await fetch(`{{ route("pos.order.show", ":id") }}`.replace(':id', orderId));
            const order = await res.json();
            currentOrder = order;

            if (order.table_id) {
                currentTable = allTables.find(t => t.id === order.table_id);
                if (currentTable) renderTableView();
            }

            renderBill();
            closeModal('heldOrdersModal');
            await loadTables();
            renderTables();
        }

        function selectCategory(categoryId) {
            document.querySelectorAll('.category-pill').forEach(btn => btn.classList.remove('active'));
            document.querySelector(`[data-category="${categoryId}"]`).classList.add('active');

            const search = document.getElementById('searchInput').value;
            loadProducts(search, categoryId);
        }

        function filterTables(section) {
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active', 'bg-red-100', 'text-red-700'));
            event.target.classList.add('active', 'bg-red-100', 'text-red-700');
            renderTables();
        }

        function setupEventListeners() {
            document.getElementById('searchInput').addEventListener('input', (e) => {
                const categoryId = document.querySelector('.category-pill.active')?.getAttribute('data-category') || 0;
                loadProducts(e.target.value, categoryId);
            });

            document.getElementById('discountValue').addEventListener('input', () => {
                // Update display when discount changes
            });

            document.getElementById('amountPaid').addEventListener('input', (e) => {
                const total = parseFloat(document.getElementById('paymentTotal').textContent.replace('LKR ', '')) || 0;
                const change = parseFloat(e.target.value) - total;
                document.getElementById('changeDisplay').textContent = `LKR ${Math.max(0, change).toFixed(2)}`;
            });

            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    document.querySelectorAll('.payment-method-btn').forEach(b => b.classList.remove('bg-red-600', 'text-white', 'border-red-600'));
                    e.currentTarget.classList.add('bg-red-600', 'text-white', 'border-red-600');
                    selectedPaymentMethod = e.currentTarget.getAttribute('data-method');
                });
            });

            // Set first payment method as active
            document.querySelector('[data-method="cash"]').click();
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('open');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('open');
        }

        function printKotReceipt() {
            window.print();
        }

        function printBotReceipt() {
            window.print();
        }

        // Initialize on load
        window.addEventListener('load', initPos);
    </script>
</body>
</html>
