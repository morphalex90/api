<?php

declare(strict_types=1);

namespace App\Models\Tools;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Star extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'tools_stars';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'vote',
        'ip_address',
    ];
}
