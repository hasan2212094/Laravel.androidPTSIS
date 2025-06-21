<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityImage extends Model
{
    use HasFactory;
    protected $fillable = ['quality_id', 'image_path'];

    public function quality()
    {
        return $this->belongsTo(Quality::class);
    }
}
