<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DriverRestriction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [

        'driver_id',

        'client_id',
        'subclient_id',
        'plant_id',
        'location_id',

        'operation_type',

        'reason',

        'start_date',
        'end_date',

        'restriction_type',

        'action_type',

        'notes',

        'is_active',

        'created_by',
        'updated_by',
    ];


    protected function casts(): array
    {
        return [

            'start_date' =>
            'date',

            'end_date' =>
            'date',

            'is_active' =>
            'boolean',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function driver()
    {
        return $this->belongsTo(
            Driver::class
        );
    }


    public function client()
    {
        return $this->belongsTo(
            Client::class
        );
    }


    public function subclient()
    {
        return $this->belongsTo(
            Subclient::class
        );
    }


    public function plant()
    {
        return $this->belongsTo(
            Plant::class
        );
    }


    public function location()
    {
        return $this->belongsTo(
            Location::class
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

    public function getOperationTypeLabelAttribute(): string
    {
        return match ($this->operation_type) {

            'EXPORT' =>
            'Exportación',

            'IMPORT' =>
            'Importación',

            'TRANSFER' =>
            'Transferencia',

            'OTHER' =>
            'Otra',

            default =>
            'Todas',
        };
    }


    public function getRestrictionTypeLabelAttribute(): string
    {
        return match ($this->restriction_type) {

            'TEMPORARY' =>
            'Temporal',

            'INDEFINITE' =>
            'Indefinida',

            default =>
            $this->restriction_type
                ?: 'No definida',
        };
    }


    public function getActionTypeLabelAttribute(): string
    {
        return match ($this->action_type) {

            'BLOCK' =>
            'Bloquear',

            'WARNING' =>
            'Advertir',

            default =>
            $this->action_type
                ?: 'No definida',
        };
    }


    public function getScopeLabelAttribute(): string
    {
        $parts = [];


        if ($this->client) {

            $parts[] =
                'Cliente: '
                . $this->client->business_name;
        }


        if ($this->subclient) {

            $parts[] =
                'Subcliente: '
                . $this->subclient->business_name;
        }


        if ($this->plant) {

            $parts[] =
                'Planta: '
                . $this->plant->name;
        }


        if ($this->location) {

            $parts[] =
                'Ubicación: '
                . $this->location->name;
        }


        if ($this->operation_type) {

            $parts[] =
                'Operación: '
                . $this->operation_type_label;
        }


        if (empty($parts)) {

            return 'Restricción general';
        }


        return implode(
            ' · ',
            $parts
        );
    }


    /*
    |--------------------------------------------------------------------------
    | VIGENCIA
    |--------------------------------------------------------------------------
    */

    public function getIsCurrentlyEffectiveAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }


        $today =
            now()->startOfDay();


        if (
            $this->start_date
            &&
            $this->start_date
            ->copy()
            ->startOfDay()
            ->gt($today)
        ) {

            return false;
        }


        if (
            $this->end_date
            &&
            $this->end_date
            ->copy()
            ->startOfDay()
            ->lt($today)
        ) {

            return false;
        }


        return true;
    }
}
