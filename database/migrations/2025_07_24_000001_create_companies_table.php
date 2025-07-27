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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('corporate_name')->unique()->comment('Razón Social o Nombre de la Empresa');
            $table->string('ruc')->unique()->nullable()->comment('Registro Único de Contribuyentes');
            $table->string('address')->nullable()->comment('Dirección principal');
            $table->string('phone_number')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('is_active')->default(true)->comment('Indica si la empresa está activa');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
