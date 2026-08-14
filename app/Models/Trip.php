<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'work_order_id',
        'trip_number',
        'sequence_number',

        'client_id',
        'client_name_snapshot',

        'subclient_id',
        'subclient_name_snapshot',

        'cargo_type_id',
        'cargo_type_name_snapshot',

        'booking_number',
        'customer_order_number',

        'operation_type',
        'service_type',

        'origin_type',
        'origin_location_id',
        'origin_plant_id',
        'origin_name_snapshot',

        'destination_type',
        'destination_location_id',
        'destination_plant_id',
        'destination_name_snapshot',

        'scheduled_start_at',
        'scheduled_end_at',

        'status',
        'notes',

        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_start_at' => 'datetime',
            'scheduled_end_at' => 'datetime',
            'sequence_number' => 'integer',
        ];
    }

    public function workOrder()
    {
        return $this->belongsTo(
            WorkOrder::class
        );
    }

    public function client()
    {
        return $this->belongsTo(
            Client::class
        );
    }

    public function subclient()
    {
        return $this->belongsTo(
            Subclient::class
        );
    }

    public function cargoType()
    {
        return $this->belongsTo(
            CargoType::class
        );
    }

    public function assignments()
    {
        return $this->hasMany(
            TripAssignment::class
        )->orderByDesc('assigned_at');
    }

    public function activeAssignment()
    {
        return $this->hasOne(
            TripAssignment::class
        )
            ->whereNull('unassigned_at')
            ->latestOfMany();
    }

    public function statusHistory()
    {
        return $this->hasMany(
            TripStatusHistory::class
        )->orderByDesc('changed_at');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'PENDING' => 'Pendiente',
            'ASSIGNED' => 'Asignado',
            'IN_TRANSIT' => 'En tránsito',
            'AT_DESTINATION' => 'En destino',
            'COMPLETED' => 'Completado',
            'CANCELLED' => 'Cancelado',
            default => $this->status,
        };
    }

    public function getOperationTypeLabelAttribute(): string
    {
        return match ($this->operation_type) {
            'EXPORT' => 'Exportación',
            'IMPORT' => 'Importación',
            'TRANSFER' => 'Transferencia',
            default => 'Otro',
        };
    }

    public function times()
    {
        return $this->hasMany(
            TripTime::class
        )->orderBy('event_at');
    }
}
