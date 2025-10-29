<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;
     protected $table = 'equipment'; // ✅ tambahkan ini
    protected $fillable = [
        'no_serial',
        'nama_alat',
    ];

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
