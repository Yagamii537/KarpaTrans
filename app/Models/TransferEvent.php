<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferEvent extends Model
{
    use HasFactory;

    protected $fillable = [

        'trip_transfer_id',

        'event_type',
        'event_at',

        'location_type',
        'location_id',
        'plant_id',

        'location_name_snapshot',

        'observation',

        'is_manual',

        'created_by',
    ];


    protected function casts(): array
    {
        return [

            'event_at' =>
            'datetime',

            'is_manual' =>
            'boolean',
        ];
    }


    public function transfer()
    {
        return $this->belongsTo(
            TripTransfer::class,
            'trip_transfer_id'
        );
    }


    public function location()
    {
        return $this->belongsTo(
            Location::class
        );
    }


    public function plant()
    {
        return $this->belongsTo(
            Plant::class
        );
    }


    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    public function getEventTypeLabelAttribute(): string
    {
        return match ($this->event_type) {

            'ARRIVAL_ORIGIN' =>
            'Llegada al origen',

            'DEPARTURE_ORIGIN' =>
            'Salida del origen',

            'ARRIVAL_DESTINATION' =>
            'Llegada al destino',

            'DELIVERY' =>
            'Entrega',

            default =>
            $this->event_type,
        };
    }
}
