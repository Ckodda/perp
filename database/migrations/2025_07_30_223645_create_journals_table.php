<?php

use App\JournalType;
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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete()->comment('Empresa a la que pertenece el diario');
            $table->string('name')->comment('Nombre del diario (Ej: Facturas de Venta F001)');
            $table->string('type')->default(JournalType::SALES->value)->comment('Tipo de diario (sales, purchase, cash, bank, etc.)'); // Columna para el Enum
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->nullOnDelete()->comment('Tipo de documento asociado (Factura, Boleta)');
            $table->string('series_prefix', 10)->comment('Prefijo de la serie (Ej: F001, B001)');
            $table->unsignedBigInteger('current_number')->default(0)->comment('Número correlativo actual');
            $table->boolean('is_active')->default(true)->comment('Indica si el diario está activo');
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
        Schema::dropIfExists('journals');
    }
};
