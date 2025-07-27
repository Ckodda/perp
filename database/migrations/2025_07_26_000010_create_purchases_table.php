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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->date('purchase_date');
            $table->string('invoice_number')->unique()->nullable();
            $table->decimal('subtotal_amount', 10, 2);
            $table->decimal('igv_percentage', 5, 2)->default(18.00);
            $table->decimal('igv_tax_amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('currency_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'returned', 'partially_received'])
                ->default('pending')
                ->comment('Estado de la compra');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
