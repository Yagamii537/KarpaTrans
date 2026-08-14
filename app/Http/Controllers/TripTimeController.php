<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Plant;
use App\Models\Trip;
use App\Models\TripTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TripTimeController extends Controller
{
    public function store(
        Request $request,
        Trip $trip
    ): RedirectResponse {

        /*
         * No permitimos agregar eventos
         * a viajes cancelados.
         */
        if ($trip->status === 'CANCELLED') {

            return back()->withErrors([
                'event' =>
                'No se pueden registrar eventos en un viaje cancelado.',
            ]);
        }

        $validated =
            $this->validateTripTime($request);

        $validated =
            $this->normalizeLocation(
                $validated
            );

        /*
         * Obtenemos el nombre histórico
         * de la ubicación.
         */
        $validated['location_name_snapshot'] =
            $this->resolveLocationName(
                $validated
            );

        $validated['trip_id'] =
            $trip->id;

        $validated['created_by'] =
            Auth::id();

        $validated['is_manual'] =
            true;

        /*
         * Regla importante:
         * No sobrescribimos eventos anteriores.
         * Siempre generamos una nueva fila.
         */
        TripTime::create(
            $validated
        );

        return redirect()
            ->route(
                'trips.show',
                $trip
            )
            ->with(
                'success',
                'Evento registrado correctamente.'
            );
    }

    public function destroy(
        Trip $trip,
        TripTime $tripTime
    ): RedirectResponse {

        /*
         * Evitar borrar un evento
         * perteneciente a otro viaje.
         */
        if (
            $tripTime->trip_id
            !== $trip->id
        ) {

            abort(404);
        }

        /*
         * Para mantener trazabilidad,
         * no eliminaremos eventos desde
         * la interfaz operativa.
         *
         * Si más adelante el cliente requiere
         * correcciones, se hará mediante
         * evento correctivo / auditoría.
         */
        return back()->withErrors([
            'event' =>
            'Los eventos registrados no pueden eliminarse para preservar la trazabilidad.',
        ]);
    }

    private function validateTripTime(
        Request $request
    ): array {

        return $request->validate([

            'event_type' => [
                'required',

                Rule::in([
                    'ARRIVAL',
                    'ENTRY',
                    'CONTAINER_PICKUP',
                    'LOAD_START',
                    'LOAD_END',
                    'UNLOAD_START',
                    'UNLOAD_END',
                    'WAIT_START',
                    'WAIT_END',
                    'DEPARTURE',
                    'POSITIONING',
                    'PICKUP',
                    'PORT_ARRIVAL',
                    'DELIVERY',
                    'OTHER',
                ]),
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

            'event_at.required' =>
            'La fecha y hora del evento es obligatoria.',

            'location_type.required' =>
            'Seleccione el tipo de ubicación.',

        ]);
    }

    private function normalizeLocation(
        array $validated
    ): array {

        if (
            $validated['location_type']
            === 'LOCATION'
        ) {

            if (empty($validated['location_id'])) {

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

            if (empty($validated['plant_id'])) {

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
}
