<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripStandbyCalculation;
use App\Models\TripStatusHistory;
use App\Models\TripTransfer;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DASHBOARD PRINCIPAL
    |--------------------------------------------------------------------------
    */

    public function index(): View
    {
        /*
        |--------------------------------------------------------------------------
        | INDICADORES PRINCIPALES
        |--------------------------------------------------------------------------
        */

        $activeWorkOrders =
            WorkOrder::query()

            ->whereNotIn(
                'status',
                [
                    'COMPLETED',
                    'CANCELLED',
                ]
            )

            ->count();


        $activeTrips =
            Trip::query()

            ->whereNotIn(
                'status',
                [
                    'COMPLETED',
                    'CANCELLED',
                ]
            )

            ->count();


        $activeTransfers =
            TripTransfer::query()

            ->whereNotIn(
                'status',
                [
                    'COMPLETED',
                    'CANCELLED',
                ]
            )

            ->count();


        $pendingStandby =
            TripStandbyCalculation::query()

            ->where(
                'status',
                'PENDING'
            )

            ->count();


        /*
        |--------------------------------------------------------------------------
        | ESTADOS DE VIAJES
        |--------------------------------------------------------------------------
        */

        $tripStats = [

            'pending' =>
            Trip::query()
                ->where(
                    'status',
                    'PENDING'
                )
                ->count(),

            'assigned' =>
            Trip::query()
                ->where(
                    'status',
                    'ASSIGNED'
                )
                ->count(),

            'in_transit' =>
            Trip::query()
                ->where(
                    'status',
                    'IN_TRANSIT'
                )
                ->count(),

            'at_destination' =>
            Trip::query()
                ->where(
                    'status',
                    'AT_DESTINATION'
                )
                ->count(),

            'completed_today' =>
            TripStatusHistory::query()

                ->where(
                    'new_status',
                    'COMPLETED'
                )

                ->whereDate(
                    'changed_at',
                    today()
                )

                ->distinct(
                    'trip_id'
                )

                ->count(
                    'trip_id'
                ),
        ];


        /*
        |--------------------------------------------------------------------------
        | VIAJES SIN RECURSOS
        |--------------------------------------------------------------------------
        */

        $tripsWithoutAssignment =
            Trip::query()

            ->whereNotIn(
                'status',
                [
                    'COMPLETED',
                    'CANCELLED',
                ]
            )

            ->whereDoesntHave(
                'assignments',
                function ($query) {

                    $query->whereNull(
                        'unassigned_at'
                    );
                }
            )

            ->count();


        /*
        |--------------------------------------------------------------------------
        | CONDUCTORES CON ALERTAS
        |--------------------------------------------------------------------------
        */

        /*
         * license_status es un accessor del modelo,
         * por eso filtramos después de obtener
         * solamente los conductores activos.
         */
        $driversWithAlerts =
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

            ->get()

            ->filter(
                function ($driver) {

                    return in_array(
                        $driver->license_status,
                        [
                            'expired',
                            'expiring',
                        ],
                        true
                    );
                }
            )

            ->values();


        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS CON ALERTAS
        |--------------------------------------------------------------------------
        */

        $vehiclesWithAlerts =
            Vehicle::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'plate'
            )

            ->get()

            ->filter(
                function ($vehicle) {

                    return (bool)
                    $vehicle
                        ->has_expired_document;
                }
            )

            ->values();


        /*
        |--------------------------------------------------------------------------
        | VIAJES RECIENTES
        |--------------------------------------------------------------------------
        */

        $recentTrips =
            Trip::query()

            ->with([
                'workOrder',

                'activeAssignment.driver',

                'activeAssignment.vehicle',
            ])

            ->orderByDesc(
                'created_at'
            )

            ->limit(8)

            ->get();


        /*
        |--------------------------------------------------------------------------
        | TRANSFERENCIAS RECIENTES
        |--------------------------------------------------------------------------
        */

        $recentTransfers =
            TripTransfer::query()

            ->with([
                'trip.workOrder',

                'activeAssignment.driver',

                'activeAssignment.vehicle',

                'assignments.driver',

                'assignments.vehicle',
            ])

            ->orderByDesc(
                'created_at'
            )

            ->limit(6)

            ->get();


        /*
        |--------------------------------------------------------------------------
        | STAND-BY RECIENTE
        |--------------------------------------------------------------------------
        */

        $recentStandby =
            TripStandbyCalculation::query()

            ->with([
                'trip.workOrder',
            ])

            ->orderByDesc(
                'updated_at'
            )

            ->limit(6)

            ->get();


        return view(
            'dashboard',

            compact(
                'activeWorkOrders',
                'activeTrips',
                'activeTransfers',
                'pendingStandby',
                'tripStats',
                'tripsWithoutAssignment',
                'driversWithAlerts',
                'vehiclesWithAlerts',
                'recentTrips',
                'recentTransfers',
                'recentStandby'
            )
        );
    }
}
