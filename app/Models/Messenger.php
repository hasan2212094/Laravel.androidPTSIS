<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messenger extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id_by',
        'user_id_to',
        'title',
        'message',
    ];
public function userBy()
    {
        return $this->belongsTo(User::class, 'user_id_by');
    }

    public function userTo()
    {
        return $this->belongsTo(User::class, 'user_id_to');
    }

}
