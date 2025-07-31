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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete(); 
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->restrictOnDelete();
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'converted'])->default('draft');

            $table->date('quote_date');
            $table->date('expiration_date')->nullable();

            $table->decimal('subtotal_amount', 12, 2)->default(0.00); // Monto sin IGV
            $table->decimal('igv_tax_amount', 12, 2)->default(0.00);   // Monto de IGV
            $table->decimal('total_amount', 12, 2)->default(0.00);     // Monto total

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
