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
            $table->string('name')->unique();
            $table->string('code', 3)->unique();
            $table->string('symbol', 10); 
            $table->string('decimal_separator', 1)->default('.');
            $table->string('thousands_separator', 1)->default(',');
            $table->unsignedTinyInteger('decimal_digits')->default(2);
            $table->boolean('symbol_first')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        \Illuminate\Support\Facades\DB::table('currencies')->insert([
            [
                'name' => 'Sol Peruano',
                'code' => 'PEN',
                'symbol' => 'S/.',
                'decimal_separator' => '.',
                'thousands_separator' => ',',
                'decimal_digits' => 2,
                'symbol_first' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dólar Estadounidense',
                'code' => 'USD',
                'symbol' => '$',
                'decimal_separator' => '.',
                'thousands_separator' => ',',
                'decimal_digits' => 2,
                'symbol_first' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
