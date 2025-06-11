<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quality extends Model
{
    use HasFactory;
    protected $fillable = [
        'project',
        'no_wo',
        'description',
        'responds',
        'image',
        'date',
    ];
}
