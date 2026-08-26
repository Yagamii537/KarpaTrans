<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [

        'trip_transfer_id',

        'previous_status',
        'new_status',

        'reason',

        'changed_by',
        'changed_at',
    ];


    protected function casts(): array
    {
        return [

            'changed_at' =>
            'datetime',
        ];
    }


    public function transfer()
    {
        return $this->belongsTo(
            TripTransfer::class,
            'trip_transfer_id'
        );
    }


    public function user()
    {
        return $this->belongsTo(
            User::class,
            'changed_by'
        );
    }
}
