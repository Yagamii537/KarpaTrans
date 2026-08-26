<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripCost;
use App\Models\TripTransfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TripCostController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO GENERAL
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $search = trim(
            (string) $request->get('search')
        );

        $type = $request->get('type');

        $status = $request->get('status');


        /*
        |--------------------------------------------------------------------------
        | COSTOS REGISTRADOS
        |--------------------------------------------------------------------------
        */

        $costs = TripCost::query()

            ->with([
                'trip.workOrder',
                'transfer',
                'creator',
            ])

            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        function ($scope) use ($search) {

                            $scope
                                ->where(
                                    'description',
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
                                )

                                ->orWhereHas(
                                    'transfer',
                                    function ($transferQuery) use ($search) {

                                        $transferQuery
                                            ->where(
                                                'transfer_number',
                                                'like',
                                                "%{$search}%"
                                            );
                                    }
                                );
                        }
                    );
                }
            )

            ->when(
                $type,
                fn($query) =>
                $query->where(
                    'cost_type',
                    $type
                )
            )

            ->when(
                $status,
                fn($query) =>
                $query->where(
                    'status',
                    $status
                )
            )

            ->orderByDesc('created_at')

            ->paginate(20)

            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | RESUMEN ECONÓMICO
        |--------------------------------------------------------------------------
        */

        $summary = [

            'pending' =>
            (float) TripCost::query()

                ->where(
                    'status',
                    'PENDING'
                )

                ->sum(
                    'subtotal'
                ),

            'approved' =>
            (float) TripCost::query()

                ->where(
                    'status',
                    'APPROVED'
                )

                ->sum(
                    'subtotal'
                ),

            'total' =>
            (float) TripCost::query()

                ->where(
                    'status',
                    '!=',
                    'CANCELLED'
                )

                ->sum(
                    'subtotal'
                ),
        ];


        /*
        |--------------------------------------------------------------------------
        | VIAJES SIN COSTOS
        |--------------------------------------------------------------------------
        |
        | Mostramos viajes que todavía no poseen
        | ningún costo vigente.
        |
        | Un costo CANCELLED no cuenta como
        | costo vigente.
        |
        */

        $tripsWithoutCosts = Trip::query()

            ->with([
                'workOrder',
                'activeAssignment.driver',
                'activeAssignment.vehicle',
                'standbyCalculation',
                'transfers',
            ])

            ->whereDoesntHave(
                'costs',
                function ($query) {

                    $query->where(
                        'status',
                        '!=',
                        'CANCELLED'
                    );
                }
            )

            ->orderByDesc(
                'scheduled_start_at'
            )

            ->limit(10)

            ->get();


        /*
        |--------------------------------------------------------------------------
        | VIAJES CON COSTOS PENDIENTES
        |--------------------------------------------------------------------------
        */

        $tripsWithPendingCosts = Trip::query()

            ->with([
                'workOrder',
            ])

            ->whereHas(
                'costs',
                function ($query) {

                    $query->where(
                        'status',
                        'PENDING'
                    );
                }
            )

            ->withSum(
                [
                    'costs as pending_cost_total' =>
                    function ($query) {

                        $query->where(
                            'status',
                            'PENDING'
                        );
                    }
                ],
                'subtotal'
            )

            ->orderByDesc(
                'scheduled_start_at'
            )

            ->limit(10)

            ->get();


        return view(
            'costs.index',

            compact(
                'costs',
                'search',
                'type',
                'status',
                'summary',
                'tripsWithoutCosts',
                'tripsWithPendingCosts'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | COSTOS DE UN VIAJE
    |--------------------------------------------------------------------------
    */

    public function trip(
        Trip $trip
    ): View {

        $trip->load([

            'workOrder',

            'standbyCalculation',

            'transfers',

            'costs.transfer',

            'costs.creator',
        ]);


        $totalPending =
            (float) $trip
                ->costs
                ->where(
                    'status',
                    'PENDING'
                )
                ->sum(
                    'subtotal'
                );


        $totalApproved =
            (float) $trip
                ->costs
                ->where(
                    'status',
                    'APPROVED'
                )
                ->sum(
                    'subtotal'
                );


        $total =
            (float) $trip
                ->costs
                ->where(
                    'status',
                    '!=',
                    'CANCELLED'
                )
                ->sum(
                    'subtotal'
                );


        return view(
            'costs.trip',

            compact(
                'trip',
                'totalPending',
                'totalApproved',
                'total'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | GUARDAR COSTO
    |--------------------------------------------------------------------------
    */

    public function store(
        Request $request,
        Trip $trip
    ): RedirectResponse {

        $validated =
            $request->validate([

                'cost_type' => [
                    'required',

                    Rule::in([
                        'BASE',
                        'STANDBY',
                        'TRANSFER',
                        'ADDITIONAL',
                    ]),
                ],

                'trip_transfer_id' => [
                    'nullable',
                    'exists:trip_transfers,id',
                ],

                'description' => [
                    'required',
                    'string',
                    'max:500',
                ],

                'quantity' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'unit_price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:3000',
                ],
            ]);


        /*
        |--------------------------------------------------------------------------
        | VALIDAR TRANSFERENCIA
        |--------------------------------------------------------------------------
        */

        $transfer = null;


        if (
            !empty($validated['trip_transfer_id'])
        ) {

            $transfer =
                TripTransfer::findOrFail(
                    $validated['trip_transfer_id']
                );


            if (
                (int) $transfer->trip_id
                !==
                (int) $trip->id
            ) {

                throw ValidationException::withMessages([

                    'trip_transfer_id' =>
                    'La transferencia seleccionada no pertenece a este viaje.',

                ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | COSTO DE TRANSFERENCIA
        |--------------------------------------------------------------------------
        */

        if (
            $validated['cost_type'] === 'TRANSFER'

            &&
            !$transfer
        ) {

            throw ValidationException::withMessages([

                'trip_transfer_id' =>
                'Para un costo de transferencia debe seleccionar una transferencia.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | SUBTOTAL
        |--------------------------------------------------------------------------
        */

        $quantity =
            (float) $validated['quantity'];


        $unitPrice =
            (float) $validated['unit_price'];


        $subtotal =
            round(
                $quantity
                    *
                    $unitPrice,
                2
            );


        DB::transaction(
            function () use (
                $trip,
                $validated,
                $quantity,
                $unitPrice,
                $subtotal
            ) {

                TripCost::create([

                    'trip_id' =>
                    $trip->id,

                    'trip_transfer_id' =>
                    $validated['trip_transfer_id'] ?? null,

                    'cost_type' =>
                    $validated['cost_type'],

                    'description' =>
                    $validated['description'],

                    'quantity' =>
                    $quantity,

                    'unit_price' =>
                    $unitPrice,

                    'subtotal' =>
                    $subtotal,

                    'source_type' =>
                    'MANUAL',

                    'source_id' =>
                    null,

                    'status' =>
                    'PENDING',

                    'notes' =>
                    $validated['notes'] ?? null,

                    'created_by' =>
                    Auth::id(),

                    'updated_by' =>
                    Auth::id(),
                ]);
            }
        );


        return redirect()

            ->route(
                'costs.trip',
                $trip
            )

            ->with(
                'success',
                'Costo registrado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR COSTO DESDE STAND-BY
    |--------------------------------------------------------------------------
    */

    public function createStandby(
        Request $request,
        Trip $trip
    ): RedirectResponse {

        $trip->load(
            'standbyCalculation'
        );


        $standby =
            $trip
            ->standbyCalculation;


        if (
            !$standby

            ||

            $standby->status
            !== 'CALCULATED'
        ) {

            return back()
                ->withErrors([

                    'standby' =>
                    'El viaje todavía no tiene un cálculo de Stand-by finalizado.',

                ]);
        }


        /*
         * Evitar duplicar el mismo
         * cálculo de Stand-by.
         */
        $alreadyExists =
            TripCost::query()

            ->where(
                'trip_id',
                $trip->id
            )

            ->where(
                'source_type',
                'STANDBY'
            )

            ->where(
                'source_id',
                $standby->id
            )

            ->where(
                'status',
                '!=',
                'CANCELLED'
            )

            ->exists();


        if ($alreadyExists) {

            return back()
                ->withErrors([

                    'standby' =>
                    'Este cálculo de Stand-by ya fue agregado a los costos.',

                ]);
        }


        $validated =
            $request->validate([

                'standby_unit_price' => [
                    'required',
                    'numeric',
                    'min:0',
                ],
            ]);


        $quantity =
            (float) $standby
                ->billable_hours;


        $unitPrice =
            (float) $validated['standby_unit_price'];


        $subtotal =
            round(
                $quantity
                    *
                    $unitPrice,
                2
            );


        DB::transaction(
            function () use (
                $trip,
                $standby,
                $quantity,
                $unitPrice,
                $subtotal
            ) {

                TripCost::create([

                    'trip_id' =>
                    $trip->id,

                    'trip_transfer_id' =>
                    null,

                    'cost_type' =>
                    'STANDBY',

                    'description' =>
                    'Stand-by del viaje '
                        . $trip->trip_number,

                    'quantity' =>
                    $quantity,

                    'unit_price' =>
                    $unitPrice,

                    'subtotal' =>
                    $subtotal,

                    'source_type' =>
                    'STANDBY',

                    'source_id' =>
                    $standby->id,

                    'status' =>
                    'PENDING',

                    'notes' =>
                    'Generado a partir del cálculo automático de Stand-by.',

                    'created_by' =>
                    Auth::id(),

                    'updated_by' =>
                    Auth::id(),
                ]);
            }
        );


        return redirect()

            ->route(
                'costs.trip',
                $trip
            )

            ->with(
                'success',
                'Stand-by agregado a los costos correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | APROBAR COSTO
    |--------------------------------------------------------------------------
    */

    public function approve(
        TripCost $cost
    ): RedirectResponse {

        if (
            $cost->status
            === 'CANCELLED'
        ) {

            return back()
                ->withErrors([

                    'cost' =>
                    'Un costo cancelado no puede aprobarse.',

                ]);
        }


        if (
            $cost->status
            === 'APPROVED'
        ) {

            return back()
                ->with(
                    'warning',
                    'El costo ya se encuentra aprobado.'
                );
        }


        $cost->update([

            'status' =>
            'APPROVED',

            'updated_by' =>
            Auth::id(),
        ]);


        return back()
            ->with(
                'success',
                'Costo aprobado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CANCELAR COSTO
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        TripCost $cost
    ): RedirectResponse {

        if (
            $cost->status
            === 'CANCELLED'
        ) {

            return back()
                ->with(
                    'warning',
                    'El costo ya está cancelado.'
                );
        }


        $validated =
            $request->validate([

                'reason' => [
                    'required',
                    'string',
                    'max:2000',
                ],
            ]);


        DB::transaction(
            function () use (
                $cost,
                $validated
            ) {

                $notes =
                    trim(
                        (
                            $cost->notes

                            ? $cost->notes
                            . PHP_EOL
                            . PHP_EOL

                            : ''
                        )

                            . 'Cancelación: '

                            . $validated['reason']
                    );


                $cost->update([

                    'status' =>
                    'CANCELLED',

                    'notes' =>
                    $notes,

                    'updated_by' =>
                    Auth::id(),
                ]);
            }
        );


        return back()
            ->with(
                'success',
                'Costo cancelado correctamente.'
            );
    }
}
