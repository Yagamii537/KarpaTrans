<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plant extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'name',
        'code',
        'city',
        'address',
        'reference',
        'contact_name',
        'phone',
        'email',
        'latitude',
        'longitude',
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
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'free_loading_hours' => 'integer',
            'free_unloading_hours' => 'integer',
            'standby_fraction_minutes' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getEffectiveFreeLoadingHoursAttribute(): int
    {
        return $this->free_loading_hours
            ?? $this->client->free_loading_hours;
    }

    public function getEffectiveFreeUnloadingHoursAttribute(): int
    {
        return $this->free_unloading_hours
            ?? $this->client->free_unloading_hours;
    }

    public function getEffectiveServiceTimeStartAttribute(): string
    {
        return $this->service_time_start
            ?? $this->client->service_time_start;
    }

    public function getEffectiveStandbyFractionMinutesAttribute(): int
    {
        return $this->standby_fraction_minutes
            ?? $this->client->standby_fraction_minutes;
    }

    public function getEffectiveServiceTimeStartLabelAttribute(): string
    {
        return match ($this->effective_service_time_start) {
            'arrival_time' => 'Hora de llegada',
            default => 'Hora solicitada',
        };
    }
}
