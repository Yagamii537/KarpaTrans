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
        'customer_reference',

        'operation_type',

        /*
         * Campo anterior.
         * Se mantiene temporalmente
         * por compatibilidad.
         */
        'service_type',

        /*
         * Nueva clasificación correcta:
         * IMMEDIATE
         * POSITIONING
         * PICKUP
         * POSITIONING_PICKUP
         */
        'service_modality',

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

        /*
         * STAND-BY APLICADO
         */
        'standby_process_type',
        'standby_free_hours',
        'standby_count_start_type',
        'standby_fraction_minutes',
        'standby_rule_source',

        'standby_rule_overridden',
        'standby_override_reason',
        'standby_override_by',

        'requested_container_type',
        'requested_container_size',

        'cargo_description',
        'estimated_weight_kg',

        'status',
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

            'standby_free_hours' => 'integer',

            'standby_fraction_minutes' => 'integer',

            'standby_rule_overridden' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

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

    public function plant()
    {
        return $this->belongsTo(
            Plant::class
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

    public function standbyOverrideUser()
    {
        return $this->belongsTo(
            User::class,
            'standby_override_by'
        );
    }

    public function trips()
    {
        return $this->hasMany(
            Trip::class
        )
            ->orderBy(
                'sequence_number'
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

            'EXPORT' =>
            'Exportación',

            'IMPORT' =>
            'Importación',

            'TRANSFER' =>
            'Transferencia',

            default =>
            'Otra',
        };
    }

    public function getServiceModalityLabelAttribute(): string
    {
        return match ($this->service_modality) {

            'IMMEDIATE' =>
            'Inmediata',

            'POSITIONING' =>
            'Posición',

            'PICKUP' =>
            'Retiro',

            'POSITIONING_PICKUP' =>
            'Posición + Retiro',

            default =>
            'No definida',
        };
    }

    public function getStandbyProcessTypeLabelAttribute(): string
    {
        return match ($this->standby_process_type) {

            'LOAD' =>
            'Carga',

            'UNLOAD' =>
            'Descarga',

            'TRANSFER' =>
            'Transferencia',

            'OTHER' =>
            'Otro',

            default =>
            'No definido',
        };
    }

    public function getStandbyCountStartTypeLabelAttribute(): string
    {
        return match ($this->standby_count_start_type) {

            'ARRIVAL_TIME' =>
            'Hora real de llegada',

            'REQUESTED_TIME' =>
            'Hora solicitada',

            default =>
            'No definido',
        };
    }

    public function getStandbyRuleSourceLabelAttribute(): string
    {
        return match ($this->standby_rule_source) {

            'CLIENT' =>
            'Cliente',

            'SUBCLIENT' =>
            'Subcliente',

            'OVERRIDE' =>
            'Excepción manual',

            default =>
            'No definido',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | ORIGEN / DESTINO
    |--------------------------------------------------------------------------
    */

    public function getOriginNameAttribute(): string
    {
        if (
            $this->origin_type
            === 'PLANT'
        ) {

            return $this->originPlant?->name
                ?? 'Planta no definida';
        }

        return $this->originLocation?->name
            ?? 'Ubicación no definida';
    }

    public function getDestinationNameAttribute(): string
    {
        if (
            $this->destination_type
            === 'PLANT'
        ) {

            return $this->destinationPlant?->name
                ?? 'Planta no definida';
        }

        return $this->destinationLocation?->name
            ?? 'Ubicación no definida';
    }
}
