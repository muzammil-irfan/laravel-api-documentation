<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = [
        'message',
        'user_id',
        'informer_id',
        'denounces_id',
        'informer_can_see'
    ];
}
