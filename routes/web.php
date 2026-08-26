<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ChassisController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SubclientController;
use App\Http\Controllers\CargoTypeController;
use App\Http\Controllers\DriverRestrictionController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripTimeController;
use App\Http\Controllers\StandbyController;
use App\Http\Controllers\TripTransferController;
use App\Http\Controllers\TransferAssignmentController;
use App\Http\Controllers\TransferEventController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\TripCostController;
use App\Http\Controllers\ReportController;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.authenticate');
});

Route::middleware('auth')->group(function () {


    Route::get(
        '/dashboard',
        [
            DashboardController::class,
            'index'
        ]
    )->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::resource('plants', PlantController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('chassis', ChassisController::class);
    Route::resource('locations', LocationController::class);

    Route::resource(
        'subclients',
        SubclientController::class
    );

    Route::get(
        '/cargo-types/available',
        [CargoTypeController::class, 'available']
    )->name('cargo-types.available');

    Route::resource(
        'cargo-types',
        CargoTypeController::class
    );

    Route::resource(
        'driver-restrictions',
        DriverRestrictionController::class
    )->except(['show']);

    Route::resource(
        'containers',
        ContainerController::class
    );

    Route::resource(
        'work-orders',
        WorkOrderController::class
    );

    Route::post(
        '/work-orders/{workOrder}/generate-trips',
        [TripController::class, 'generateFromWorkOrder']
    )->name('work-orders.generate-trips');

    Route::post(
        '/trips/{trip}/assign',
        [TripController::class, 'assign']
    )->name('trips.assign');

    Route::post(
        '/trips/{trip}/status',
        [TripController::class, 'updateStatus']
    )->name('trips.status');

    Route::resource(
        'trips',
        TripController::class
    );

    Route::post(
        '/trips/{trip}/times',
        [TripTimeController::class, 'store']
    )->name('trips.times.store');

    Route::delete(
        '/trips/{trip}/times/{tripTime}',
        [TripTimeController::class, 'destroy']
    )->name('trips.times.destroy');

    Route::get(
        '/standby',
        [StandbyController::class, 'index']
    )->name('standby.index');

    Route::get(
        '/transfers',
        [TripTransferController::class, 'index']
    )->name('transfers.index');


    Route::get(
        '/trips/{trip}/transfers/create',
        [TripTransferController::class, 'create']
    )->name('transfers.create');


    Route::post(
        '/trips/{trip}/transfers',
        [TripTransferController::class, 'store']
    )->name('transfers.store');


    Route::get(
        '/transfers/{transfer}',
        [TripTransferController::class, 'show']
    )->name('transfers.show');


    Route::post(
        '/transfers/{transfer}/cancel',
        [TripTransferController::class, 'cancel']
    )->name('transfers.cancel');


    Route::post(
        '/transfers/{transfer}/assign',
        [TransferAssignmentController::class, 'store']
    )->name('transfers.assign');


    Route::post(
        '/transfers/{transfer}/events',
        [TransferEventController::class, 'store']
    )->name('transfers.events.store');

    Route::get(
        '/settings',
        [
            SystemSettingController::class,
            'edit'
        ]
    )->name('settings.edit');


    Route::put(
        '/settings',
        [
            SystemSettingController::class,
            'update'
        ]
    )->name('settings.update');

    Route::get(
        '/costs',
        [
            TripCostController::class,
            'index'
        ]
    )->name('costs.index');


    Route::get(
        '/trips/{trip}/costs',
        [
            TripCostController::class,
            'trip'
        ]
    )->name('costs.trip');


    Route::post(
        '/trips/{trip}/costs',
        [
            TripCostController::class,
            'store'
        ]
    )->name('costs.store');


    Route::post(
        '/trips/{trip}/costs/standby',
        [
            TripCostController::class,
            'createStandby'
        ]
    )->name('costs.standby');


    Route::post(
        '/costs/{cost}/approve',
        [
            TripCostController::class,
            'approve'
        ]
    )->name('costs.approve');


    Route::post(
        '/costs/{cost}/cancel',
        [
            TripCostController::class,
            'cancel'
        ]
    )->name('costs.cancel');

    Route::get(
        '/reports',
        [
            ReportController::class,
            'index'
        ]
    )->name('reports.index');



    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});
