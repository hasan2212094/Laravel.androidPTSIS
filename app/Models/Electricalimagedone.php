<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Electricalimagedone extends Model
{
    use HasFactory;
     protected $fillable = [
        'electrical_id',
        'image_path_done',
    ];
    public function electrical()
    {
        return $this->belongsTo(Electrical::class);
    }
}
