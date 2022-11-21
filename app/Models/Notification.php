<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'readed',
        'user_id',
        'title',
        'type',
        'description',
        'denounces_id',
    ];
}
