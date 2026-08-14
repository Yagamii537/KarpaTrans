<?php

namespace App\Http\Controllers;

use App\Models\Chassis;
use App\Models\Container;
use App\Models\Driver;
use App\Models\DriverRestriction;
use App\Models\Trip;
use App\Models\TripAssignment;
use App\Models\TripStatusHistory;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TripController extends Controller
{
    public function index(Request $request): View
    {
        $search =
            trim((string) $request->get('search'));

        $status =
            $request->get('status');

        $trips = Trip::query()
            ->with([
                'workOrder',
                'activeAssignment.driver',
                'activeAssignment.vehicle',
                'activeAssignment.chassis',
                'activeAssignment.container',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        function ($subquery) use ($search) {

                            $subquery
                                ->where(
                                    'trip_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'booking_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'client_name_snapshot',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )
            ->when(
                $status,
                fn($query) =>
                $query->where(
                    'status',
                    $status
                )
            )
            ->orderByDesc('scheduled_start_at')
            ->paginate(15)
            ->withQueryString();

        return view(
            'trips.index',
            compact(
                'trips',
                'search',
                'status'
            )
        );
    }

    /*
     |--------------------------------------------------------------------------
     | CREAR MANUALMENTE DESDE UNA ORDEN
     |--------------------------------------------------------------------------
     */

    public function create(Request $request): View
    {
        $workOrderId =
            $request->get('work_order_id');

        $workOrders =
            WorkOrder::query()
            ->with([
                'client',
                'subclient',
                'cargoType',
                'originLocation',
                'originPlant',
                'destinationLocation',
                'destinationPlant',
            ])
            ->whereNotIn(
                'status',
                ['COMPLETED', 'CANCELLED']
            )
            ->orderByDesc('id')
            ->get();

        $selectedWorkOrder =
            $workOrderId
            ? $workOrders
            ->firstWhere(
                'id',
                $workOrderId
            )
            : null;

        return view(
            'trips.create',
            compact(
                'workOrders',
                'selectedWorkOrder'
            )
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $request->validate([

                'work_order_id' => [
                    'required',
                    'exists:work_orders,id',
                ],

                'scheduled_start_at' => [
                    'required',
                    'date',
                ],

                'scheduled_end_at' => [
                    'nullable',
                    'date',
                    'after:scheduled_start_at',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:3000',
                ],
            ]);

        $workOrder =
            WorkOrder::with([
                'client',
                'subclient',
                'cargoType',
                'originLocation',
                'originPlant',
                'destinationLocation',
                'destinationPlant',
            ])
            ->findOrFail(
                $validated['work_order_id']
            );

        $trip =
            $this->createTripFromWorkOrder(
                $workOrder,
                $validated['scheduled_start_at'],
                $validated['scheduled_end_at']
                    ?? null,
                $validated['notes']
                    ?? null
            );

        return redirect()
            ->route(
                'trips.show',
                $trip
            )
            ->with(
                'success',
                'Viaje creado correctamente.'
            );
    }

    /*
     |--------------------------------------------------------------------------
     | GENERAR VIAJES FALTANTES DESDE OT
     |--------------------------------------------------------------------------
     */

    public function generateFromWorkOrder(
        WorkOrder $workOrder
    ): RedirectResponse {

        $workOrder->load([
            'client',
            'subclient',
            'cargoType',
            'originLocation',
            'originPlant',
            'destinationLocation',
            'destinationPlant',
            'trips',
        ]);

        $alreadyCreated =
            $workOrder->trips()->count();

        $remaining =
            max(
                0,
                $workOrder->requested_trips
                    - $alreadyCreated
            );

        if ($remaining === 0) {

            return back()->with(
                'warning',
                'Todos los viajes solicitados ya fueron generados.'
            );
        }

        DB::transaction(
            function () use (
                $workOrder,
                $remaining,
                $alreadyCreated
            ) {

                for (
                    $i = 1;
                    $i <= $remaining;
                    $i++
                ) {

                    /*
                     * Inicialmente tomamos fecha/hora
                     * solicitada de la OT.
                     */
                    $date =
                        $workOrder
                        ->requested_date
                        ->format('Y-m-d');

                    $time =
                        $workOrder->requested_time
                        ?: '00:00';

                    $scheduledStart =
                        "{$date} {$time}";

                    $this->createTripFromWorkOrder(
                        $workOrder,
                        $scheduledStart,
                        null,
                        null,
                        $alreadyCreated + $i
                    );
                }
            }
        );

        return redirect()
            ->route(
                'work-orders.show',
                $workOrder
            )
            ->with(
                'success',
                "{$remaining} viaje(s) generado(s) correctamente."
            );
    }

    public function show(Trip $trip): View
    {
        $trip->load([
            'workOrder',
            'assignments.driver',
            'assignments.vehicle',
            'assignments.chassis',
            'assignments.container',
            'assignments.assignedBy',
            'assignments.releasedBy',
            'statusHistory.user',

            'activeAssignment.driver',
            'activeAssignment.vehicle',
            'activeAssignment.chassis',
            'activeAssignment.container',

            'times.location',
            'times.plant',
            'times.creator',
        ]);

        $drivers =
            Driver::query()
            ->where('is_active', true)
            ->orderBy('last_names')
            ->get();

        $vehicles =
            Vehicle::query()
            ->where('is_active', true)
            ->whereIn(
                'operational_status',
                [
                    'AVAILABLE',
                    'ASSIGNED',
                ]
            )
            ->orderBy('plate')
            ->get();

        $chassisList =
            Chassis::query()
            ->where('is_active', true)
            ->whereIn(
                'operational_status',
                [
                    'AVAILABLE',
                    'ASSIGNED',
                ]
            )
            ->orderBy('code')
            ->get();

        $containers =
            Container::query()
            ->where('is_active', true)
            ->whereNotIn(
                'operational_status',
                [
                    'MAINTENANCE',
                    'OUT_OF_SERVICE',
                ]
            )
            ->orderBy('container_number')
            ->get();

        $locations =
            \App\Models\Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $plants =
            \App\Models\Plant::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'trips.show',
            compact(
                'trip',
                'drivers',
                'vehicles',
                'chassisList',
                'containers',
                'locations',
                'plants'
            )
        );
    }

    /*
     * En esta etapa la planificación principal
     * viene heredada de la OT.
     */
    public function edit(Trip $trip): View
    {
        return view(
            'trips.edit',
            compact('trip')
        );
    }

    public function update(
        Request $request,
        Trip $trip
    ): RedirectResponse {

        $validated =
            $request->validate([

                'scheduled_start_at' => [
                    'required',
                    'date',
                ],

                'scheduled_end_at' => [
                    'nullable',
                    'date',
                    'after:scheduled_start_at',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:3000',
                ],
            ]);

        $validated['updated_by'] =
            Auth::id();

        $trip->update($validated);

        return redirect()
            ->route(
                'trips.show',
                $trip
            )
            ->with(
                'success',
                'Planificación del viaje actualizada.'
            );
    }

    public function destroy(
        Trip $trip
    ): RedirectResponse {

        if (
            !in_array(
                $trip->status,
                ['PENDING', 'CANCELLED']
            )
        ) {

            return back()->withErrors([
                'delete' =>
                'Solo se pueden eliminar viajes pendientes o cancelados.',
            ]);
        }

        if (
            $trip->assignments()->exists()
        ) {

            return back()->withErrors([
                'delete' =>
                'El viaje ya posee historial de asignaciones y no puede eliminarse.',
            ]);
        }

        $trip->delete();

        return redirect()
            ->route('trips.index')
            ->with(
                'success',
                'Viaje eliminado correctamente.'
            );
    }

    /*
     |--------------------------------------------------------------------------
     | ASIGNAR RECURSOS
     |--------------------------------------------------------------------------
     */

    public function assign(
        Request $request,
        Trip $trip
    ): RedirectResponse {

        if (
            in_array(
                $trip->status,
                ['COMPLETED', 'CANCELLED']
            )
        ) {

            return back()->withErrors([
                'assignment' =>
                'No se pueden asignar recursos a un viaje finalizado o cancelado.',
            ]);
        }

        $validated =
            $request->validate([

                'driver_id' => [
                    'required',
                    'exists:drivers,id',
                ],

                'vehicle_id' => [
                    'required',
                    'exists:vehicles,id',
                ],

                'chassis_id' => [
                    'nullable',
                    'exists:chassis,id',
                ],

                'container_id' => [
                    'nullable',
                    'exists:containers,id',
                ],

                'assignment_reason' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);

        $driver =
            Driver::findOrFail(
                $validated['driver_id']
            );

        $vehicle =
            Vehicle::findOrFail(
                $validated['vehicle_id']
            );

        $chassis =
            !empty($validated['chassis_id'])
            ? Chassis::findOrFail(
                $validated['chassis_id']
            )
            : null;

        $container =
            !empty($validated['container_id'])
            ? Container::findOrFail(
                $validated['container_id']
            )
            : null;

        /*
         * RECURSOS ACTIVOS
         */
        if (!$driver->is_active) {
            throw ValidationException::withMessages([
                'driver_id' =>
                'El conductor seleccionado está inactivo.',
            ]);
        }

        if (
            !$vehicle->is_active
            ||
            in_array(
                $vehicle->operational_status,
                [
                    'MAINTENANCE',
                    'OUT_OF_SERVICE',
                ]
            )
        ) {

            throw ValidationException::withMessages([
                'vehicle_id' =>
                'El vehículo no está disponible para operación.',
            ]);
        }

        if (
            $chassis
            &&
            (
                !$chassis->is_active
                ||
                in_array(
                    $chassis->operational_status,
                    [
                        'MAINTENANCE',
                        'OUT_OF_SERVICE',
                    ]
                )
            )
        ) {

            throw ValidationException::withMessages([
                'chassis_id' =>
                'El chasis no está disponible para operación.',
            ]);
        }

        if (
            $container
            &&
            (
                !$container->is_active
                ||
                in_array(
                    $container->operational_status,
                    [
                        'MAINTENANCE',
                        'OUT_OF_SERVICE',
                    ]
                )
            )
        ) {

            throw ValidationException::withMessages([
                'container_id' =>
                'El contenedor no está disponible para operación.',
            ]);
        }

        /*
         * RN-15:
         * Restricciones del conductor.
         */
        $restrictionResult =
            $this->checkDriverRestrictions(
                $trip,
                $driver
            );

        if (
            !empty($restrictionResult['blocks'])
        ) {

            throw ValidationException::withMessages([
                'driver_id' =>
                implode(
                    ' | ',
                    $restrictionResult['blocks']
                ),
            ]);
        }

        /*
         * COMPATIBILIDAD CHASIS / CONTENEDOR
         */
        if ($chassis && $container) {

            if (
                $container->container_size === '20FT'
                &&
                !$chassis->supports_20ft
            ) {

                throw ValidationException::withMessages([
                    'chassis_id' =>
                    'El chasis seleccionado no admite contenedores de 20 pies.',
                ]);
            }

            if (
                in_array(
                    $container->container_size,
                    [
                        '40FT',
                        '40HC',
                    ]
                )
                &&
                !$chassis->supports_40ft
            ) {

                throw ValidationException::withMessages([
                    'chassis_id' =>
                    'El chasis seleccionado no admite contenedores de 40 pies.',
                ]);
            }

            if (
                $container->container_type === 'REEFER'
                &&
                !$chassis->supports_reefer
            ) {

                throw ValidationException::withMessages([
                    'chassis_id' =>
                    'El chasis seleccionado no está habilitado para contenedores refrigerados.',
                ]);
            }
        }

        /*
         * PESO
         */
        if (
            $container
            &&
            $vehicle->max_weight_kg
            &&
            $container->max_gross_weight_kg
            &&
            (
                (float)
                $container->max_gross_weight_kg
                >
                (float)
                $vehicle->max_weight_kg
            )
        ) {

            throw ValidationException::withMessages([
                'vehicle_id' =>
                'El peso bruto máximo del contenedor supera el peso máximo configurado para el vehículo.',
            ]);
        }

        $warnings = [];

        /*
         * DOCUMENTOS.
         * La política final de bloqueo todavía
         * está pendiente de confirmación,
         * por eso actualmente ADVERTIMOS.
         */
        if (
            $driver->license_expiration_date
            &&
            $driver
            ->license_expiration_date
            ->isPast()
        ) {

            $warnings[] =
                'La licencia del conductor está vencida.';
        }

        if ($vehicle->has_expired_document) {

            $warnings[] =
                'El vehículo posee documentación vencida.';
        }

        if (
            $chassis
            &&
            $chassis->has_expired_document
        ) {

            $warnings[] =
                'El chasis posee documentación vencida.';
        }

        foreach (
            $restrictionResult['warnings']
            as $warning
        ) {

            $warnings[] = $warning;
        }

        DB::transaction(
            function () use (
                $trip,
                $validated
            ) {

                /*
                 * Cerramos asignación anterior.
                 */
                $current =
                    $trip
                    ->assignments()
                    ->whereNull(
                        'unassigned_at'
                    )
                    ->first();

                if ($current) {

                    $current->update([
                        'unassigned_at' =>
                        now(),

                        'release_reason' =>
                        'Reasignación de recursos.',

                        'released_by' =>
                        Auth::id(),
                    ]);
                }

                TripAssignment::create([
                    'trip_id' =>
                    $trip->id,

                    'driver_id' =>
                    $validated['driver_id'],

                    'vehicle_id' =>
                    $validated['vehicle_id'],

                    'chassis_id' =>
                    $validated['chassis_id']
                        ?? null,

                    'container_id' =>
                    $validated['container_id']
                        ?? null,

                    'assigned_at' =>
                    now(),

                    'assignment_reason' =>
                    $validated['assignment_reason'] ?? null,

                    'assigned_by' =>
                    Auth::id(),
                ]);

                /*
                 * Primer cambio a ASIGNADO.
                 */
                if (
                    $trip->status
                    === 'PENDING'
                ) {

                    $this->changeStatus(
                        $trip,
                        'ASSIGNED',
                        'Asignación inicial de recursos.'
                    );
                }
            }
        );

        $response =
            redirect()
            ->route(
                'trips.show',
                $trip
            )
            ->with(
                'success',
                'Recursos asignados correctamente.'
            );

        if (!empty($warnings)) {

            $response->with(
                'warning',
                implode(
                    ' ',
                    $warnings
                )
            );
        }

        return $response;
    }

    /*
     |--------------------------------------------------------------------------
     | CAMBIAR ESTADO
     |--------------------------------------------------------------------------
     */

    public function updateStatus(
        Request $request,
        Trip $trip
    ): RedirectResponse {

        $validated =
            $request->validate([

                'status' => [
                    'required',

                    Rule::in([
                        'PENDING',
                        'ASSIGNED',
                        'IN_TRANSIT',
                        'AT_DESTINATION',
                        'COMPLETED',
                        'CANCELLED',
                    ]),
                ],

                'reason' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);

        if (
            $validated['status']
            === $trip->status
        ) {

            return back()->with(
                'warning',
                'El viaje ya se encuentra en ese estado.'
            );
        }

        $this->changeStatus(
            $trip,
            $validated['status'],
            $validated['reason'] ?? null
        );

        return redirect()
            ->route(
                'trips.show',
                $trip
            )
            ->with(
                'success',
                'Estado actualizado correctamente.'
            );
    }

    /*
     |--------------------------------------------------------------------------
     | MÉTODOS INTERNOS
     |--------------------------------------------------------------------------
     */

    private function createTripFromWorkOrder(
        WorkOrder $workOrder,
        string $scheduledStart,
        ?string $scheduledEnd = null,
        ?string $notes = null,
        ?int $sequence = null
    ): Trip {

        if (!$sequence) {

            $sequence =
                (
                    $workOrder
                    ->trips()
                    ->max('sequence_number')
                    ?? 0
                )
                + 1;
        }

        $trip =
            Trip::create([

                'work_order_id' =>
                $workOrder->id,

                'trip_number' =>
                $this->generateTripNumber(),

                'sequence_number' =>
                $sequence,

                /*
                 * SNAPSHOTS
                 */
                'client_id' =>
                $workOrder->client_id,

                'client_name_snapshot' =>
                $workOrder
                    ->client
                    ->business_name,

                'subclient_id' =>
                $workOrder->subclient_id,

                'subclient_name_snapshot' =>
                $workOrder
                    ->subclient
                    ?->business_name,

                'cargo_type_id' =>
                $workOrder->cargo_type_id,

                'cargo_type_name_snapshot' =>
                $workOrder
                    ->cargoType
                    ?->name,

                'booking_number' =>
                $workOrder
                    ->booking_number,

                'customer_order_number' =>
                $workOrder
                    ->customer_order_number,

                'operation_type' =>
                $workOrder
                    ->operation_type,

                'service_type' =>
                $workOrder
                    ->service_type,

                /*
                 * ORIGEN
                 */
                'origin_type' =>
                $workOrder
                    ->origin_type,

                'origin_location_id' =>
                $workOrder
                    ->origin_location_id,

                'origin_plant_id' =>
                $workOrder
                    ->origin_plant_id,

                'origin_name_snapshot' =>
                $workOrder
                    ->origin_name,

                /*
                 * DESTINO
                 */
                'destination_type' =>
                $workOrder
                    ->destination_type,

                'destination_location_id' =>
                $workOrder
                    ->destination_location_id,

                'destination_plant_id' =>
                $workOrder
                    ->destination_plant_id,

                'destination_name_snapshot' =>
                $workOrder
                    ->destination_name,

                'scheduled_start_at' =>
                $scheduledStart,

                'scheduled_end_at' =>
                $scheduledEnd,

                'status' =>
                'PENDING',

                'notes' =>
                $notes,

                'created_by' =>
                Auth::id(),

                'updated_by' =>
                Auth::id(),
            ]);

        TripStatusHistory::create([
            'trip_id' =>
            $trip->id,

            'previous_status' =>
            null,

            'new_status' =>
            'PENDING',

            'reason' =>
            'Creación del viaje.',

            'changed_by' =>
            Auth::id(),

            'changed_at' =>
            now(),
        ]);

        return $trip;
    }

    private function generateTripNumber(): string
    {
        $year =
            now()->format('Y');

        $lastId =
            Trip::withTrashed()
            ->max('id')
            ?? 0;

        return 'VIA-'
            . $year
            . '-'
            . str_pad(
                $lastId + 1,
                6,
                '0',
                STR_PAD_LEFT
            );
    }

    private function changeStatus(
        Trip $trip,
        string $newStatus,
        ?string $reason = null
    ): void {

        $oldStatus =
            $trip->status;

        $trip->update([
            'status' =>
            $newStatus,

            'updated_by' =>
            Auth::id(),
        ]);

        TripStatusHistory::create([
            'trip_id' =>
            $trip->id,

            'previous_status' =>
            $oldStatus,

            'new_status' =>
            $newStatus,

            'reason' =>
            $reason,

            'changed_by' =>
            Auth::id(),

            'changed_at' =>
            now(),
        ]);
    }

    private function checkDriverRestrictions(
        Trip $trip,
        Driver $driver
    ): array {

        $date =
            $trip
            ->scheduled_start_at
            ->toDateString();

        $restrictions =
            DriverRestriction::query()
            ->where(
                'driver_id',
                $driver->id
            )
            ->where(
                'is_active',
                true
            )
            ->whereDate(
                'start_date',
                '<=',
                $date
            )
            ->where(
                function ($query) use ($date) {

                    $query
                        ->whereNull(
                            'end_date'
                        )
                        ->orWhereDate(
                            'end_date',
                            '>=',
                            $date
                        );
                }
            )
            ->where(
                function ($query) use ($trip) {

                    /*
                         * Restricción general:
                         * ninguno de los campos
                         * de alcance está definido.
                         */
                    $query
                        ->where(
                            function ($general) {

                                $general
                                    ->whereNull(
                                        'client_id'
                                    )
                                    ->whereNull(
                                        'subclient_id'
                                    )
                                    ->whereNull(
                                        'plant_id'
                                    )
                                    ->whereNull(
                                        'location_id'
                                    );
                            }
                        )

                        ->orWhere(
                            'client_id',
                            $trip->client_id
                        );

                    if ($trip->subclient_id) {

                        $query->orWhere(
                            'subclient_id',
                            $trip->subclient_id
                        );
                    }

                    if ($trip->origin_plant_id) {

                        $query->orWhere(
                            'plant_id',
                            $trip->origin_plant_id
                        );
                    }

                    if (
                        $trip
                        ->destination_plant_id
                    ) {

                        $query->orWhere(
                            'plant_id',
                            $trip
                                ->destination_plant_id
                        );
                    }

                    if (
                        $trip
                        ->origin_location_id
                    ) {

                        $query->orWhere(
                            'location_id',
                            $trip
                                ->origin_location_id
                        );
                    }

                    if (
                        $trip
                        ->destination_location_id
                    ) {

                        $query->orWhere(
                            'location_id',
                            $trip
                                ->destination_location_id
                        );
                    }
                }
            )
            ->get();

        $blocks = [];
        $warnings = [];

        foreach (
            $restrictions
            as $restriction
        ) {

            $message =
                'Restricción del conductor: '
                . $restriction->reason;

            if (
                $restriction->action_type
                === 'BLOCK'
            ) {

                $blocks[] =
                    $message;
            } else {

                $warnings[] =
                    $message;
            }
        }

        return [
            'blocks' => $blocks,
            'warnings' => $warnings,
        ];
    }
}
