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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Nombre completo de la moneda (ej. Dólar Estadounidense)');
            $table->string('code', 3)->unique()->comment('Código ISO 4217 de la moneda (ej. USD)');
            $table->string('symbol', 10)->nullable()->comment('Símbolo de la moneda (ej. $)');
            $table->string('decimal_separator', 1)->default('.')->comment('Separador decimal (ej. . o ,)');
            $table->string('thousands_separator', 1)->default(',')->nullable()->comment('Separador de miles (ej. , o . o espacio)');
            $table->unsignedTinyInteger('decimal_digits')->default(2)->comment('Número de dígitos decimales');
            $table->boolean('symbol_first')->default(true)->comment('Indica si el símbolo va antes (true) o después (false) del número');

            $table->decimal('exchange_rate', 10, 4)->default(1.0000)->comment('Tasa de cambio respecto a una moneda base (ej. USD)');
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
        Schema::dropIfExists('currencies');
    }
};
