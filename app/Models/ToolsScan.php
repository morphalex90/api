<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolsScan extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'uuid',
        'url',
        'ip_address',
    ];
}
