<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Plant;
use App\Models\TransferEvent;
use App\Models\TransferStatusHistory;
use App\Models\TripTransfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransferEventController extends Controller
{
    public function store(
        Request $request,
        TripTransfer $transfer
    ): RedirectResponse {

        if (
            in_array(
                $transfer->status,
                [
                    'COMPLETED',
                    'CANCELLED',
                ],
                true
            )
        ) {

            return back()
                ->withErrors([

                    'event' =>
                    'No se pueden registrar eventos en una transferencia completada o cancelada.',

                ]);
        }


        if (
            !$transfer
                ->activeAssignment()
                ->exists()
        ) {

            return back()
                ->withErrors([

                    'event' =>
                    'Primero debe asignar recursos a la transferencia.',

                ]);
        }


        $transfer->load(
            'events'
        );


        $validated =
            $request->validate([

                'event_type' => [

                    'required',

                    Rule::in(
                        array_keys(
                            TripTransfer::eventLabels()
                        )
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
            ]);


        /*
        |--------------------------------------------------------------------------
        | NO DUPLICAR
        |--------------------------------------------------------------------------
        */

        if (
            $transfer
            ->events
            ->contains(
                'event_type',
                $validated['event_type']
            )
        ) {

            throw ValidationException::withMessages([

                'event_type' =>
                'Este evento ya fue registrado.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | SECUENCIA
        |--------------------------------------------------------------------------
        */

        if (
            !$transfer
                ->canRegisterEvent(
                    $validated['event_type']
                )
        ) {

            throw ValidationException::withMessages([

                'event_type' =>
                'Todavía no corresponde registrar este evento.',

            ]);
        }


        /*
         * Fecha no puede ser anterior
         * al último evento.
         */
        $lastEvent =
            $transfer
            ->events
            ->sortByDesc(
                'event_at'
            )
            ->first();


        if (
            $lastEvent
            &&
            strtotime(
                $validated['event_at']
            )

            <
            $lastEvent
            ->event_at
            ->timestamp
        ) {

            throw ValidationException::withMessages([

                'event_at' =>
                'La fecha/hora no puede ser anterior al último evento registrado.',

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | UBICACIÓN
        |--------------------------------------------------------------------------
        */

        $validated =
            $this->normalizeLocation(
                $validated
            );


        $validated['location_name_snapshot'] =
            $this->resolveLocationName(
                $validated
            );


        $validated['trip_transfer_id'] =
            $transfer->id;


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
                $transfer,
                $validated
            ) {

                TransferEvent::create(
                    $validated
                );


                $newStatus =
                    $transfer
                    ->statusForEvent(
                        $validated['event_type']
                    );


                if (
                    $newStatus
                    &&
                    $newStatus
                    !== $transfer->status
                ) {

                    $this->changeStatus(

                        $transfer,

                        $newStatus,

                        'Cambio automático por evento: '
                            . (
                                TripTransfer::eventLabels()[$validated['event_type']]
                                ?? $validated['event_type']
                            )
                            . '.'
                    );
                }


                /*
                 * Primera llegada.
                 */
                if (
                    $validated['event_type']
                    === 'ARRIVAL_ORIGIN'
                    &&
                    !$transfer->started_at
                ) {

                    $transfer->update([

                        'started_at' =>
                        $validated['event_at'],

                        'updated_by' =>
                        Auth::id(),
                    ]);
                }


                /*
                 * Entrega completa la transferencia.
                 */
                if (
                    $validated['event_type']
                    === 'DELIVERY'
                ) {

                    $transfer->update([

                        'completed_at' =>
                        $validated['event_at'],

                        'updated_by' =>
                        Auth::id(),
                    ]);


                    $this
                        ->releaseActiveAssignment(
                            $transfer
                        );
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
                'Evento registrado correctamente.'
            );
    }


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
                    'Seleccione una ubicación.',

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
                    'Seleccione una planta.',

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


    private function changeStatus(
        TripTransfer $transfer,
        string $newStatus,
        ?string $reason = null
    ): void {

        $oldStatus =
            $transfer->status;


        $transfer->update([

            'status' =>
            $newStatus,

            'updated_by' =>
            Auth::id(),
        ]);


        TransferStatusHistory::create([

            'trip_transfer_id' =>
            $transfer->id,

            'previous_status' =>
            $oldStatus,

            'new_status' =>
            $newStatus,

            'reason' =>
            $reason,

            'changed_by' =>
            Auth::id(),

            'changed_at' =>
            now(),
        ]);


        $transfer->status =
            $newStatus;
    }


    private function releaseActiveAssignment(
        TripTransfer $transfer
    ): void {

        $assignment =
            $transfer
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
            'Liberación automática al completar la transferencia.',

            'released_by' =>
            Auth::id(),
        ]);
    }
}
