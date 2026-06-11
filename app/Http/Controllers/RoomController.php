<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\ShiftTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomController extends Controller
{
    /* ===========================================================
     |  Dashboard
     * =========================================================== */

    public function index()
    {
        $rooms = Room::with('activeBooking.order.items')->orderBy('room_number')->get();
        $categories = Category::where('status', 'active')->orderBy('sort_order')->get();
        $products = Product::where('status', 'active')->get();
        $modules = $this->currentUser()->role->modules()->get();

        $activeShift = Shift::where('user_id', auth()->id())
            ->where('status', 'active')
            ->first();

        return view('modules.rooms', [
            'rooms' => $rooms,
            'categories' => $categories,
            'products' => $products,
            'modules' => $modules,
            'activeShift' => $activeShift,
            'bookingDurationMinutes' => (int) Setting::get('room_booking_duration_minutes', 120),
        ]);
    }

    /**
     * Past room bookings (completed / cancelled) with their final bills.
     */
    public function roomHistory(Request $request)
    {
        $query = RoomBooking::with('room', 'order')
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderByDesc('checked_out_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('room', fn ($r) => $r->where('room_number', $search));
            });
        }

        if (in_array($request->input('status'), ['completed', 'cancelled'], true)) {
            $query->where('status', $request->input('status'));
        }

        $bookings = $query->paginate(15)->withQueryString();
        $modules = $this->currentUser()->role->modules()->get();

        return view('modules.room-history', [
            'bookings' => $bookings,
            'modules' => $modules,
            'search' => $request->input('search', ''),
            'status' => $request->input('status', ''),
        ]);
    }

    /**
     * Live snapshot of all rooms (used by the dashboard and the POS rooms panel).
     */
    public function getRooms()
    {
        $rooms = Room::with('activeBooking.order.items')->orderBy('room_number')->get()->map(function ($room) {
            $booking = $room->activeBooking;
            $order = $booking?->order;

            $foodItems = $order ? $order->items->whereNotNull('product_id') : collect();

            return [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'name' => $room->name,
                'capacity' => $room->capacity,
                'base_price' => (float) $room->base_price,
                'status' => $room->status,
                'has_booking' => $booking ? true : false,
                'booking_id' => $booking?->id,
                'booking_number' => $booking?->booking_number,
                'customer_name' => $booking?->customer_name,
                'started_at' => $booking?->started_at,
                'expires_at' => $booking?->expires_at,
                'remaining_seconds' => $booking ? $booking->remainingSeconds() : null,
                'food_items_count' => (int) $foodItems->sum('quantity'),
                'total' => $order ? (float) $order->items->sum('subtotal') : 0,
            ];
        });

        return response()->json($rooms);
    }

    /* ===========================================================
     |  Booking lifecycle
     * =========================================================== */

    public function book(Request $request, Room $room)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        if ($room->activeBooking) {
            return response()->json([
                'success' => false,
                'message' => 'Room ' . $room->room_number . ' already has an active booking.',
            ], 422);
        }

        $baseMinutes = (int) Setting::get('room_booking_duration_minutes', 120);
        $now = now();

        $booking = RoomBooking::create([
            'booking_number' => 'RB-' . strtoupper(Str::random(8)),
            'room_id' => $room->id,
            'user_id' => $this->currentUser()->id,
            'customer_name' => $validated['customer_name'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'status' => 'active',
            'base_minutes' => $baseMinutes,
            'base_price' => $room->base_price,
            'started_at' => $now,
            'expires_at' => $now->copy()->addMinutes($baseMinutes),
        ]);

        // Master order that accumulates room-time + food charges.
        $order = Order::create([
            'order_number' => 'ORD-' . Str::random(8),
            'room_booking_id' => $booking->id,
            'customer_name' => $booking->customer_name,
            'customer_phone' => $booking->customer_phone,
            'user_id' => $this->currentUser()->id,
            'order_type' => 'vip_room',
            'status' => 'pending',
            'waiter_name' => $this->currentUser()->name,
        ]);

        // Base room-time charge as a non-product line (never sent to kitchen).
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => null,
            'product_name' => 'Room Booking (first ' . $this->formatDuration($baseMinutes) . ')',
            'unit_price' => $room->base_price,
            'quantity' => 1,
            'subtotal' => $room->base_price,
            'is_bar_item' => true,
            'kot_printed' => true,
            'printed_qty' => 1,
        ]);

        $this->syncOrderTotals($order);
        $room->update(['status' => 'occupied']);

        return response()->json([
            'success' => true,
            'booking_id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'room_number' => $room->room_number,
        ]);
    }

    public function getBooking(RoomBooking $booking)
    {
        $booking->load('room', 'order.items.product');
        $order = $booking->order;
        $items = $order ? $order->items : collect();

        $roomItems = $items->whereNull('product_id')->values();
        $foodItems = $items->whereNotNull('product_id')->values();

        $roomCharge = (float) $roomItems->sum('subtotal');
        $foodTotal = (float) $foodItems->sum('subtotal');

        return response()->json([
            'id' => $booking->id,
            'booking_number' => $booking->booking_number,
            'room_id' => $booking->room_id,
            'room_number' => $booking->room->room_number,
            'order_id' => $order?->id,
            'status' => $booking->status,
            'customer_name' => $booking->customer_name,
            'customer_phone' => $booking->customer_phone,
            'started_at' => $booking->started_at,
            'expires_at' => $booking->expires_at,
            'remaining_seconds' => $booking->remainingSeconds(),
            'base_minutes' => $booking->base_minutes,
            'room_charge' => $roomCharge,
            'food_total' => $foodTotal,
            'subtotal' => $roomCharge + $foodTotal,
            'room_items' => $roomItems->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'unit_price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
            ]),
            'food_items' => $foodItems->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'unit_price' => (float) $item->unit_price,
                'quantity' => $item->quantity,
                'subtotal' => (float) $item->subtotal,
                'kitchen_notes' => $item->kitchen_notes,
                'is_bar_item' => (bool) $item->is_bar_item,
                'kot_printed' => (bool) $item->kot_printed,
                'image' => $item->product?->image,
            ]),
        ]);
    }

    public function addExtension(Request $request, RoomBooking $booking)
    {
        $validated = $request->validate([
            'hours' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        if ($booking->status !== 'active' || !$booking->order) {
            return response()->json(['success' => false, 'message' => 'Booking is not active.'], 422);
        }

        OrderItem::create([
            'order_id' => $booking->order->id,
            'product_id' => null,
            'product_name' => 'Extra Time (' . $validated['hours'] . 'h)',
            'unit_price' => $validated['price'],
            'quantity' => 1,
            'subtotal' => $validated['price'],
            'is_bar_item' => true,
            'kot_printed' => true,
            'printed_qty' => 1,
        ]);

        // Extend the countdown from the current expiry.
        $booking->update([
            'expires_at' => $booking->expires_at->copy()->addMinutes($validated['hours'] * 60),
        ]);

        $this->syncOrderTotals($booking->order);

        return response()->json([
            'success' => true,
            'message' => 'Added ' . $validated['hours'] . ' hour(s).',
            'remaining_seconds' => $booking->fresh()->remainingSeconds(),
        ]);
    }

    public function updateCustomer(Request $request, RoomBooking $booking)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        $booking->update($validated);
        if ($booking->order) {
            $booking->order->update($validated);
        }

        return response()->json(['success' => true, 'message' => 'Customer details updated']);
    }

    /* ===========================================================
     |  Master-order item editing (staff side)
     * =========================================================== */

    public function addItem(Request $request, RoomBooking $booking)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'kitchen_notes' => 'nullable|string',
        ]);

        if (!$booking->order) {
            return response()->json(['success' => false, 'message' => 'Booking has no order.'], 422);
        }

        $product = Product::find($validated['product_id']);
        $this->addProductToOrder(
            $booking->order,
            $product,
            $validated['quantity'],
            $validated['kitchen_notes'] ?? null
        );

        $this->syncOrderTotals($booking->order);

        return response()->json([
            'success' => true,
            'message' => $product->name . ' added to room bill',
        ]);
    }

    public function removeItem(RoomBooking $booking, OrderItem $item)
    {
        // Restock product-backed items only (room-time lines have no product).
        if ($item->product_id) {
            Product::where('id', $item->product_id)
                ->where('is_unlimited_stock', false)
                ->increment('quantity', $item->quantity);
        }

        $item->delete();
        $this->syncOrderTotals($booking->order);

        return response()->json(['success' => true, 'message' => 'Item removed']);
    }

    public function updateItem(Request $request, RoomBooking $booking, OrderItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($item->product_id) {
            $diff = $validated['quantity'] - $item->quantity;
            if ($diff > 0) {
                Product::where('id', $item->product_id)
                    ->where('is_unlimited_stock', false)
                    ->decrement('quantity', $diff);
            } elseif ($diff < 0) {
                Product::where('id', $item->product_id)
                    ->where('is_unlimited_stock', false)
                    ->increment('quantity', abs($diff));
            }
        }

        $item->update([
            'quantity' => $validated['quantity'],
            'subtotal' => $item->unit_price * $validated['quantity'],
        ]);

        $this->syncOrderTotals($booking->order);

        return response()->json(['success' => true, 'message' => 'Item updated']);
    }

    /**
     * Print kitchen tickets for the room's food items (mirrors PosController::printKot).
     */
    public function printKot(RoomBooking $booking)
    {
        $order = $booking->order;
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'No order for this booking.'], 422);
        }

        $order->load('items');
        $printableItems = $order->items
            ->where('is_bar_item', false)
            ->filter(fn ($item) => $item->quantity > $item->printed_qty)
            ->values();

        if ($printableItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No new items to print.',
                'order_number' => $order->order_number,
            ], 422);
        }

        $itemsToPrint = [];
        foreach ($printableItems as $item) {
            $itemsToPrint[] = [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity - $item->printed_qty,
                'kitchen_notes' => $item->kitchen_notes,
            ];
            $item->update(['kot_printed' => true, 'printed_qty' => $item->quantity]);
        }

        $order->update(['kot_printed_at' => now()]);

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'room_number' => $booking->room->room_number,
            'items' => $itemsToPrint,
        ]);
    }

    /* ===========================================================
     |  Checkout & cancel
     * =========================================================== */

    public function checkout(Request $request, RoomBooking $booking)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,bank_transfer,mixed',
            'amount_paid' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        if ($booking->status !== 'active' || !$booking->order) {
            return response()->json(['success' => false, 'message' => 'Booking is not active.'], 422);
        }

        $order = $booking->order;
        $order->load('items');
        $booking->load('room');

        $roomCharge = (float) $order->items->whereNull('product_id')->sum('subtotal');
        $foodTotal = (float) $order->items->whereNotNull('product_id')->sum('subtotal');
        $subtotal = $roomCharge + $foodTotal;

        $discount = 0;
        if (($validated['discount_type'] ?? null) === 'percentage') {
            $discount = ($subtotal * $validated['discount_value']) / 100;
        } elseif (($validated['discount_type'] ?? null) === 'fixed') {
            $discount = $validated['discount_value'];
        }

        $total = $subtotal - $discount;
        $amountPaid = $validated['amount_paid'];
        $change = max(0, $amountPaid - $total);

        // 1. Settle the master order so it is counted in reports (exactly once).
        $order->update([
            'status' => 'completed',
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => 0,
            'total' => $total,
            'payment_method' => $validated['payment_method'],
            'amount_paid' => $amountPaid,
            'change_amount' => $change,
            'printed_at' => now(),
            'kot_printed_at' => $order->kot_printed_at ?? ($order->items->where('is_bar_item', false)->count() > 0 ? now() : null),
        ]);

        // 2. Record to the active shift/till (mirrors PosController::payOrder).
        $room = $booking->room;
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'active')->first();
        if ($activeShift) {
            ShiftTransaction::create([
                'shift_id' => $activeShift->id,
                'order_id' => $order->id,
                'transaction_type' => 'sale',
                'amount' => $total,
                'payment_method' => $validated['payment_method'],
                'description' => "Room {$room->room_number} / {$booking->booking_number}",
            ]);

            if ($discount > 0) {
                ShiftTransaction::create([
                    'shift_id' => $activeShift->id,
                    'order_id' => $order->id,
                    'transaction_type' => 'discount',
                    'amount' => $discount,
                    'description' => "Discount on Room {$room->room_number}",
                ]);
            }
        }

        // 3. Finalize the booking and free the room.
        $booking->update([
            'status' => 'completed',
            'room_charge' => $roomCharge,
            'food_total' => $foodTotal,
            'discount_amount' => $discount,
            'total' => $total,
            'payment_method' => $validated['payment_method'],
            'amount_paid' => $amountPaid,
            'change_amount' => $change,
            'checked_out_at' => now(),
        ]);

        $room->update(['status' => 'available']);

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'booking_number' => $booking->booking_number,
            'room_number' => $room->room_number,
            'customer_name' => $booking->customer_name,
            'customer_phone' => $booking->customer_phone,
            'room_charge' => $roomCharge,
            'food_total' => $foodTotal,
            'subtotal' => $subtotal,
            'discount_amount' => (float) $discount,
            'total' => (float) $total,
            'payment_method' => $validated['payment_method'],
            'amount_paid' => (float) $amountPaid,
            'change_amount' => (float) $change,
            'items' => $order->items->map(fn ($item) => [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => (float) $item->subtotal,
                'is_room_charge' => $item->product_id === null,
            ]),
        ]);
    }

    public function cancelBooking(RoomBooking $booking)
    {
        if ($booking->order) {
            $booking->order->load('items');
            foreach ($booking->order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)
                        ->where('is_unlimited_stock', false)
                        ->increment('quantity', $item->quantity);
                }
            }
            $booking->order->update(['status' => 'cancelled']);
        }

        $booking->update(['status' => 'cancelled', 'checked_out_at' => now()]);
        $booking->room->update(['status' => 'available']);

        return response()->json(['success' => true, 'message' => 'Booking cancelled']);
    }

    /* ===========================================================
     |  QR codes
     * =========================================================== */

    public function generateRoomQrCode(Room $room)
    {
        try {
            $qrUrl = route('room.order.menu', ['roomId' => $room->id], true);

            $renderer = new \BaconQrCode\Renderer\Image\Svg();
            $renderer->setHeight(200);
            $renderer->setWidth(200);

            $qrCode = \BaconQrCode\Encoder\Encoder::encode(
                $qrUrl,
                \BaconQrCode\Common\ErrorCorrectionLevel::H,
                \BaconQrCode\Encoding\Encoding::UTF_8
            );

            $image = $renderer->render($qrCode);

            return response($image, 200)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'inline; filename="room-' . $room->room_number . '-qr.svg"');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate QR code: ' . $e->getMessage()], 500);
        }
    }

    public function roomQrCodesSettings()
    {
        $rooms = Room::orderBy('room_number')->get();
        $qrCodes = [];

        foreach ($rooms as $room) {
            $qrUrl = route('room.order.menu', ['roomId' => $room->id], true);
            $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrUrl);

            $qrCodes[] = [
                'room_id' => $room->id,
                'room_number' => $room->room_number,
                'room_name' => $room->name,
                'capacity' => $room->capacity,
                'qr_url' => $qrUrl,
                'qr_image_url' => $qrImageUrl,
            ];
        }

        $modules = $this->currentUser()->role->modules()->get();
        return view('modules.room-qr-settings', ['qrCodes' => $qrCodes, 'modules' => $modules]);
    }

    public function downloadRoomQrCode($roomId)
    {
        $room = Room::findOrFail($roomId);
        $qrUrl = route('room.order.menu', ['roomId' => $room->id], true);

        try {
            $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&format=png&data=' . urlencode($qrUrl);
            $qrImageData = file_get_contents($apiUrl);

            return response($qrImageData)
                ->header('Content-Type', 'image/png')
                ->header('Content-Disposition', 'attachment; filename="room-' . $room->room_number . '-qr.png"');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate QR code: ' . $e->getMessage()], 500);
        }
    }

    /* ===========================================================
     |  Settings (duration + per-room base price)
     * =========================================================== */

    public function settings()
    {
        $rooms = Room::orderBy('room_number')->get();
        $modules = $this->currentUser()->role->modules()->get();

        return view('modules.room-settings', [
            'rooms' => $rooms,
            'modules' => $modules,
            'bookingDurationMinutes' => (int) Setting::get('room_booking_duration_minutes', 120),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'booking_duration_minutes' => 'required|integer|min:1',
            'base_prices' => 'nullable|array',
            'base_prices.*' => 'nullable|numeric|min:0',
        ]);

        Setting::set('room_booking_duration_minutes', $validated['booking_duration_minutes'], 'integer');

        foreach ($validated['base_prices'] ?? [] as $roomId => $price) {
            Room::where('id', $roomId)->update(['base_price' => $price ?? 0]);
        }

        return redirect()->route('rooms.settings')->with('success', 'Room settings updated successfully');
    }

    /* ===========================================================
     |  Public QR ordering (no auth)
     * =========================================================== */

    public function roomQrMenu($roomId)
    {
        $room = Room::findOrFail($roomId);
        $activeBooking = $room->activeBooking;
        $categories = Category::where('status', 'active')->orderBy('sort_order')->get();
        $products = Product::where('status', 'active')->get();

        return view('qr-menu.room-order', [
            'room' => $room,
            'hasActiveBooking' => (bool) $activeBooking,
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function submitRoomOrder(Request $request, $roomId)
    {
        $room = Room::findOrFail($roomId);
        $booking = $room->activeBooking;

        if (!$booking || !$booking->order) {
            return response()->json([
                'success' => false,
                'message' => 'This room is not currently open for ordering. Please contact reception.',
            ], 422);
        }

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.kitchen_notes' => 'nullable|string',
        ]);

        try {
            $order = $booking->order;

            foreach ($validated['items'] as $line) {
                $product = Product::find($line['product_id']);
                $this->addProductToOrder($order, $product, $line['quantity'], $line['kitchen_notes'] ?? null);
            }

            $this->syncOrderTotals($order);

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'room_number' => $room->room_number,
                'message' => 'Order placed successfully! Your order has been sent to the kitchen and added to your room bill.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ===========================================================
     |  Helpers
     * =========================================================== */

    /**
     * Add a product to an order, merging with an existing line and decrementing stock.
     */
    private function addProductToOrder(Order $order, Product $product, int $quantity, ?string $notes): OrderItem
    {
        $price = $product->selling_price ?? $product->price;

        $existing = OrderItem::where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->quantity += $quantity;
            $existing->subtotal = $existing->unit_price * $existing->quantity;
            if ($notes) {
                $existing->kitchen_notes = $notes;
            }
            // New units still need a kitchen ticket.
            $existing->kot_printed = false;
            $existing->save();
            $item = $existing;
        } else {
            $item = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $price,
                'quantity' => $quantity,
                'subtotal' => $price * $quantity,
                'kitchen_notes' => $notes,
                'is_bar_item' => false,
                'kot_printed' => false,
                'printed_qty' => 0,
            ]);
        }

        if (!$product->is_unlimited_stock) {
            $product->decrement('quantity', $quantity);
        }

        return $item;
    }

    private function syncOrderTotals(Order $order): void
    {
        $subtotal = $order->items()->sum('subtotal');
        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'total' => $subtotal,
        ]);
    }

    private function formatDuration(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours}h {$mins}m";
        }
        if ($hours > 0) {
            return "{$hours}h";
        }
        return "{$mins}m";
    }
}
