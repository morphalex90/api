<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YahtzeePlayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'playername',
        'session_id',
        'status'
    ];
}
