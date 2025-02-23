<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'role_id',
        'name',
        'title',
        'description',
        'date',
        'image',
        'level_urgent',
        'status',
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
