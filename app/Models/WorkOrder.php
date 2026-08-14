<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'work_order_number',

        'client_id',
        'subclient_id',
        'cargo_type_id',

        'booking_number',
        'customer_order_number',

        'operation_type',
        'service_type',

        'plant_id',

        'origin_type',
        'origin_location_id',
        'origin_plant_id',

        'destination_type',
        'destination_location_id',
        'destination_plant_id',

        'requested_date',
        'requested_time',
        'appointment_at',

        'requested_trips',

        'requested_container_type',
        'requested_container_size',

        'cargo_description',
        'estimated_weight_kg',

        'status',

        'customer_reference',
        'notes',

        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'requested_date' => 'date',

            'appointment_at' => 'datetime',

            'requested_trips' => 'integer',

            'estimated_weight_kg' => 'decimal:2',
        ];
    }

    /*
     |--------------------------------------------------------------------------
     | RELACIONES
     |--------------------------------------------------------------------------
     */

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subclient()
    {
        return $this->belongsTo(Subclient::class);
    }

    public function cargoType()
    {
        return $this->belongsTo(CargoType::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
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
     | ETIQUETAS
     |--------------------------------------------------------------------------
     */

    public function getOperationTypeLabelAttribute(): string
    {
        return match ($this->operation_type) {
            'EXPORT' => 'Exportación',
            'IMPORT' => 'Importación',
            'TRANSFER' => 'Transferencia',
            default => 'Otro',
        };
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return match ($this->service_type) {
            'TRANSPORT' => 'Transporte',
            'POSITIONING' => 'Posicionamiento',
            'PICKUP' => 'Retiro',
            'POSITIONING_PICKUP' => 'Posición y retiro',
            'TRANSFER' => 'Transferencia',
            default => 'Otro',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'PENDING' => 'Pendiente',
            'PLANNED' => 'Planificada',
            'IN_PROGRESS' => 'En ejecución',
            'COMPLETED' => 'Completada',
            'CANCELLED' => 'Cancelada',
            default => $this->status,
        };
    }

    public function getOriginNameAttribute(): string
    {
        if ($this->origin_type === 'PLANT') {
            return $this->originPlant?->name
                ?? 'Sin origen';
        }

        if ($this->origin_type === 'LOCATION') {
            return $this->originLocation?->name
                ?? 'Sin origen';
        }

        return 'Sin origen';
    }

    public function getDestinationNameAttribute(): string
    {
        if ($this->destination_type === 'PLANT') {
            return $this->destinationPlant?->name
                ?? 'Sin destino';
        }

        if ($this->destination_type === 'LOCATION') {
            return $this->destinationLocation?->name
                ?? 'Sin destino';
        }

        return 'Sin destino';
    }

    public function trips()
    {
        return $this->hasMany(
            Trip::class
        )->orderBy('sequence_number');
    }
}
