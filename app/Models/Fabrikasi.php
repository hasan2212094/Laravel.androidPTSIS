<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fabrikasi extends Model
{
    use HasFactory;
    use SoftDeletes;
     protected $fillable = [
        'user_id_by',
        'user_id_to',
        'jenis_Pekerjaan',
        'keterangan',
        'qty',
        'status_pekerjaan',
        'date_start',
        'workorder_id',
        'comment_done',
        'date_end',
        'unit_id',
    ];

    public function userBy()
    {
        return $this->belongsTo(User::class, 'user_id_by');
    }
    public function userTo()
    {
        return $this->belongsTo(User::class, 'user_id_to');
    }

    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'workorder_id');
    }
    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id' );
    }
}
