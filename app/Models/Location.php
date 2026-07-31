<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'type',
        'city',
        'province',
        'address',
        'reference',
        'contact_name',
        'phone',
        'email',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'receives_empty_containers',
        'receives_full_containers',
        'requires_appointment',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'receives_empty_containers' => 'boolean',
            'receives_full_containers' => 'boolean',
            'requires_appointment' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'PORT' => 'Puerto',
            'DEPOT' => 'Depósito',
            'YARD' => 'Patio',
            'WAREHOUSE' => 'Bodega',
            'EXTERNAL_PLANT' => 'Planta externa',
            'WORKSHOP' => 'Taller',
            'CUSTOMER_LOCATION' => 'Punto del cliente',
            default => 'Otro',
        };
    }

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address,
            $this->city,
            $this->province,
        ])->filter()->implode(', ');
    }
}
