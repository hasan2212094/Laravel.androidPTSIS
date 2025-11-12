<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workorder extends Model
{
    use HasFactory;
     protected $fillable = [
        'nomor',
        'client'
    ];
        public function qualities()
    {
        return $this->hasMany(Quality::class, 'workorder_id');
    }
     public function fabrikasis()
    {
        return $this->hasMany(Fabrikasi::class, 'workorder_id');
    }

     public function komponens()
    {
        return $this->hasMany(Komponen::class, 'workorder_id');
    }
     public function electricals()
    {
        return $this->hasMany(Electrical::class, 'workorder_id');
    }
    public function paintings()
    {
        return $this->hasMany(Painting::class, 'workorder_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'workorder_id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'workorder_id');
    }
  
}
