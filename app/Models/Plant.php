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

        'notes',
        'is_active',
    ];


    protected function casts(): array
    {
        return [

            'latitude' =>
            'decimal:7',

            'longitude' =>
            'decimal:7',

            'is_active' =>
            'boolean',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function client()
    {
        return $this->belongsTo(
            Client::class
        );
    }


    public function workOrders()
    {
        return $this->hasMany(
            WorkOrder::class,
            'plant_id'
        );
    }


    public function originWorkOrders()
    {
        return $this->hasMany(
            WorkOrder::class,
            'origin_plant_id'
        );
    }


    public function destinationWorkOrders()
    {
        return $this->hasMany(
            WorkOrder::class,
            'destination_plant_id'
        );
    }


    public function tripTimes()
    {
        return $this->hasMany(
            TripTime::class
        );
    }
}
