<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_type',
        'document_number',
        'name',
        'last_name',
        'maternal_last_name',
        'commercial_name',
        'email',
        'phone_number',
        'address',
        'city',
        'country',
        'economic_activity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getFullNameAttribute(): string
    {
        if ($this->document_type === 'RUC') {
            return $this->name ?? $this->commercial_name ?? $this->document_number; // Razón social o nombre comercial
        }

        // Para DNI, CE, PASAPORTE, etc.
        return trim("{$this->name} {$this->last_name} {$this->maternal_last_name}");
    }

}
