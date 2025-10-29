<?php

namespace App\Models;

use App\Models\MaintenancesImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class maintenance extends Model
{
    use HasFactory;
    use SoftDeletes;
   protected $fillable = [
        'user_id_by',
        'user_id_to',
        'name_mesin',
        'jenis_perbaikan',
        'keterangan',
        'status_perbaikan',
        'date_start',
        'equipment_id',
        'comment_done',
        'date_end',
    ];

    public function userBy()
    {
        return $this->belongsTo(User::class, 'user_id_by');
    }

    public function userTo()
    {
        return $this->belongsTo(User::class, 'user_id_to');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function images()
    {
        return $this->hasMany(MaintenancesImage::class, 'maintenance_id');
    }

   public function images_done()
{
    return $this->hasMany(MaintenancesImageDone::class, 'maintenance_id');
}

}
