<?php

namespace App\Models;

use App\TaxIdType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_id_type',
        'tax_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'tax_id_type' => TaxIdType::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // public function purchases(): HasMany
    // {
    //     return $this->hasMany(Purchase::class);
    // }
}
