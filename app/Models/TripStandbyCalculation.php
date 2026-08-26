<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripStandbyCalculation extends Model
{
    use HasFactory;

    protected $fillable = [

        'trip_id',

        'process_type',

        'count_start_type',

        'end_event_type',

        'free_hours',

        'fraction_minutes',

        'rule_source',

        'requested_at',

        'arrival_at',

        'start_at',

        'end_at',

        'total_minutes',

        'free_minutes',

        'excess_minutes',

        'billable_hours',

        'status',

        'observation',

        'calculated_at',

        'calculated_by',
    ];


    protected function casts(): array
    {
        return [

            'requested_at' =>
            'datetime',

            'arrival_at' =>
            'datetime',

            'start_at' =>
            'datetime',

            'end_at' =>
            'datetime',

            'total_minutes' =>
            'integer',

            'free_minutes' =>
            'integer',

            'excess_minutes' =>
            'integer',

            'billable_hours' =>
            'integer',

            'free_hours' =>
            'integer',

            'fraction_minutes' =>
            'integer',

            'calculated_at' =>
            'datetime',
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


    public function calculatedBy()
    {
        return $this->belongsTo(
            User::class,
            'calculated_by'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ETIQUETAS
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            'CALCULATED' =>
            'Calculado',

            'PENDING' =>
            'Pendiente',

            default =>
            $this->status
                ?: 'No definido',
        };
    }


    public function getProcessTypeLabelAttribute(): string
    {
        return match ($this->process_type) {

            'LOAD' =>
            'Carga',

            'UNLOAD' =>
            'Descarga',

            default =>
            $this->process_type
                ?: 'No definido',
        };
    }


    public function getCountStartTypeLabelAttribute(): string
    {
        return match ($this->count_start_type) {

            'REQUESTED_TIME' =>
            'Hora solicitada',

            'ARRIVAL_TIME' =>
            'Llegada real',

            default =>
            $this->count_start_type
                ?: 'No definido',
        };
    }
}
