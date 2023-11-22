<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = [
        // 'id'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'DESC')->with('user');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->orderBy('created_at', 'DESC')->with('user')->latest();
    }

    public function participants()
    {
        return $this->hasMany(Participant::class)->with('user');
    }
}
