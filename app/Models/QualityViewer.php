<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityViewer extends Model
{
    protected $table = 'qualities_viewer';

    protected $fillable = [
        'quality_id',
        'user_id'
    ];

    public $timestamps = true;

    /**
     * Hubungan dengan model Quality
     */
    public function quality()
    {
        return $this->belongsTo(Quality::class, 'quality_id');
    }

    /**
     * Hubungan dengan model User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
