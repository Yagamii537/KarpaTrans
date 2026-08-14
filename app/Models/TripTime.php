<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
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
            'event_at' => 'datetime',
            'is_manual' => 'boolean',
        ];
    }

    public function trip()
    {
        return $this->belongsTo(
            Trip::class
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

            'ARRIVAL' =>
            'Llegada',

            'ENTRY' =>
            'Ingreso',

            'CONTAINER_PICKUP' =>
            'Retiro de contenedor',

            'LOAD_START' =>
            'Inicio de carga',

            'LOAD_END' =>
            'Fin de carga',

            'UNLOAD_START' =>
            'Inicio de descarga',

            'UNLOAD_END' =>
            'Fin de descarga',

            'WAIT_START' =>
            'Inicio de espera',

            'WAIT_END' =>
            'Fin de espera',

            'DEPARTURE' =>
            'Salida',

            'POSITIONING' =>
            'Posicionamiento',

            'PICKUP' =>
            'Retiro',

            'PORT_ARRIVAL' =>
            'Llegada a puerto',

            'DELIVERY' =>
            'Entrega',

            default =>
            'Otro',
        };
    }

    public function getLocationTypeLabelAttribute(): string
    {
        return match ($this->location_type) {
            'LOCATION' => 'Ubicación',
            'PLANT' => 'Planta',
            default => 'Sin ubicación',
        };
    }
}
