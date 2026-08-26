<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subclient extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client_id',

        'business_name',
        'trade_name',

        'identification_type',
        'identification',

        'contact_name',
        'email',
        'phone',

        'address',

        'inherits_operational_rules',
        'free_loading_hours',
        'free_unloading_hours',
        'service_time_start',
        'standby_fraction_minutes',

        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'inherits_operational_rules' => 'boolean',
            'free_loading_hours' => 'integer',
            'free_unloading_hours' => 'integer',
            'standby_fraction_minutes' => 'integer',
            'is_active' => 'boolean',
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

    public function cargoTypes()
    {
        return $this->belongsToMany(
            CargoType::class,
            'subclient_cargo_types'
        )->withTimestamps();
    }

    public function workOrders()
    {
        return $this->hasMany(
            WorkOrder::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DATOS DE VISUALIZACIÓN
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        return $this->trade_name
            ?: $this->business_name;
    }

    /*
    |--------------------------------------------------------------------------
    | REGLAS OPERATIVAS EFECTIVAS
    |--------------------------------------------------------------------------
    |
    | Si el subcliente hereda reglas:
    |     usamos las del cliente principal.
    |
    | Si NO hereda:
    |     usamos las propias del subcliente.
    |
    */

    public function getEffectiveFreeLoadingHoursAttribute(): int
    {
        if ($this->inherits_operational_rules) {

            return (int) (
                $this->client?->free_loading_hours
                ?? 0
            );
        }

        return (int) (
            $this->free_loading_hours
            ?? 0
        );
    }

    public function getEffectiveFreeUnloadingHoursAttribute(): int
    {
        if ($this->inherits_operational_rules) {

            return (int) (
                $this->client?->free_unloading_hours
                ?? 0
            );
        }

        return (int) (
            $this->free_unloading_hours
            ?? 0
        );
    }

    public function getEffectiveServiceTimeStartAttribute(): string
    {
        if ($this->inherits_operational_rules) {

            return $this->client?->service_time_start
                ?? 'requested_time';
        }

        return $this->service_time_start
            ?? 'requested_time';
    }

    public function getEffectiveStandbyFractionMinutesAttribute(): int
    {
        if ($this->inherits_operational_rules) {

            return (int) (
                $this->client?->standby_fraction_minutes
                ?? 30
            );
        }

        return (int) (
            $this->standby_fraction_minutes
            ?? 30
        );
    }

    public function getEffectiveServiceTimeStartLabelAttribute(): string
    {
        return match ($this->effective_service_time_start) {

            'arrival_time' =>
            'Hora real de llegada',

            default =>
            'Hora solicitada por el cliente',
        };
    }

    public function getOperationalRulesSourceLabelAttribute(): string
    {
        return $this->inherits_operational_rules
            ? 'Heredadas del cliente'
            : 'Configuración propia';
    }
}
