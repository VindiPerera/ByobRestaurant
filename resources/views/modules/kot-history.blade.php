<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KOT History — Restaurant BYOB</title>
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

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-kitchen { background: #dbeafe; color: #1e40af; }
        .badge-bar { background: #fef3c7; color: #92400e; }
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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">KOT & BOT History</h1>
                <p class="text-gray-600">View and reprint kitchen and bar orders</p>
            </div>

            <!-- Search -->
            <div class="bg-white rounded-lg p-6 mb-6 border border-gray-200">
                <div class="flex gap-4">
                    <input type="text" id="searchInput" placeholder="Search by order # or customer name..."
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    <button onclick="loadKotHistory()" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <button onclick="resetFilters()" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </div>

            <!-- KOT History table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <tr>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Order #</th>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Customer</th>
                                <th style="text-align: center; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Items</th>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">KOT Printed</th>
                                <th style="text-align: left; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">BOT Printed</th>
                                <th style="text-align: center; padding: 12px 16px; font-weight: 600; font-size: 13px; color: #475569;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="kotTableBody">
                            <tr style="height: 80px;">
                                <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                                    <i class="fas fa-spinner fa-spin" style="font-size: 24px;"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- KOT/BOT Modal -->
    <div id="kotModal" class="modal">
        <div class="modal-content">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 id="kotModalTitle" style="font-size: 18px; font-weight: 700; color: #1e293b;">Kitchen Order</h3>
                <button onclick="closeKotModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div id="kotContent" style="text-align: center; padding: 20px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #dc2626;"></i>
            </div>
        </div>
    </div>

    <script>
        function loadKotHistory() {
            const search = document.getElementById('searchInput').value;
            const tbody = document.getElementById('kotTableBody');

            tbody.innerHTML = '<tr style="height: 80px;"><td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;"><i class="fas fa-spinner fa-spin" style="font-size: 24px;"></i></td></tr>';

            let url = '/api/kot-history';
            if (search) url += `?search=${encodeURIComponent(search)}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!Array.isArray(data)) {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: #ef4444;">Error loading KOT history</td></tr>';
                        return;
                    }

                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">No KOT records found</td></tr>';
                        return;
                    }

                    tbody.innerHTML = data.map(order => {
                        const kitchenItems = order.items.filter(i => !i.is_bar_item);
                        const barItems = order.items.filter(i => i.is_bar_item);

                        return `
                            <tr class="table-row">
                                <td style="padding: 12px 16px; font-weight: 600; color: #1e293b;">${order.order_number}</td>
                                <td style="padding: 12px 16px; color: #475569;">${order.customer_name}</td>
                                <td style="padding: 12px 16px; text-align: center; color: #475569;">${order.items_count}</td>
                                <td style="padding: 12px 16px; color: #475569; font-size: 13px;">${order.kot_printed_at || '-'}</td>
                                <td style="padding: 12px 16px; color: #475569; font-size: 13px;">${order.bot_printed_at || '-'}</td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    <div style="display: flex; gap: 4px; justify-content: center; flex-wrap: wrap;">
                                        ${kitchenItems.length > 0 ? `
                                            <button onclick="viewKot(${order.id})" class="btn btn-primary" style="font-size: 11px;">
                                                <i class="fas fa-print"></i> KOT
                                            </button>
                                        ` : ''}
                                        ${barItems.length > 0 ? `
                                            <button onclick="viewBot(${order.id})" class="btn btn-primary" style="font-size: 11px;">
                                                <i class="fas fa-print"></i> BOT
                                            </button>
                                        ` : ''}
                                    </div>
                                </td>
                            </tr>
                        `;
                    }).join('');
                })
                .catch(err => {
                    console.error(err);
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px; color: #ef4444;">Failed to load KOT history</td></tr>';
                });
        }

        function viewKot(orderId) {
            const modal = document.getElementById('kotModal');
            const content = document.getElementById('kotContent');
            const title = document.getElementById('kotModalTitle');

            modal.classList.add('active');
            title.textContent = 'Kitchen Order (KOT)';
            content.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #dc2626;"></i>';

            fetch(`/pos/order/${orderId}/kot/reprint`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const itemsHtml = data.items.map(item => `
                            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                <div style="text-align: left;">
                                    <div style="font-weight: 600; color: #1e293b;">${item.product_name}</div>
                                    ${item.kitchen_notes ? `<div style="font-size: 12px; color: #ef4444; margin-top: 4px;">Notes: ${item.kitchen_notes}</div>` : ''}
                                </div>
                                <div style="font-weight: 600; color: #1e293b; min-width: 40px; text-align: right;">x${item.quantity}</div>
                            </div>
                        `).join('');

                        content.innerHTML = `
                            <div style="border-bottom: 2px dashed #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
                                <h4 style="font-size: 14px; font-weight: 700; color: #1e293b;">${data.order_number}</h4>
                                <p style="font-size: 12px; color: #475569;">${data.customer_name}</p>
                            </div>

                            <div style="margin-bottom: 16px;">
                                ${itemsHtml}
                            </div>

                            <div style="border-top: 2px dashed #e2e8f0; padding-top: 12px; margin-top: 12px;">
                                <button onclick="window.print()" class="w-full btn btn-primary" style="justify-content: center;">
                                    <i class="fas fa-print"></i> Print KOT
                                </button>
                                <button onclick="closeKotModal()" class="w-full btn btn-secondary" style="justify-content: center; margin-top: 8px;">
                                    <i class="fas fa-times"></i> Close
                                </button>
                            </div>
                        `;
                    } else {
                        content.innerHTML = `<p style="color: #ef4444;">${data.message || 'Error loading KOT'}</p>`;
                    }
                })
                .catch(err => {
                    console.error(err);
                    content.innerHTML = '<p style="color: #ef4444;">Failed to load KOT</p>';
                });
        }

        function viewBot(orderId) {
            const modal = document.getElementById('kotModal');
            const content = document.getElementById('kotContent');
            const title = document.getElementById('kotModalTitle');

            modal.classList.add('active');
            title.textContent = 'Bar Order (BOT)';
            content.innerHTML = '<i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #dc2626;"></i>';

            fetch(`/pos/order/${orderId}/bot/reprint`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const itemsHtml = data.items.map(item => `
                            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e2e8f0;">
                                <div style="text-align: left;">
                                    <div style="font-weight: 600; color: #1e293b;">${item.product_name}</div>
                                    ${item.kitchen_notes ? `<div style="font-size: 12px; color: #ef4444; margin-top: 4px;">Notes: ${item.kitchen_notes}</div>` : ''}
                                </div>
                                <div style="font-weight: 600; color: #1e293b; min-width: 40px; text-align: right;">x${item.quantity}</div>
                            </div>
                        `).join('');

                        content.innerHTML = `
                            <div style="border-bottom: 2px dashed #e2e8f0; padding-bottom: 12px; margin-bottom: 16px;">
                                <h4 style="font-size: 14px; font-weight: 700; color: #1e293b;">${data.order_number}</h4>
                                <p style="font-size: 12px; color: #475569;">${data.customer_name}</p>
                            </div>

                            <div style="margin-bottom: 16px;">
                                ${itemsHtml}
                            </div>

                            <div style="border-top: 2px dashed #e2e8f0; padding-top: 12px; margin-top: 12px;">
                                <button onclick="window.print()" class="w-full btn btn-primary" style="justify-content: center;">
                                    <i class="fas fa-print"></i> Print BOT
                                </button>
                                <button onclick="closeKotModal()" class="w-full btn btn-secondary" style="justify-content: center; margin-top: 8px;">
                                    <i class="fas fa-times"></i> Close
                                </button>
                            </div>
                        `;
                    } else {
                        content.innerHTML = `<p style="color: #ef4444;">${data.message || 'Error loading BOT'}</p>`;
                    }
                })
                .catch(err => {
                    console.error(err);
                    content.innerHTML = '<p style="color: #ef4444;">Failed to load BOT</p>';
                });
        }

        function closeKotModal() {
            document.getElementById('kotModal').classList.remove('active');
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            loadKotHistory();
        }

        document.getElementById('kotModal').addEventListener('click', (e) => {
            if (e.target.id === 'kotModal') closeKotModal();
        });

        loadKotHistory();
    </script>

</body>
</html>
