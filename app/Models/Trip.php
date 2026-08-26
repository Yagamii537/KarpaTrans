<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'work_order_id',
        'trip_number',
        'sequence_number',
        'service_number',
        'service_stage',
        'client_id',
        'client_name_snapshot',
        'subclient_id',
        'subclient_name_snapshot',
        'cargo_type_id',
        'cargo_type_name_snapshot',
        'booking_number',
        'customer_order_number',
        'operation_type',
        'service_type',
        'origin_type',
        'origin_location_id',
        'origin_plant_id',
        'origin_name_snapshot',
        'destination_type',
        'destination_location_id',
        'destination_plant_id',
        'destination_name_snapshot',
        'scheduled_start_at',
        'scheduled_end_at',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'sequence_number' => 'integer',
            'service_number' => 'integer',
            'scheduled_start_at' => 'datetime',
            'scheduled_end_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function subclient()
    {
        return $this->belongsTo(Subclient::class);
    }

    public function cargoType()
    {
        return $this->belongsTo(CargoType::class);
    }

    public function assignments()
    {
        return $this->hasMany(TripAssignment::class)
            ->orderByDesc('assigned_at');
    }

    public function activeAssignment()
    {
        return $this->hasOne(TripAssignment::class)
            ->whereNull('unassigned_at')
            ->latestOfMany();
    }

    public function statusHistory()
    {
        return $this->hasMany(TripStatusHistory::class)
            ->orderByDesc('changed_at');
    }

    public function times()
    {
        return $this->hasMany(TripTime::class)
            ->orderBy('event_at')
            ->orderBy('id');
    }

    /*
    |--------------------------------------------------------------------------
    | ETIQUETAS
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'PENDING' => 'Pendiente',
            'ASSIGNED' => 'Asignado',
            'IN_TRANSIT' => 'En tránsito',
            'AT_DESTINATION' => 'En destino',
            'COMPLETED' => 'Completado',
            'CANCELLED' => 'Cancelado',
            default => $this->status,
        };
    }

    public function getOperationTypeLabelAttribute(): string
    {
        return match ($this->operation_type) {
            'EXPORT' => 'Exportación',
            'IMPORT' => 'Importación',
            'TRANSFER' => 'Transferencia',
            default => 'Otra',
        };
    }

    public function getServiceStageLabelAttribute(): string
    {
        return match ($this->service_stage) {
            'IMMEDIATE' => 'Inmediata',
            'POSITIONING' => 'Posición',
            'PICKUP' => 'Retiro',
            'TRANSFER' => 'Transferencia',
            default => 'No definida',
        };
    }

    public function getServiceStageBadgeClassAttribute(): string
    {
        return match ($this->service_stage) {
            'POSITIONING' => 'bg-info-subtle text-info',
            'PICKUP' => 'bg-warning-subtle text-warning',
            'IMMEDIATE' => 'bg-primary-subtle text-primary',
            'TRANSFER' => 'bg-secondary-subtle text-secondary',
            default => 'bg-light text-dark',
        };
    }

    /*
    |--------------------------------------------------------------------------
    | EVENTOS OPERATIVOS
    |--------------------------------------------------------------------------
    |
    | Cada etapa solo muestra los eventos que realmente tienen sentido.
    | La configuración puede ajustarse después si el cliente cambia el flujo.
    |
    */

    public static function eventLabels(): array
    {
        return [
            'ARRIVAL' => 'Llegada',
            'ENTRY' => 'Ingreso',
            'CONTAINER_PICKUP' => 'Retiro de contenedor',
            'LOAD_START' => 'Inicio de carga',
            'LOAD_END' => 'Fin de carga',
            'UNLOAD_START' => 'Inicio de descarga',
            'UNLOAD_END' => 'Fin de descarga',
            'WAIT_START' => 'Inicio de espera',
            'WAIT_END' => 'Fin de espera',
            'DEPARTURE' => 'Salida',
            'POSITIONING' => 'Posicionamiento',
            'PICKUP' => 'Retiro',
            'PORT_ARRIVAL' => 'Llegada a puerto',
            'DELIVERY' => 'Entrega',
            'OTHER' => 'Otro',
        ];
    }

    public function allowedEventTypes(): array
    {
        if ($this->service_stage === 'POSITIONING') {
            return [
                'ARRIVAL',
                'ENTRY',
                'WAIT_START',
                'WAIT_END',
                'POSITIONING',
                'DEPARTURE',
                'OTHER',
            ];
        }

        if ($this->service_stage === 'PICKUP') {
            return [
                'ARRIVAL',
                'ENTRY',
                'WAIT_START',
                'WAIT_END',
                'PICKUP',
                'DEPARTURE',
                'PORT_ARRIVAL',
                'DELIVERY',
                'OTHER',
            ];
        }

        if ($this->service_stage === 'TRANSFER') {
            return [
                'ARRIVAL',
                'ENTRY',
                'WAIT_START',
                'WAIT_END',
                'DEPARTURE',
                'DELIVERY',
                'OTHER',
            ];
        }

        // INMEDIATA: diferenciamos por tipo de operación.
        if ($this->operation_type === 'IMPORT') {
            return [
                'ARRIVAL',
                'ENTRY',
                'WAIT_START',
                'WAIT_END',
                'UNLOAD_START',
                'UNLOAD_END',
                'DEPARTURE',
                'DELIVERY',
                'OTHER',
            ];
        }

        if ($this->operation_type === 'EXPORT') {
            return [
                'ARRIVAL',
                'ENTRY',
                'WAIT_START',
                'WAIT_END',
                'LOAD_START',
                'LOAD_END',
                'DEPARTURE',
                'PORT_ARRIVAL',
                'DELIVERY',
                'OTHER',
            ];
        }

        return [
            'ARRIVAL',
            'ENTRY',
            'WAIT_START',
            'WAIT_END',
            'DEPARTURE',
            'DELIVERY',
            'OTHER',
        ];
    }

    public function availableEventOptions(): array
    {
        $labels = self::eventLabels();

        $used = $this->relationLoaded('times')
            ? $this->times->pluck('event_type')->all()
            : $this->times()->pluck('event_type')->all();

        $available = [];

        foreach ($this->allowedEventTypes() as $eventType) {
            if (in_array($eventType, $used, true)) {
                continue;
            }

            $prerequisites = $this->eventPrerequisites($eventType);

            $prerequisitesCompleted = collect($prerequisites)
                ->every(fn(string $required) => in_array($required, $used, true));

            if (!$prerequisitesCompleted) {
                continue;
            }

            $available[$eventType] = $labels[$eventType] ?? $eventType;
        }

        return $available;
    }

    public function eventPrerequisites(string $eventType): array
    {
        $common = [
            'ENTRY' => ['ARRIVAL'],
            'WAIT_START' => ['ARRIVAL'],
            'WAIT_END' => ['WAIT_START'],
        ];

        if ($this->service_stage === 'POSITIONING') {
            return array_merge($common, [
                'POSITIONING' => ['ARRIVAL'],
                'DEPARTURE' => ['POSITIONING'],
            ])[$eventType] ?? [];
        }

        if ($this->service_stage === 'PICKUP') {
            return array_merge($common, [
                'PICKUP' => ['ARRIVAL'],
                'DEPARTURE' => ['PICKUP'],
                'PORT_ARRIVAL' => ['DEPARTURE'],
                'DELIVERY' => ['DEPARTURE'],
            ])[$eventType] ?? [];
        }

        if ($this->service_stage === 'TRANSFER') {
            return array_merge($common, [
                'DEPARTURE' => ['ARRIVAL'],
                'DELIVERY' => ['DEPARTURE'],
            ])[$eventType] ?? [];
        }

        if ($this->operation_type === 'IMPORT') {
            return array_merge($common, [
                'UNLOAD_START' => ['ARRIVAL'],
                'UNLOAD_END' => ['UNLOAD_START'],
                'DEPARTURE' => ['UNLOAD_END'],
                'DELIVERY' => ['DEPARTURE'],
            ])[$eventType] ?? [];
        }

        if ($this->operation_type === 'EXPORT') {
            return array_merge($common, [
                'LOAD_START' => ['ARRIVAL'],
                'LOAD_END' => ['LOAD_START'],
                'DEPARTURE' => ['LOAD_END'],
                'PORT_ARRIVAL' => ['DEPARTURE'],
                'DELIVERY' => ['DEPARTURE'],
            ])[$eventType] ?? [];
        }

        return array_merge($common, [
            'DEPARTURE' => ['ARRIVAL'],
            'DELIVERY' => ['DEPARTURE'],
        ])[$eventType] ?? [];
    }

    public function statusForEvent(string $eventType): ?string
    {
        if ($this->service_stage === 'POSITIONING') {
            return match ($eventType) {
                'POSITIONING' => 'AT_DESTINATION',
                'DEPARTURE' => 'COMPLETED',
                default => null,
            };
        }

        return match ($eventType) {
            'DEPARTURE' => 'IN_TRANSIT',
            'PORT_ARRIVAL' => 'AT_DESTINATION',
            'DELIVERY' => 'COMPLETED',
            default => null,
        };
    }

    public function eventSequenceHelp(): string
    {
        $labels = self::eventLabels();

        $mainSequence = match ($this->service_stage) {
            'POSITIONING' => ['ARRIVAL', 'POSITIONING', 'DEPARTURE'],
            'PICKUP' => ['ARRIVAL', 'PICKUP', 'DEPARTURE', 'PORT_ARRIVAL', 'DELIVERY'],
            'TRANSFER' => ['ARRIVAL', 'DEPARTURE', 'DELIVERY'],
            default => $this->operation_type === 'IMPORT'
                ? ['ARRIVAL', 'UNLOAD_START', 'UNLOAD_END', 'DEPARTURE', 'DELIVERY']
                : ($this->operation_type === 'EXPORT'
                    ? ['ARRIVAL', 'LOAD_START', 'LOAD_END', 'DEPARTURE', 'PORT_ARRIVAL', 'DELIVERY']
                    : ['ARRIVAL', 'DEPARTURE', 'DELIVERY']),
        };

        return implode(
            ' → ',
            array_map(
                fn(string $eventType) => $labels[$eventType] ?? $eventType,
                $mainSequence
            )
        );
    }
    /*
|--------------------------------------------------------------------------
| DEPENDENCIA ENTRE ETAPAS
|--------------------------------------------------------------------------
*/

    public function previousStage()
    {
        if (
            $this->service_stage !== 'PICKUP'
            ||
            !$this->work_order_id
            ||
            !$this->service_number
        ) {
            return null;
        }

        return self::query()
            ->where(
                'work_order_id',
                $this->work_order_id
            )
            ->where(
                'service_number',
                $this->service_number
            )
            ->where(
                'service_stage',
                'POSITIONING'
            )
            ->first();
    }


    public function getIsStageUnlockedAttribute(): bool
    {
        /*
     * Solo RETIRO depende de POSICIÓN.
     */
        if (
            $this->service_stage !== 'PICKUP'
        ) {
            return true;
        }


        $positioning =
            $this->previousStage();


        /*
     * Si no existe viaje de posición,
     * no bloqueamos para no dañar
     * modalidades independientes.
     */
        if (!$positioning) {
            return true;
        }


        return $positioning->status
            === 'COMPLETED';
    }


    public function getStageProgressLabelAttribute(): string
    {
        if (
            $this->workOrder?->service_modality
            === 'POSITIONING_PICKUP'
        ) {

            return match ($this->service_stage) {

                'POSITIONING' =>
                'Etapa 1 de 2',

                'PICKUP' =>
                'Etapa 2 de 2',

                default =>
                'Etapa',
            };
        }


        return 'Etapa única';
    }

    public function standbyCalculation()
    {
        return $this->hasOne(
            \App\Models\TripStandbyCalculation::class
        );
    }
}
