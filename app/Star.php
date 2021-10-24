<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Star extends Model
{
    const UPDATED_AT = null;
    protected $fillable = ['vote', 'ip', 'created_at'];
}
