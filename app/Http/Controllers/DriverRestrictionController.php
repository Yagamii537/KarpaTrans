<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Driver;
use App\Models\DriverRestriction;
use App\Models\Location;
use App\Models\Plant;
use App\Models\Subclient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DriverRestrictionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
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


        $actionType =
            $request->get(
                'action_type'
            );


        $driverId =
            $request->get(
                'driver_id'
            );


        $restrictions =
            DriverRestriction::query()

            ->with([
                'driver',
                'client',
                'subclient',
                'plant',
                'location',
            ])

            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        function ($subquery) use ($search) {

                            $subquery

                                ->where(
                                    'reason',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'notes',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhereHas(
                                    'driver',
                                    function ($driverQuery) use ($search) {

                                        $driverQuery
                                            ->where(
                                                'first_names',
                                                'like',
                                                "%{$search}%"
                                            )

                                            ->orWhere(
                                                'last_names',
                                                'like',
                                                "%{$search}%"
                                            )

                                            ->orWhere(
                                                'identification',
                                                'like',
                                                "%{$search}%"
                                            );
                                    }
                                )

                                ->orWhereHas(
                                    'client',
                                    fn($clientQuery) =>
                                    $clientQuery
                                        ->where(
                                            'business_name',
                                            'like',
                                            "%{$search}%"
                                        )
                                )

                                ->orWhereHas(
                                    'plant',
                                    fn($plantQuery) =>
                                    $plantQuery
                                        ->where(
                                            'name',
                                            'like',
                                            "%{$search}%"
                                        )
                                );
                        }
                    );
                }
            )

            ->when(
                $actionType,
                fn($query) =>
                $query->where(
                    'action_type',
                    $actionType
                )
            )

            ->when(
                $driverId,
                fn($query) =>
                $query->where(
                    'driver_id',
                    $driverId
                )
            )

            ->orderByDesc(
                'is_active'
            )

            ->orderByDesc(
                'start_date'
            )

            ->orderByDesc('id')

            ->paginate(15)

            ->withQueryString();


        $drivers =
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
            ->get();


        return view(
            'driver-restrictions.index',
            compact(
                'restrictions',
                'drivers',
                'search',
                'actionType',
                'driverId'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        return view(
            'driver-restrictions.create',
            $this->formData()
        );
    }


    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $this->validateRestriction(
                $request
            );


        $this->validateRelationships(
            $validated
        );


        $validated['is_active'] =
            $request->boolean(
                'is_active'
            );


        $validated['created_by'] =
            Auth::id();


        $validated['updated_by'] =
            Auth::id();


        /*
         * Restricción indefinida:
         * no debe tener fecha final.
         */
        if (
            $validated['restriction_type'] === 'INDEFINITE'
        ) {

            $validated['end_date'] =
                null;
        }


        DriverRestriction::create(
            $validated
        );


        return redirect()
            ->route(
                'driver-restrictions.index'
            )
            ->with(
                'success',
                'Restricción registrada correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */

    public function show(
        DriverRestriction $driverRestriction
    ): View {

        $driverRestriction->load([

            'driver',

            'client',

            'subclient',

            'plant',

            'location',

            'creator',

            'updater',

        ]);


        return view(
            'driver-restrictions.show',
            compact(
                'driverRestriction'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    public function edit(
        DriverRestriction $driverRestriction
    ): View {

        return view(
            'driver-restrictions.edit',

            array_merge(
                $this->formData(),
                compact(
                    'driverRestriction'
                )
            )
        );
    }


    public function update(
        Request $request,
        DriverRestriction $driverRestriction
    ): RedirectResponse {

        $validated =
            $this->validateRestriction(
                $request,
                $driverRestriction
            );


        $this->validateRelationships(
            $validated
        );


        $validated['is_active'] =
            $request->boolean(
                'is_active'
            );


        $validated['updated_by'] =
            Auth::id();


        if (
            $validated['restriction_type'] === 'INDEFINITE'
        ) {

            $validated['end_date'] =
                null;
        }


        $driverRestriction->update(
            $validated
        );


        return redirect()
            ->route(
                'driver-restrictions.index'
            )
            ->with(
                'success',
                'Restricción actualizada correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        DriverRestriction $driverRestriction
    ): RedirectResponse {

        /*
         * Soft delete para conservar
         * trazabilidad.
         */

        $driverRestriction->delete();


        return redirect()
            ->route(
                'driver-restrictions.index'
            )
            ->with(
                'success',
                'Restricción eliminada correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS DEL FORMULARIO
    |--------------------------------------------------------------------------
    */

    private function formData(): array
    {
        return [

            'drivers' =>
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

                ->get(),


            'clients' =>
            Client::query()

                ->where(
                    'is_active',
                    true
                )

                ->orderBy(
                    'business_name'
                )

                ->get(),


            'subclients' =>
            Subclient::query()

                ->where(
                    'is_active',
                    true
                )

                ->orderBy(
                    'business_name'
                )

                ->get(),


            'plants' =>
            Plant::query()

                ->where(
                    'is_active',
                    true
                )

                ->orderBy(
                    'name'
                )

                ->get(),


            'locations' =>
            Location::query()

                ->where(
                    'is_active',
                    true
                )

                ->orderBy(
                    'name'
                )

                ->get(),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN
    |--------------------------------------------------------------------------
    */

    private function validateRestriction(
        Request $request,
        ?DriverRestriction $restriction = null
    ): array {

        return $request->validate([

            'driver_id' => [
                'required',
                'exists:drivers,id',
            ],


            'client_id' => [
                'nullable',
                'exists:clients,id',
            ],


            'subclient_id' => [
                'nullable',
                'exists:subclients,id',
            ],


            'plant_id' => [
                'nullable',
                'exists:plants,id',
            ],


            'location_id' => [
                'nullable',
                'exists:locations,id',
            ],


            'operation_type' => [
                'nullable',

                Rule::in([
                    'EXPORT',
                    'IMPORT',
                    'TRANSFER',
                    'OTHER',
                ]),
            ],


            'reason' => [
                'required',
                'string',
                'max:1000',
            ],


            'restriction_type' => [
                'required',

                Rule::in([
                    'TEMPORARY',
                    'INDEFINITE',
                ]),
            ],


            'start_date' => [
                'required',
                'date',
            ],


            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],


            'action_type' => [
                'required',

                Rule::in([
                    'BLOCK',
                    'WARNING',
                ]),
            ],


            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

        ], [

            'driver_id.required' =>
            'Seleccione el conductor.',

            'reason.required' =>
            'El motivo de la restricción es obligatorio.',

            'restriction_type.required' =>
            'Seleccione el tipo de restricción.',

            'start_date.required' =>
            'Ingrese la fecha de inicio.',

            'end_date.after_or_equal' =>
            'La fecha final no puede ser anterior a la fecha inicial.',

            'action_type.required' =>
            'Seleccione si la restricción advierte o bloquea.',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN RELACIONAL
    |--------------------------------------------------------------------------
    */

    private function validateRelationships(
        array $validated
    ): void {

        /*
         * Si existe subcliente y cliente,
         * deben coincidir.
         */

        if (
            !empty($validated['subclient_id'])
            &&
            !empty($validated['client_id'])
        ) {

            $valid =
                Subclient::query()

                ->where(
                    'id',
                    $validated['subclient_id']
                )

                ->where(
                    'client_id',
                    $validated['client_id']
                )

                ->exists();


            if (!$valid) {

                throw ValidationException::withMessages([

                    'subclient_id' =>
                    'El subcliente no pertenece al cliente seleccionado.',

                ]);
            }
        }


        /*
         * Si existe planta y cliente,
         * la planta debe ser del cliente.
         */

        if (
            !empty($validated['plant_id'])
            &&
            !empty($validated['client_id'])
        ) {

            $valid =
                Plant::query()

                ->where(
                    'id',
                    $validated['plant_id']
                )

                ->where(
                    'client_id',
                    $validated['client_id']
                )

                ->exists();


            if (!$valid) {

                throw ValidationException::withMessages([

                    'plant_id' =>
                    'La planta no pertenece al cliente seleccionado.',

                ]);
            }
        }
    }
}
