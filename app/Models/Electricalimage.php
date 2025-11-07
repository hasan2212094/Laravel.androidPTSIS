<?php

namespace App\Models;

use App\Models\Electrical;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Electricalimage extends Model
{
    use HasFactory;
    protected $fillable = [
        'electrical_id',
        'image_path',
    ];
    public function electrical()
    {
        return $this->belongsTo(Electrical::class);
    }
}
