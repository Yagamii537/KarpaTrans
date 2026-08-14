<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CargoType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'client_cargo_types'
        )->withTimestamps();
    }

    public function subclients()
    {
        return $this->belongsToMany(
            Subclient::class,
            'subclient_cargo_types'
        )->withTimestamps();
    }
}
