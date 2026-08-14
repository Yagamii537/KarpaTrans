<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripStatusHistory extends Model
{
    use HasFactory;

    protected $table =
    'trip_status_history';

    protected $fillable = [
        'trip_id',
        'previous_status',
        'new_status',
        'reason',
        'changed_by',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'changed_at' => 'datetime',
        ];
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'changed_by'
        );
    }
}
