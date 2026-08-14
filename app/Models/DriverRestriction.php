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
        'reason',
        'start_date',
        'end_date',
        'restriction_type',
        'action_type',
        'notes',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subclient()
    {
        return $this->belongsTo(Subclient::class);
    }

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRestrictionTypeLabelAttribute(): string
    {
        return $this->restriction_type === 'TEMPORARY'
            ? 'Temporal'
            : 'Indefinida';
    }

    public function getActionTypeLabelAttribute(): string
    {
        return $this->action_type === 'WARNING'
            ? 'Advertencia'
            : 'Bloqueo';
    }
}
