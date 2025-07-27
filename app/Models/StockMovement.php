<?php

namespace App\Models;

use App\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    //
    use HasFactory, SoftDeletes; // Usar SoftDeletes

    protected $fillable = [
        'company_id', // Añadir company_id
        'product_id',
        'warehouse_id',
        'type',
        'quantity',
        'reference',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'type' => StockMovementType::class,
        'quantity' => 'integer',
    ];

    // Relaciones
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
