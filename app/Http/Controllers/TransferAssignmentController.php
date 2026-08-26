<?php

namespace App\Http\Controllers;

use App\Models\Chassis;
use App\Models\Container;
use App\Models\Driver;
use App\Models\DriverRestriction;
use App\Models\TransferAssignment;
use App\Models\TransferStatusHistory;
use App\Models\TripAssignment;
use App\Models\TripTransfer;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransferAssignmentController extends Controller
{
    public function store(
        Request $request,
        TripTransfer $transfer
    ): RedirectResponse {

        if (
            in_array(
                $transfer->status,
                [
                    'COMPLETED',
                    'CANCELLED',
                ],
                true
            )
        ) {

            return back()
                ->withErrors([

                    'assignment' =>
                    'No se pueden asignar recursos a una transferencia completada o cancelada.',

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


        $transfer->loadMissing(
            'trip.workOrder'
        );


        $trip =
            $transfer->trip;


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


        $driverBusyInTransfers =
            TransferAssignment::query()

            ->where(
                'driver_id',
                $driver->id
            )

            ->whereNull(
                'unassigned_at'
            )

            ->where(
                'trip_transfer_id',
                '!=',
                $transfer->id
            )

            ->exists();


        $driverBusyInTrips =
            TripAssignment::query()

            ->where(
                'driver_id',
                $driver->id
            )

            ->whereNull(
                'unassigned_at'
            )

            ->exists();


        /*
         * Si es el MISMO conductor que tiene
         * el viaje padre activo, sí permitimos
         * utilizarlo en su propia transferencia.
         */
        $parentDriverId =
            $trip
            ->activeAssignment
            ?->driver_id;


        if (
            $driverBusyInTransfers
        ) {

            throw ValidationException::withMessages([

                'driver_id' =>
                'El conductor ya tiene otra transferencia activa.',

            ]);
        }


        if (
            $driverBusyInTrips
            &&
            (int) $parentDriverId
            !== (int) $driver->id
        ) {

            throw ValidationException::withMessages([

                'driver_id' =>
                'El conductor ya está asignado a otro viaje activo.',

            ]);
        }


        /*
         * Restricciones.
         */
        $restrictionResult =
            $this
            ->checkDriverRestrictions(
                $transfer,
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
                ],
                true
            )
        ) {

            throw ValidationException::withMessages([

                'vehicle_id' =>
                'El vehículo no está disponible para operación.',

            ]);
        }


        if (
            $vehicle
            ->has_expired_document
        ) {

            $warnings[] =
                'El vehículo tiene uno o más documentos vencidos.';
        }


        $estimatedWeight =
            $trip
            ->workOrder
            ?->estimated_weight_kg;


        if (
            $estimatedWeight
            &&
            $vehicle->max_load_capacity_kg
            &&
            (float) $estimatedWeight
            >
            (float) $vehicle->max_load_capacity_kg
        ) {

            throw ValidationException::withMessages([

                'vehicle_id' =>
                'El vehículo no tiene capacidad suficiente para el peso estimado de la OT.',

            ]);
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
                    $chassis->operational_status,
                    [
                        'MAINTENANCE',
                        'OUT_OF_SERVICE',
                    ],
                    true
                )
            ) {

                throw ValidationException::withMessages([

                    'chassis_id' =>
                    'El chasis no está disponible para operación.',

                ]);
            }


            if (
                $chassis
                ->has_expired_document
            ) {

                $warnings[] =
                    'El chasis tiene uno o más documentos vencidos.';
            }
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
                $chassis
                &&
                $container->container_size === '20FT'
                &&
                !$chassis->supports_20ft
            ) {

                throw ValidationException::withMessages([

                    'chassis_id' =>
                    'El chasis no es compatible con un contenedor de 20 pies.',

                ]);
            }


            if (
                $chassis
                &&
                in_array(
                    $container->container_size,
                    [
                        '40FT',
                        '40HC',
                    ],
                    true
                )
                &&
                !$chassis->supports_40ft
            ) {

                throw ValidationException::withMessages([

                    'chassis_id' =>
                    'El chasis no es compatible con un contenedor de 40 pies.',

                ]);
            }


            if (
                $chassis
                &&
                $container->container_type === 'REEFER'
                &&
                !$chassis->supports_reefer
            ) {

                throw ValidationException::withMessages([

                    'chassis_id' =>
                    'El chasis no está habilitado para contenedores refrigerados.',

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
                $transfer,
                $validated
            ) {

                $current =
                    $transfer
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


                TransferAssignment::create([

                    'trip_transfer_id' =>
                    $transfer->id,

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
                    $transfer->status
                    === 'PENDING'
                ) {

                    $this->changeStatus(

                        $transfer,

                        'ASSIGNED',

                        'Asignación inicial de recursos.'
                    );
                }
            }
        );


        $response =
            redirect()

            ->route(
                'transfers.show',
                $transfer
            )

            ->with(
                'success',
                'Recursos asignados correctamente a la transferencia.'
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


    private function changeStatus(
        TripTransfer $transfer,
        string $newStatus,
        ?string $reason = null
    ): void {

        $oldStatus =
            $transfer->status;


        $transfer->update([

            'status' =>
            $newStatus,

            'updated_by' =>
            Auth::id(),
        ]);


        TransferStatusHistory::create([

            'trip_transfer_id' =>
            $transfer->id,

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
        TripTransfer $transfer,
        Driver $driver
    ): array {

        $trip =
            $transfer->trip;


        $date =
            $transfer->scheduled_at

            ? $transfer
            ->scheduled_at
            ->toDateString()

            : now()
            ->toDateString();


        $plantIds =
            array_values(
                array_filter([

                    $transfer
                        ->origin_plant_id,

                    $transfer
                        ->destination_plant_id,
                ])
            );


        $locationIds =
            array_values(
                array_filter([

                    $transfer
                        ->origin_location_id,

                    $transfer
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


                    if (!empty($plantIds)) {

                        $query->orWhereIn(
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


                    if (!empty($locationIds)) {

                        $query->orWhereIn(
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

            'blocks' =>
            $blocks,

            'warnings' =>
            $warnings,
        ];
    }
}
