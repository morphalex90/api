<?php

namespace App\Models\Tools;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'uuid',
        'url',
        'ip_address',
    ];

    protected $table = 'tools_scans';
}
