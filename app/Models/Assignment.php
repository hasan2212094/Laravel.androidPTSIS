<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'title',
        'description',
        'date'];
        public function user()
    {
        return $this->belongsTo(User::class);
    }

}
