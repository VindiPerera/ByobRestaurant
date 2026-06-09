<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the MOST RECENT order
$order = \App\Models\Order::orderBy('id', 'desc')->first();
echo "\n=== Order Details ===\n";
echo "Order ID: " . $order->id . "\n";
echo "Order Number: " . $order->order_number . "\n";
echo "Status: " . $order->status . "\n";
echo "\n=== Items ===\n";

$items = $order->items()->get();
foreach($items as $item) {
    $printed = $item->kot_printed ? 'YES' : 'NO ';
    echo "ID:{$item->id} | Product:{$item->product_name} | Qty:{$item->quantity} | KOT:{$printed} | Unit Price:{$item->unit_price} | is_bar_item:" . ($item->is_bar_item ? 'Y' : 'N') . "\n";
}

echo "\n=== Unprinted Items (kitchen items only) ===\n";
$unprintedItems = $order->items()->where('is_bar_item', false)->where('kot_printed', false)->get();
echo "Count: " . $unprintedItems->count() . "\n";
foreach($unprintedItems as $item) {
    echo "ID:{$item->id} | Product:{$item->product_name} | Qty:{$item->quantity}\n";
}
