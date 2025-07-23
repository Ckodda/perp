<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    //
    use SoftDeletes;
    protected $fillable = [
        'corporate_name',
        'ruc',
        'address',
        'phone_number',
        'email',
        'logo_url',
        'is_active',
    ];
}
