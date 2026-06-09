<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order History — Restaurant BYOB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #f1f5f9 0%, #e9eef5 100%); }

        .table-row {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            transition: background 0.2s;
        }
        .table-row:hover { background: #f8fafc; }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-dine_in { background: #dcfce7; color: #166534; }
        .badge-takeaway { background: #fef3c7; color: #92400e; }
        .badge-delivery { background: #dbeafe; color: #1e40af; }
        .badge-vip_room { background: #f3e8ff; color: #6b21a8; }

        .btn {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: #dc2626;
            color: #fff;
        }
        .btn-primary:hover { background: #b91c1c; }

        .btn-secondary {
            background: #e2e8f0;
            color: #1e293b;
        }
        .btn-secondary:hover { background: #cbd5e1; }

        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 24px;
        }

        .loader {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .loader.active { display: block; }
    </style>
</head>
<body>

    <!-- Navbar -->
    @include('layouts.navbar')

    <!-- Page content -->
    <div style="padding-top: 67px;">
        <div class="w-full px-6 py-8 max-w-screen-2xl mx-auto">

            <!-- Page header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Order History</h1>
                <p class="text-gray-600">View and reprint receipts from past orders</p>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg p-6 mb-6 border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" id="searchInput" placeholder="Order #, customer name, phone..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Type</label>
                        <select id="orderTypeFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="all">All Types</option>
                            <option value="dine_in">Dine In</option>
                            <option value="takeaway">Takeaway</option>
                            <option value="delivery">Delivery</option>
                            <option value="vip_room">VIP Room</option>
                        </select>
                    </div>
                    <div style="display: flex; align-items: flex-end; gap: 8px;">
                        <button onclick="loadOrders()" class="flex-1 btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button onclick="resetFilters()" class="flex-1 btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Orders table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <tr>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Order #</th>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Customer</th>
                                <th style="text-align: center; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Type</th>
                                <th style="text-align: right; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Total</th>
                                <th style="text-align: center; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Items</th>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Date</th>
                                <th style="text-align: center; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <tr style="height: 80px;">
                                <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 24px;"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Receipt Modal -->
    <div id="receiptModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1e293b;">Receipt</h3>
                <button onclick="closeReceiptModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="receiptContent" class="loader active">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #dc2626;"></i>
            </div>
        </div>
    </div>

    <script>
        function loadOrders() {
            const search = document.getElementById('searchInput').value;
            const orderType = document.getElementById('orderTypeFilter').value;
            const tbody = document.getElementById('ordersTableBody');

            tbody.innerHTML = '<tr style="height: 80px;"><td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;"><i class="fas fa-spinner fa-spin" style="font-size: 24px;"></i></td></tr>';

            let url = '/api/order-history?';
            if (search) url += `search=${encodeURIComponent(search)}&`;
            if (orderType !== 'all') url += `order_type=${orderType}&`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!Array.isArray(data)) {
                        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: #ef4444;">Error loading orders</td></tr>';
                        return;
                    }

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">No orders found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = data.map(order => `
                        <tr class="table-row">
                            <td style="padding: 12px 16px; font-weight: 600; color: #1e293b;">${order.order_number}</td>
                            <td style="padding: 12px 16px; color: #475569;">
                                <div>${order.customer_name}</div>
                                ${order.customer_phone ? `<div style="font-size: 12px; color: #94a3b8;">${order.customer_phone}</div>` : ''}
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <span class="badge badge-${order.order_type}">${order.order_type.replace('_', ' ')}</span>
                            </td>
                            <td style="padding: 12px 16px; text-align: right; font-weight: 600; color: #1e293b;">LKR ${parseFloat(order.total).toFixed(2)}</td>
                            <td style="padding: 12px 16px; text-align: center; color: #475569;">${order.items_count}</td>
                            <td style="padding: 12px 16px; color: #475569; font-size: 13px;">${order.created_at}</td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <button onclick="viewReceipt(${order.id})" class="btn btn-primary" style="font-size: 11px;">
                                    <i class="fas fa-receipt"></i> View
                                </button>
                            </td>
                        </tr>
                    `).join('');
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px; color: #ef4444;">Failed to load orders</td></tr>';
                });
        }

        function viewReceipt(orderId) {
            const modal = document.getElementById('receiptModal');
            const content = document.getElementById('receiptContent');

            modal.classList.add('active');
            content.classList.add('active');
            content.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #dc2626;"></i>';

            fetch(`/pos/order/${orderId}/receipt/reprint`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const itemsHtml = data.items.map(item => `
                            <tr>
                                <td style="padding: 8px; color: #475569;">${item.product_name}</td>
                                <td style="padding: 8px; text-align: right; color: #475569;">${item.quantity}</td>
                                <td style="padding: 8px; text-align: right; color: #475569;">LKR ${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td style="padding: 8px; text-align: right; color: #1e293b; font-weight: 600;">LKR ${parseFloat(item.subtotal).toFixed(2)}</td>
                            </tr>
                        `).join('');

                        content.classList.remove('active');
                        content.innerHTML = `
                            <div style="border-bottom: 2px dashed #e2e8f0; padding-bottom: 16px; margin-bottom: 16px;">
                                <h4 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">${data.order_number}</h4>
                                <p style="font-size: 13px; color: #475569;">
                                    ${data.customer_name}<br>
                                    ${data.order_type.replace('_', ' ')}<br>
                                    <span style="color: #94a3b8;">${data.printed_at}</span>
                                </p>
                            </div>

                            <table style="width: 100%; margin-bottom: 16px; font-size: 13px;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e2e8f0;">
                                        <th style="text-align: left; padding: 8px; font-weight: 600; color: #475569;">Item</th>
                                        <th style="text-align: right; padding: 8px; font-weight: 600; color: #475569;">Qty</th>
                                        <th style="text-align: right; padding: 8px; font-weight: 600; color: #475569;">Price</th>
                                        <th style="text-align: right; padding: 8px; font-weight: 600; color: #475569;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsHtml}
                                </tbody>
                            </table>

                            <div style="border-top: 2px dashed #e2e8f0; padding-top: 12px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #475569;">
                                    <span>Subtotal:</span>
                                    <span>LKR ${parseFloat(data.subtotal).toFixed(2)}</span>
                                </div>
                                ${data.discount_amount > 0 ? `
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: #475569;">
                                        <span>Discount:</span>
                                        <span>-LKR ${parseFloat(data.discount_amount).toFixed(2)}</span>
                                    </div>
                                ` : ''}
                                <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 16px; color: #1e293b;">
                                    <span>Total:</span>
                                    <span>LKR ${parseFloat(data.total).toFixed(2)}</span>
                                </div>
                                ${data.payment_method ? `
                                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e2e8f0; color: #475569; font-size: 12px;">
                                        <div>Payment: ${data.payment_method}</div>
                                        <div>Paid: LKR ${parseFloat(data.amount_paid).toFixed(2)}</div>
                                        ${data.change_amount > 0 ? `<div>Change: LKR ${parseFloat(data.change_amount).toFixed(2)}</div>` : ''}
                                    </div>
                                ` : ''}
                            </div>

                            <div style="margin-top: 20px; display: flex; gap: 8px;">
                                <button onclick="window.print()" class="flex-1 btn btn-primary">
                                    <i class="fas fa-print"></i> Print
                                </button>
                                <button onclick="closeReceiptModal()" class="flex-1 btn btn-secondary">
                                    <i class="fas fa-times"></i> Close
                                </button>
                            </div>
                        `;
                    } else {
                        content.classList.remove('active');
                        content.innerHTML = `<p style="color: #ef4444;">${data.message || 'Error loading receipt'}</p>`;
                    }
                })
                .catch(err => {
                    console.error(err);
                    content.classList.remove('active');
                    content.innerHTML = '<p style="color: #ef4444;">Failed to load receipt</p>';
                });
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').classList.remove('active');
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('orderTypeFilter').value = 'all';
            loadOrders();
        }

        document.getElementById('receiptModal').addEventListener('click', (e) => {
            if (e.target.id === 'receiptModal') closeReceiptModal();
        });

        loadOrders();
    </script>

</body>
</html>
