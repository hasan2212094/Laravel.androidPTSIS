<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $table = 'units'; // ← tambahkan ini
    protected $fillable = [
        'name'
    ];
     public function fabrikasis()
    {
        return $this->hasMany(Fabrikasi::class,);
    }

     public function komponens()
    {
        return $this->hasMany(Komponen::class,);
    }
     public function electricals()
    {
        return $this->hasMany(Electrical::class,);
    }
    public function paintings()
    {
        return $this->hasMany(Painting::class,);
    }

}
