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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
              $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->string('sku')->unique()->nullable();
            $table->string('unit_of_measure')->default('UNIT'); 
            $table->decimal('purchase_price', 10, 2)->default(0.00); 
            $table->decimal('sale_price', 10, 2)->default(0.00); 
            $table->decimal('stock', 10, 2)->default(0.00);
            $table->integer('min_stock_alert')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
