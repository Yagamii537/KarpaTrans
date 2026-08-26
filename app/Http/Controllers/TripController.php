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
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->get('search')
        );

        $status = $request->get('status');

        $trips = Trip::query()

            ->with([
                'workOrder',

                'activeAssignment.driver',
                'activeAssignment.vehicle',
                'activeAssignment.chassis',
                'activeAssignment.container',

                /*
                 * Necesitamos también el historial
                 * para mostrar recursos utilizados
                 * cuando el viaje ya terminó.
                 */
                'assignments.driver',
                'assignments.vehicle',
                'assignments.chassis',
                'assignments.container',
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
                                )

                                ->orWhere(
                                    'subclient_name_snapshot',
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

            ->orderByDesc(
                'scheduled_start_at'
            )

            ->orderBy(
                'work_order_id'
            )

            ->orderBy(
                'service_number'
            )

            ->orderBy(
                'sequence_number'
            )

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
    | CREACIÓN MANUAL
    |--------------------------------------------------------------------------
    */

    public function create(Request $request): View
    {
        $workOrderId =
            $request->get(
                'work_order_id'
            );


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
                [
                    'COMPLETED',
                    'CANCELLED',
                ]
            )

            ->orderByDesc('id')

            ->get();


        $selectedWorkOrder =
            $workOrderId

            ? $workOrders->firstWhere(
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


    public function store(Request $request): RedirectResponse
    {
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


        $serviceNumber =
            (
                $workOrder
                ->trips()
                ->max(
                    'service_number'
                )
                ?? 0
            ) + 1;


        $stage =
            $this->defaultStage(
                $workOrder
                    ->service_modality
            );


        $trip =
            DB::transaction(
                function () use (
                    $workOrder,
                    $validated,
                    $serviceNumber,
                    $stage
                ) {

                    return $this
                        ->createTripFromWorkOrder(

                            $workOrder,

                            $serviceNumber,

                            $stage,

                            $validated['scheduled_start_at'],

                            $validated['scheduled_end_at'] ?? null,

                            $validated['notes'] ?? null
                        );
                }
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
    | GENERAR DESDE OT
    |--------------------------------------------------------------------------
    */

    public function generateFromWorkOrder(
        WorkOrder $workOrder
    ): RedirectResponse {

        if (
            in_array(
                $workOrder->status,
                [
                    'COMPLETED',
                    'CANCELLED',
                ]
            )
        ) {

            return back()
                ->withErrors([

                    'trips' =>
                    'No se pueden generar viajes para una orden completada o cancelada.',

                ]);
        }


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


        if (
            empty($workOrder
                ->service_modality)
        ) {

            return back()
                ->withErrors([

                    'trips' =>
                    'La Orden de Trabajo no tiene modalidad de servicio definida.',

                ]);
        }


        $createdTrips = 0;


        DB::transaction(
            function () use (
                $workOrder,
                &$createdTrips
            ) {

                for (
                    $serviceNumber = 1;
                    $serviceNumber
                        <= $workOrder->requested_trips;
                    $serviceNumber++
                ) {

                    $requiredStages =
                        $this->stagesForModality(
                            $workOrder
                                ->service_modality
                        );


                    foreach (
                        $requiredStages
                        as $stage
                    ) {

                        $exists =
                            $workOrder
                            ->trips()

                            ->where(
                                'service_number',
                                $serviceNumber
                            )

                            ->where(
                                'service_stage',
                                $stage
                            )

                            ->exists();


                        if ($exists) {
                            continue;
                        }


                        $scheduledStart =
                            $this
                            ->buildScheduledStart(
                                $workOrder
                            );


                        $this
                            ->createTripFromWorkOrder(

                                $workOrder,

                                $serviceNumber,

                                $stage,

                                $scheduledStart,

                                null,

                                null
                            );


                        $createdTrips++;
                    }
                }
            }
        );


        if (
            $createdTrips === 0
        ) {

            return back()
                ->with(
                    'warning',

                    'Todos los servicios y etapas de esta orden ya fueron generados.'
                );
        }


        if (
            $workOrder->status
            === 'PENDING'
        ) {

            $workOrder->update([

                'status' =>
                'PLANNED',

                'updated_by' =>
                Auth::id(),

            ]);
        }


        return redirect()
            ->route(
                'work-orders.show',
                $workOrder
            )
            ->with(
                'success',

                "{$createdTrips} viaje(s) / etapa(s) generado(s) correctamente."
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */

    public function show(
        Trip $trip
    ): View {

        $trip->load([

            'workOrder',

            'standbyCalculation',

            /*
            |--------------------------------------------------------------------------
            | ASIGNACIONES DEL VIAJE
            |--------------------------------------------------------------------------
            */

            'assignments.driver',

            'assignments.vehicle',

            'assignments.chassis',

            'assignments.container',

            'assignments.assignedBy',

            'assignments.releasedBy',

            /*
            |--------------------------------------------------------------------------
            | HISTORIAL DEL VIAJE
            |--------------------------------------------------------------------------
            */

            'statusHistory.user',

            /*
            |--------------------------------------------------------------------------
            | ASIGNACIÓN ACTIVA
            |--------------------------------------------------------------------------
            */

            'activeAssignment.driver',

            'activeAssignment.vehicle',

            'activeAssignment.chassis',

            'activeAssignment.container',

            'activeAssignment.assignedBy',

            /*
            |--------------------------------------------------------------------------
            | EVENTOS DEL VIAJE
            |--------------------------------------------------------------------------
            */

            'times.location',

            'times.plant',

            'times.creator',

            /*
            |--------------------------------------------------------------------------
            | TRANSFERENCIAS RELACIONADAS
            |--------------------------------------------------------------------------
            */

            'transfers.activeAssignment.driver',

            'transfers.activeAssignment.vehicle',

            'transfers.activeAssignment.chassis',

            'transfers.activeAssignment.container',

            /*
             * Historial de recursos de la transferencia.
             *
             * Esto permite mostrar los recursos
             * incluso después de completar la transferencia,
             * cuando ya no existe activeAssignment.
             */
            'transfers.assignments.driver',

            'transfers.assignments.vehicle',

            'transfers.assignments.chassis',

            'transfers.assignments.container',

        ]);


        /*
         * Etapa de Posición relacionada
         * con este Retiro.
         */
        $positioningTrip =
            $this->findPositioningStage(
                $trip
            );


        /*
         * RETIRO se habilita solamente
         * cuando Posición terminó.
         */
        $stageUnlocked =
            $this->isStageUnlocked(
                $trip
            );


        /*
         * Asignación que mostramos.
         *
         * Si está completado ya no existe
         * activeAssignment, por eso usamos
         * la última histórica.
         */
        $displayAssignment =
            $trip->activeAssignment
            ??
            $trip
            ->assignments
            ->sortByDesc(
                'assigned_at'
            )
            ->first();


        $drivers =
            Driver::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'last_names'
            )

            ->orderBy(
                'first_names'
            )

            ->get();


        $vehicles =
            Vehicle::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'plate'
            )

            ->get();


        $chassisList =
            Chassis::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'code'
            )

            ->get();


        $containers =
            Container::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'container_number'
            )

            ->get();


        $locations =
            \App\Models\Location::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'name'
            )

            ->get();


        $plants =
            \App\Models\Plant::query()

            ->where(
                'is_active',
                true
            )

            ->with(
                'client'
            )

            ->orderBy(
                'name'
            )

            ->get();


        /*
         * Si estamos en RETIRO,
         * sugerimos el contenedor
         * usado en POSICIÓN.
         */
        $suggestedContainer =
            null;


        if (
            $trip->service_stage
            === 'PICKUP'

            &&
            $trip->service_number
        ) {

            $suggestedContainerId =
                TripAssignment::query()

                ->whereHas(
                    'trip',

                    function ($query) use ($trip) {

                        $query
                            ->where(
                                'work_order_id',
                                $trip->work_order_id
                            )

                            ->where(
                                'service_number',
                                $trip->service_number
                            )

                            ->where(
                                'service_stage',
                                'POSITIONING'
                            );
                    }
                )

                ->whereNotNull(
                    'container_id'
                )

                ->orderByDesc(
                    'assigned_at'
                )

                ->value(
                    'container_id'
                );


            if ($suggestedContainerId) {

                $suggestedContainer =
                    $containers
                    ->firstWhere(
                        'id',
                        $suggestedContainerId
                    );
            }
        }


        $availableEvents =
            $stageUnlocked
            ? $trip->availableEventOptions()
            : [];


        $eventSequenceHelp =
            $trip->eventSequenceHelp();


        return view(
            'trips.show',

            compact(

                'trip',

                'drivers',

                'vehicles',

                'chassisList',

                'containers',

                'locations',

                'plants',

                'suggestedContainer',

                'availableEvents',

                'eventSequenceHelp',

                'positioningTrip',

                'stageUnlocked',

                'displayAssignment'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR PLANIFICACIÓN
    |--------------------------------------------------------------------------
    */

    public function edit(
        Trip $trip
    ): View {

        return view(
            'trips.edit',
            compact(
                'trip'
            )
        );
    }


    public function update(
        Request $request,
        Trip $trip
    ): RedirectResponse {

        if (
            in_array(
                $trip->status,
                [
                    'COMPLETED',
                    'CANCELLED',
                ]
            )
        ) {

            return back()
                ->withErrors([

                    'trip' =>
                    'No se puede modificar la planificación de un viaje finalizado.',

                ]);
        }


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


        $trip->update(
            $validated
        );


        return redirect()
            ->route(
                'trips.show',
                $trip
            )
            ->with(
                'success',

                'Planificación actualizada correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Trip $trip
    ): RedirectResponse {

        if (
            !in_array(
                $trip->status,
                [
                    'PENDING',
                    'CANCELLED',
                ]
            )
        ) {

            return back()
                ->withErrors([

                    'delete' =>
                    'Solo se pueden eliminar viajes pendientes o cancelados.',

                ]);
        }


        /*
         * No eliminamos un viaje si ya existe
         * cualquier información operativa asociada.
         *
         * Ahora también se consideran
         * las transferencias relacionadas.
         */
        if (
            $trip
            ->assignments()
            ->exists()

            ||

            $trip
            ->times()
            ->exists()

            ||

            $trip
            ->transfers()
            ->exists()
        ) {

            return back()
                ->withErrors([

                    'delete' =>
                    'El viaje ya posee información operativa o transferencias registradas y no puede eliminarse.',

                ]);
        }


        $trip->delete();


        return redirect()
            ->route(
                'trips.index'
            )
            ->with(
                'success',
                'Viaje eliminado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ASIGNACIÓN
    |--------------------------------------------------------------------------
    */

    public function assign(
        Request $request,
        Trip $trip
    ): RedirectResponse {

        /*
         * PRIMERO:
         * validar estado final.
         */
        if (
            in_array(
                $trip->status,
                [
                    'COMPLETED',
                    'CANCELLED',
                ]
            )
        ) {

            return back()
                ->withErrors([

                    'assignment' =>
                    'No se pueden asignar recursos a un viaje finalizado o cancelado.',

                ]);
        }


        /*
         * SEGUNDO:
         * RETIRO no puede comenzar
         * sin completar POSICIÓN.
         */
        if (
            !$this->isStageUnlocked(
                $trip
            )
        ) {

            $positioning =
                $this
                ->findPositioningStage(
                    $trip
                );


            throw ValidationException::withMessages([

                'assignment' =>

                'No se puede asignar la etapa de Retiro todavía. '

                    . 'Primero debe completarse la etapa de Posición '

                    . 'del Servicio #'

                    . $trip->service_number

                    . (

                        $positioning

                        ? ' (' . $positioning->trip_number . ').'

                        : '.'
                    ),

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


        $trip->load([
            'workOrder',
            'activeAssignment',
        ]);


        /*
         * RETIRO reutiliza contenedor
         * de POSICIÓN si no seleccionan otro.
         */
        if (
            empty($validated['container_id'])

            &&
            $trip->service_stage
            === 'PICKUP'

            &&
            $trip->service_number
        ) {

            $positioningContainerId =
                TripAssignment::query()

                ->whereHas(
                    'trip',

                    function ($query) use ($trip) {

                        $query
                            ->where(
                                'work_order_id',
                                $trip->work_order_id
                            )

                            ->where(
                                'service_number',
                                $trip->service_number
                            )

                            ->where(
                                'service_stage',
                                'POSITIONING'
                            );
                    }
                )

                ->whereNotNull(
                    'container_id'
                )

                ->orderByDesc(
                    'assigned_at'
                )

                ->value(
                    'container_id'
                );


            if ($positioningContainerId) {

                $validated['container_id'] =
                    $positioningContainerId;
            }
        }


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


        $warnings = [];


        /*
        |--------------------------------------------------------------------------
        | CONDUCTOR
        |--------------------------------------------------------------------------
        */

        if (!$driver->is_active) {

            throw ValidationException::withMessages([

                'driver_id' =>
                'El conductor seleccionado está inactivo.',

            ]);
        }


        if (
            $driver->license_status
            === 'expired'
        ) {

            $warnings[] =
                'La licencia del conductor está vencida.';
        } elseif (
            $driver->license_status
            === 'expiring'
        ) {

            $warnings[] =
                'La licencia del conductor está próxima a vencer.';
        }


        $driverBusy =
            TripAssignment::query()

            ->where(
                'driver_id',
                $driver->id
            )

            ->whereNull(
                'unassigned_at'
            )

            ->where(
                'trip_id',
                '!=',
                $trip->id
            )

            ->exists();


        if ($driverBusy) {

            throw ValidationException::withMessages([

                'driver_id' =>
                'El conductor ya tiene una asignación activa en otro viaje.',

            ]);
        }


        $restrictionResult =
            $this
            ->checkDriverRestrictions(
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


        if (
            !empty($restrictionResult['warnings'])
        ) {

            $warnings =
                array_merge(

                    $warnings,

                    $restrictionResult['warnings']
                );
        }


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULO
        |--------------------------------------------------------------------------
        */

        if (!$vehicle->is_active) {

            throw ValidationException::withMessages([

                'vehicle_id' =>
                'El vehículo seleccionado está inactivo.',

            ]);
        }


        if (
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
                'El vehículo no se encuentra disponible para operación.',

            ]);
        }


        $vehicleBusy =
            TripAssignment::query()

            ->where(
                'vehicle_id',
                $vehicle->id
            )

            ->whereNull(
                'unassigned_at'
            )

            ->where(
                'trip_id',
                '!=',
                $trip->id
            )

            ->exists();


        if ($vehicleBusy) {

            throw ValidationException::withMessages([

                'vehicle_id' =>
                'El vehículo ya tiene una asignación activa en otro viaje.',

            ]);
        }


        $estimatedWeight =
            $trip
            ->workOrder
            ?->estimated_weight_kg;


        if (
            $estimatedWeight

            &&
            $vehicle
            ->max_load_capacity_kg

            &&
            (float) $estimatedWeight

            >
            (float) $vehicle
                ->max_load_capacity_kg
        ) {

            throw ValidationException::withMessages([

                'vehicle_id' =>

                'El vehículo no tiene capacidad suficiente. '

                    . 'La OT requiere aproximadamente '

                    . number_format(
                        (float) $estimatedWeight,
                        2
                    )

                    . ' kg y el vehículo admite '

                    . number_format(
                        (float) $vehicle
                            ->max_load_capacity_kg,
                        2
                    )

                    . ' kg.',

            ]);
        }


        if (
            $vehicle
            ->has_expired_document
        ) {

            $warnings[] =
                'El vehículo tiene uno o más documentos vencidos.';
        }


        /*
        |--------------------------------------------------------------------------
        | CONTENEDOR
        |--------------------------------------------------------------------------
        */

        if ($container) {

            if (!$container->is_active) {

                throw ValidationException::withMessages([

                    'container_id' =>
                    'El contenedor seleccionado está inactivo.',

                ]);
            }


            if (
                in_array(
                    $container
                        ->operational_status,

                    [
                        'MAINTENANCE',
                        'OUT_OF_SERVICE',
                    ]
                )
            ) {

                throw ValidationException::withMessages([

                    'container_id' =>
                    'El contenedor no se encuentra disponible para operación.',

                ]);
            }


            $requestedType =
                $trip
                ->workOrder
                ?->requested_container_type;


            $requestedSize =
                $trip
                ->workOrder
                ?->requested_container_size;


            if (
                $requestedType

                &&
                $container
                ->container_type
                !== $requestedType
            ) {

                throw ValidationException::withMessages([

                    'container_id' =>

                    'El contenedor no coincide con el tipo requerido por la OT. '

                        . 'Requerido: '

                        . $requestedType

                        . '. Seleccionado: '

                        . $container
                        ->container_type

                        . '.',

                ]);
            }


            if (
                $requestedSize

                &&
                $container
                ->container_size
                !== $requestedSize
            ) {

                throw ValidationException::withMessages([

                    'container_id' =>

                    'El contenedor no coincide con el tamaño requerido por la OT. '

                        . 'Requerido: '

                        . $requestedSize

                        . '. Seleccionado: '

                        . $container
                        ->container_size

                        . '.',

                ]);
            }


            /*
             * Puede existir en la etapa hermana
             * del mismo servicio.
             */
            $containerBusyElsewhere =
                TripAssignment::query()

                ->where(
                    'container_id',
                    $container->id
                )

                ->whereNull(
                    'unassigned_at'
                )

                ->where(
                    'trip_id',
                    '!=',
                    $trip->id
                )

                ->whereHas(
                    'trip',

                    function ($query) use ($trip) {

                        $query
                            ->where(
                                function ($scope) use ($trip) {

                                    $scope
                                        ->where(
                                            'work_order_id',
                                            '!=',
                                            $trip->work_order_id
                                        )

                                        ->orWhere(
                                            'service_number',
                                            '!=',
                                            $trip->service_number
                                        )

                                        ->orWhereNull(
                                            'service_number'
                                        );
                                }
                            );
                    }
                )

                ->exists();


            if ($containerBusyElsewhere) {

                throw ValidationException::withMessages([

                    'container_id' =>
                    'El contenedor ya está asignado a otro servicio activo.',

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CHASIS
        |--------------------------------------------------------------------------
        */

        if ($chassis) {

            if (!$chassis->is_active) {

                throw ValidationException::withMessages([

                    'chassis_id' =>
                    'El chasis seleccionado está inactivo.',

                ]);
            }


            if (
                in_array(
                    $chassis
                        ->operational_status,

                    [
                        'MAINTENANCE',
                        'OUT_OF_SERVICE',
                    ]
                )
            ) {

                throw ValidationException::withMessages([

                    'chassis_id' =>
                    'El chasis no se encuentra disponible para operación.',

                ]);
            }


            $chassisBusy =
                TripAssignment::query()

                ->where(
                    'chassis_id',
                    $chassis->id
                )

                ->whereNull(
                    'unassigned_at'
                )

                ->where(
                    'trip_id',
                    '!=',
                    $trip->id
                )

                ->exists();


            if ($chassisBusy) {

                throw ValidationException::withMessages([

                    'chassis_id' =>
                    'El chasis ya tiene una asignación activa en otro viaje.',

                ]);
            }


            if (
                $chassis
                ->has_expired_document
            ) {

                $warnings[] =
                    'El chasis tiene uno o más documentos vencidos.';
            }


            if ($container) {

                if (
                    $container->container_size
                    === '20FT'

                    &&
                    !$chassis
                        ->supports_20ft
                ) {

                    throw ValidationException::withMessages([

                        'chassis_id' =>
                        'El chasis no es compatible con contenedores de 20 pies.',

                    ]);
                }


                if (
                    in_array(
                        $container
                            ->container_size,

                        [
                            '40FT',
                            '40HC',
                        ]
                    )

                    &&
                    !$chassis
                        ->supports_40ft
                ) {

                    throw ValidationException::withMessages([

                        'chassis_id' =>
                        'El chasis no es compatible con contenedores de 40 pies.',

                    ]);
                }


                if (
                    $container
                    ->container_type
                    === 'REEFER'

                    &&
                    !$chassis
                        ->supports_reefer
                ) {

                    throw ValidationException::withMessages([

                        'chassis_id' =>
                        'El chasis no está habilitado para contenedores refrigerados.',

                    ]);
                }
            }


            if (
                $estimatedWeight

                &&
                $chassis
                ->maximum_capacity_tons

                &&
                (
                    (float) $estimatedWeight
                    /
                    1000
                )

                >
                (float) $chassis
                    ->maximum_capacity_tons
            ) {

                throw ValidationException::withMessages([

                    'chassis_id' =>
                    'El chasis no tiene capacidad suficiente para el peso estimado de la OT.',

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | GUARDAR
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $trip,
                $validated
            ) {

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
                        $validated['assignment_reason']

                            ?: 'Reasignación de recursos.',

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
                    $validated['chassis_id'] ?? null,

                    'container_id' =>
                    $validated['container_id'] ?? null,

                    'assigned_at' =>
                    now(),

                    'assignment_reason' =>
                    $validated['assignment_reason'] ?? null,

                    'assigned_by' =>
                    Auth::id(),

                ]);


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
                    array_unique(
                        $warnings
                    )
                )
            );
        }


        return $response;
    }


    /*
    |--------------------------------------------------------------------------
    | CANCELAR VIAJE
    |--------------------------------------------------------------------------
    */

    public function updateStatus(
        Request $request,
        Trip $trip
    ): RedirectResponse {

        if (
            $trip->status
            === 'COMPLETED'
        ) {

            return back()
                ->withErrors([

                    'status' =>
                    'Un viaje completado no puede cancelarse desde esta pantalla.',

                ]);
        }


        if (
            $trip->status
            === 'CANCELLED'
        ) {

            return back()
                ->with(
                    'warning',

                    'El viaje ya se encuentra cancelado.'
                );
        }


        $validated =
            $request->validate([

                'status' => [
                    'required',
                    Rule::in([
                        'CANCELLED',
                    ]),
                ],

                'reason' => [
                    'required',
                    'string',
                    'max:2000',
                ],

            ], [

                'reason.required' =>
                'Debe indicar el motivo de la cancelación.',

            ]);


        DB::transaction(
            function () use (
                $trip,
                $validated
            ) {

                $this->changeStatus(

                    $trip,

                    'CANCELLED',

                    $validated['reason']
                );


                $activeAssignment =
                    $trip
                    ->assignments()

                    ->whereNull(
                        'unassigned_at'
                    )

                    ->first();


                if ($activeAssignment) {

                    $activeAssignment->update([

                        'unassigned_at' =>
                        now(),

                        'release_reason' =>
                        $validated['reason'],

                        'released_by' =>
                        Auth::id(),

                    ]);
                }
            }
        );


        return redirect()
            ->route(
                'trips.show',
                $trip
            )
            ->with(
                'success',
                'Viaje cancelado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR VIAJE DESDE OT
    |--------------------------------------------------------------------------
    */

    private function createTripFromWorkOrder(
        WorkOrder $workOrder,
        int $serviceNumber,
        string $stage,
        string $scheduledStart,
        ?string $scheduledEnd = null,
        ?string $notes = null
    ): Trip {

        $sequenceNumber =
            (
                $workOrder
                ->trips()
                ->max(
                    'sequence_number'
                )
                ?? 0
            ) + 1;


        $legacyServiceType =
            match ($stage) {

                'POSITIONING' =>
                'POSITIONING',

                'PICKUP' =>
                'PICKUP',

                default =>
                'TRANSPORT',
            };


        $trip =
            Trip::create([

                'work_order_id' =>
                $workOrder->id,

                'trip_number' =>
                $this
                    ->generateTripNumber(),

                'sequence_number' =>
                $sequenceNumber,

                'service_number' =>
                $serviceNumber,

                'service_stage' =>
                $stage,

                'client_id' =>
                $workOrder
                    ->client_id,

                'client_name_snapshot' =>
                $workOrder
                    ->client
                    ->business_name,

                'subclient_id' =>
                $workOrder
                    ->subclient_id,

                'subclient_name_snapshot' =>
                $workOrder
                    ->subclient
                    ?->business_name,

                'cargo_type_id' =>
                $workOrder
                    ->cargo_type_id,

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
                $legacyServiceType,

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
            'Viaje generado desde la Orden de Trabajo.',

            'changed_by' =>
            Auth::id(),

            'changed_at' =>
            now(),
        ]);


        return $trip;
    }


    /*
    |--------------------------------------------------------------------------
    | ETAPAS SEGÚN MODALIDAD
    |--------------------------------------------------------------------------
    */

    private function stagesForModality(
        string $modality
    ): array {

        return match ($modality) {

            'POSITIONING' => [
                'POSITIONING',
            ],

            'PICKUP' => [
                'PICKUP',
            ],

            'POSITIONING_PICKUP' => [
                'POSITIONING',
                'PICKUP',
            ],

            default => [
                'IMMEDIATE',
            ],
        };
    }


    private function defaultStage(
        string $modality
    ): string {

        return match ($modality) {

            'POSITIONING' =>
            'POSITIONING',

            'PICKUP' =>
            'PICKUP',

            default =>
            'IMMEDIATE',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | DEPENDENCIA POSICIÓN → RETIRO
    |--------------------------------------------------------------------------
    */

    private function findPositioningStage(
        Trip $trip
    ): ?Trip {

        if (
            $trip->service_stage
            !== 'PICKUP'

            ||
            !$trip->service_number
        ) {

            return null;
        }


        return Trip::query()

            ->where(
                'work_order_id',
                $trip->work_order_id
            )

            ->where(
                'service_number',
                $trip->service_number
            )

            ->where(
                'service_stage',
                'POSITIONING'
            )

            ->first();
    }


    private function isStageUnlocked(
        Trip $trip
    ): bool {

        /*
         * Solo RETIRO depende
         * de otra etapa.
         */
        if (
            $trip->service_stage
            !== 'PICKUP'
        ) {

            return true;
        }


        /*
         * Si la modalidad de la OT
         * es solamente RETIRO,
         * no debe bloquearse.
         */
        if (
            $trip
            ->workOrder
            ?->service_modality
            !== 'POSITIONING_PICKUP'
        ) {

            return true;
        }


        $positioning =
            $this
            ->findPositioningStage(
                $trip
            );


        if (!$positioning) {

            return false;
        }


        return $positioning->status
            === 'COMPLETED';
    }


    /*
    |--------------------------------------------------------------------------
    | PROGRAMACIÓN
    |--------------------------------------------------------------------------
    */

    private function buildScheduledStart(
        WorkOrder $workOrder
    ): string {

        $date =
            $workOrder
            ->requested_date
            ->format(
                'Y-m-d'
            );


        $time =
            $workOrder
            ->requested_time

            ?: '00:00';


        return $date
            . ' '
            . $time;
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADO
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | RESTRICCIONES CONDUCTOR
    |--------------------------------------------------------------------------
    */

    private function checkDriverRestrictions(
        Trip $trip,
        Driver $driver
    ): array {

        $date =
            $trip->scheduled_start_at

            ? $trip
            ->scheduled_start_at
            ->toDateString()

            : now()
            ->toDateString();


        $plantIds =
            array_values(
                array_filter([

                    $trip
                        ->origin_plant_id,

                    $trip
                        ->destination_plant_id,

                ])
            );


        $locationIds =
            array_values(
                array_filter([

                    $trip
                        ->origin_location_id,

                    $trip
                        ->destination_location_id,

                ])
            );


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

                    $query
                        ->whereNull(
                            'client_id'
                        )

                        ->orWhere(
                            'client_id',
                            $trip->client_id
                        );
                }
            )

            ->where(
                function ($query) use ($trip) {

                    $query
                        ->whereNull(
                            'subclient_id'
                        )

                        ->orWhere(
                            'subclient_id',
                            $trip->subclient_id
                        );
                }
            )

            ->where(
                function ($query) use ($trip) {

                    $query
                        ->whereNull(
                            'operation_type'
                        )

                        ->orWhere(
                            'operation_type',
                            $trip->operation_type
                        );
                }
            )

            ->where(
                function ($query) use ($plantIds) {

                    $query
                        ->whereNull(
                            'plant_id'
                        );


                    if (
                        !empty($plantIds)
                    ) {

                        $query
                            ->orWhereIn(
                                'plant_id',
                                $plantIds
                            );
                    }
                }
            )

            ->where(
                function ($query) use ($locationIds) {

                    $query
                        ->whereNull(
                            'location_id'
                        );


                    if (
                        !empty($locationIds)
                    ) {

                        $query
                            ->orWhereIn(
                                'location_id',
                                $locationIds
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

                . $restriction
                ->reason;


            if (
                $restriction
                ->action_type
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

            'blocks' =>
            $blocks,

            'warnings' =>
            $warnings,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NUMERACIÓN
    |--------------------------------------------------------------------------
    */

    private function generateTripNumber(): string
    {
        $year =
            now()
            ->format('Y');


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
}
