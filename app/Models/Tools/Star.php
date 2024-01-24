<?php

namespace App\Models\Tools;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Star extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'vote',
        'ip_address',
    ];

    protected $table = 'tools_stars';
}
