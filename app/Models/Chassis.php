<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chassis extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'code',
        'plate',
        'chassis_type',
        'brand',
        'model',
        'year',
        'color',
        'serial_number',
        'axles',
        'maximum_capacity_tons',
        'supports_20ft',
        'supports_40ft',
        'supports_reefer',
        'registration_expiration_date',
        'technical_review_expiration_date',
        'photo',
        'registration_document',
        'technical_review_document',
        'operational_status',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'axles' => 'integer',
            'maximum_capacity_tons' => 'decimal:2',
            'supports_20ft' => 'boolean',
            'supports_40ft' => 'boolean',
            'supports_reefer' => 'boolean',
            'registration_expiration_date' => 'date',
            'technical_review_expiration_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->plate
            ? "{$this->code} - {$this->plate}"
            : $this->code;
    }

    public function getOperationalStatusLabelAttribute(): string
    {
        return match ($this->operational_status) {
            'AVAILABLE' => 'Disponible',
            'ASSIGNED' => 'Asignado',
            'MAINTENANCE' => 'Mantenimiento',
            'OUT_OF_SERVICE' => 'Fuera de servicio',
            default => 'Sin estado',
        };
    }

    public function getHasExpiredDocumentAttribute(): bool
    {
        return collect([
            $this->registration_expiration_date,
            $this->technical_review_expiration_date,
        ])->filter()->contains(
            fn($date) => $date->isBefore(Carbon::today())
        );
    }
}
