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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Información de identificación
            $table->string('document_type')->comment('Tipo de documento (DNI, RUC, CE, PASAPORTE, OTRO)');
            $table->string('document_number')->unique()->comment('Número de documento de identidad o RUC'); // Debe ser único para el tipo de documento

            // Información del cliente (puede ser persona natural o jurídica)
            $table->string('name')->nullable()->comment('Nombre/Razón Social'); // Para personas naturales, el nombre; para jurídicas, la razón social
            $table->string('last_name')->nullable()->comment('Apellido Paterno (solo para personas naturales)');
            $table->string('maternal_last_name')->nullable()->comment('Apellido Materno (solo para personas naturales)');
            $table->string('commercial_name')->nullable()->comment('Nombre comercial (opcional, para empresas)');
            $table->string('email')->nullable()->unique();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable(); // Dirección fiscal/domicilio
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('economic_activity')->nullable()->comment('Actividad económica (para RUC)'); // Para RUC

            $table->boolean('is_active')->default(true); // Si el cliente está activo

            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
