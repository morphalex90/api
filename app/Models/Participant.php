<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'user_id',
        'last_read',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select('id', 'name');
    }
}
