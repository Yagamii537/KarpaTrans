<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\TripStandbyCalculation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TripStandbyService
{
    /*
    |--------------------------------------------------------------------------
    | CALCULAR / RECALCULAR
    |--------------------------------------------------------------------------
    */

    public function calculate(
        Trip $trip
    ): TripStandbyCalculation {

        $trip->loadMissing([
            'workOrder',
            'times',
        ]);


        $workOrder =
            $trip->workOrder;


        /*
        |--------------------------------------------------------------------------
        | REGLA CONGELADA EN LA OT
        |--------------------------------------------------------------------------
        */

        $processType =
            $workOrder
            ?->standby_process_type;


        $freeHours =
            (int) (
                $workOrder
                ?->standby_free_hours
                ?? 0
            );


        $countStartType =
            $workOrder
            ?->standby_count_start_type
            ?: 'REQUESTED_TIME';


        $fractionMinutes =
            (int) (
                $workOrder
                ?->standby_fraction_minutes
                ?? 30
            );


        $ruleSource =
            $workOrder
            ?->standby_rule_source;


        /*
         * Evitar valores absurdos.
         *
         * La reunión plantea la fracción
         * como umbral dentro de la hora.
         */
        if (
            $fractionMinutes < 1
        ) {

            $fractionMinutes = 30;
        }


        if (
            $fractionMinutes > 60
        ) {

            $fractionMinutes = 60;
        }


        /*
        |--------------------------------------------------------------------------
        | HORA SOLICITADA
        |--------------------------------------------------------------------------
        */

        $requestedAt =
            $trip
            ->scheduled_start_at;


        /*
        |--------------------------------------------------------------------------
        | LLEGADA REAL
        |--------------------------------------------------------------------------
        */

        $arrivalEvent =
            $trip
            ->times
            ->firstWhere(
                'event_type',
                'ARRIVAL'
            );


        $arrivalAt =
            $arrivalEvent
            ?->event_at;


        /*
        |--------------------------------------------------------------------------
        | EVENTO FINAL DEL STAND-BY
        |--------------------------------------------------------------------------
        */

        $endEventType =
            $this->resolveEndEventType(
                $trip,
                $processType
            );


        $endEvent =
            $endEventType

            ? $trip
            ->times
            ->firstWhere(
                'event_type',
                $endEventType
            )

            : null;


        $endAt =
            $endEvent
            ?->event_at;


        /*
        |--------------------------------------------------------------------------
        | DETERMINAR INICIO
        |--------------------------------------------------------------------------
        */

        $startAt =
            null;


        if (
            $countStartType
            === 'ARRIVAL_TIME'
        ) {

            $startAt =
                $arrivalAt;
        } else {

            /*
             * REQUESTED_TIME
             *
             * Si llega antes:
             * empieza hora solicitada.
             *
             * Si llega tarde:
             * empieza cuando realmente llega.
             */
            if (
                $requestedAt
                &&
                $arrivalAt
            ) {

                $startAt =
                    $arrivalAt->greaterThan(
                        $requestedAt
                    )

                    ? $arrivalAt

                    : $requestedAt;
            } elseif ($requestedAt) {

                $startAt =
                    $requestedAt;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | CÁLCULO BASE
        |--------------------------------------------------------------------------
        */

        $totalMinutes =
            null;


        $freeMinutes =
            $freeHours * 60;


        $excessMinutes =
            0;


        $billableHours =
            0;


        $status =
            'PENDING';


        $observation =
            null;


        /*
         * Para ARRIVAL_TIME necesariamente
         * necesitamos llegada.
         */
        if (
            $countStartType
            === 'ARRIVAL_TIME'

            &&
            !$arrivalAt
        ) {

            $observation =
                'Esperando evento de Llegada para iniciar el cálculo.';
        }


        /*
         * Para REQUESTED_TIME también
         * necesitamos saber que el vehículo
         * realmente llegó.
         */ elseif (!$arrivalAt) {

            $observation =
                'Esperando evento de Llegada.';
        } elseif (!$startAt) {

            $observation =
                'No se pudo determinar el inicio del Stand-by.';
        } elseif (!$endEventType) {

            $observation =
                'No se pudo determinar el evento final del Stand-by para esta etapa.';
        } elseif (!$endAt) {

            $observation =
                'Esperando evento '
                . $this->eventLabel(
                    $endEventType
                )
                . ' para finalizar el cálculo.';
        } elseif (
            $endAt->lt(
                $startAt
            )
        ) {

            $observation =
                'El evento final ocurre antes del inicio calculado del Stand-by.';
        } else {

            /*
             * Minutos totales.
             */
            $totalMinutes =
                $startAt
                ->diffInMinutes(
                    $endAt
                );


            /*
             * Minutos excedentes luego
             * de las horas libres.
             */
            $excessMinutes =
                max(
                    0,
                    $totalMinutes
                        -
                        $freeMinutes
                );


            /*
             * Fórmula explicada por cliente:
             *
             * Con fracción = 30:
             *
             * 0:29  => 0
             * 0:30  => 1
             * 1:29  => 1
             * 1:30  => 2
             */

            $billableHours =
                $this->calculateBillableHours(

                    $excessMinutes,

                    $fractionMinutes
                );


            $status =
                'CALCULATED';


            $observation =
                'Stand-by calculado automáticamente con la regla congelada en la Orden de Trabajo.';
        }


        /*
        |--------------------------------------------------------------------------
        | GUARDAR
        |--------------------------------------------------------------------------
        */

        return TripStandbyCalculation::updateOrCreate(

            [
                'trip_id' =>
                $trip->id,
            ],

            [
                'process_type' =>
                $processType,

                'count_start_type' =>
                $countStartType,

                'end_event_type' =>
                $endEventType,

                'free_hours' =>
                $freeHours,

                'fraction_minutes' =>
                $fractionMinutes,

                'rule_source' =>
                $ruleSource,

                'requested_at' =>
                $requestedAt,

                'arrival_at' =>
                $arrivalAt,

                'start_at' =>
                $startAt,

                'end_at' =>
                $endAt,

                'total_minutes' =>
                $totalMinutes,

                'free_minutes' =>
                $freeMinutes,

                'excess_minutes' =>
                $excessMinutes,

                'billable_hours' =>
                $billableHours,

                'status' =>
                $status,

                'observation' =>
                $observation,

                'calculated_at' =>
                $status === 'CALCULATED'
                    ? now()
                    : null,

                'calculated_by' =>
                Auth::id(),
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EVENTO QUE TERMINA LA ESPERA
    |--------------------------------------------------------------------------
    */

    private function resolveEndEventType(
        Trip $trip,
        ?string $processType
    ): ?string {

        /*
         * POSICIÓN
         *
         * El cabezal deja el contenedor
         * en la planta.
         */
        if (
            $trip->service_stage
            === 'POSITIONING'
        ) {

            return 'POSITIONING';
        }


        /*
         * RETIRO
         *
         * La espera termina cuando
         * logra retirar/enganchar
         * el contenedor.
         */
        if (
            $trip->service_stage
            === 'PICKUP'
        ) {

            return 'PICKUP';
        }


        /*
         * SERVICIO INMEDIATO
         */
        if (
            $trip->service_stage
            === 'IMMEDIATE'
        ) {

            if (
                $processType
                === 'UNLOAD'
            ) {

                return 'UNLOAD_END';
            }


            return 'LOAD_END';
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | HORAS FACTURABLES
    |--------------------------------------------------------------------------
    */

    private function calculateBillableHours(
        int $excessMinutes,
        int $fractionMinutes
    ): int {

        if (
            $excessMinutes
            <= 0
        ) {

            return 0;
        }


        /*
         * Todavía no alcanza
         * la fracción mínima.
         */
        if (
            $excessMinutes
            < $fractionMinutes
        ) {

            return 0;
        }


        /*
         * Ejemplo fracción 30:
         *
         * 30  => 1
         * 89  => 1
         * 90  => 2
         * 149 => 2
         * 150 => 3
         */
        return

            intdiv(

                $excessMinutes
                    -
                    $fractionMinutes,

                60
            )

            + 1;
    }


    /*
    |--------------------------------------------------------------------------
    | ETIQUETA EVENTO
    |--------------------------------------------------------------------------
    */

    private function eventLabel(
        string $eventType
    ): string {

        return match ($eventType) {

            'POSITIONING' =>
            'Posicionamiento',

            'PICKUP' =>
            'Retiro',

            'LOAD_END' =>
            'Fin de carga',

            'UNLOAD_END' =>
            'Fin de descarga',

            default =>
            $eventType,
        };
    }
}
