<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class imageprogressass extends Model
{
    use HasFactory;
    protected $table = 'imageprogressasses';
     protected $fillable = [
        'afterservice_id',
        'image_path',
    ];
    public function afterserviceprogress()
    {
        return $this->belongsTo(AfterService::class);
    }
}
