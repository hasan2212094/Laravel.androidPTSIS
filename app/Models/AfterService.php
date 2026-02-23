<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AfterService extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = 
    [
        'id',
        'user_id_by',
        'user_id_to',
        'client',
        'jenis_kendaraan',
        'no_polisi',
        'no_rangka',
        'produk',
        'waranti',
        'date_start',
        'date_end',
        'keterangan',
        'status_pekerjaan',
        'comment_progress',
        'comment_done',
    ];
   public function images_Progress()
{
    return $this->hasMany(imageprogressass::class, 'afterservice_id');
}

public function imagesDone()
{
    return $this->hasMany(imagedoneass::class, 'afterservice_id');
}
    public function userBy()
    {
        return $this->belongsTo(User::class, 'user_id_by');
    }

    public function userTo()
    {
        return $this->belongsTo(User::class, 'user_id_to');
    }

}
