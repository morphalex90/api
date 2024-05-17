<?php

namespace App\Models\WM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'profile_id',
        'score',
    ];
}
