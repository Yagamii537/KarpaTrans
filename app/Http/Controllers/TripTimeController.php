<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Plant;
use App\Models\Trip;
use App\Models\TripStatusHistory;
use App\Models\TripTime;
use App\Services\TripStandbyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TripTimeController extends Controller
{
    public function store(
        Request $request,
        Trip $trip,
        TripStandbyService $standbyService
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | ESTADO
        |--------------------------------------------------------------------------
        */

        if (
            in_array(
                $trip->status,
                [
                    'CANCELLED',
                    'COMPLETED',
                ],
                true
            )
        ) {

            return back()
                ->withErrors([

                    'event' =>
                    'No se pueden registrar nuevos eventos en un viaje cancelado o completado.',

                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | DEPENDENCIA POSICIÓN + RETIRO
        |--------------------------------------------------------------------------
        */

        $trip->loadMissing(
            'workOrder'
        );


        if (
            !$this->isStageUnlocked(
                $trip
            )
        ) {

            $positioning =
                $this
                ->findPositioningStage(
                    $trip
                );


            throw ValidationException::withMessages([

                'event_type' =>

                'No se pueden registrar eventos del Retiro todavía. '

                    . 'Primero debe completarse la etapa de Posición '

                    . 'del Servicio #'

                    . $trip->service_number

                    . (

                        $positioning

                        ? ' (' . $positioning->trip_number . ').'

                        : '.'
                    ),

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | ASIGNACIÓN
        |--------------------------------------------------------------------------
        */

        if (
            !$trip
                ->activeAssignment()
                ->exists()
        ) {

            return back()
                ->withErrors([

                    'event' =>
                    'Primero debe asignar conductor y vehículo antes de registrar eventos operativos.',

                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | CARGAR EVENTOS
        |--------------------------------------------------------------------------
        */

        $trip->load(
            'times'
        );


        /*
        |--------------------------------------------------------------------------
        | VALIDACIÓN
        |--------------------------------------------------------------------------
        */

        $validated =
            $this->validateTripTime(
                $request,
                $trip
            );


        $this
            ->validateEventAvailability(

                $trip,

                $validated['event_type']
            );


        $this
            ->validateEventSequence(

                $trip,

                $validated['event_type'],

                $validated['event_at']
            );


        /*
        |--------------------------------------------------------------------------
        | UBICACIÓN
        |--------------------------------------------------------------------------
        */

        $validated =
            $this
            ->normalizeLocation(
                $validated
            );


        $validated['location_name_snapshot'] =
            $this
            ->resolveLocationName(
                $validated
            );


        /*
        |--------------------------------------------------------------------------
        | AUDITORÍA
        |--------------------------------------------------------------------------
        */

        $validated['trip_id'] =
            $trip->id;


        $validated['created_by'] =
            Auth::id();


        $validated['is_manual'] =
            true;


        /*
        |--------------------------------------------------------------------------
        | GUARDAR
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $trip,
                $validated,
                $standbyService
            ) {

                /*
                 * Crear evento.
                 */
                TripTime::create(
                    $validated
                );


                /*
                 * Recargar eventos para que
                 * el cálculo vea el nuevo.
                 */
                $trip->unsetRelation(
                    'times'
                );


                $trip->load(
                    'times'
                );


                /*
                 * Estado automático.
                 */
                $newStatus =
                    $trip
                    ->statusForEvent(
                        $validated['event_type']
                    );


                if (
                    $newStatus
                    &&
                    $newStatus
                    !== $trip->status
                ) {

                    $this
                        ->changeStatusFromEvent(

                            $trip,

                            $newStatus,

                            $validated['event_type']
                        );
                }


                /*
                 * Cada evento relevante puede
                 * cambiar el Stand-by.
                 *
                 * Por eso recalculamos.
                 */
                $standbyService
                    ->calculate(
                        $trip
                    );


                /*
                 * Si se completó,
                 * liberar recursos.
                 */
                if (
                    $newStatus
                    === 'COMPLETED'
                ) {

                    $this
                        ->releaseActiveAssignment(
                            $trip
                        );
                }
            }
        );


        return redirect()

            ->route(
                'trips.show',
                $trip
            )

            ->with(

                'success',

                'Evento registrado correctamente. Estado y Stand-by actualizados automáticamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Trip $trip,
        TripTime $tripTime
    ): RedirectResponse {

        if (
            $tripTime->trip_id
            !== $trip->id
        ) {

            abort(404);
        }


        return back()
            ->withErrors([

                'event' =>
                'Los eventos registrados no pueden eliminarse para preservar la trazabilidad.',

            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN
    |--------------------------------------------------------------------------
    */

    private function validateTripTime(
        Request $request,
        Trip $trip
    ): array {

        return $request->validate([

            'event_type' => [

                'required',

                Rule::in(
                    $trip
                        ->allowedEventTypes()
                ),
            ],


            'event_at' => [
                'required',
                'date',
            ],


            'location_type' => [

                'required',

                Rule::in([
                    'LOCATION',
                    'PLANT',
                    'NONE',
                ]),
            ],


            'location_id' => [
                'nullable',
                'exists:locations,id',
            ],


            'plant_id' => [
                'nullable',
                'exists:plants,id',
            ],


            'observation' => [
                'nullable',
                'string',
                'max:3000',
            ],

        ], [

            'event_type.required' =>
            'Seleccione el tipo de evento.',

            'event_type.in' =>
            'El evento seleccionado no corresponde a esta etapa del servicio.',

            'event_at.required' =>
            'La fecha y hora del evento es obligatoria.',

            'location_type.required' =>
            'Seleccione el tipo de ubicación.',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | EVENTO NO REPETIDO
    |--------------------------------------------------------------------------
    */

    private function validateEventAvailability(
        Trip $trip,
        string $eventType
    ): void {

        if (
            !in_array(
                $eventType,
                $trip->allowedEventTypes(),
                true
            )
        ) {

            throw ValidationException::withMessages([

                'event_type' =>

                'Este evento no está permitido para la etapa '

                    . $trip
                    ->service_stage_label

                    . '.',

            ]);
        }


        $alreadyExists =
            $trip
            ->times
            ->contains(
                'event_type',
                $eventType
            );


        if ($alreadyExists) {

            throw ValidationException::withMessages([

                'event_type' =>
                'Este evento ya fue registrado y no puede seleccionarse nuevamente.',

            ]);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SECUENCIA
    |--------------------------------------------------------------------------
    */

    private function validateEventSequence(
        Trip $trip,
        string $eventType,
        string $eventAt
    ): void {

        $labels =
            Trip::eventLabels();


        $prerequisites =
            $trip
            ->eventPrerequisites(
                $eventType
            );


        foreach (
            $prerequisites
            as $requiredEvent
        ) {

            $required =
                $trip
                ->times
                ->firstWhere(
                    'event_type',
                    $requiredEvent
                );


            if (!$required) {

                throw ValidationException::withMessages([

                    'event_type' =>

                    'Antes de registrar "'

                        . (
                            $labels[$eventType]
                            ?? $eventType
                        )

                        . '" debe registrar "'

                        . (
                            $labels[$requiredEvent]
                            ?? $requiredEvent
                        )

                        . '".',

                ]);
            }


            if (
                strtotime(
                    $eventAt
                )

                <
                $required
                ->event_at
                ->timestamp
            ) {

                throw ValidationException::withMessages([

                    'event_at' =>

                    'La fecha/hora de "'

                        . (
                            $labels[$eventType]
                            ?? $eventType
                        )

                        . '" no puede ser anterior a "'

                        . (
                            $labels[$requiredEvent]
                            ?? $requiredEvent
                        )

                        . '".',

                ]);
            }
        }


        if (
            $eventType
            === 'WAIT_END'
        ) {

            $waitStart =
                $trip
                ->times
                ->firstWhere(
                    'event_type',
                    'WAIT_START'
                );


            if (
                $waitStart

                &&
                strtotime(
                    $eventAt
                )

                <
                $waitStart
                ->event_at
                ->timestamp
            ) {

                throw ValidationException::withMessages([

                    'event_at' =>
                    'El fin de espera no puede ser anterior al inicio de espera.',

                ]);
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | UBICACIÓN
    |--------------------------------------------------------------------------
    */

    private function normalizeLocation(
        array $validated
    ): array {

        if (
            $validated['location_type']
            === 'LOCATION'
        ) {

            if (
                empty($validated['location_id'])
            ) {

                throw ValidationException::withMessages([

                    'location_id' =>
                    'Seleccione la ubicación.',

                ]);
            }


            $validated['plant_id'] =
                null;
        }


        if (
            $validated['location_type']
            === 'PLANT'
        ) {

            if (
                empty($validated['plant_id'])
            ) {

                throw ValidationException::withMessages([

                    'plant_id' =>
                    'Seleccione la planta.',

                ]);
            }


            $validated['location_id'] =
                null;
        }


        if (
            $validated['location_type']
            === 'NONE'
        ) {

            $validated['location_id'] =
                null;


            $validated['plant_id'] =
                null;
        }


        return $validated;
    }


    private function resolveLocationName(
        array $validated
    ): ?string {

        if (
            $validated['location_type']
            === 'LOCATION'
        ) {

            return Location::find(
                $validated['location_id']
            )?->name;
        }


        if (
            $validated['location_type']
            === 'PLANT'
        ) {

            return Plant::find(
                $validated['plant_id']
            )?->name;
        }


        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | POSICIÓN + RETIRO
    |--------------------------------------------------------------------------
    */

    private function findPositioningStage(
        Trip $trip
    ): ?Trip {

        if (
            $trip->service_stage
            !== 'PICKUP'

            ||
            !$trip->service_number
        ) {

            return null;
        }


        return Trip::query()

            ->where(
                'work_order_id',
                $trip->work_order_id
            )

            ->where(
                'service_number',
                $trip->service_number
            )

            ->where(
                'service_stage',
                'POSITIONING'
            )

            ->first();
    }


    private function isStageUnlocked(
        Trip $trip
    ): bool {

        /*
         * No es Retiro.
         */
        if (
            $trip->service_stage
            !== 'PICKUP'
        ) {

            return true;
        }


        /*
         * Retiro independiente.
         */
        if (
            $trip
            ->workOrder
            ?->service_modality

            !== 'POSITIONING_PICKUP'
        ) {

            return true;
        }


        /*
         * Retiro perteneciente
         * a Posición + Retiro.
         */
        $positioning =
            $this
            ->findPositioningStage(
                $trip
            );


        if (!$positioning) {

            return false;
        }


        return $positioning->status
            === 'COMPLETED';
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADO AUTOMÁTICO
    |--------------------------------------------------------------------------
    */

    private function changeStatusFromEvent(
        Trip $trip,
        string $newStatus,
        string $eventType
    ): void {

        $oldStatus =
            $trip->status;


        $label =
            Trip::eventLabels()[$eventType]

            ?? $eventType;


        $trip->update([

            'status' =>
            $newStatus,

            'updated_by' =>
            Auth::id(),

        ]);


        TripStatusHistory::create([

            'trip_id' =>
            $trip->id,

            'previous_status' =>
            $oldStatus,

            'new_status' =>
            $newStatus,

            'reason' =>
            'Cambio automático por evento: '
                . $label
                . '.',

            'changed_by' =>
            Auth::id(),

            'changed_at' =>
            now(),

        ]);


        $trip->status =
            $newStatus;
    }


    /*
    |--------------------------------------------------------------------------
    | LIBERAR ASIGNACIÓN
    |--------------------------------------------------------------------------
    */

    private function releaseActiveAssignment(
        Trip $trip
    ): void {

        $assignment =
            $trip
            ->assignments()

            ->whereNull(
                'unassigned_at'
            )

            ->first();


        if (!$assignment) {

            return;
        }


        $assignment->update([

            'unassigned_at' =>
            now(),

            'release_reason' =>
            'Liberación automática al completar el viaje por eventos.',

            'released_by' =>
            Auth::id(),

        ]);
    }
}
