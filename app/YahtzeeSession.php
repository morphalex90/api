<?php namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class YahtzeeSession extends Model { 
    protected $fillable = ['status', 'name', 'partecipants_max_number'];
}
