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
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

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



    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});
