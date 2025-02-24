<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory, SoftDeletes ; // Tambahkan SoftDeletes;
    protected $fillable = [
        'user_id_by',
        'role_by',
        'user_id_to',
        'role_to',
        'title',
        'description',
        'date_start',
        'level_urgent',
        'status',
        'image',
        'finish_note',
        'date_end',
    ];

    protected $dates = ['deleted_at']; // Pastikan ada kolom deleted_at untuk soft delete
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function userBy()
    {
        return $this->belongsTo(User::class, 'user_id_by');
    }

    public function userTo()
    {
        return $this->belongsTo(User::class, 'user_id_to');
    }

    public function roleBy()
    {
        return $this->belongsTo(Role::class, 'role_by');
    }

    public function roleTo()
    {
        return $this->belongsTo(Role::class, 'role_to');
    }
    
}
