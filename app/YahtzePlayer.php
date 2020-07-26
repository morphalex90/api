<?php namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class YahtzeePlayer extends Model { 
    protected $fillable = ['playername', 'session_id', 'status'];
}
