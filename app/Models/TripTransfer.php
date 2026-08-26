<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripTransfer extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [

        'trip_id',

        'transfer_number',

        'origin_type',

        'origin_location_id',
        'origin_plant_id',
        'origin_name_snapshot',

        'destination_type',

        'destination_location_id',
        'destination_plant_id',
        'destination_name_snapshot',

        'scheduled_at',

        'started_at',
        'completed_at',

        'status',

        'reason',
        'notes',

        'created_by',
        'updated_by',
    ];


    protected function casts(): array
    {
        return [

            'scheduled_at' =>
            'datetime',

            'started_at' =>
            'datetime',

            'completed_at' =>
            'datetime',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RELACIONES GENERALES
    |--------------------------------------------------------------------------
    */

    public function trip()
    {
        return $this->belongsTo(
            Trip::class
        );
    }


    public function originLocation()
    {
        return $this->belongsTo(
            Location::class,
            'origin_location_id'
        );
    }


    public function originPlant()
    {
        return $this->belongsTo(
            Plant::class,
            'origin_plant_id'
        );
    }


    public function destinationLocation()
    {
        return $this->belongsTo(
            Location::class,
            'destination_location_id'
        );
    }


    public function destinationPlant()
    {
        return $this->belongsTo(
            Plant::class,
            'destination_plant_id'
        );
    }


    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ASIGNACIONES
    |--------------------------------------------------------------------------
    */

    public function assignments()
    {
        return $this->hasMany(
            TransferAssignment::class
        )
            ->orderBy(
                'assigned_at'
            );
    }


    public function activeAssignment()
    {
        return $this->hasOne(
            TransferAssignment::class
        )
            ->whereNull(
                'unassigned_at'
            )
            ->latestOfMany(
                'assigned_at'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EVENTOS
    |--------------------------------------------------------------------------
    */

    public function events()
    {
        return $this->hasMany(
            TransferEvent::class
        )
            ->orderBy(
                'event_at'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADOS
    |--------------------------------------------------------------------------
    */

    public function statusHistory()
    {
        return $this->hasMany(
            TransferStatusHistory::class
        )
            ->orderByDesc(
                'changed_at'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ETIQUETAS
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            'PENDING' =>
            'Pendiente',

            'ASSIGNED' =>
            'Asignada',

            'IN_TRANSIT' =>
            'En tránsito',

            'COMPLETED' =>
            'Completada',

            'CANCELLED' =>
            'Cancelada',

            default =>
            $this->status ?: 'No definido',
        };
    }


    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {

            'PENDING' =>
            'bg-light text-dark',

            'ASSIGNED' =>
            'bg-primary-subtle text-primary',

            'IN_TRANSIT' =>
            'bg-warning-subtle text-warning',

            'COMPLETED' =>
            'bg-success-subtle text-success',

            'CANCELLED' =>
            'bg-danger-subtle text-danger',

            default =>
            'bg-light text-dark',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | EVENTOS DISPONIBLES
    |--------------------------------------------------------------------------
    */

    public static function eventLabels(): array
    {
        return [

            'ARRIVAL_ORIGIN' =>
            'Llegada al origen',

            'DEPARTURE_ORIGIN' =>
            'Salida del origen',

            'ARRIVAL_DESTINATION' =>
            'Llegada al destino',

            'DELIVERY' =>
            'Entrega',
        ];
    }


    public function availableEventOptions(): array
    {
        $registered =
            $this
            ->events
            ->pluck(
                'event_type'
            )
            ->toArray();


        $available = [];


        foreach (
            self::eventLabels()
            as $value => $label
        ) {

            if (
                in_array(
                    $value,
                    $registered,
                    true
                )
            ) {
                continue;
            }


            if (
                !$this->canRegisterEvent(
                    $value
                )
            ) {
                continue;
            }


            $available[$value] =
                $label;
        }


        return $available;
    }


    public function canRegisterEvent(
        string $eventType
    ): bool {

        $registered =
            $this
            ->events
            ->pluck(
                'event_type'
            )
            ->toArray();


        return match ($eventType) {

            'ARRIVAL_ORIGIN' =>
            true,

            'DEPARTURE_ORIGIN' =>
            in_array(
                'ARRIVAL_ORIGIN',
                $registered,
                true
            ),

            'ARRIVAL_DESTINATION' =>
            in_array(
                'DEPARTURE_ORIGIN',
                $registered,
                true
            ),

            'DELIVERY' =>
            in_array(
                'ARRIVAL_DESTINATION',
                $registered,
                true
            ),

            default =>
            false,
        };
    }


    public function statusForEvent(
        string $eventType
    ): ?string {

        return match ($eventType) {

            'DEPARTURE_ORIGIN' =>
            'IN_TRANSIT',

            'DELIVERY' =>
            'COMPLETED',

            default =>
            null,
        };
    }
}
