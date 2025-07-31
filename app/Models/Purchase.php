<?php

namespace App\Models;

use App\PurchaseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    //
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'company_id',
        'supplier_id',
        'purchase_date',
        'invoice_number',
        'total_amount',
        'currency_id',
        'subtotal_amount',
        'igv_percentage',
        'igv_tax_amount',
        'notes',
        'status',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'subtotal_amount' => 'decimal:2',
        'igv_tax_amount' => 'decimal:2',
        'status' => PurchaseStatus::class,
    ];

    // Relación: Una compra pertenece a una empresa.
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Relación: Una compra pertenece a un proveedor.
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function recalculateTotals(): void
    {
        $subtotalItems = $this->purchaseItems()->sum('subtotal');
        $this->igv_tax_amount = $this->purchaseItems()->sum('igv_tax_amount');
        $this->subtotal_amount = $subtotalItems;
        $this->total_amount = $subtotalItems + ($this->igv_tax_amount ?? 0);
        $this->save();
    }

    protected static function booted(): void
    {
        static::saving(function (Purchase $purchase) {
            $purchase->total_amount = ($purchase->subtotal_amount ?? 0) + ($purchase->igv_tax_amount ?? 0);
        });
    }
}
