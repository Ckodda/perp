<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    //
    use HasFactory, SoftDeletes; // Usa el trait SoftDeletes

    protected $fillable = [
        'company_id',
        'product_category_id', 
        'name',
        'sku',
        'unit_of_measure',
        'purchase_price',
        'purchase_currency_id',
        'sale_price',
        'sale_currency_id',
        'stock',
        'min_stock_alert',
        'description',
        'image',
        'weight',
        'weight_unit',
        'length',
        'width',
        'height',
        'dimension_unit',
        'is_active',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'decimal:2',
        'min_stock_alert' => 'integer',
        'is_active' => 'boolean',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns the product.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function purchaseCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'purchase_currency_id');
    }
    public function saleCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'sale_currency_id');
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
