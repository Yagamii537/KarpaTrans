<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContainerMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'container_id',
        'from_location_id',
        'to_location_id',
        'movement_type',
        'movement_at',
        'reference_type',
        'reference_id',
        'seal_number',
        'load_status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'movement_at' => 'datetime',
        ];
    }

    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(
            Location::class,
            'from_location_id'
        );
    }

    public function toLocation()
    {
        return $this->belongsTo(
            Location::class,
            'to_location_id'
        );
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function getMovementTypeLabelAttribute(): string
    {
        return match ($this->movement_type) {
            'INITIAL' => 'Registro inicial',
            'PICKUP' => 'Retiro',
            'DELIVERY' => 'Entrega',
            'TRANSFER' => 'Transferencia',
            'RETURN' => 'Devolución',
            'POSITIONING' => 'Posicionamiento',
            default => 'Otro',
        };
    }
}
