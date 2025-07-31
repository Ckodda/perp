<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteItem extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'quote_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'igv_percentage',
        'igv_tax_amount',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:4',
        'subtotal' => 'decimal:2',
        'igv_percentage' => 'decimal:2',
        'igv_tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
