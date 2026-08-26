<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Plant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PlantController extends Controller
{
    public function index(Request $request): View
    {
        $search =
            trim(
                (string) $request->get('search')
            );

        $clientId =
            $request->get('client_id');


        $plants =
            Plant::query()

            ->with('client')

            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        function ($subquery) use ($search) {

                            $subquery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'code',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'city',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'address',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhereHas(
                                    'client',
                                    function ($clientQuery) use ($search) {

                                        $clientQuery
                                            ->where(
                                                'business_name',
                                                'like',
                                                "%{$search}%"
                                            )

                                            ->orWhere(
                                                'trade_name',
                                                'like',
                                                "%{$search}%"
                                            );
                                    }
                                );
                        }
                    );
                }
            )

            ->when(
                $clientId,
                fn($query) =>
                $query->where(
                    'client_id',
                    $clientId
                )
            )

            ->orderBy('name')

            ->paginate(10)

            ->withQueryString();


        $clients =
            Client::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'business_name'
            )

            ->get();


        return view(
            'plants.index',
            compact(
                'plants',
                'clients',
                'search',
                'clientId'
            )
        );
    }


    public function create(): View
    {
        $clients =
            Client::query()

            ->where(
                'is_active',
                true
            )

            ->orderBy(
                'business_name'
            )

            ->get();


        return view(
            'plants.create',
            compact('clients')
        );
    }


    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $this->validatePlant(
                $request
            );


        $validated['is_active'] =
            $request->boolean(
                'is_active'
            );


        Plant::create(
            $validated
        );


        return redirect()
            ->route(
                'plants.index'
            )
            ->with(
                'success',
                'Planta registrada correctamente.'
            );
    }


    public function show(
        Plant $plant
    ): View {

        $plant->load(
            'client'
        );


        return view(
            'plants.show',
            compact('plant')
        );
    }


    public function edit(
        Plant $plant
    ): View {

        $clients =
            Client::query()

            ->where(
                function ($query) use ($plant) {

                    $query
                        ->where(
                            'is_active',
                            true
                        )

                        ->orWhere(
                            'id',
                            $plant->client_id
                        );
                }
            )

            ->orderBy(
                'business_name'
            )

            ->get();


        return view(
            'plants.edit',
            compact(
                'plant',
                'clients'
            )
        );
    }


    public function update(
        Request $request,
        Plant $plant
    ): RedirectResponse {

        $validated =
            $this->validatePlant(
                $request,
                $plant
            );


        $validated['is_active'] =
            $request->boolean(
                'is_active'
            );


        $plant->update(
            $validated
        );


        return redirect()
            ->route(
                'plants.index'
            )
            ->with(
                'success',
                'Planta actualizada correctamente.'
            );
    }


    public function destroy(
        Plant $plant
    ): RedirectResponse {

        /*
         * No eliminar si la planta
         * ya está usada en órdenes.
         */

        if (
            $plant->workOrders()
            ->exists()
            ||
            $plant->originWorkOrders()
            ->exists()
            ||
            $plant->destinationWorkOrders()
            ->exists()
        ) {

            throw ValidationException::withMessages([

                'plant' =>
                'No se puede eliminar la planta porque ya está relacionada con órdenes de trabajo.',

            ]);
        }


        $plant->delete();


        return redirect()
            ->route(
                'plants.index'
            )
            ->with(
                'success',
                'Planta eliminada correctamente.'
            );
    }


    private function validatePlant(
        Request $request,
        ?Plant $plant = null
    ): array {

        return $request->validate([

            'client_id' => [
                'required',
                'exists:clients,id',
            ],


            'name' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'plants',
                    'name'
                )
                    ->where(
                        fn($query) =>
                        $query->where(
                            'client_id',
                            $request->client_id
                        )
                    )
                    ->ignore(
                        $plant?->id
                    ),
            ],


            'code' => [
                'nullable',
                'string',
                'max:50',
            ],


            'city' => [
                'nullable',
                'string',
                'max:150',
            ],


            'address' => [
                'required',
                'string',
                'max:1000',
            ],


            'reference' => [
                'nullable',
                'string',
                'max:500',
            ],


            'contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],


            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],


            'email' => [
                'nullable',
                'email',
                'max:255',
            ],


            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
            ],


            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
            ],


            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

        ], [

            'client_id.required' =>
            'Seleccione el cliente.',

            'client_id.exists' =>
            'El cliente seleccionado no existe.',

            'name.required' =>
            'El nombre de la planta es obligatorio.',

            'name.unique' =>
            'Este cliente ya tiene una planta con ese nombre.',

            'address.required' =>
            'La dirección es obligatoria.',

            'email.email' =>
            'Ingrese un correo electrónico válido.',

        ]);
    }
}
