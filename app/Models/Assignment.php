<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id_by',
        'role_by',
        'user_id_to',
        'role_to',
        'title',
        'description_note',
        'date_start',
        'level_urgent',
        'status',
        'image',
        'description_end',
        'date_end',
    ];
        public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

}
