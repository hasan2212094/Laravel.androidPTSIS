<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workorder extends Model
{
    use HasFactory;
     protected $fillable = [
        'nomor'
    ];
     public function quality(){
        return $this->hasMany(Quality::class);
    }
    public function fabrikasi(){
        return $this->hasMany(Fabrikasi::class, 'workorder_id');
    }
    public function electrical(){
        return $this->hasMany(Electrical::class, 'workorder_id');
    }
    public function painting(){
        return $this->hasMany(Painting::class, 'workorder_id');
    }
    public function komponen(){
        return $this->hasMany(Komponen::class, 'workorder_id');
    }
  
}
