@extends('layouts.app')

@section('title', 'Room Management')

@section('styles')
<style>
    .room-card {
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.15s;
        position: relative;
    }
    .room-card:hover { box-shadow: 0 6px 18px rgba(0,0,0,0.08); transform: translateY(-2px); }
    .room-card.is-available { border-color: #bbf7d0; }
    .room-card.is-occupied  { border-color: #fecaca; }
    .room-card.is-selected  { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,0.18); }

    .status-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; }
    .dot-available { background: #16a34a; }
    .dot-occupied  { background: #dc2626; }

    .countdown { font-variant-numeric: tabular-nums; font-weight: 800; }
    .countdown.overtime { color: #dc2626; }

    .panel { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; }
    .bill-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #e2e8f0; }
    .btn { border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: all .15s; }
    .btn-primary { background: #7c3aed; color: #fff; } .btn-primary:hover { background: #6d28d9; }
    .btn-green { background: #16a34a; color: #fff; } .btn-green:hover { background: #15803d; }
    .btn-amber { background: #d97706; color: #fff; } .btn-amber:hover { background: #b45309; }
    .btn-gray { background: #e2e8f0; color: #334155; } .btn-gray:hover { background: #cbd5e1; }
    .btn-red { background: #dc2626; color: #fff; } .btn-red:hover { background: #b91c1c; }
    .btn:disabled { background: #cbd5e1; color:#fff; cursor: not-allowed; }
    .field { width: 100%; padding: 9px 11px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 13px; outline: none; }
    .field:focus { border-color: #7c3aed; }

    .qa-link {
        display: inline-flex; align-items: center; gap: 8px;
        background: #fff; border: 1px solid #e2e8f0; color: #334155;
        padding: 9px 14px; border-radius: 10px; font-size: 13px; font-weight: 700;
        text-decoration: none; transition: all .15s;
    }
    .qa-link:hover { background: #f5f3ff; color: #6d28d9; border-color: #ddd6fe; }
    .qa-link i { color: #7c3aed; }

    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,.55); backdrop-filter: blur(3px); z-index: 60; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #fff; border-radius: 16px; padding: 24px; max-width: 440px; width: 94%; box-shadow: 0 24px 64px rgba(0,0,0,.2); max-height: 92vh; overflow-y: auto; }
</style>
@endsection

@section('content')
<div class="mb-4 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <i class="fas fa-door-open text-purple-600"></i>Room Management
        </h1>
        <p class="text-gray-600 mt-1 text-sm">Book rooms, track time, and bill room food orders — base duration {{ intdiv($bookingDurationMinutes, 60) }}h {{ $bookingDurationMinutes % 60 ? ($bookingDurationMinutes % 60).'m' : '' }}</p>
    </div>
    @if(!$activeShift)
        <span class="text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-lg">
            <i class="fas fa-triangle-exclamation"></i> No active shift — checkout won't record to till
        </span>
    @endif
</div>

<!-- Quick access toolbar -->
<div class="mb-6 flex flex-wrap gap-2">
    <a href="{{ route('reports.index') }}" class="qa-link"><i class="fas fa-chart-bar"></i> Reports</a>
    <a href="{{ route('shifts.index') }}" class="qa-link"><i class="fas fa-clock"></i> Shift Management</a>
    <a href="{{ route('order.history') }}" class="qa-link"><i class="fas fa-receipt"></i> Order History</a>
    <a href="{{ route('rooms.history') }}" class="qa-link"><i class="fas fa-clock-rotate-left"></i> Room History</a>
    <a href="{{ route('rooms.settings') }}" class="qa-link"><i class="fas fa-sliders-h"></i> Room Settings</a>
    <a href="{{ route('settings.room.qr') }}" class="qa-link"><i class="fas fa-qrcode"></i> Room QR Codes</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Rooms grid -->
    <div class="lg:col-span-2">
        <div id="roomsGrid" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="col-span-full text-center text-gray-400 py-12">
                <i class="fas fa-spinner fa-spin text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Billing panel -->
    <div class="lg:col-span-1">
        <div class="panel p-5 sticky" style="top: 84px;">
            <div id="panelEmpty" class="text-center text-gray-400 py-16">
                <i class="fas fa-receipt text-4xl mb-3"></i>
                <p class="text-sm">Select a room to view its bill</p>
            </div>

            <div id="panelContent" style="display:none;">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-xl font-bold text-gray-900"><i class="fas fa-door-open text-purple-600 mr-1"></i>Room <span id="bRoomNumber"></span></h2>
                    <span id="bBookingNumber" class="text-xs font-mono text-gray-400"></span>
                </div>

                <!-- Countdown -->
                <div class="rounded-lg p-3 mb-3 text-center" style="background:#f5f3ff;">
                    <div class="text-xs text-gray-500 font-semibold mb-1">Time Remaining</div>
                    <div id="bCountdown" class="countdown text-3xl text-purple-700" data-expires="">--:--:--</div>
                    <div class="text-[11px] text-gray-400 mt-1">Started <span id="bStarted"></span></div>
                </div>

                <!-- Customer -->
                <div class="mb-3">
                    <input type="text" id="bCustomerName" class="field mb-2" placeholder="Guest name (optional)">
                    <div class="flex gap-2">
                        <input type="tel" id="bCustomerPhone" class="field" placeholder="Phone (optional)">
                        <button class="btn btn-gray" style="padding:0 12px;" onclick="saveCustomer()" title="Save guest"><i class="fas fa-save"></i></button>
                    </div>
                </div>

                <!-- Bill items -->
                <div class="mb-2">
                    <div class="text-xs font-bold text-gray-500 uppercase mb-1">Room Charges</div>
                    <div id="bRoomItems"></div>
                </div>
                <div class="mb-2">
                    <div class="text-xs font-bold text-gray-500 uppercase mb-1 mt-3">Food &amp; Drinks</div>
                    <div id="bFoodItems"><p class="text-xs text-gray-400 py-1">No food orders yet.</p></div>
                </div>

                <!-- Add extra hours -->
                <div class="rounded-lg p-3 my-3" style="background:#fffbeb; border:1px solid #fde68a;">
                    <div class="text-xs font-bold text-amber-700 mb-2"><i class="fas fa-clock"></i> Add Extra Time</div>
                    <div class="flex gap-2">
                        <input type="number" id="extHours" class="field" min="1" value="1" style="width:70px;" placeholder="Hrs">
                        <input type="number" id="extPrice" class="field" min="0" step="0.01" placeholder="Price (Rs.)">
                        <button class="btn btn-amber" style="padding:0 14px;" onclick="addExtension()">Add</button>
                    </div>
                </div>

                <!-- Add food item (staff) -->
                <div class="rounded-lg p-3 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                    <div class="text-xs font-bold text-gray-600 mb-2"><i class="fas fa-plus"></i> Add Item (staff)</div>
                    <div class="flex gap-2">
                        <select id="staffProduct" class="field">
                            <option value="">Select product…</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} — Rs. {{ number_format($p->selling_price ?? $p->price, 2) }}</option>
                            @endforeach
                        </select>
                        <input type="number" id="staffQty" class="field" min="1" value="1" style="width:60px;">
                        <button class="btn btn-gray" style="padding:0 12px;" onclick="addStaffItem()"><i class="fas fa-plus"></i></button>
                    </div>
                </div>

                <!-- Totals -->
                <div class="border-t-2 border-gray-100 pt-3 mb-3">
                    <div class="flex justify-between text-sm text-gray-600"><span>Room charge</span><span id="tRoom">Rs. 0.00</span></div>
                    <div class="flex justify-between text-sm text-gray-600"><span>Food &amp; drinks</span><span id="tFood">Rs. 0.00</span></div>
                    <div class="flex justify-between text-lg font-extrabold text-gray-900 mt-1"><span>Total</span><span id="tTotal">Rs. 0.00</span></div>
                </div>

                <!-- Actions -->
                <div class="grid grid-cols-2 gap-2">
                    <button class="btn btn-gray" onclick="printKot()"><i class="fas fa-print"></i> KOT</button>
                    <button class="btn btn-red" onclick="cancelBooking()"><i class="fas fa-ban"></i> Cancel</button>
                    <button class="btn btn-green col-span-2" style="padding:11px;" onclick="openCheckout()"><i class="fas fa-cash-register"></i> Checkout &amp; Pay</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Book Modal -->
<div class="modal-overlay" id="bookModal">
    <div class="modal-box">
        <div class="text-lg font-extrabold text-gray-900 mb-1"><i class="fas fa-door-open text-purple-600 mr-1"></i>Book Room <span id="bookRoomNumber"></span></div>
        <p class="text-sm text-gray-500 mb-4">Countdown starts immediately for the configured base duration.</p>
        <input type="text" id="bookName" class="field mb-3" placeholder="Guest name (optional)">
        <input type="tel" id="bookPhone" class="field mb-4" placeholder="Phone (optional)">
        <div class="flex gap-2">
            <button class="btn btn-gray flex-1" style="padding:11px;" onclick="closeModal('bookModal')">Cancel</button>
            <button class="btn btn-primary flex-1" style="padding:11px;" onclick="confirmBook()"><i class="fas fa-play"></i> Start Booking</button>
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div class="modal-overlay" id="checkoutModal">
    <div class="modal-box">
        <div class="text-lg font-extrabold text-gray-900 mb-3"><i class="fas fa-cash-register text-green-600 mr-1"></i>Checkout — Room <span id="coRoomNumber"></span></div>
        <div class="rounded-lg p-3 mb-3" style="background:#f8fafc;">
            <div class="flex justify-between text-sm text-gray-600"><span>Room charge</span><span id="coRoom">Rs. 0.00</span></div>
            <div class="flex justify-between text-sm text-gray-600"><span>Food &amp; drinks</span><span id="coFood">Rs. 0.00</span></div>
            <div class="flex justify-between font-extrabold text-gray-900 mt-1"><span>Total</span><span id="coTotal">Rs. 0.00</span></div>
        </div>
        <label class="text-xs font-semibold text-gray-500">Payment Method</label>
        <select id="coMethod" class="field mb-3 mt-1">
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="bank_transfer">Bank Transfer</option>
        </select>
        <div class="grid grid-cols-2 gap-2 mb-3">
            <div>
                <label class="text-xs font-semibold text-gray-500">Discount</label>
                <select id="coDiscType" class="field mt-1" onchange="recalcCheckout()">
                    <option value="">None</option>
                    <option value="percentage">Percentage %</option>
                    <option value="fixed">Fixed Rs.</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-500">Value</label>
                <input type="number" id="coDiscValue" class="field mt-1" min="0" step="0.01" value="0" oninput="recalcCheckout()">
            </div>
        </div>
        <label class="text-xs font-semibold text-gray-500">Amount Paid</label>
        <input type="number" id="coPaid" class="field mb-2 mt-1" min="0" step="0.01" value="0" oninput="recalcChange()">
        <div class="flex justify-between text-sm font-semibold text-gray-700 mb-4"><span>Change</span><span id="coChange">Rs. 0.00</span></div>
        <div class="flex gap-2">
            <button class="btn btn-gray flex-1" style="padding:11px;" onclick="closeModal('checkoutModal')">Cancel</button>
            <button class="btn btn-green flex-1" style="padding:11px;" onclick="doCheckout()"><i class="fas fa-check"></i> Confirm Payment</button>
        </div>
    </div>
</div>

<!-- Paid Bill Modal -->
<div class="modal-overlay" id="paidBillModal">
    <div class="modal-box" style="max-width:380px; padding:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 style="font-size:16px; font-weight:800; color:#0f172a; margin:0;"><i class="fas fa-receipt" style="color:#16a34a; margin-right:6px;"></i>Final Bill</h2>
            <button onclick="closeModal('paidBillModal'); loadRooms();" style="background:none; border:none; font-size:22px; cursor:pointer; color:#94a3b8; line-height:1;">&times;</button>
        </div>
        <div id="paidBillContent" style="font-family:'Courier New',monospace; background:#fafafa; border-radius:8px; padding:16px; font-size:12px; border:1px solid #e2e8f0; max-height:60vh; overflow-y:auto;"></div>
        <div style="display:flex; gap:10px; margin-top:16px;">
            <button class="btn btn-gray flex-1" style="padding:11px;" onclick="closeModal('paidBillModal'); loadRooms();">Close</button>
            <button class="btn btn-primary flex-1" style="padding:11px;" onclick="printPaidBill()"><i class="fas fa-print" style="margin-right:4px;"></i>Print</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const CSRF = '{{ csrf_token() }}';
    const APP_NAME = @json(config('app.name'));
    let currentBooking = null;   // currently selected booking detail
    let roomsCache = [];

    const money = n => 'Rs. ' + Number(n || 0).toFixed(2);

    function headers(json = true) {
        const h = { 'X-CSRF-TOKEN': CSRF };
        if (json) h['Content-Type'] = 'application/json';
        return h;
    }

    /* ---------- Rooms grid ---------- */
    async function loadRooms() {
        const res = await fetch('{{ route('rooms.list') }}', { headers: { 'X-CSRF-TOKEN': CSRF } });
        roomsCache = await res.json();
        renderRooms();
    }

    function renderRooms() {
        const grid = document.getElementById('roomsGrid');
        grid.innerHTML = roomsCache.map(r => {
            const occupied = r.has_booking;
            const selected = currentBooking && currentBooking.id === r.booking_id;
            const cls = 'room-card ' + (occupied ? 'is-occupied' : 'is-available') + (selected ? ' is-selected' : '');
            const onclick = occupied ? `loadBooking(${r.booking_id})` : `openBook(${r.id}, ${r.room_number})`;
            const countdown = occupied
                ? `<div class="countdown text-lg text-gray-800" data-expires="${r.expires_at}">--:--:--</div>`
                : `<span class="text-xs font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded">Available</span>`;
            const meta = occupied
                ? `<div class="text-[11px] text-gray-500 mt-1">${r.food_items_count} item(s) · ${money(r.total)}</div>`
                : `<div class="text-[11px] text-gray-400 mt-1">Base Rs. ${Number(r.base_price).toFixed(2)}</div>`;
            return `
                <div class="${cls}" onclick="${onclick}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-extrabold text-gray-900">Room ${r.room_number}</span>
                        <span class="status-dot ${occupied ? 'dot-occupied' : 'dot-available'}"></span>
                    </div>
                    ${countdown}
                    ${meta}
                    ${occupied && r.customer_name ? `<div class="text-[11px] text-gray-600 mt-1 truncate"><i class="fas fa-user text-gray-400"></i> ${escapeHtml(r.customer_name)}</div>` : ''}
                </div>`;
        }).join('');
    }

    function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

    /* ---------- Countdown ticker ---------- */
    function fmt(sec) {
        const overtime = sec < 0;
        sec = Math.abs(sec);
        const h = String(Math.floor(sec / 3600)).padStart(2, '0');
        const m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
        const s = String(sec % 60).padStart(2, '0');
        return (overtime ? '+' : '') + `${h}:${m}:${s}`;
    }
    setInterval(() => {
        document.querySelectorAll('[data-expires]').forEach(el => {
            const exp = el.getAttribute('data-expires');
            if (!exp) return;
            const sec = Math.floor((new Date(exp).getTime() - Date.now()) / 1000);
            el.textContent = fmt(sec);
            el.classList.toggle('overtime', sec < 0);
        });
    }, 1000);

    /* ---------- Booking ---------- */
    let bookRoomId = null;
    function openBook(roomId, roomNumber) {
        bookRoomId = roomId;
        document.getElementById('bookRoomNumber').textContent = roomNumber;
        document.getElementById('bookName').value = '';
        document.getElementById('bookPhone').value = '';
        openModal('bookModal');
    }

    async function confirmBook() {
        const res = await fetch(`/rooms/${bookRoomId}/book`, {
            method: 'POST', headers: headers(),
            body: JSON.stringify({
                customer_name: document.getElementById('bookName').value || null,
                customer_phone: document.getElementById('bookPhone').value || null,
            })
        });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Failed to book'); return; }
        closeModal('bookModal');
        await loadRooms();
        loadBooking(data.booking_id);
    }

    async function loadBooking(bookingId) {
        const res = await fetch(`/rooms/booking/${bookingId}`, { headers: { 'X-CSRF-TOKEN': CSRF } });
        currentBooking = await res.json();
        renderBilling();
        renderRooms();
    }

    function renderBilling() {
        const b = currentBooking;
        document.getElementById('panelEmpty').style.display = 'none';
        document.getElementById('panelContent').style.display = 'block';

        document.getElementById('bRoomNumber').textContent = b.room_number;
        document.getElementById('bBookingNumber').textContent = b.booking_number;
        document.getElementById('bCustomerName').value = b.customer_name || '';
        document.getElementById('bCustomerPhone').value = b.customer_phone || '';
        const cd = document.getElementById('bCountdown');
        cd.setAttribute('data-expires', b.expires_at);
        document.getElementById('bStarted').textContent = b.started_at ? new Date(b.started_at).toLocaleTimeString() : '';

        document.getElementById('bRoomItems').innerHTML = b.room_items.map(i => `
            <div class="bill-row">
                <span class="text-sm text-gray-700">${escapeHtml(i.product_name)}</span>
                <span class="text-sm font-semibold">${money(i.subtotal)}</span>
            </div>`).join('') || '<p class="text-xs text-gray-400 py-1">No room charges.</p>';

        document.getElementById('bFoodItems').innerHTML = b.food_items.length ? b.food_items.map(i => `
            <div class="bill-row">
                <span class="text-sm text-gray-700">${escapeHtml(i.product_name)} <span class="text-gray-400">×${i.quantity}</span></span>
                <span class="flex items-center gap-2">
                    <span class="text-sm font-semibold">${money(i.subtotal)}</span>
                    <button class="text-red-400 hover:text-red-600" onclick="removeItem(${i.id})"><i class="fas fa-times"></i></button>
                </span>
            </div>`).join('') : '<p class="text-xs text-gray-400 py-1">No food orders yet.</p>';

        document.getElementById('tRoom').textContent = money(b.room_charge);
        document.getElementById('tFood').textContent = money(b.food_total);
        document.getElementById('tTotal').textContent = money(b.subtotal);
    }

    async function saveCustomer() {
        if (!currentBooking) return;
        await fetch(`/rooms/booking/${currentBooking.id}/customer`, {
            method: 'POST', headers: headers(),
            body: JSON.stringify({
                customer_name: document.getElementById('bCustomerName').value || null,
                customer_phone: document.getElementById('bCustomerPhone').value || null,
            })
        });
        toast('Guest details saved');
    }

    async function addExtension() {
        if (!currentBooking) return;
        const hours = parseInt(document.getElementById('extHours').value);
        const price = parseFloat(document.getElementById('extPrice').value);
        if (!hours || hours < 1) { alert('Enter hours'); return; }
        if (isNaN(price) || price < 0) { alert('Enter a price for the extra time'); return; }
        const res = await fetch(`/rooms/booking/${currentBooking.id}/extension`, {
            method: 'POST', headers: headers(), body: JSON.stringify({ hours, price })
        });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Failed'); return; }
        document.getElementById('extPrice').value = '';
        await loadBooking(currentBooking.id);
        loadRooms();
    }

    async function addStaffItem() {
        if (!currentBooking) return;
        const productId = document.getElementById('staffProduct').value;
        const qty = parseInt(document.getElementById('staffQty').value) || 1;
        if (!productId) { alert('Select a product'); return; }
        const res = await fetch(`/rooms/booking/${currentBooking.id}/item`, {
            method: 'POST', headers: headers(),
            body: JSON.stringify({ product_id: productId, quantity: qty })
        });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Failed'); return; }
        document.getElementById('staffProduct').value = '';
        document.getElementById('staffQty').value = 1;
        await loadBooking(currentBooking.id);
        loadRooms();
    }

    async function removeItem(itemId) {
        if (!currentBooking) return;
        const res = await fetch(`/rooms/booking/${currentBooking.id}/item/${itemId}`, {
            method: 'DELETE', headers: headers()
        });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Failed'); return; }
        await loadBooking(currentBooking.id);
        loadRooms();
    }

    async function cancelBooking() {
        if (!currentBooking) return;
        if (!confirm('Cancel this booking? Food items will be returned to stock.')) return;
        const res = await fetch(`/rooms/booking/${currentBooking.id}/cancel`, { method: 'POST', headers: headers() });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Failed'); return; }
        currentBooking = null;
        document.getElementById('panelContent').style.display = 'none';
        document.getElementById('panelEmpty').style.display = 'block';
        loadRooms();
    }

    async function printKot() {
        if (!currentBooking) return;
        const res = await fetch(`/rooms/booking/${currentBooking.id}/kot`, { method: 'POST', headers: headers() });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Nothing to print'); return; }
        const rows = data.items.map(i => `<tr><td style="padding:4px 0;">${i.quantity} × ${i.product_name}</td></tr>${i.kitchen_notes ? `<tr><td style="font-size:11px;color:#555;">↳ ${i.kitchen_notes}</td></tr>` : ''}`).join('');
        printWindow(`<h2>KITCHEN ORDER — Room ${data.room_number}</h2><div>${data.order_number}</div><hr><table style="width:100%">${rows}</table>`);
        await loadBooking(currentBooking.id);
    }

    /* ---------- Checkout ---------- */
    function openCheckout() {
        if (!currentBooking) return;
        document.getElementById('coRoomNumber').textContent = currentBooking.room_number;
        document.getElementById('coRoom').textContent = money(currentBooking.room_charge);
        document.getElementById('coFood').textContent = money(currentBooking.food_total);
        document.getElementById('coDiscType').value = '';
        document.getElementById('coDiscValue').value = 0;
        document.getElementById('coPaid').value = currentBooking.subtotal.toFixed(2);
        recalcCheckout();
        openModal('checkoutModal');
    }

    function checkoutTotal() {
        const sub = currentBooking.subtotal;
        const type = document.getElementById('coDiscType').value;
        const val = parseFloat(document.getElementById('coDiscValue').value) || 0;
        let disc = 0;
        if (type === 'percentage') disc = sub * val / 100;
        else if (type === 'fixed') disc = val;
        return Math.max(0, sub - disc);
    }
    function recalcCheckout() {
        document.getElementById('coTotal').textContent = money(checkoutTotal());
        recalcChange();
    }
    function recalcChange() {
        const paid = parseFloat(document.getElementById('coPaid').value) || 0;
        document.getElementById('coChange').textContent = money(Math.max(0, paid - checkoutTotal()));
    }

    async function doCheckout() {
        const payload = {
            payment_method: document.getElementById('coMethod').value,
            amount_paid: parseFloat(document.getElementById('coPaid').value) || 0,
            discount_type: document.getElementById('coDiscType').value || null,
            discount_value: parseFloat(document.getElementById('coDiscValue').value) || 0,
        };
        const res = await fetch(`/rooms/booking/${currentBooking.id}/checkout`, {
            method: 'POST', headers: headers(), body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Checkout failed'); return; }
        closeModal('checkoutModal');
        showPaidBill(data);
        currentBooking = null;
        document.getElementById('panelContent').style.display = 'none';
        document.getElementById('panelEmpty').style.display = 'block';
    }

    // Builds the SAME thermal receipt structure used by the POS final bill.
    let lastBillHtml = '';
    function showPaidBill(d) {
        const methodLabel = { cash:'Cash', card:'Card', bank_transfer:'Bank Transfer', mixed:'Mixed' };
        const now = new Date();
        const dateStr = now.toLocaleDateString('en-GB') + ', ' + now.toLocaleTimeString('en-GB');

        // ── Update these values to match your restaurant ──
        const CO_NAME    = 'Suasa Family Restaurant';
        const CO_CONTACT = '071 979 9799';
        const CO_ADDRESS = '583 Avissawella Road, mulleriyawa';
        const CO_EMAIL   = 'info@suasafamily.com';

        const itemRows = d.items.map(function(i) {
            return '<tr>'
                + '<td style="padding:3px 0; vertical-align:top; width:62%;">' + escapeHtml(i.product_name)
                + '<br><span style="font-size:10px;">1 x Rs.' + i.unit_price.toFixed(2) + '</span></td>'
                + '<td style="text-align:center; padding:3px 0; vertical-align:top; width:10%;">' + i.quantity + '</td>'
                + '<td style="text-align:right; padding:3px 0; vertical-align:top; width:28%;">Rs.' + i.subtotal.toFixed(2) + '</td>'
                + '</tr>';
        }).join('');

        // ── HEADER: Logo + Company Details ──
        const html =
            '<div style="text-align:center; padding-bottom:8px;">'
            + '<img src="/images/logo.jpeg" style="max-width:150px; max-height:150px; margin-bottom:6px; display:block; margin-left:auto; margin-right:auto;" />'
            + '<div style="font-size:14px; letter-spacing:1px; color:#000; font-weight:bold;">' + CO_NAME + '</div>'
            + '<div style="font-size:11px; color:#000;">' + CO_CONTACT + '</div>'
            + '<div style="font-size:11px; color:#000;">' + CO_EMAIL + '</div>'
            + '<div style="font-size:11px; color:#000;">' + CO_ADDRESS + '</div>'
            + '</div>'

            // ── RECEIPT METADATA ──
            + '<div style="border-top:2px solid #000; border-bottom:2px solid #000; padding:6px 0; margin-bottom:8px;">'
            + '<div style="text-align:center; font-size:13px; letter-spacing:3px; color:#000; margin-bottom:5px;">RECEIPT</div>'
            + '<table width="100%" cellspacing="0" cellpadding="2" style="font-size:11px; color:#000; width:100%; table-layout:fixed;">'
            + '<tr><td style="width:35%;">Order</td><td style="text-align:right; width:65%; word-break:break-all;">' + d.order_number + '</td></tr>'
            + '<tr><td>Room</td><td style="text-align:right;">' + d.room_number + '</td></tr>'
            + '<tr><td>Booking</td><td style="text-align:right; word-break:break-all;">' + d.booking_number + '</td></tr>'
            + (d.customer_name  ? '<tr><td>Customer</td><td style="text-align:right;">' + escapeHtml(d.customer_name) + '</td></tr>' : '')
            + (d.customer_phone ? '<tr><td>Phone</td><td style="text-align:right;">' + d.customer_phone + '</td></tr>' : '')
            + '<tr><td>Date</td><td style="text-align:right;">' + dateStr + '</td></tr>'
            + '</table>'
            + '</div>'

            // ── ITEM TABLE ──
            + '<table width="100%" cellspacing="0" cellpadding="2" style="font-size:12px; color:#000; width:100%; table-layout:fixed;">'
            + '<thead><tr style="border-bottom:1px dashed #000;">'
            + '<th style="text-align:left; padding-bottom:4px; font-size:11px; width:62%;">ITEM</th>'
            + '<th style="text-align:center; padding-bottom:4px; font-size:11px; width:10%;">QTY</th>'
            + '<th style="text-align:right; padding-bottom:4px; font-size:11px; width:28%;">AMOUNT</th>'
            + '</tr></thead>'
            + '<tbody>' + itemRows + '</tbody>'
            + '</table>'

            // ── SUMMARY ──
            + '<table width="100%" cellspacing="0" cellpadding="2" style="font-size:12px; color:#000; border-top:1px dashed #000; margin-top:4px; width:100%; table-layout:fixed;">'
            + '<tr><td style="width:65%;">Subtotal</td><td style="text-align:right; width:35%;">Rs.' + d.subtotal.toFixed(2) + '</td></tr>'
            + (d.discount_amount > 0 ? '<tr><td>Discount</td><td style="text-align:right;">-Rs.' + d.discount_amount.toFixed(2) + '</td></tr>' : '')
            + '<tr style="border-top:1px solid #000; font-size:14px;"><td style="padding-top:4px;">TOTAL</td><td style="text-align:right; padding-top:4px;">Rs.' + d.total.toFixed(2) + '</td></tr>'
            + '</table>'

            // ── PAYMENT DETAILS ──
            + '<table width="100%" cellspacing="0" cellpadding="2" style="font-size:12px; color:#000; border-top:1px dashed #000; margin-top:6px; width:100%; table-layout:fixed;">'
            + '<tr><td style="width:65%;">Paid (' + (methodLabel[d.payment_method] || d.payment_method) + ')</td><td style="text-align:right; width:35%;">Rs.' + d.amount_paid.toFixed(2) + '</td></tr>'
            + (d.change_amount > 0 ? '<tr><td>Change</td><td style="text-align:right;">Rs.' + d.change_amount.toFixed(2) + '</td></tr>' : '')
            + '</table>'

            // ── FOOTER ──
            + '<div style="text-align:center; font-size:11px; margin-top:8px; color:#000; border-top:1px dashed #000; padding-top:6px;">Thank you for dining with us!<br>We look forward to seeing you again.<br>Powered By JAAN Network (PVT) Ltd</div>';

        lastBillHtml = html;
        document.getElementById('paidBillContent').innerHTML = html;
        openModal('paidBillModal');
    }
    function printPaidBill() { printWindow(lastBillHtml); }

    // Same 80mm thermal print window as POS (printReceipt).
    function printWindow(html) {
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

    /* ---------- misc ---------- */
    function openModal(id) { document.getElementById(id).classList.add('open'); }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    function toast(msg) {
        const t = document.createElement('div');
        t.textContent = msg;
        t.style.cssText = 'position:fixed;bottom:20px;left:50%;transform:translateX(-50%);background:#0f172a;color:#fff;padding:10px 18px;border-radius:8px;font-size:13px;z-index:80;';
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 1800);
    }

    loadRooms();
    setInterval(loadRooms, 15000); // keep statuses fresh
</script>
@endsection
