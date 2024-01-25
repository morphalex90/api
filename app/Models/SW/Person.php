<?php

namespace App\Models\SW;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'height',
        'mass',
        'hair_color',
        'skin_color',
        'eye_color',
        'birth_year',
        'gender',
        'planet_id',
    ];

    protected $table = 'sw_people';

    /**
     * The planet that belong to the person.
     */
    public function planet()
    {
        return $this->hasOne(Planet::class, 'id', 'planet_id')->select('id', 'name');
    }
}
