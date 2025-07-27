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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('set null');
            $table->enum('type', ['purchase_in', 'sale_out', 'adjustment_in', 'adjustment_out', 'transfer_in', 'transfer_out'])->comment('Tipo de movimiento');
            $table->integer('quantity')->comment('Cantidad de productos en este movimiento');
            $table->string('reference')->nullable()->comment('Número de documento (factura, guía, ajuste)');
            $table->text('notes')->nullable()->comment('Observaciones o razón del movimiento');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario que realizó el movimiento');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
