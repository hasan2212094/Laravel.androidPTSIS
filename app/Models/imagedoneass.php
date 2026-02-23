<?php

namespace App\Models;

use App\Models\AfterService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class imagedoneass extends Model
{
    use HasFactory;
     protected $table = 'imagedoneasses';
     protected $fillable = [
        'afterservice_id',
        'image_path',
    ];
    public function afterservicedone()
    {
        return $this->belongsTo(AfterService::class);
    }
}
