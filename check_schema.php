<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check the actual column definition
$columns = \Illuminate\Support\Facades\DB::select("DESCRIBE order_items");
echo "\n=== order_items columns ===\n";
foreach($columns as $col) {
    echo $col->Field . " | Type: " . $col->Type . " | Null: " . $col->Null . " | Default: " . ($col->Default ?? 'NULL') . "\n";
    if ($col->Field === 'kot_printed') {
        echo "  ^^^ KOT_PRINTED COLUMN FOUND ^^^\n";
    }
}
