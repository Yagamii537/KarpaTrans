<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferAssignment extends Model
{
    use HasFactory;

    protected $fillable = [

        'trip_transfer_id',

        'driver_id',
        'vehicle_id',
        'chassis_id',
        'container_id',

        'assigned_at',
        'unassigned_at',

        'assignment_reason',
        'release_reason',

        'assigned_by',
        'released_by',
    ];


    protected function casts(): array
    {
        return [

            'assigned_at' =>
            'datetime',

            'unassigned_at' =>
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


    public function driver()
    {
        return $this->belongsTo(
            Driver::class
        );
    }


    public function vehicle()
    {
        return $this->belongsTo(
            Vehicle::class
        );
    }


    public function chassis()
    {
        return $this->belongsTo(
            Chassis::class
        );
    }


    public function container()
    {
        return $this->belongsTo(
            Container::class
        );
    }


    public function assignedBy()
    {
        return $this->belongsTo(
            User::class,
            'assigned_by'
        );
    }


    public function releasedBy()
    {
        return $this->belongsTo(
            User::class,
            'released_by'
        );
    }
}
