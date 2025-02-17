<?php

namespace App\Models;

use App\Models\Listuser;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public function users(){
        return $this->hasMany(User::class);
    }
  
}
