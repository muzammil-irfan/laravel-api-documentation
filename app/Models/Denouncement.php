<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Denouncement extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_in_hash',
        'state',
        'priority',
        'description',
        'people',
        'investigator_id',
        'area_id',
        'source_id',
        'informer_id',
        'office_id',
        'category_id',
        'company_id',
        'closing_reason_id',
        'closing_description',
    ];
}
