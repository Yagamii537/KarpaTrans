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
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

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

    public function getDisplayNameAttribute(): string
    {
        return $this->trade_name
            ?: $this->business_name;
    }
    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }
}
