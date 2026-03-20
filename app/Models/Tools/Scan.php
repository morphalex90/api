<?php

declare(strict_types=1);

namespace App\Models\Tools;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Scan extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'tools_scans';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'url',
        'ip_address',
    ];
}
