<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\RestaurantTable;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $tables = RestaurantTable::all()->load('activeOrder.items');
        $categories = Category::where('status', 'active')->orderBy('sort_order')->get();
        $products = Product::where('status', 'active')->get();
        $modules = auth()->user()->role->modules()->get();

        return view('modules.pos', [
            'tables' => $tables,
            'categories' => $categories,
            'products' => $products,
            'modules' => $modules,
        ]);
    }

    public function getTables()
    {
        $tables = RestaurantTable::with('activeOrder.items')->get()->map(function ($table) {
            $activeOrder = $table->activeOrder;
            return [
                'id' => $table->id,
                'table_number' => $table->table_number,
                'name' => $table->name,
                'capacity' => $table->capacity,
                'status' => $table->status,
                'section' => $table->section,
                'occupied_at' => $table->occupied_at,
                'has_order' => $activeOrder ? true : false,
                'order_id' => $activeOrder?->id,
                'order_items_count' => $activeOrder?->items->count() ?? 0,
            ];
        });

        return response()->json($tables);
    }

    public function getProducts(Request $request)
    {
        $query = Product::where('status', 'active');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('barcode', $search);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

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
            ];
        });

        return response()->json($products);
    }

    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'table_id' => 'nullable|exists:restaurant_tables,id',
            'customer_id' => 'nullable|exists:customers,id',
            'order_type' => 'required|in:dine_in,takeaway,delivery,vip_room',
            'waiter_name' => 'nullable|string',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-' . Str::random(8),
            'table_id' => $validated['table_id'] ?? null,
            'customer_id' => $validated['customer_id'] ?? null,
            'user_id' => auth()->id(),
            'order_type' => $validated['order_type'],
            'waiter_name' => $validated['waiter_name'] ?? auth()->user()->name,
        ]);

        if ($validated['table_id']) {
            RestaurantTable::find($validated['table_id'])->update([
                'status' => 'occupied',
                'occupied_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    public function getOrder(Order $order)
    {
        $order->load('items.product', 'table', 'customer');

        return response()->json([
            'id' => $order->id,
            'order_number' => $order->order_number,
            'table_id' => $order->table_id,
            'table_number' => $order->table?->table_number,
            'order_type' => $order->order_type,
            'status' => $order->status,
            'subtotal' => (float) $order->subtotal,
            'discount_amount' => (float) $order->discount_amount,
            'tax_amount' => (float) $order->tax_amount,
            'total' => (float) $order->total,
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
                ];
            }),
        ]);
    }

    public function addItem(Request $request, Order $order)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'kitchen_notes' => 'nullable|string',
            'is_bar_item' => 'nullable|boolean',
        ]);

        $product = Product::find($validated['product_id']);
        $price = $product->selling_price ?? $product->price;

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => $price,
            'quantity' => $validated['quantity'],
            'subtotal' => $price * $validated['quantity'],
            'kitchen_notes' => $validated['kitchen_notes'] ?? null,
            'is_bar_item' => $validated['is_bar_item'] ?? false,
        ]);

        $this->updateOrderTotals($order);

        return response()->json([
            'success' => true,
            'item_id' => $item->id,
            'message' => $product->name . ' added to order',
        ]);
    }

    public function removeItem(Request $request, Order $order, OrderItem $item)
    {
        $item->delete();
        $this->updateOrderTotals($order);

        return response()->json(['success' => true, 'message' => 'Item removed']);
    }

    public function updateItem(Request $request, Order $order, OrderItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'kitchen_notes' => 'nullable|string',
        ]);

        $item->update([
            'quantity' => $validated['quantity'],
            'subtotal' => $item->unit_price * $validated['quantity'],
            'kitchen_notes' => $validated['kitchen_notes'] ?? $item->kitchen_notes,
        ]);

        $this->updateOrderTotals($order);

        return response()->json(['success' => true, 'message' => 'Item updated']);
    }

    public function holdOrder(Order $order)
    {
        $order->update(['status' => 'hold']);
        return response()->json(['success' => true, 'message' => 'Order held']);
    }

    public function completeOrder(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,bank_transfer,mixed',
            'amount_paid' => 'required|numeric|min:0',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
        ]);

        $subtotal = $order->items->sum('subtotal');
        $discount = 0;

        if ($validated['discount_type'] === 'percentage') {
            $discount = ($subtotal * $validated['discount_value']) / 100;
        } elseif ($validated['discount_type'] === 'fixed') {
            $discount = $validated['discount_value'];
        }

        $tax = ($subtotal - $discount) * 0.10;
        $total = $subtotal - $discount + $tax;
        $change = $validated['amount_paid'] - $total;

        $order->update([
            'status' => 'completed',
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'total' => $total,
            'payment_method' => $validated['payment_method'],
            'amount_paid' => $validated['amount_paid'],
            'change_amount' => $change,
            'printed_at' => now(),
        ]);

        if ($order->table_id) {
            RestaurantTable::find($order->table_id)->update([
                'status' => 'available',
                'occupied_at' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'total' => (float) $total,
            'change' => (float) $change,
            'message' => 'Order completed',
        ]);
    }

    public function printKot(Order $order)
    {
        $order->update(['kot_printed_at' => now()]);
        $kitchenItems = $order->items->where('is_bar_item', false)->values();

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'items' => $kitchenItems->map(fn($item) => [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'kitchen_notes' => $item->kitchen_notes,
            ]),
        ]);
    }

    public function printBot(Order $order)
    {
        $order->update(['bot_printed_at' => now()]);
        $barItems = $order->items->where('is_bar_item', true)->values();

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'items' => $barItems->map(fn($item) => [
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'kitchen_notes' => $item->kitchen_notes,
            ]),
        ]);
    }

    public function getHeldOrders()
    {
        $orders = Order::where('status', 'hold')->with('table', 'items')->latest()->get();

        return response()->json($orders->map(fn($order) => [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'table_number' => $order->table?->table_number,
            'items_count' => $order->items->count(),
            'total' => (float) $order->total,
        ]));
    }

    private function updateOrderTotals(Order $order)
    {
        $subtotal = $order->items->sum('subtotal');
        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $subtotal * 0.10,
            'total' => $subtotal + ($subtotal * 0.10),
        ]);
    }
}
