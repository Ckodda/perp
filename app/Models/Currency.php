<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    //
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'code',
        'symbol',
        'decimal_separator',
        'thousands_separator',
        'decimal_digits',
        'symbol_first',
        'is_active',
    ];

    protected $casts = [
        'decimal_digits' => 'integer',
        'symbol_first' => 'boolean',
        'is_active' => 'boolean',
    ];
}
