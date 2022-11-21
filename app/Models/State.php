<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    const CREATED = 'created';
    const ASSGINED = 'assigned';
    const WAIT_ANSWER = 'wait_answer';
    const REPLIED = 'replied';
    const DESESTIMATE = 'desestimate';
    const CLOSED = 'closed';
}
