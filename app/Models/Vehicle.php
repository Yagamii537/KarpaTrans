<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'registration_expiration_date',
        'technical_review_expiration_date',
        'insurance_expiration_date',
        'photo',
        'registration_document',
        'insurance_document',
        'technical_review_document',
        'operational_status',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'fuel_capacity' => 'decimal:2',
            'current_odometer' => 'decimal:2',
            'registration_expiration_date' => 'date',
            'technical_review_expiration_date' => 'date',
            'insurance_expiration_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function chassis(): HasMany
    {
        return $this->hasMany(Chassis::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->plate} - {$this->brand} {$this->model}";
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
            $this->insurance_expiration_date,
        ])->filter()->contains(
            fn($date) => $date->isBefore(Carbon::today())
        );
    }

    public function getHasExpiringDocumentAttribute(): bool
    {
        return collect([
            $this->registration_expiration_date,
            $this->technical_review_expiration_date,
            $this->insurance_expiration_date,
        ])->filter()->contains(function ($date) {
            return !$date->isBefore(Carbon::today())
                && $date->lte(Carbon::today()->addDays(30));
        });
    }
}
