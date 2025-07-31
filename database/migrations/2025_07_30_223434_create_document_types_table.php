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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Nombre del tipo de documento (Ej: Factura, Boleta de Venta)');
            $table->string('code', 5)->unique()->comment('Código oficial del tipo de documento (Ej: 01, 03, 07)');
            $table->string('description')->nullable();
            $table->string('applicable_to', 20)->nullable()->comment('Indica si aplica a ventas, compras o ambos (sales, purchase, both)'); // 'sales', 'purchase', 'both'
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
