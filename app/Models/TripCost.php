<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripCost extends Model
{
    use HasFactory;

    protected $fillable = [

        'trip_id',

        'trip_transfer_id',

        'cost_type',

        'description',

        'quantity',

        'unit_price',

        'subtotal',

        'source_type',

        'source_id',

        'status',

        'notes',

        'created_by',

        'updated_by',
    ];


    protected function casts(): array
    {
        return [

            'quantity' =>
            'decimal:3',

            'unit_price' =>
            'decimal:2',

            'subtotal' =>
            'decimal:2',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function trip()
    {
        return $this->belongsTo(
            Trip::class
        );
    }


    public function transfer()
    {
        return $this->belongsTo(
            TripTransfer::class,
            'trip_transfer_id'
        );
    }


    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }


    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ETIQUETAS
    |--------------------------------------------------------------------------
    */

    public function getCostTypeLabelAttribute(): string
    {
        return match ($this->cost_type) {

            'BASE' =>
            'Tarifa base',

            'STANDBY' =>
            'Stand-by',

            'TRANSFER' =>
            'Transferencia',

            'ADDITIONAL' =>
            'Adicional',

            default =>
            $this->cost_type,
        };
    }


    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            'PENDING' =>
            'Pendiente',

            'APPROVED' =>
            'Aprobado',

            'CANCELLED' =>
            'Cancelado',

            default =>
            $this->status,
        };
    }


    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {

            'APPROVED' =>
            'bg-success-subtle text-success',

            'CANCELLED' =>
            'bg-danger-subtle text-danger',

            default =>
            'bg-warning-subtle text-warning',
        };
    }
}
