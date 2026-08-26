<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripCost;
use App\Models\TripStandbyCalculation;
use App\Models\TripTransfer;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REPORTE GENERAL
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        /*
        |--------------------------------------------------------------------------
        | FILTROS
        |--------------------------------------------------------------------------
        */

        $dateFrom =
            $request->get('date_from');

        $dateTo =
            $request->get('date_to');

        $status =
            $request->get('status');

        $operationType =
            $request->get('operation_type');

        $client =
            trim(
                (string) $request->get('client')
            );


        /*
        |--------------------------------------------------------------------------
        | CONSULTA BASE
        |--------------------------------------------------------------------------
        */

        $baseTripQuery =
            Trip::query()

            ->when(
                $dateFrom,
                function ($query) use ($dateFrom) {

                    $query->whereDate(
                        'scheduled_start_at',
                        '>=',
                        $dateFrom
                    );
                }
            )

            ->when(
                $dateTo,
                function ($query) use ($dateTo) {

                    $query->whereDate(
                        'scheduled_start_at',
                        '<=',
                        $dateTo
                    );
                }
            )

            ->when(
                $status,
                function ($query) use ($status) {

                    $query->where(
                        'status',
                        $status
                    );
                }
            )

            ->when(
                $operationType,
                function ($query) use ($operationType) {

                    $query->where(
                        'operation_type',
                        $operationType
                    );
                }
            )

            ->when(
                $client !== '',
                function ($query) use ($client) {

                    $query->where(
                        function ($scope) use ($client) {

                            $scope
                                ->where(
                                    'client_name_snapshot',
                                    'like',
                                    "%{$client}%"
                                )

                                ->orWhere(
                                    'subclient_name_snapshot',
                                    'like',
                                    "%{$client}%"
                                );
                        }
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | IDS FILTRADOS
        |--------------------------------------------------------------------------
        */

        $tripIds =
            (clone $baseTripQuery)
            ->pluck('id');


        /*
        |--------------------------------------------------------------------------
        | TABLA DE VIAJES
        |--------------------------------------------------------------------------
        */

        $trips =
            (clone $baseTripQuery)

            ->with([
                'workOrder',

                'activeAssignment.driver',

                'activeAssignment.vehicle',

                'assignments.driver',

                'assignments.vehicle',

                'standbyCalculation',

                'transfers',

                'costs',
            ])

            ->orderByDesc(
                'scheduled_start_at'
            )

            ->paginate(20)

            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | RESUMEN GENERAL
        |--------------------------------------------------------------------------
        */

        $summary = [

            'work_orders' =>
            WorkOrder::query()

                ->when(
                    $dateFrom,
                    function ($query) use ($dateFrom) {

                        $query->whereDate(
                            'requested_date',
                            '>=',
                            $dateFrom
                        );
                    }
                )

                ->when(
                    $dateTo,
                    function ($query) use ($dateTo) {

                        $query->whereDate(
                            'requested_date',
                            '<=',
                            $dateTo
                        );
                    }
                )

                ->count(),

            'trips' =>
            $tripIds->count(),

            'completed_trips' =>
            Trip::query()

                ->whereIn(
                    'id',
                    $tripIds
                )

                ->where(
                    'status',
                    'COMPLETED'
                )

                ->count(),

            'transfers' =>
            TripTransfer::query()

                ->whereIn(
                    'trip_id',
                    $tripIds
                )

                ->count(),

            'standby_hours' =>
            (float) TripStandbyCalculation::query()

                ->whereIn(
                    'trip_id',
                    $tripIds
                )

                ->where(
                    'status',
                    'CALCULATED'
                )

                ->sum(
                    'billable_hours'
                ),

            'cost_total' =>
            (float) TripCost::query()

                ->whereIn(
                    'trip_id',
                    $tripIds
                )

                ->where(
                    'status',
                    '!=',
                    'CANCELLED'
                )

                ->sum(
                    'subtotal'
                ),

            'approved_cost_total' =>
            (float) TripCost::query()

                ->whereIn(
                    'trip_id',
                    $tripIds
                )

                ->where(
                    'status',
                    'APPROVED'
                )

                ->sum(
                    'subtotal'
                ),
        ];


        /*
        |--------------------------------------------------------------------------
        | GRÁFICO 1 - VIAJES POR ESTADO
        |--------------------------------------------------------------------------
        */

        $statusData =
            Trip::query()

            ->whereIn(
                'id',
                $tripIds
            )

            ->select(
                'status',
                DB::raw('COUNT(*) as total')
            )

            ->groupBy(
                'status'
            )

            ->pluck(
                'total',
                'status'
            );


        $statusChart = [

            'labels' => [
                'Pendientes',
                'Asignados',
                'En tránsito',
                'En destino',
                'Completados',
                'Cancelados',
            ],

            'data' => [

                (int) ($statusData['PENDING'] ?? 0),

                (int) ($statusData['ASSIGNED'] ?? 0),

                (int) ($statusData['IN_TRANSIT'] ?? 0),

                (int) ($statusData['AT_DESTINATION'] ?? 0),

                (int) ($statusData['COMPLETED'] ?? 0),

                (int) ($statusData['CANCELLED'] ?? 0),
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | GRÁFICO 2 - VIAJES POR OPERACIÓN
        |--------------------------------------------------------------------------
        */

        $operationData =
            Trip::query()

            ->whereIn(
                'id',
                $tripIds
            )

            ->select(
                'operation_type',
                DB::raw('COUNT(*) as total')
            )

            ->groupBy(
                'operation_type'
            )

            ->pluck(
                'total',
                'operation_type'
            );


        $operationChart = [

            'labels' => [
                'Exportación',
                'Importación',
                'Transferencia',
                'Otro',
            ],

            'data' => [

                (int) ($operationData['EXPORT'] ?? 0),

                (int) ($operationData['IMPORT'] ?? 0),

                (int) ($operationData['TRANSFER'] ?? 0),

                (int) ($operationData['OTHER'] ?? 0),
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | GRÁFICO 3 - VIAJES POR CLIENTE
        |--------------------------------------------------------------------------
        */

        $clientData =
            Trip::query()

            ->whereIn(
                'id',
                $tripIds
            )

            ->select(
                'client_name_snapshot',
                DB::raw('COUNT(*) as total')
            )

            ->groupBy(
                'client_name_snapshot'
            )

            ->orderByDesc(
                'total'
            )

            ->limit(10)

            ->get();


        $clientChart = [

            'labels' =>
            $clientData
                ->pluck(
                    'client_name_snapshot'
                )
                ->values(),

            'data' =>
            $clientData
                ->pluck(
                    'total'
                )
                ->map(
                    fn($value) =>
                    (int) $value
                )
                ->values(),
        ];


        /*
        |--------------------------------------------------------------------------
        | GRÁFICO 4 - VIAJES POR DÍA
        |--------------------------------------------------------------------------
        */

        $dailyTrips =
            Trip::query()

            ->whereIn(
                'id',
                $tripIds
            )

            ->whereNotNull(
                'scheduled_start_at'
            )

            ->selectRaw(
                'DATE(scheduled_start_at) as trip_date, COUNT(*) as total'
            )

            ->groupBy(
                'trip_date'
            )

            ->orderBy(
                'trip_date'
            )

            ->get();


        $dailyChart = [

            'labels' =>
            $dailyTrips
                ->pluck(
                    'trip_date'
                )
                ->map(
                    function ($date) {

                        return \Carbon\Carbon::parse(
                            $date
                        )
                            ->format(
                                'd/m/Y'
                            );
                    }
                )
                ->values(),

            'data' =>
            $dailyTrips
                ->pluck(
                    'total'
                )
                ->map(
                    fn($value) =>
                    (int) $value
                )
                ->values(),
        ];


        /*
        |--------------------------------------------------------------------------
        | GRÁFICO 5 - COSTOS POR TIPO
        |--------------------------------------------------------------------------
        */

        $costTypeData =
            TripCost::query()

            ->whereIn(
                'trip_id',
                $tripIds
            )

            ->where(
                'status',
                '!=',
                'CANCELLED'
            )

            ->select(
                'cost_type',
                DB::raw('SUM(subtotal) as total')
            )

            ->groupBy(
                'cost_type'
            )

            ->pluck(
                'total',
                'cost_type'
            );


        $costChart = [

            'labels' => [
                'Tarifa base',
                'Stand-by',
                'Transferencias',
                'Adicionales',
            ],

            'data' => [

                (float) ($costTypeData['BASE'] ?? 0),

                (float) ($costTypeData['STANDBY'] ?? 0),

                (float) ($costTypeData['TRANSFER'] ?? 0),

                (float) ($costTypeData['ADDITIONAL'] ?? 0),
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | GRÁFICO 6 - STAND-BY POR VIAJE
        |--------------------------------------------------------------------------
        */

        $standbyData =
            TripStandbyCalculation::query()

            ->with(
                'trip'
            )

            ->whereIn(
                'trip_id',
                $tripIds
            )

            ->where(
                'status',
                'CALCULATED'
            )

            ->where(
                'billable_hours',
                '>',
                0
            )

            ->orderByDesc(
                'billable_hours'
            )

            ->limit(10)

            ->get();


        $standbyChart = [

            'labels' =>
            $standbyData
                ->map(
                    fn($item) =>
                    $item
                        ->trip
                        ?->trip_number
                        ?? 'Viaje'
                )
                ->values(),

            'data' =>
            $standbyData
                ->map(
                    fn($item) =>
                    (float) $item->billable_hours
                )
                ->values(),
        ];


        return view(
            'reports.index',
            compact(
                'trips',
                'summary',

                'dateFrom',
                'dateTo',
                'status',
                'operationType',
                'client',

                'statusChart',
                'operationChart',
                'clientChart',
                'dailyChart',
                'costChart',
                'standbyChart'
            )
        );
    }
}
