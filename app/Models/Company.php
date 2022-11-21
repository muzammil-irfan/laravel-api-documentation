<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'ruc',
        'department',
        'city',
        'phone',
        'address',
        'site',
        'logo',
        'description',
        'enabled',
        'country_id',
        'bussiness_id',
        'sector_id'
    ];
}
