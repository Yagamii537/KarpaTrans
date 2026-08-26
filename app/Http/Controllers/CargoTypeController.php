<?php

namespace App\Http\Controllers;

use App\Models\CargoType;
use App\Models\Client;
use App\Models\Subclient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CargoTypeController extends Controller
{
    public function index(Request $request): View
    {
        $search =
            trim(
                (string)
                $request->get('search')
            );

        $cargoTypes =
            CargoType::query()
            ->withCount([
                'clients',
                'subclients',
            ])

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
                                    'description',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )

            ->orderBy('name')

            ->paginate(10)

            ->withQueryString();


        return view(
            'cargo-types.index',
            compact(
                'cargoTypes',
                'search'
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
            'cargo-types.create',
            $this->formData()
        );
    }


    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $this->validateCargoType(
                $request
            );


        $clientIds =
            collect(
                $request->input(
                    'clients',
                    []
                )
            )
            ->map(
                fn($id) => (int) $id
            )
            ->unique()
            ->values()
            ->toArray();


        $subclientIds =
            collect(
                $request->input(
                    'subclients',
                    []
                )
            )
            ->map(
                fn($id) => (int) $id
            )
            ->unique()
            ->values()
            ->toArray();


        /*
         * Validamos que los subclientes
         * seleccionados pertenezcan a
         * alguno de los clientes seleccionados.
         */
        $this->validateSubclientsForClients(
            $clientIds,
            $subclientIds
        );


        $validated['is_active'] =
            $request->boolean(
                'is_active'
            );


        $cargoType =
            CargoType::create(
                $validated
            );


        $cargoType
            ->clients()
            ->sync(
                $clientIds
            );


        $cargoType
            ->subclients()
            ->sync(
                $subclientIds
            );


        return redirect()
            ->route(
                'cargo-types.index'
            )
            ->with(
                'success',
                'Tipo de carga registrado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */

    public function show(
        CargoType $cargoType
    ): View {

        $cargoType->load([

            'clients' => function ($query) {
                $query->orderBy(
                    'business_name'
                );
            },

            'subclients.client',

        ]);


        return view(
            'cargo-types.show',
            compact(
                'cargoType'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    public function edit(
        CargoType $cargoType
    ): View {

        $cargoType->load([
            'clients',
            'subclients',
        ]);


        return view(
            'cargo-types.edit',
            array_merge(
                $this->formData(),
                compact(
                    'cargoType'
                )
            )
        );
    }


    public function update(
        Request $request,
        CargoType $cargoType
    ): RedirectResponse {

        $validated =
            $this->validateCargoType(
                $request,
                $cargoType
            );


        $clientIds =
            collect(
                $request->input(
                    'clients',
                    []
                )
            )
            ->map(
                fn($id) => (int) $id
            )
            ->unique()
            ->values()
            ->toArray();


        $subclientIds =
            collect(
                $request->input(
                    'subclients',
                    []
                )
            )
            ->map(
                fn($id) => (int) $id
            )
            ->unique()
            ->values()
            ->toArray();


        $this->validateSubclientsForClients(
            $clientIds,
            $subclientIds
        );


        $validated['is_active'] =
            $request->boolean(
                'is_active'
            );


        $cargoType->update(
            $validated
        );


        $cargoType
            ->clients()
            ->sync(
                $clientIds
            );


        $cargoType
            ->subclients()
            ->sync(
                $subclientIds
            );


        return redirect()
            ->route(
                'cargo-types.index'
            )
            ->with(
                'success',
                'Tipo de carga actualizado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        CargoType $cargoType
    ): RedirectResponse {

        /*
         * No eliminamos físicamente.
         * El modelo usa SoftDeletes.
         *
         * Primero quitamos configuraciones
         * activas de cliente/subcliente.
         */

        $cargoType
            ->clients()
            ->detach();


        $cargoType
            ->subclients()
            ->detach();


        $cargoType->delete();


        return redirect()
            ->route(
                'cargo-types.index'
            )
            ->with(
                'success',
                'Tipo de carga eliminado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | API PARA ÓRDENES DE TRABAJO
    |--------------------------------------------------------------------------
    */

    public function available(
        Request $request
    ): JsonResponse {

        $validated =
            $request->validate([

                'client_id' => [
                    'required',
                    'exists:clients,id',
                ],

                'subclient_id' => [
                    'nullable',
                    'exists:subclients,id',
                ],

            ]);


        $clientId =
            (int)
            $validated['client_id'];


        $subclientId =
            !empty($validated['subclient_id'])
            ? (int)
            $validated['subclient_id']
            : null;


        /*
         * Validar que el subcliente
         * corresponde al cliente.
         */

        if ($subclientId) {

            $validSubclient =
                Subclient::query()

                ->where(
                    'id',
                    $subclientId
                )

                ->where(
                    'client_id',
                    $clientId
                )

                ->where(
                    'is_active',
                    true
                )

                ->exists();


            if (!$validSubclient) {

                return response()->json(
                    [
                        'message' =>
                        'El subcliente no pertenece al cliente seleccionado.',
                    ],
                    422
                );
            }
        }


        /*
         * Siempre debe pertenecer al cliente.
         */

        $cargoTypes =
            CargoType::query()

            ->where(
                'is_active',
                true
            )

            ->whereHas(
                'clients',
                function ($query) use (
                    $clientId
                ) {

                    $query->where(
                        'clients.id',
                        $clientId
                    );
                }
            )


            /*
                 * Si existe subcliente,
                 * también debe estar
                 * relacionado expresamente.
                 */
            ->when(
                $subclientId,

                function ($query) use (
                    $subclientId
                ) {

                    $query->whereHas(
                        'subclients',
                        function ($subquery) use (
                            $subclientId
                        ) {

                            $subquery->where(
                                'subclients.id',
                                $subclientId
                            );
                        }
                    );
                }
            )

            ->orderBy('name')

            ->get([
                'cargo_types.id',
                'cargo_types.name',
                'cargo_types.code',
            ]);


        return response()->json(
            $cargoTypes
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

            'clients' =>
            Client::query()

                ->where(
                    'is_active',
                    true
                )

                ->with([
                    'subclients' =>
                    function ($query) {

                        $query
                            ->where(
                                'is_active',
                                true
                            )

                            ->orderBy(
                                'business_name'
                            );
                    },
                ])

                ->orderBy(
                    'business_name'
                )

                ->get(),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN
    |--------------------------------------------------------------------------
    */

    private function validateCargoType(
        Request $request,
        ?CargoType $cargoType = null
    ): array {

        return $request->validate([

            'name' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'cargo_types',
                    'name'
                )
                    ->ignore(
                        $cargoType?->id
                    ),
            ],


            'code' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique(
                    'cargo_types',
                    'code'
                )
                    ->ignore(
                        $cargoType?->id
                    ),
            ],


            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],


            'clients' => [
                'required',
                'array',
                'min:1',
            ],


            'clients.*' => [
                'integer',
                'exists:clients,id',
            ],


            'subclients' => [
                'nullable',
                'array',
            ],


            'subclients.*' => [
                'integer',
                'exists:subclients,id',
            ],

        ], [

            'clients.required' =>
            'Seleccione al menos un cliente para este tipo de carga.',

            'clients.min' =>
            'Seleccione al menos un cliente para este tipo de carga.',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDAR SUBCLIENTES
    |--------------------------------------------------------------------------
    */

    private function validateSubclientsForClients(
        array $clientIds,
        array $subclientIds
    ): void {

        if (
            empty($subclientIds)
        ) {
            return;
        }


        $validCount =
            Subclient::query()

            ->whereIn(
                'id',
                $subclientIds
            )

            ->whereIn(
                'client_id',
                $clientIds
            )

            ->where(
                'is_active',
                true
            )

            ->count();


        if (
            $validCount
            !== count(
                $subclientIds
            )
        ) {

            throw ValidationException::withMessages([

                'subclients' =>
                'Uno o más subclientes seleccionados no pertenecen a los clientes seleccionados.',

            ]);
        }
    }
}
