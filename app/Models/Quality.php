<?php

namespace App\Models;

use App\Models\Workorder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quality extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project',
        'no_wo',
        'description',
        'responds',
        'image',
        'date',
        'status',
    ];

    protected $casts = [
        'responds' => 'boolean',
        'date' => 'date',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'no_wo');
    }
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            0 => 'In Progress',
            1 => 'Done',
            default => 'Unknown'
        };
    }
    public function images()
    {
        return $this->hasMany(QualityImage::class);
    }
}
