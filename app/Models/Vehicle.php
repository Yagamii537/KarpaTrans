<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [

        'plate',
        'internal_code',

        'brand',
        'model',
        'year',
        'color',

        'vehicle_type',

        'chassis_number',
        'engine_number',

        'ownership_type',
        'owner_name',
        'owner_identification',

        'fuel_capacity',
        'current_odometer',

        /*
         * DATOS TÉCNICOS
         */
        'tare_weight_kg',

        /*
         * Campo antiguo.
         * Lo mantenemos temporalmente.
         */
        'max_weight_kg',

        'gross_weight_kg',
        'max_load_capacity_kg',

        'length_m',
        'width_m',
        'height_m',

        'axles',
        'volume_m3',

        /*
         * DOCUMENTACIÓN
         */
        'registration_expiration_date',
        'technical_review_expiration_date',
        'insurance_expiration_date',

        'registration_document',
        'technical_review_document',
        'insurance_document',

        'photo',

        'operational_status',

        'notes',

        'is_active',
    ];


    protected function casts(): array
    {
        return [

            'year' =>
            'integer',

            'fuel_capacity' =>
            'decimal:2',

            'current_odometer' =>
            'decimal:2',

            'tare_weight_kg' =>
            'decimal:2',

            'max_weight_kg' =>
            'decimal:2',

            'gross_weight_kg' =>
            'decimal:2',

            'max_load_capacity_kg' =>
            'decimal:2',

            'length_m' =>
            'decimal:2',

            'width_m' =>
            'decimal:2',

            'height_m' =>
            'decimal:2',

            'axles' =>
            'integer',

            'volume_m3' =>
            'decimal:2',

            'registration_expiration_date' =>
            'date',

            'technical_review_expiration_date' =>
            'date',

            'insurance_expiration_date' =>
            'date',

            'is_active' =>
            'boolean',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function assignments()
    {
        return $this->hasMany(
            TripAssignment::class
        );
    }


    public function activeAssignments()
    {
        return $this->hasMany(
            TripAssignment::class
        )
            ->whereNull(
                'unassigned_at'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ETIQUETAS
    |--------------------------------------------------------------------------
    */

    public function getVehicleTypeLabelAttribute(): string
    {
        return match ($this->vehicle_type) {

            'TRACTOCAMION' =>
            'Tractocamión',

            'CAMION' =>
            'Camión',

            'CAMIONETA' =>
            'Camioneta',

            'OTRO' =>
            'Otro',

            default =>
            $this->vehicle_type
                ?: 'No definido',
        };
    }


    public function getOwnershipTypeLabelAttribute(): string
    {
        return match ($this->ownership_type) {

            'PROPIO' =>
            'Propio',

            'ALQUILADO' =>
            'Alquilado',

            'TERCERO' =>
            'Tercero',

            default =>
            $this->ownership_type
                ?: 'No definido',
        };
    }


    public function getOperationalStatusLabelAttribute(): string
    {
        return match ($this->operational_status) {

            'AVAILABLE' =>
            'Disponible',

            'ASSIGNED' =>
            'Asignado',

            'MAINTENANCE' =>
            'Mantenimiento',

            'OUT_OF_SERVICE' =>
            'Fuera de servicio',

            default =>
            $this->operational_status
                ?: 'No definido',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS VENCIDOS
    |--------------------------------------------------------------------------
    */

    public function getHasExpiredDocumentAttribute(): bool
    {
        $today =
            now()->startOfDay();


        foreach (
            [
                $this->registration_expiration_date,
                $this->technical_review_expiration_date,
                $this->insurance_expiration_date,
            ] as $date
        ) {

            if (
                $date
                &&
                $date->copy()
                ->startOfDay()
                ->lt($today)
            ) {

                return true;
            }
        }


        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | CAPACIDAD
    |--------------------------------------------------------------------------
    */

    public function canCarryWeight(
        ?float $weightKg
    ): bool {

        if (
            $weightKg === null
            ||
            $weightKg <= 0
        ) {
            return true;
        }


        if (
            $this->max_load_capacity_kg
            === null
        ) {

            return true;
        }


        return $weightKg
            <= (float)
            $this
                ->max_load_capacity_kg;
    }
}
