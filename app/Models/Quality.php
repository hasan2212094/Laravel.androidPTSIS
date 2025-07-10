<?php

namespace App\Models;

use App\Models\Workorder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quality extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id_by',
        'user_id_to',
        'project',
        'no_wo',
        'description',
        'responds',
        'date',
        'status',
        'status_relevan',
        'comment',
        'description_relevan',
        'comment_done',
        'description_progress',
    ];

    protected $casts = [
        'responds' => 'boolean',
        'date' => 'datetime',
    ];
    protected $dates = ['deleted_at', 'date', 'date_end'];
    public $timestamps = true; // Tambahkan baris ini
    public function workorder()
    {
        return $this->belongsTo(Workorder::class, 'no_wo');
    }
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            0 => 'waiting',
            1 => 'Inprogress',
            2 => 'Done',
            default => 'Unknown'
        };
    }
    public function images()
    {
        return $this->hasMany(QualityImage::class);
    }
    public function getStatusLabelAttributerelevan()
    {
        $statusLabels = [
            0 => 'Notrelevan',
            1 => 'Relevan',
        ];

        return $statusLabels[$this->status] ?? 'Unknown';
    }
    public function imagesrelevan()
    {
        return $this->hasMany(QualityImageRelevan::class,); // ganti jika foreign key berbeda
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function userBy()
    {
        return $this->belongsTo(User::class, 'user_id_by');
    }

    public function userTo()
    {
        return $this->belongsTo(User::class, 'user_id_to');
    }
    public function imagesprogress()
    {
        return $this->hasMany(QualityImageInprogress::class, 'quality_id');
    }
}
