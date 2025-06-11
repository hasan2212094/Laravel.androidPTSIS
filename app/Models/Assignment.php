<?php

namespace App\Models;


use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory, SoftDeletes; // Tambahkan SoftDeletes;
    protected $fillable = [
        'user_id_by',
        'role_by',
        'user_id_to',
        'role_to',
        'title',
        'description',
        'date_start',
        'level_urgent',
        'status',
        'image',
        'finish_note',
        'date_end',
    ];

    protected $casts = [
        'user_id_by' => 'integer',
        'role_by' => 'integer',
        'user_id_to' => 'integer',
        'role_to' => 'integer',
        'status' => 'integer',
        'level_urgent' => 'integer',
    ];
    protected $dates = ['deleted_at', 'date_start', 'date_end'];
    public $timestamps = true; // Pastikan timestamps aktif

    protected static function boot()
    {
        parent::boot();

        // Set otomatis date_start saat pembuatan tugas
        static::creating(function ($assignment) {
            $assignment->date_start = Carbon::now()->format('Y-m-d H:i:s');
        });

        // Set otomatis date_end hanya jika status berubah menjadi true
        static::updating(function ($assignment) {
            if ($assignment->isDirty('status') && $assignment->status == true && !$assignment->date_end) {
                $assignment->date_end = Carbon::now()->format('Y-m-d H:i:s');
            }
        });
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function userBy()
    {
        return $this->belongsTo(User::class, 'user_id_by');
    }

    public function userTo()
    {
        return $this->belongsTo(User::class, 'user_id_to');
    }

    public function roleBy()
    {
        return $this->belongsTo(Role::class, 'role_by');
    }

    public function roleTo()
    {
        return $this->belongsTo(Role::class, 'role_to');
    }

    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            0 => 'Unfinish',
            1 => 'Onprogress',
            2 => 'Finish',
        ];

        return $statusLabels[$this->status] ?? 'Unknown';
    }
}
