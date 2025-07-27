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
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            $table->string('document_type')->comment('Tipo de documento (DNI, RUC, CE, PASAPORTE)');
            $table->string('document_number')->comment('Número de documento de identidad o RUC');
            $table->unique(['document_type', 'document_number', 'company_id'], 'customers_document_unique_by_company');

            $table->string('name')->nullable()->comment('Nombre/Razón Social');
            $table->string('last_name')->nullable()->comment('Apellido Paterno');
            $table->string('maternal_last_name')->nullable()->comment('Apellido Materno');
            $table->string('commercial_name')->nullable()->comment('Nombre comercial (opcional)');
            $table->string('email')->nullable();

            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('economic_activity')->nullable();

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
        Schema::dropIfExists('customers');
    }
};
