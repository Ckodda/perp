<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    //
    use HasFactory, SoftDeletes; 

    protected $fillable = [
        'company_id',
        'customer_id',
        'user_id',
        'currency_id',
        'status',
        'quote_date',
        'expiration_date',
        'subtotal_amount',
        'igv_tax_amount',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'quote_date' => 'date',
        'expiration_date' => 'date',
        'subtotal_amount' => 'decimal:2', 
        'igv_tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relaciones

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }


    /**
     * Get the client that owns the quote.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user (salesperson) that created the quote.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the currency of the quote.
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get the quote items for the quote.
     */
    public function quoteItems(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    // Método para recalcular los totales de la cotización
    // Este método será llamado, por ejemplo, en el `saving` hook del modelo
    // o después de añadir/editar/eliminar QuoteItems.
    public function recalculateTotals(): void
    {
        // Asegúrate de recargar la relación para obtener los datos más recientes
        // Esto es importante si los items han sido modificados pero no el modelo padre aún.
        $this->load('quoteItems');

        $subtotal = $this->quoteItems->sum('subtotal');
        $igv = $this->quoteItems->sum('igv_tax_amount');
        $total = $this->quoteItems->sum('total');

        $this->subtotal_amount = round($subtotal, 2);
        $this->igv_tax_amount = round($igv, 2);
        $this->total_amount = round($total, 2);

        // Guardar el modelo para persistir los cambios en la base de datos.
        // Si este método es llamado desde un 'saving' hook, no necesitas `$this->save()` aquí.
        // Pero si lo llamas manualmente (ej. desde un RelationManager), sí.
        // Por seguridad, si lo llamas manualmente, es mejor incluirlo:
        if ($this->isDirty('subtotal_amount') || $this->isDirty('igv_tax_amount') || $this->isDirty('total_amount')) {
            $this->save();
        }
    }


    // El hook `booted` es ideal para asegurar que los totales se actualicen
    // automáticamente cada vez que una cotización se guarda.
    protected static function booted(): void
    {
        static::saving(function (Quote $quote) {
            // Recalcular los totales justo antes de guardar el modelo
            // Esto asegura que estén actualizados cuando se persista el Quote en la DB
            $quote->subtotal_amount = round($quote->quoteItems()->sum('subtotal'), 2);
            $quote->igv_tax_amount = round($quote->quoteItems()->sum('igv_tax_amount'), 2);
            $quote->total_amount = round($quote->quoteItems()->sum('total'), 2);
        });
    }
}
