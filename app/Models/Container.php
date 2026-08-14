<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Container extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'container_number',
        'container_type',
        'container_size',
        'load_status',
        'operational_status',
        'current_location_id',
        'seal_number',
        'tare_weight_kg',
        'max_gross_weight_kg',
        'shipping_line',
        'last_inspection_date',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tare_weight_kg' => 'decimal:2',
            'max_gross_weight_kg' => 'decimal:2',
            'last_inspection_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function currentLocation()
    {
        return $this->belongsTo(
            Location::class,
            'current_location_id'
        );
    }

    public function movements()
    {
        return $this->hasMany(
            ContainerMovement::class
        )->orderByDesc('movement_at');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->container_number
            . ' - '
            . $this->container_size
            . ' '
            . $this->container_type;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->container_type) {
            'DRY' => 'Seco',
            'REEFER' => 'Refrigerado',
            'OPEN_TOP' => 'Open Top',
            'FLAT_RACK' => 'Flat Rack',
            'TANK' => 'Tanque',
            default => 'Otro',
        };
    }

    public function getLoadStatusLabelAttribute(): string
    {
        return match ($this->load_status) {
            'EMPTY' => 'Vacío',
            'FULL' => 'Lleno',
            default => 'No definido',
        };
    }

    public function getOperationalStatusLabelAttribute(): string
    {
        return match ($this->operational_status) {
            'AVAILABLE' => 'Disponible',
            'ASSIGNED' => 'Asignado',
            'IN_TRANSIT' => 'En tránsito',
            'AT_CLIENT' => 'En cliente',
            'AT_PORT' => 'En puerto',
            'AT_DEPOT' => 'En depósito',
            'MAINTENANCE' => 'Mantenimiento',
            'OUT_OF_SERVICE' => 'Fuera de servicio',
            default => 'Sin estado',
        };
    }
}
