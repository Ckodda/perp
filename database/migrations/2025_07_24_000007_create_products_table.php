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
            $table->foreignId('product_category_id')
                  ->nullable() 
                  ->constrained('product_categories')
                  ->onDelete('set null');
            $table->string('name');
            $table->string('sku')->comment('Stock Keeping Unit - Código de Producto');
            $table->string('unit_of_measure')->nullable()->comment('Unidad de medida (ej. kg, unidad, litro)');
            $table->text('description')->nullable();
            $table->string('image')->nullable()->comment('Ruta a la imagen del producto');
            $table->decimal('purchase_price', 10, 2)->nullable()->comment('Precio de compra unitario');
            $table->foreignId('purchase_currency_id')
                  ->nullable()
                  ->constrained('currencies')
                  ->onDelete('set null');
            $table->decimal('sale_price', 10, 2)->nullable()->comment('Precio de venta unitario');
            $table->foreignId('sale_currency_id')
                  ->nullable()
                  ->constrained('currencies')
                  ->onDelete('set null');
            $table->integer('stock')->default(0)->comment('Cantidad actual en inventario');
            $table->integer('min_stock_alert')->default(0)->comment('Nivel mínimo de stock para alerta');
            $table->decimal('weight', 10, 3)->nullable()->comment('Peso del producto');
            $table->string('weight_unit', 10)->nullable()->comment('Unidad de peso (ej. kg, lb)');
            $table->decimal('length', 10, 3)->nullable()->comment('Longitud del producto');
            $table->decimal('width', 10, 3)->nullable()->comment('Ancho del producto');
            $table->decimal('height', 10, 3)->nullable()->comment('Altura del producto');
            $table->string('dimension_unit', 10)->nullable()->comment('Unidad de dimensión (ej. cm, in)');
            $table->boolean('is_active')->default(true);
            $table->unique(['name', 'company_id'], 'products_name_unique_by_company');
            $table->unique(['sku', 'company_id'], 'products_sku_unique_by_company');
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
