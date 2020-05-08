<?php namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class Star extends Model { 

    protected $table = 'star';

    // disable column updated_at
    const UPDATED_AT = null;
    protected $fillable = ['created_at'];
}
