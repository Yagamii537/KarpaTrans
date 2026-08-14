<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Plant;
use App\Models\Subclient;
use App\Models\CargoType;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'business_name',
        'trade_name',
        'identification_type',
        'identification',
        'contact_name',
        'email',
        'phone',
        'secondary_phone',
        'address',
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
            'free_loading_hours' => 'integer',
            'free_unloading_hours' => 'integer',
            'standby_fraction_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getServiceTimeStartLabelAttribute(): string
    {
        return match ($this->service_time_start) {
            'arrival_time' => 'Desde la hora de llegada',
            default => 'Desde la hora solicitada',
        };
    }

    public function plants()
    {
        return $this->hasMany(Plant::class);
    }

    public function subclients()
    {
        return $this->hasMany(Subclient::class);
    }

    public function cargoTypes()
    {
        return $this->belongsToMany(
            CargoType::class,
            'client_cargo_types'
        )->withTimestamps();
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
