@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div>
    <!-- Page header -->
    <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Reports</h1>
            <p class="text-gray-600 mt-2">Business analytics and sales overview</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-sm text-gray-500 font-medium">
                <i class="fas fa-clock mr-1"></i>As of {{ now()->format('d M Y, H:i') }}
            </span>
            <div class="flex gap-2">
                <a href="{{ route('reports.export.sales') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                    <i class="fas fa-download"></i> Sales PDF
                </a>
                <a href="{{ route('reports.export.products') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition text-sm font-medium">
                    <i class="fas fa-download"></i> Products PDF
                </a>
                <a href="{{ route('reports.export.combined') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                    <i class="fas fa-download"></i> Complete PDF
                </a>
            </div>
        </div>
    </div>

    <!-- ── SUMMARY CARDS (7 cards) ── -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-7 gap-4 mb-8">

        <!-- Total Revenue -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Total Revenue</p>
            <p class="text-xl font-bold text-gray-900">LKR {{ number_format($totalRevenue, 2) }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-red-500"></div>
        </div>

        <!-- Today's Sales -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Today's Sales</p>
            <p class="text-xl font-bold text-gray-900">LKR {{ number_format($todaySales, 2) }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-amber-400"></div>
        </div>

        <!-- This Month -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">This Month</p>
            <p class="text-xl font-bold text-gray-900">LKR {{ number_format($monthRevenue, 2) }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-teal-400"></div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Completed Orders</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($totalOrders) }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-blue-400"></div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Avg. Order Value</p>
            <p class="text-xl font-bold text-gray-900">LKR {{ number_format($avgOrderValue, 2) }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-purple-400"></div>
        </div>

        <!-- Top Selling Product -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Top Product</p>
            <p class="text-base font-bold text-gray-900 truncate" title="{{ $topProduct }}">{{ $topProduct }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-green-400"></div>
        </div>

        <!-- Pending Sales -->
        <div class="bg-amber-50 rounded-2xl p-5 border border-amber-200 shadow-sm hover:shadow-md transition-shadow">
            <p class="text-xs font-semibold text-amber-600 uppercase tracking-wide mb-1">Pending Bills</p>
            <p class="text-xl font-bold text-amber-700">{{ $pendingCount }} <span class="text-sm font-normal">open</span></p>
            <p class="text-xs text-amber-600 mt-0.5">LKR {{ number_format($pendingTotal, 2) }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-amber-400"></div>
        </div>

    </div>

    <!-- ── PENDING SALES ── -->
    <div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-amber-100 flex items-center justify-between bg-amber-50">
            <h2 class="text-lg font-bold text-amber-800">
                <i class="fas fa-clock text-amber-500 mr-2"></i>Pending Sales
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-200 text-amber-800">{{ $pendingCount }} unsettled</span>
            </h2>
            <span class="text-sm font-semibold text-amber-700">Total: LKR {{ number_format($pendingTotal, 2) }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Order #</th>
                        <th class="px-4 py-3 text-left">Table</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Items</th>
                        <th class="px-4 py-3 text-right">Bill Total</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-right">Started</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pendingSales as $pending)
                    @php
                        $statusColors = [
                            'pending'   => 'bg-amber-100 text-amber-700',
                            'hold'      => 'bg-blue-100 text-blue-700',
                            'confirmed' => 'bg-green-100 text-green-700',
                        ];
                        $statusColor = $statusColors[$pending->status] ?? 'bg-gray-100 text-gray-600';
                        $itemList = $pending->items->map(fn($i) => $i->quantity . '× ' . $i->product_name)->implode(', ');
                    @endphp
                    <tr class="hover:bg-amber-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $pending->order_number }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $pending->table?->name ?? ($pending->table?->table_number ? 'T'.$pending->table->table_number : '—') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $pending->customer_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 capitalize">{{ str_replace('_', ' ', $pending->order_type) }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-xs truncate" title="{{ $itemList }}">
                            {{ $itemList ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">
                            LKR {{ number_format($pending->total, 2) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColor }}">
                                {{ ucfirst($pending->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-400 text-xs whitespace-nowrap">
                            {{ $pending->created_at->format('d M, H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-gray-400">No pending bills right now — all clear!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ── SALES REPORT SUMMARY ── -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">

        <!-- Header with Flatpickr date range filter -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <div class="flex items-center justify-between flex-wrap gap-4 mb-0">
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-receipt text-red-500 mr-2"></i>Sales Report
                    @if($from && $to)
                        <span class="ml-2 text-sm font-normal text-gray-400">{{ $from->format('d M Y') }} — {{ $to->format('d M Y') }}</span>
                    @else
                        <span class="ml-2 text-sm font-normal text-gray-400">All time</span>
                    @endif
                </h2>
                <form id="salesFilterForm" method="GET" action="{{ route('reports.sales') }}" class="flex items-center gap-2 flex-wrap">
                    <div class="relative flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-1.5 shadow-sm">
                        <i class="fas fa-calendar text-red-400 text-sm"></i>
                        <input id="dateRangePicker" type="text" placeholder="Select date range..."
                            class="text-sm text-gray-700 bg-transparent outline-none w-52 cursor-pointer"
                            readonly>
                        <input type="hidden" name="from" id="fromHidden" value="{{ $from ? $from->format('Y-m-d') : '' }}">
                        <input type="hidden" name="to"   id="toHidden"   value="{{ $to   ? $to->format('Y-m-d')   : '' }}">
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition shadow-sm">
                        <i class="fas fa-filter text-xs"></i> Filter
                    </button>
                    @if($from && $to)
                    <a href="{{ route('reports.index') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                        <i class="fas fa-xmark text-xs"></i> Clear
                    </a>
                    <a href="{{ route('reports.export.sales.range', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                        <i class="fas fa-download text-xs"></i> Print PDF
                    </a>
                    @endif
                    <button type="button" id="printSalesReceiptBtn" onclick="printSalesReceipt()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-800 text-white text-sm font-semibold rounded-xl hover:bg-gray-900 transition shadow-sm">
                        <i class="fas fa-print text-xs"></i> Print Receipt
                    </button>
                    <button type="button" id="printCategorySalesBtn" onclick="printCategorySales()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-teal-700 text-white text-sm font-semibold rounded-xl hover:bg-teal-800 transition shadow-sm">
                        <i class="fas fa-tags text-xs"></i> Category Sales
                    </button>
                </form>
            </div>
        </div>

        <!-- Summary stats bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100 border-b border-gray-100">
            <div class="px-6 py-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">{{ ($from && $to) ? 'Period Revenue' : 'Total Revenue' }}</p>
                <p class="text-xl font-bold text-gray-900">LKR {{ number_format($rangeRevenue, 2) }}</p>
            </div>
            <div class="px-6 py-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Orders</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($rangeCount) }}</p>
            </div>
            <div class="px-6 py-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Avg Order Value</p>
                <p class="text-xl font-bold text-gray-900">LKR {{ number_format($rangeAvg, 2) }}</p>
            </div>
            <div class="px-6 py-4 flex flex-wrap items-center gap-2">
                @foreach($rangePayments as $rp)
                @php $rpc = ['cash'=>'bg-green-100 text-green-700','card'=>'bg-blue-100 text-blue-700','bank_transfer'=>'bg-purple-100 text-purple-700','mixed'=>'bg-orange-100 text-orange-700'][$rp->payment_method] ?? 'bg-gray-100 text-gray-600'; @endphp
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full {{ $rpc }}">
                    {{ ucfirst(str_replace('_',' ',$rp->payment_method)) }}
                    <span class="font-normal opacity-75">{{ $rp->order_count }}</span>
                </span>
                @endforeach
            </div>
        </div>

        <!-- Sales table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">Order #</th>
                        <th class="px-4 py-3 text-left">Table</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-center">Payment</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                    @php
                        $pc = ['cash'=>'bg-green-100 text-green-700','card'=>'bg-blue-100 text-blue-700','bank_transfer'=>'bg-purple-100 text-purple-700','mixed'=>'bg-orange-100 text-orange-700'][$sale->payment_method] ?? 'bg-gray-100 text-gray-600';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $sale->order_number }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $sale->table?->name ?? ($sale->table?->table_number ? 'T'.$sale->table->table_number : '—') }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $sale->customer_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 capitalize text-xs">{{ str_replace('_',' ',$sale->order_type ?? '—') }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $pc }}">
                                {{ ucfirst(str_replace('_',' ',$sale->payment_method ?? '—')) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-900">LKR {{ number_format($sale->total, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-400 text-xs whitespace-nowrap">{{ $sale->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-400">No completed sales found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Data island for the thermal receipt printer (current page of results shown above) -->
        <script type="application/json" id="salesReceiptData">
            {!! json_encode([
                'from'    => $from ? $from->format('d M Y') : null,
                'to'      => $to   ? $to->format('d M Y')   : null,
                'revenue' => (float) $rangeRevenue,
                'count'   => (int) $rangeCount,
                'avg'     => (float) $rangeAvg,
                'sales'   => $sales->map(fn($sale) => [
                    'order_number' => $sale->order_number,
                    'table'        => $sale->table?->name ?? ($sale->table?->table_number ? 'T'.$sale->table->table_number : '-'),
                    'payment'      => ucfirst(str_replace('_', ' ', $sale->payment_method ?? '-')),
                    'total'        => (float) $sale->total,
                    'date'         => $sale->created_at->format('d M, H:i'),
                ])->values(),
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
        </script>

        <!-- Pagination -->
        @if($sales->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-3">
            <p class="text-sm text-gray-500">
                Showing {{ $sales->firstItem() }}–{{ $sales->lastItem() }} of {{ $sales->total() }} orders
            </p>
            <div class="flex items-center gap-1">
                @if($sales->onFirstPage())
                    <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $sales->previousPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Prev</a>
                @endif
                @foreach($sales->getUrlRange(max(1,$sales->currentPage()-2), min($sales->lastPage(),$sales->currentPage()+2)) as $page => $url)
                    @if($page == $sales->currentPage())
                        <span class="px-3 py-1.5 text-sm font-semibold bg-red-600 text-white rounded-lg">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">{{ $page }}</a>
                    @endif
                @endforeach
                @if($sales->hasMorePages())
                    <a href="{{ $sales->nextPageUrl() }}" class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">Next</a>
                @else
                    <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif

    </div>

    <!-- ── REVENUE CHART (last 7 days) ── -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
        <h2 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-chart-bar text-red-500 mr-2"></i>Revenue — Last 7 Days
        </h2>
        <div style="position:relative; height:260px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- ── TWO-COLUMN LOWER SECTION (Recent Sales + Payment Breakdown) ── -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

        <!-- Recent Sales Table (2/3) -->
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-receipt text-red-500 mr-2"></i>Recent Sales
                </h2>
                <span class="text-xs text-gray-400">Last 20 completed orders</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        <tr>
                            <th class="px-4 py-3 text-left">Order #</th>
                            <th class="px-4 py-3 text-left">Table</th>
                            <th class="px-4 py-3 text-left">Customer</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-center">Payment</th>
                            <th class="px-4 py-3 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentSales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $sale->order_number }}</td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ $sale->table?->name ?? ($sale->table?->table_number ? 'T'.$sale->table->table_number : '—') }}
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $sale->customer_name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                LKR {{ number_format($sale->total, 2) }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $pmColors = [
                                        'cash'          => 'bg-green-100 text-green-700',
                                        'card'          => 'bg-blue-100 text-blue-700',
                                        'bank_transfer' => 'bg-purple-100 text-purple-700',
                                        'mixed'         => 'bg-orange-100 text-orange-700',
                                    ];
                                    $pmColor = $pmColors[$sale->payment_method] ?? 'bg-gray-100 text-gray-600';
                                @endphp
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium {{ $pmColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $sale->payment_method ?? '—')) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-400 text-xs whitespace-nowrap">
                                {{ $sale->created_at->format('d M, H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">No completed orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Methods Breakdown (1/3) -->
        <div class="xl:col-span-1 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-3">
                    <i class="fas fa-credit-card text-red-500 mr-2"></i>Payment Methods
                </h2>
                <!-- Date range filter -->
                <div class="flex items-center gap-2">
                    <div class="relative flex-1 flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5">
                        <i class="fas fa-calendar text-red-400 text-xs"></i>
                        <input id="pmDatePicker" type="text" placeholder="All time"
                            class="text-xs text-gray-600 bg-transparent outline-none w-full cursor-pointer"
                            readonly>
                    </div>
                    <button id="pmClearBtn" onclick="clearPmFilter()"
                        class="hidden px-2 py-1.5 text-xs text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>
                <p id="pmDateLabel" class="text-xs text-gray-400 mt-1.5 hidden"></p>
            </div>
            <div id="pmBreakdownBody" class="p-6 space-y-4">
                @forelse($paymentBreakdown as $pm)
                @php
                    $pmIcons = [
                        'cash'          => 'fa-money-bill-wave text-green-500',
                        'card'          => 'fa-credit-card text-blue-500',
                        'bank_transfer' => 'fa-university text-purple-500',
                        'mixed'         => 'fa-shuffle text-orange-500',
                    ];
                    $icon = $pmIcons[$pm->payment_method] ?? 'fa-circle-question text-gray-400';
                    $pct  = $totalOrders > 0 ? round(($pm->order_count / $totalOrders) * 100) : 0;
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="flex items-center gap-2 text-sm font-medium text-gray-700">
                            <i class="fas {{ $icon }}"></i>
                            {{ ucfirst(str_replace('_', ' ', $pm->payment_method)) }}
                        </span>
                        <span class="text-sm font-bold text-gray-900">{{ $pm->order_count }} orders</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 bg-gray-100 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs text-gray-400 w-10 text-right">{{ $pct }}%</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-0.5">LKR {{ number_format($pm->total_revenue, 2) }}</p>
                </div>
                @empty
                <p class="text-center text-gray-400 py-6">No payment data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ── TOP PRODUCTS TABLE ── -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-900">
                    <i class="fas fa-trophy text-amber-400 mr-2"></i>Top Selling Products
                </h2>
                <span class="text-xs text-gray-400">By quantity sold</span>
            </div>
            <!-- Date range filter -->
            <div class="flex items-center gap-2">
                <div class="relative flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5">
                    <i class="fas fa-calendar text-red-400 text-xs"></i>
                    <input id="tpDatePicker" type="text" placeholder="All time"
                        class="text-xs text-gray-600 bg-transparent outline-none w-40 cursor-pointer"
                        readonly>
                </div>
                <button id="tpClearBtn" onclick="clearTpFilter()"
                    class="hidden px-2 py-1.5 text-xs text-gray-500 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-6 py-3 text-left">#</th>
                        <th class="px-6 py-3 text-left">Product</th>
                        <th class="px-6 py-3 text-left">Category</th>
                        <th class="px-6 py-3 text-right">Qty Sold</th>
                        <th class="px-6 py-3 text-right">Total Revenue</th>
                    </tr>
                </thead>
                <tbody id="topProductsBody" class="divide-y divide-gray-100">
                    @forelse($topProducts as $i => $prod)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-3 text-gray-400 font-semibold">
                            @if($i === 0) <i class="fas fa-medal text-amber-400"></i>
                            @elseif($i === 1) <i class="fas fa-medal text-gray-400"></i>
                            @elseif($i === 2) <i class="fas fa-medal text-orange-400"></i>
                            @else {{ $i + 1 }}
                            @endif
                        </td>
                        <td class="px-6 py-3 font-semibold text-gray-900">{{ $prod->product_name }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $prod->category_name }}</td>
                        <td class="px-6 py-3 text-right font-bold text-gray-900">{{ number_format($prod->total_qty) }}</td>
                        <td class="px-6 py-3 text-right font-semibold text-gray-900">
                            LKR {{ number_format($prod->total_revenue, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No sales data yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar { border-radius: 14px !important; box-shadow: 0 8px 30px rgba(0,0,0,0.12) !important; border: 1px solid #e2e8f0 !important; }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background: #dc2626 !important; border-color: #dc2626 !important; }
    .flatpickr-day.inRange { background: #fee2e2 !important; border-color: #fee2e2 !important; color: #dc2626 !important; }
    .flatpickr-day:hover { background: #fef2f2 !important; border-color: #fca5a5 !important; }
    .flatpickr-months .flatpickr-month { background: #dc2626 !important; color: #fff !important; border-radius: 14px 14px 0 0 !important; }
    .flatpickr-current-month, .flatpickr-current-month select, .flatpickr-current-month .numInputWrapper { color: #fff !important; }
    .flatpickr-weekday { color: #dc2626 !important; font-weight: 700 !important; }
    .flatpickr-prev-month svg, .flatpickr-next-month svg { fill: #fff !important; }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const labels  = {!! $chartLabels !!};
    const data    = {!! $chartData !!};

    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue (LKR)',
                data: data,
                backgroundColor: 'rgba(220, 38, 38, 0.15)',
                borderColor: '#dc2626',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' LKR ' + ctx.parsed.y.toLocaleString('en-US', { minimumFractionDigits: 2 })
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        callback: v => 'LKR ' + v.toLocaleString()
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
})();

// ── Flatpickr — Sales Report date range ──
(function () {
    const fromHidden = document.getElementById('fromHidden');
    const toHidden   = document.getElementById('toHidden');

    const fp = flatpickr('#dateRangePicker', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        showMonths: 2,
        disableMobile: true,
        onChange: function (selectedDates) {
            if (selectedDates.length === 2) {
                const fmt = d => d.toISOString().slice(0, 10);
                fromHidden.value = fmt(selectedDates[0]);
                toHidden.value   = fmt(selectedDates[1]);
            }
        }
    });

    const from = fromHidden.value;
    const to   = toHidden.value;
    if (from && to) fp.setDate([from, to]);
})();

// ── Sales Report — thermal receipt printer (80mm) ──
function escapeHtmlReport(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

function printReceiptReport(html) {
    const w = window.open('', '', 'width=400,height=700,toolbar=0,menubar=0,scrollbars=1');
    w.document.write(
        '<!DOCTYPE html><html><head><style>'
        + '@page { size: 80mm auto; margin: 2mm 6mm; }'
        + '* { box-sizing: border-box; font-weight: bold !important; }'
        + 'body { font-family: \'Courier New\', monospace; width: 100%; margin: 0; padding: 0; font-size: 12px; }'
        + 'table { width: 100%; border-collapse: collapse; table-layout: fixed; }'
        + 'td, th { word-break: break-word; overflow-wrap: break-word; }'
        + '</style></head><body>' + html + '</body></html>'
    );
    w.document.close();
    w.focus();
    w.print();
    setTimeout(function() { w.close(); }, 1200);
}

function printSalesReceipt() {
    const raw = document.getElementById('salesReceiptData');
    if (!raw) { return; }
    const d = JSON.parse(raw.textContent);

    if (!d.sales.length) {
        alert('No sales to print for the current filter.');
        return;
    }

    const rangeLabel = (d.from && d.to) ? (d.from + ' - ' + d.to) : 'All time';

    const rows = d.sales.map(function(s) {
        return '<tr>'
            + '<td style="padding:3px 0; vertical-align:top; width:34%;">' + escapeHtmlReport(s.order_number)
            + '<br><span style="font-size:10px;">' + escapeHtmlReport(s.table) + ' &middot; ' + escapeHtmlReport(s.payment) + '</span></td>'
            + '<td style="text-align:right; padding:3px 0; vertical-align:top; width:30%;">Rs.' + s.total.toFixed(2) + '</td>'
            + '<td style="text-align:right; padding:3px 0; vertical-align:top; width:36%; font-size:10px;">' + escapeHtmlReport(s.date) + '</td>'
            + '</tr>';
    }).join('');

    const html =
        '<div style="text-align:center; padding-bottom:6px;">'
        + '<div style="font-size:14px; letter-spacing:1px;">SALES REPORT</div>'
        + '<div style="font-size:11px;">' + escapeHtmlReport(rangeLabel) + '</div>'
        + '</div>'

        + '<table width="100%" cellspacing="0" cellpadding="2" style="font-size:12px; border-top:2px solid #000; border-bottom:2px solid #000; padding:4px 0; margin-bottom:6px;">'
        + '<tr><td style="width:50%;">Orders</td><td style="text-align:right; width:50%;">' + d.count + '</td></tr>'
        + '<tr><td>Revenue</td><td style="text-align:right;">Rs.' + d.revenue.toFixed(2) + '</td></tr>'
        + '<tr><td>Avg Order</td><td style="text-align:right;">Rs.' + d.avg.toFixed(2) + '</td></tr>'
        + '</table>'

        + '<table width="100%" cellspacing="0" cellpadding="2" style="font-size:12px; table-layout:fixed;">'
        + '<thead><tr style="border-bottom:1px dashed #000;">'
        + '<th style="text-align:left; padding-bottom:4px; font-size:11px; width:34%;">ORDER</th>'
        + '<th style="text-align:right; padding-bottom:4px; font-size:11px; width:30%;">TOTAL</th>'
        + '<th style="text-align:right; padding-bottom:4px; font-size:11px; width:36%;">DATE</th>'
        + '</tr></thead>'
        + '<tbody>' + rows + '</tbody>'
        + '</table>'

        + '<div style="text-align:center; font-size:11px; margin-top:8px; border-top:1px dashed #000; padding-top:6px;">Printed: ' + new Date().toLocaleString() + '</div>';

    printReceiptReport(html);
}

// ── Category-wise Sales receipt (80mm) ──
async function printCategorySales() {
    const from = document.getElementById('fromHidden')?.value || '';
    const to   = document.getElementById('toHidden')?.value   || '';

    const url = new URL('{{ route("reports.category_sales") }}');
    if (from) url.searchParams.set('from', from);
    if (to)   url.searchParams.set('to', to);

    let d;
    try {
        const res = await fetch(url);
        d = await res.json();
    } catch (e) {
        alert('Could not load category sales.');
        return;
    }

    if (!d.categories.length) {
        alert('No sales to print for the current filter.');
        return;
    }

    const now = new Date();
    const dateStr = now.toLocaleDateString('en-GB');
    const timeStr = now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: false });

    const sections = d.categories.map(function(cat) {
        const itemRows = cat.items.map(function(item) {
            return '<tr>'
                + '<td style="padding:1px 0; vertical-align:top;">' + escapeHtmlReport(item.product_name) + '</td>'
                + '<td style="text-align:center; padding:1px 0; vertical-align:top; width:15%;">' + item.qty + '</td>'
                + '<td style="text-align:right; padding:1px 0; vertical-align:top; width:25%;">' + item.amount.toFixed(2) + '</td>'
                + '</tr>';
        }).join('');

        return '<div style="margin-top:10px;">'
            + '<div style="font-size:12px;">' + escapeHtmlReport(cat.category.toUpperCase()) + '</div>'
            + '<div style="border-top:1px dashed #000; margin:3px 0;"></div>'
            + '<table width="100%" cellspacing="0" cellpadding="0" style="font-size:11px; table-layout:fixed;">'
            + '<tbody>' + itemRows + '</tbody>'
            + '</table>'
            + '<div style="border-top:1px dashed #000; margin-top:3px;"></div>'
            + '</div>';
    }).join('');

    const html =
        '<div style="padding-bottom:4px;">'
        + '<div style="font-size:14px;">Item Category wise Sales</div>'
        + '<div style="font-size:11px; margin-top:4px;">Report Date : ' + dateStr + '</div>'
        + '<div style="font-size:11px;">Report Time : ' + timeStr + '</div>'
        + '</div>'
        + '<div style="border-top:1px dashed #000; margin:4px 0;"></div>'

        + '<table width="100%" cellspacing="0" cellpadding="0" style="font-size:11px; table-layout:fixed;">'
        + '<thead><tr>'
        + '<th style="text-align:left; padding-bottom:2px;">Description</th>'
        + '<th style="text-align:center; padding-bottom:2px; width:15%;">Qty</th>'
        + '<th style="text-align:right; padding-bottom:2px; width:25%;">Amount</th>'
        + '</tr></thead>'
        + '</table>'
        + '<div style="border-top:1px dashed #000; margin:2px 0;"></div>'

        + sections

        + '<div style="text-align:right; font-size:12px; margin-top:6px;">GRAND TOTAL &nbsp; ' + d.grand_total.toFixed(2) + '</div>'
        + '<div style="text-align:center; font-size:11px; margin-top:8px; border-top:1px dashed #000; padding-top:6px;">Printed: ' + now.toLocaleString() + '</div>';

    printReceiptReport(html);
}

// ── Flatpickr — Payment Methods date range ──
(function () {
    const pmIcons = {
        cash:          'fa-money-bill-wave text-green-500',
        card:          'fa-credit-card text-blue-500',
        bank_transfer: 'fa-university text-purple-500',
        mixed:         'fa-shuffle text-orange-500',
    };

    const endpoint = '{{ route("reports.payment.breakdown") }}';
    let activeDates = { from: null, to: null };

    function fmtDate(d) { return d.toISOString().slice(0, 10); }

    function renderBreakdown(data) {
        const body = document.getElementById('pmBreakdownBody');
        const label = document.getElementById('pmDateLabel');

        if (!data.breakdown.length) {
            body.innerHTML = '<p class="text-center text-gray-400 py-6">No payment data for this range.</p>';
            return;
        }

        body.innerHTML = data.breakdown.map(pm => {
            const icon = pmIcons[pm.method] || 'fa-circle-question text-gray-400';
            return `<div>
                <div class="flex items-center justify-between mb-1">
                    <span class="flex items-center gap-2 text-sm font-medium text-gray-700">
                        <i class="fas ${icon}"></i>${pm.label}
                    </span>
                    <span class="text-sm font-bold text-gray-900">${pm.order_count} orders</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-100 rounded-full h-2">
                        <div class="bg-red-500 h-2 rounded-full transition-all" style="width:${pm.pct}%"></div>
                    </div>
                    <span class="text-xs text-gray-400 w-10 text-right">${pm.pct}%</span>
                </div>
                <p class="text-xs text-gray-400 mt-0.5">LKR ${parseFloat(pm.total_revenue).toLocaleString('en-US', {minimumFractionDigits:2})}</p>
            </div>`;
        }).join('');

        if (activeDates.from && activeDates.to) {
            label.textContent = `${data.total_count} orders · LKR ${parseFloat(data.total_revenue).toLocaleString('en-US', {minimumFractionDigits:2})}`;
            label.classList.remove('hidden');
        } else {
            label.classList.add('hidden');
        }
    }

    async function loadBreakdown(from, to) {
        const url = new URL(endpoint);
        if (from) url.searchParams.set('from', from);
        if (to)   url.searchParams.set('to', to);
        try {
            const res  = await fetch(url);
            const data = await res.json();
            renderBreakdown(data);
        } catch(e) { console.error('Payment breakdown error', e); }
    }

    window.clearPmFilter = function () {
        activeDates = { from: null, to: null };
        pmFp.clear();
        document.getElementById('pmClearBtn').classList.add('hidden');
        document.getElementById('pmDateLabel').classList.add('hidden');
        loadBreakdown(null, null);
    };

    const pmFp = flatpickr('#pmDatePicker', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        showMonths: 1,
        disableMobile: true,
        onChange: function (selectedDates) {
            if (selectedDates.length === 2) {
                activeDates.from = fmtDate(selectedDates[0]);
                activeDates.to   = fmtDate(selectedDates[1]);
                document.getElementById('pmClearBtn').classList.remove('hidden');
                loadBreakdown(activeDates.from, activeDates.to);
            }
        }
    });
})();

// ── Flatpickr — Top Selling Products date range ──
(function () {
    const endpoint = '{{ route("reports.top_products") }}';

    function fmtDate(d) { return d.toISOString().slice(0, 10); }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str ?? '';
        return div.innerHTML;
    }

    function medalIcon(i) {
        if (i === 0) return '<i class="fas fa-medal text-amber-400"></i>';
        if (i === 1) return '<i class="fas fa-medal text-gray-400"></i>';
        if (i === 2) return '<i class="fas fa-medal text-orange-400"></i>';
        return i + 1;
    }

    function renderTopProducts(data) {
        const body = document.getElementById('topProductsBody');

        if (!data.products.length) {
            body.innerHTML = '<tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">No sales data for this range.</td></tr>';
            return;
        }

        body.innerHTML = data.products.map((prod, i) => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-3 text-gray-400 font-semibold">${medalIcon(i)}</td>
                <td class="px-6 py-3 font-semibold text-gray-900">${escapeHtml(prod.product_name)}</td>
                <td class="px-6 py-3 text-gray-500">${escapeHtml(prod.category_name)}</td>
                <td class="px-6 py-3 text-right font-bold text-gray-900">${prod.total_qty.toLocaleString('en-US')}</td>
                <td class="px-6 py-3 text-right font-semibold text-gray-900">LKR ${parseFloat(prod.total_revenue).toLocaleString('en-US', {minimumFractionDigits:2})}</td>
            </tr>
        `).join('');
    }

    async function loadTopProducts(from, to) {
        const url = new URL(endpoint);
        if (from) url.searchParams.set('from', from);
        if (to)   url.searchParams.set('to', to);
        try {
            const res  = await fetch(url);
            const data = await res.json();
            renderTopProducts(data);
        } catch(e) { console.error('Top products error', e); }
    }

    window.clearTpFilter = function () {
        tpFp.clear();
        document.getElementById('tpClearBtn').classList.add('hidden');
        loadTopProducts(null, null);
    };

    const tpFp = flatpickr('#tpDatePicker', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        altInput: true,
        altFormat: 'd M Y',
        showMonths: 1,
        disableMobile: true,
        onChange: function (selectedDates) {
            if (selectedDates.length === 2) {
                const from = fmtDate(selectedDates[0]);
                const to   = fmtDate(selectedDates[1]);
                document.getElementById('tpClearBtn').classList.remove('hidden');
                loadTopProducts(from, to);
            }
        }
    });
})();
</script>
@endsection
