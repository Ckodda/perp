<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
            $table->foreignId('purchase_currency_id')
                  ->nullable()
                  ->constrained('currencies')
                  ->after('purchase_price');

            $table->foreignId('sale_currency_id')
                  ->nullable()
                  ->constrained('currencies')
                  ->after('sale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['purchase_currency_id']);
            $table->dropColumn('purchase_currency_id');
            $table->dropForeign(['sale_currency_id']);
            $table->dropColumn('sale_currency_id');
        });
    }
};
