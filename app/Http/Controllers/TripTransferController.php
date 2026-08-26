<?php

namespace App\Http\Controllers;

use App\Models\Chassis;
use App\Models\Container;
use App\Models\Driver;
use App\Models\Location;
use App\Models\Plant;
use App\Models\TransferStatusHistory;
use App\Models\Trip;
use App\Models\TripTransfer;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TripTransferController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): View {

        $search =
            trim(
                (string)
                $request->get(
                    'search'
                )
            );

        $status =
            $request->get(
                'status'
            );

        $transfers =
            TripTransfer::query()

            ->with([
                'trip.workOrder',

                'activeAssignment.driver',
                'activeAssignment.vehicle',
                'activeAssignment.chassis',
                'activeAssignment.container',

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
                                    'transfer_number',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'origin_name_snapshot',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'destination_name_snapshot',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhereHas(
                                    'trip',
                                    function ($tripQuery) use ($search) {

                                        $tripQuery
                                            ->where(
                                                'trip_number',
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
                                            )

                                            ->orWhere(
                                                'booking_number',
                                                'like',
                                                "%{$search}%"
                                            )

                                            ->orWhereHas(
                                                'workOrder',
                                                function ($workOrderQuery) use ($search) {

                                                    $workOrderQuery
                                                        ->where(
                                                            'work_order_number',
                                                            'like',
                                                            "%{$search}%"
                                                        );
                                                }
                                            );
                                    }
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
                'created_at'
            )

            ->paginate(15)

            ->withQueryString();

        return view(
            'transfers.index',
            compact(
                'transfers',
                'search',
                'status'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

    public function create(
        Trip $trip
    ): View {

        if (
            in_array(
                $trip->status,
                [
                    'COMPLETED',
                    'CANCELLED',
                ]
            )
        ) {

            abort(
                403,
                'No se puede registrar una transferencia en un viaje completado o cancelado.'
            );
        }

        $trip->load([
            'workOrder',
        ]);

        $locations =
            Location::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'name'
            )

            ->get();

        $plants =
            Plant::query()

            ->where(
                'is_active',
                true
            )

            ->where(
                'client_id',
                $trip->client_id
            )

            ->orderBy(
                'name'
            )

            ->get();

        return view(
            'transfers.create',
            compact(
                'trip',
                'locations',
                'plants'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | GUARDAR
    |--------------------------------------------------------------------------
    */

    public function store(
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

                    'transfer' =>
                    'No se puede registrar una transferencia en un viaje completado o cancelado.',

                ]);
        }

        $validated =
            $request->validate([

                'origin_type' => [
                    'required',
                    Rule::in([
                        'LOCATION',
                        'PLANT',
                    ]),
                ],

                'origin_location_id' => [
                    'nullable',
                    'exists:locations,id',
                ],

                'origin_plant_id' => [
                    'nullable',
                    'exists:plants,id',
                ],

                'destination_type' => [
                    'required',
                    Rule::in([
                        'LOCATION',
                        'PLANT',
                    ]),
                ],

                'destination_location_id' => [
                    'nullable',
                    'exists:locations,id',
                ],

                'destination_plant_id' => [
                    'nullable',
                    'exists:plants,id',
                ],

                'scheduled_at' => [
                    'nullable',
                    'date',
                ],

                'reason' => [
                    'required',
                    'string',
                    'max:2000',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:3000',
                ],
            ], [

                'reason.required' =>
                'El motivo de la transferencia es obligatorio.',

            ]);

        $validated =
            $this->resolveRoute(
                $trip,
                $validated
            );

        /*
        |--------------------------------------------------------------------------
        | MISMO ORIGEN / DESTINO
        |--------------------------------------------------------------------------
        */

        if (
            $validated['origin_type']
            === $validated['destination_type']
        ) {

            if (
                $validated['origin_type']
                === 'PLANT'

                &&
                $validated['origin_plant_id']
                === $validated['destination_plant_id']
            ) {

                throw ValidationException::withMessages([

                    'destination_plant_id' =>
                    'La planta de origen y destino no pueden ser la misma.',

                ]);
            }

            if (
                $validated['origin_type']
                === 'LOCATION'

                &&
                $validated['origin_location_id']
                === $validated['destination_location_id']
            ) {

                throw ValidationException::withMessages([

                    'destination_location_id' =>
                    'La ubicación de origen y destino no puede ser la misma.',

                ]);
            }
        }

        $validated['trip_id'] =
            $trip->id;

        $validated['transfer_number'] =
            $this
            ->generateTransferNumber();

        $validated['status'] =
            'PENDING';

        $validated['created_by'] =
            Auth::id();

        $validated['updated_by'] =
            Auth::id();

        $transfer =
            DB::transaction(
                function () use (
                    $validated,
                    $trip
                ) {

                    $transfer =
                        TripTransfer::create(
                            $validated
                        );

                    TransferStatusHistory::create([

                        'trip_transfer_id' =>
                        $transfer->id,

                        'previous_status' =>
                        null,

                        'new_status' =>
                        'PENDING',

                        'reason' =>
                        'Transferencia registrada desde el viaje '
                            . $trip->trip_number
                            . '.',

                        'changed_by' =>
                        Auth::id(),

                        'changed_at' =>
                        now(),
                    ]);

                    return $transfer;
                }
            );

        return redirect()

            ->route(
                'transfers.show',
                $transfer
            )

            ->with(
                'success',
                'Transferencia registrada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */

    public function show(
        TripTransfer $transfer
    ): View {

        $transfer->load([

            'trip.workOrder',
            'trip.activeAssignment.driver',
            'trip.activeAssignment.vehicle',
            'trip.activeAssignment.chassis',
            'trip.activeAssignment.container',

            'originLocation',
            'originPlant',

            'destinationLocation',
            'destinationPlant',

            'activeAssignment.driver',
            'activeAssignment.vehicle',
            'activeAssignment.chassis',
            'activeAssignment.container',
            'activeAssignment.assignedBy',

            'assignments.driver',
            'assignments.vehicle',
            'assignments.chassis',
            'assignments.container',
            'assignments.assignedBy',
            'assignments.releasedBy',

            'events.location',
            'events.plant',
            'events.creator',

            'statusHistory.user',
        ]);

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
            Location::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'name'
            )

            ->get();

        $plants =
            Plant::query()

            ->where(
                'is_active',
                true
            )

            ->where(
                'client_id',
                $transfer
                    ->trip
                    ->client_id
            )

            ->orderBy(
                'name'
            )

            ->get();

        /*
         * Recursos sugeridos:
         * los del viaje principal.
         */
        $parentAssignment =
            $transfer
            ->trip
            ->activeAssignment;

        $availableEvents =
            $transfer
            ->availableEventOptions();

        $displayAssignment =
            $transfer->activeAssignment
            ??
            $transfer
            ->assignments
            ->sortByDesc(
                'assigned_at'
            )
            ->first();

        return view(
            'transfers.show',

            compact(
                'transfer',
                'drivers',
                'vehicles',
                'chassisList',
                'containers',
                'locations',
                'plants',
                'parentAssignment',
                'availableEvents',
                'displayAssignment'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CANCELAR
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        TripTransfer $transfer
    ): RedirectResponse {

        if (
            $transfer->status
            === 'COMPLETED'
        ) {

            return back()
                ->withErrors([

                    'transfer' =>
                    'Una transferencia completada no puede cancelarse.',

                ]);
        }

        if (
            $transfer->status
            === 'CANCELLED'
        ) {

            return back()
                ->with(
                    'warning',
                    'La transferencia ya está cancelada.'
                );
        }

        $validated =
            $request->validate([

                'reason' => [
                    'required',
                    'string',
                    'max:2000',
                ],
            ], [

                'reason.required' =>
                'Debe indicar el motivo de cancelación.',

            ]);

        DB::transaction(
            function () use (
                $transfer,
                $validated
            ) {

                $oldStatus =
                    $transfer->status;

                $transfer->update([

                    'status' =>
                    'CANCELLED',

                    'notes' =>
                    trim(
                        (
                            $transfer->notes

                            ? $transfer->notes
                            . PHP_EOL
                            . PHP_EOL

                            : ''
                        )

                            . 'Cancelación: '

                            . $validated['reason']
                    ),

                    'updated_by' =>
                    Auth::id(),
                ]);

                TransferStatusHistory::create([

                    'trip_transfer_id' =>
                    $transfer->id,

                    'previous_status' =>
                    $oldStatus,

                    'new_status' =>
                    'CANCELLED',

                    'reason' =>
                    $validated['reason'],

                    'changed_by' =>
                    Auth::id(),

                    'changed_at' =>
                    now(),
                ]);

                $assignment =
                    $transfer
                    ->assignments()

                    ->whereNull(
                        'unassigned_at'
                    )

                    ->first();

                if ($assignment) {

                    $assignment->update([

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
                'transfers.show',
                $transfer
            )

            ->with(
                'success',
                'Transferencia cancelada correctamente.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVER RUTA
    |--------------------------------------------------------------------------
    */

    private function resolveRoute(
        Trip $trip,
        array $validated
    ): array {

        /*
        |--------------------------------------------------------------------------
        | ORIGEN
        |--------------------------------------------------------------------------
        */

        if (
            $validated['origin_type']
            === 'PLANT'
        ) {

            if (
                empty($validated['origin_plant_id'])
            ) {

                throw ValidationException::withMessages([

                    'origin_plant_id' =>
                    'Seleccione la planta de origen.',

                ]);
            }

            $plant =
                Plant::findOrFail(
                    $validated['origin_plant_id']
                );

            if (
                (int) $plant->client_id
                !== (int) $trip->client_id
            ) {

                throw ValidationException::withMessages([

                    'origin_plant_id' =>
                    'La planta de origen no pertenece al cliente.',

                ]);
            }

            $validated['origin_location_id'] =
                null;

            $validated['origin_name_snapshot'] =
                $plant->name;
        } else {

            if (
                empty($validated['origin_location_id'])
            ) {

                throw ValidationException::withMessages([

                    'origin_location_id' =>
                    'Seleccione la ubicación de origen.',

                ]);
            }

            $location =
                Location::findOrFail(
                    $validated['origin_location_id']
                );

            $validated['origin_plant_id'] =
                null;

            $validated['origin_name_snapshot'] =
                $location->name;
        }

        /*
        |--------------------------------------------------------------------------
        | DESTINO
        |--------------------------------------------------------------------------
        */

        if (
            $validated['destination_type']
            === 'PLANT'
        ) {

            if (
                empty($validated['destination_plant_id'])
            ) {

                throw ValidationException::withMessages([

                    'destination_plant_id' =>
                    'Seleccione la planta de destino.',

                ]);
            }

            $plant =
                Plant::findOrFail(
                    $validated['destination_plant_id']
                );

            if (
                (int) $plant->client_id
                !== (int) $trip->client_id
            ) {

                throw ValidationException::withMessages([

                    'destination_plant_id' =>
                    'La planta de destino no pertenece al cliente.',

                ]);
            }

            $validated['destination_location_id'] =
                null;

            $validated['destination_name_snapshot'] =
                $plant->name;
        } else {

            if (
                empty($validated['destination_location_id'])
            ) {

                throw ValidationException::withMessages([

                    'destination_location_id' =>
                    'Seleccione la ubicación de destino.',

                ]);
            }

            $location =
                Location::findOrFail(
                    $validated['destination_location_id']
                );

            $validated['destination_plant_id'] =
                null;

            $validated['destination_name_snapshot'] =
                $location->name;
        }

        return $validated;
    }

    /*
    |--------------------------------------------------------------------------
    | NUMERACIÓN
    |--------------------------------------------------------------------------
    */

    private function generateTransferNumber(): string
    {
        $year =
            now()
            ->format('Y');

        $lastId =
            TripTransfer::withTrashed()
            ->max('id')
            ?? 0;

        return 'TRA-'
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
