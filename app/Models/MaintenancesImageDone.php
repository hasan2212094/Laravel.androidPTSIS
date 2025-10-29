<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancesImageDone extends Model
{
     protected $fillable = [
        'maintenance_id',
        'image_path_done',
    ];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }
}
