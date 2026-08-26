<?php

namespace App\Console\Commands;

use App\Models\Trip;
use App\Services\TripStandbyService;
use Illuminate\Console\Command;
use Throwable;

class RecalculateTripStandby extends Command
{
    /*
    |--------------------------------------------------------------------------
    | COMANDO
    |--------------------------------------------------------------------------
    |
    | Ejemplos:
    |
    | php artisan standby:recalculate
    |
    | php artisan standby:recalculate --trip=2
    |
    */

    protected $signature = 'standby:recalculate
                            {--trip= : ID específico del viaje}';


    protected $description =
    'Recalcula el Stand-by de viajes existentes usando sus eventos y la regla congelada de la OT.';


    /*
    |--------------------------------------------------------------------------
    | EJECUCIÓN
    |--------------------------------------------------------------------------
    */

    public function handle(
        TripStandbyService $standbyService
    ): int {

        $tripId =
            $this->option(
                'trip'
            );


        /*
        |--------------------------------------------------------------------------
        | VIAJE ESPECÍFICO
        |--------------------------------------------------------------------------
        */

        if ($tripId) {

            $trip =
                Trip::query()

                ->with([
                    'workOrder',
                    'times',
                ])

                ->find(
                    $tripId
                );


            if (!$trip) {

                $this->error(
                    'No existe el viaje con ID '
                        . $tripId
                        . '.'
                );


                return self::FAILURE;
            }


            return $this
                ->calculateTrip(
                    $trip,
                    $standbyService
                )

                ? self::SUCCESS
                : self::FAILURE;
        }


        /*
        |--------------------------------------------------------------------------
        | TODOS LOS VIAJES
        |--------------------------------------------------------------------------
        */

        $trips =
            Trip::query()

            ->with([
                'workOrder',
                'times',
            ])

            ->orderBy('id')

            ->get();


        if ($trips->isEmpty()) {

            $this->info(
                'No existen viajes para recalcular.'
            );


            return self::SUCCESS;
        }


        $this->info(
            'Recalculando Stand-by de '
                . $trips->count()
                . ' viaje(s)...'
        );


        $success =
            0;


        $errors =
            0;


        foreach (
            $trips
            as $trip
        ) {

            if (
                $this->calculateTrip(
                    $trip,
                    $standbyService
                )
            ) {

                $success++;
            } else {

                $errors++;
            }
        }


        $this->newLine();


        $this->info(
            'Finalizado.'
        );


        $this->line(
            'Correctos: '
                . $success
        );


        if ($errors > 0) {

            $this->warn(
                'Con error: '
                    . $errors
            );
        }


        return $errors > 0
            ? self::FAILURE
            : self::SUCCESS;
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULAR UN VIAJE
    |--------------------------------------------------------------------------
    */

    private function calculateTrip(
        Trip $trip,
        TripStandbyService $standbyService
    ): bool {

        try {

            $calculation =
                $standbyService
                ->calculate(
                    $trip
                );


            $this->line(

                $trip->trip_number

                    . ' | '

                    . $trip->service_stage_label

                    . ' | '

                    . $calculation->status

                    . ' | '

                    . $calculation->billable_hours

                    . ' h facturable(s)'
            );


            return true;
        } catch (Throwable $exception) {

            $this->error(

                $trip->trip_number

                    . ' | ERROR: '

                    . $exception->getMessage()
            );


            return false;
        }
    }
}
