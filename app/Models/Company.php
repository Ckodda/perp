<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    //
    use HasFactory,SoftDeletes;
    
    protected $fillable = [
        'corporate_name',
        'ruc',
        'address',
        'phone_number',
        'logo_url',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relaciones inversas (para que una Empresa pueda acceder a sus entidades)
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    // public function stockMovements(): HasMany
    // {
    //     return $this->hasMany(StockMovement::class);
    // }
}
