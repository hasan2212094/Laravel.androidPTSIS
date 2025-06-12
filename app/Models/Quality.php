<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quality extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project',
        'no_wo',
        'description',
        'responds',
        'image',
        'date',
    ];

    protected $casts = [
        'responds' => 'boolean',
        'date' => 'date',
    ];
}