<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\TripStandbyCalculation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StandbyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO GENERAL
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request
    ): View {

        $search =
            trim(
                (string)
                $request->get('search')
            );


        $status =
            $request->get('status');


        $clientId =
            $request->get('client_id');


        $dateFrom =
            $request->get('date_from');


        $dateTo =
            $request->get('date_to');


        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */

        $calculations =
            TripStandbyCalculation::query()

            ->with([

                'trip.workOrder',

                'trip.activeAssignment.driver',

                'trip.activeAssignment.vehicle',

                'trip.assignments.driver',

                'trip.assignments.vehicle',

            ])


            /*
                 * BUSCADOR GENERAL
                 */
            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->whereHas(
                        'trip',

                        function ($tripQuery) use ($search) {

                            $tripQuery
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
            )


            /*
                 * ESTADO CÁLCULO
                 */
            ->when(
                $status,
                fn($query) =>
                $query->where(
                    'status',
                    $status
                )
            )


            /*
                 * CLIENTE
                 */
            ->when(
                $clientId,

                function ($query) use ($clientId) {

                    $query->whereHas(
                        'trip',

                        fn($tripQuery) =>
                        $tripQuery->where(
                            'client_id',
                            $clientId
                        )
                    );
                }
            )


            /*
                 * FECHA DESDE
                 */
            ->when(
                $dateFrom,

                function ($query) use ($dateFrom) {

                    $query->whereHas(
                        'trip',

                        fn($tripQuery) =>
                        $tripQuery->whereDate(
                            'scheduled_start_at',
                            '>=',
                            $dateFrom
                        )
                    );
                }
            )


            /*
                 * FECHA HASTA
                 */
            ->when(
                $dateTo,

                function ($query) use ($dateTo) {

                    $query->whereHas(
                        'trip',

                        fn($tripQuery) =>
                        $tripQuery->whereDate(
                            'scheduled_start_at',
                            '<=',
                            $dateTo
                        )
                    );
                }
            )


            ->orderByDesc(
                'updated_at'
            )

            ->paginate(20)

            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | CLIENTES
        |--------------------------------------------------------------------------
        */

        $clients =
            Client::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'business_name'
            )

            ->get();


        /*
        |--------------------------------------------------------------------------
        | RESUMEN
        |--------------------------------------------------------------------------
        */

        $summaryQuery =
            TripStandbyCalculation::query();


        $totalCalculations =
            (clone $summaryQuery)
            ->count();


        $pendingCalculations =
            (clone $summaryQuery)

            ->where(
                'status',
                'PENDING'
            )

            ->count();


        $calculatedCalculations =
            (clone $summaryQuery)

            ->where(
                'status',
                'CALCULATED'
            )

            ->count();


        $totalBillableHours =
            (int) (
                (clone $summaryQuery)

                ->where(
                    'status',
                    'CALCULATED'
                )

                ->sum(
                    'billable_hours'
                )
            );


        return view(
            'standby.index',

            compact(

                'calculations',

                'clients',

                'search',

                'status',

                'clientId',

                'dateFrom',

                'dateTo',

                'totalCalculations',

                'pendingCalculations',

                'calculatedCalculations',

                'totalBillableHours'
            )
        );
    }
}
