<?php

namespace App\Models;

use App\JournalType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'document_type_id',
        'series_prefix',
        'current_number',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'type' => JournalType::class,
        'is_active' => 'boolean',
        'current_number' => 'integer',
    ];

    // Relaciones
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function getNextDocumentNumber(): string
    {
        return str_pad($this->current_number + 1, 6, '0', STR_PAD_LEFT);
    }
}
