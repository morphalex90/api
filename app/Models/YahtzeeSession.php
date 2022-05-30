<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YahtzeeSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'name',
        'partecipants_max_number'
    ];
}
