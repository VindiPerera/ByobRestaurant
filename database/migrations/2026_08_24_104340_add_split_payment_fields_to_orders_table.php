<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Split payment details (method1/amount1 + method2/amount2)
            $table->string('split_method1')->nullable()->after('payment_method');
            $table->decimal('split_amount1', 12, 2)->nullable()->after('split_method1');
            $table->string('split_method2')->nullable()->after('split_amount1');
            $table->decimal('split_amount2', 12, 2)->nullable()->after('split_method2');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['split_method1', 'split_amount1', 'split_method2', 'split_amount2']);
        });
    }
};
