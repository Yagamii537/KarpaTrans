<?php

namespace App\Http\Controllers;

use App\Models\CargoType;
use App\Models\Client;
use App\Models\Subclient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubclientController extends Controller
{
    public function index(Request $request): View
    {
        $search =
            trim((string) $request->get('search'));

        $clientId =
            $request->get('client_id');

        $subclients = Subclient::query()
            ->with([
                'client',
                'cargoTypes',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        function ($subquery) use ($search) {

                            $subquery
                                ->where(
                                    'business_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'trade_name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'identification',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'contact_name',
                                    'like',
                                    "%{$search}%"
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
            ->orderBy('business_name')
            ->paginate(10)
            ->withQueryString();

        $clients = Client::query()
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        return view(
            'subclients.index',
            compact(
                'subclients',
                'clients',
                'search',
                'clientId'
            )
        );
    }

    public function create(): View
    {
        $clients = Client::query()
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        $cargoTypes = CargoType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'subclients.create',
            compact(
                'clients',
                'cargoTypes'
            )
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $this->validateSubclient($request);

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['inherits_operational_rules'] =
            $request->boolean(
                'inherits_operational_rules'
            );

        /*
         * Si hereda reglas, limpiamos
         * cualquier configuración propia.
         */
        if (
            $validated['inherits_operational_rules']
        ) {

            $validated['free_loading_hours'] =
                null;

            $validated['free_unloading_hours'] =
                null;

            $validated['service_time_start'] =
                null;

            $validated['standby_fraction_minutes'] =
                null;
        }

        $cargoTypes =
            $request->input(
                'cargo_types',
                []
            );

        $this->validateCargoTypesForClient(
            (int) $validated['client_id'],
            $cargoTypes
        );

        $subclient =
            Subclient::create($validated);

        $subclient
            ->cargoTypes()
            ->sync($cargoTypes);

        return redirect()
            ->route('subclients.index')
            ->with(
                'success',
                'Subcliente registrado correctamente.'
            );
    }

    public function show(
        Subclient $subclient
    ): View {

        $subclient->load([
            'client',
            'cargoTypes',
        ]);

        return view(
            'subclients.show',
            compact('subclient')
        );
    }

    public function edit(
        Subclient $subclient
    ): View {

        $clients = Client::query()
            ->where('is_active', true)
            ->orWhere(
                'id',
                $subclient->client_id
            )
            ->orderBy('business_name')
            ->get();

        /*
         * Por ahora mostramos tipos activos.
         * El filtrado dinámico cliente/subcliente
         * lo haremos en el Punto 2.
         */
        $cargoTypes = CargoType::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $subclient->load([
            'client',
            'cargoTypes',
        ]);

        return view(
            'subclients.edit',
            compact(
                'subclient',
                'clients',
                'cargoTypes'
            )
        );
    }

    public function update(
        Request $request,
        Subclient $subclient
    ): RedirectResponse {

        $validated =
            $this->validateSubclient(
                $request,
                $subclient
            );

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['inherits_operational_rules'] =
            $request->boolean(
                'inherits_operational_rules'
            );

        if (
            $validated['inherits_operational_rules']
        ) {

            $validated['free_loading_hours'] =
                null;

            $validated['free_unloading_hours'] =
                null;

            $validated['service_time_start'] =
                null;

            $validated['standby_fraction_minutes'] =
                null;
        }

        $cargoTypes =
            $request->input(
                'cargo_types',
                []
            );

        $this->validateCargoTypesForClient(
            (int) $validated['client_id'],
            $cargoTypes
        );

        $subclient->update($validated);

        $subclient
            ->cargoTypes()
            ->sync($cargoTypes);

        return redirect()
            ->route('subclients.index')
            ->with(
                'success',
                'Subcliente actualizado correctamente.'
            );
    }

    public function destroy(
        Subclient $subclient
    ): RedirectResponse {

        $subclient
            ->cargoTypes()
            ->detach();

        $subclient->delete();

        return redirect()
            ->route('subclients.index')
            ->with(
                'success',
                'Subcliente eliminado correctamente.'
            );
    }

    private function validateSubclient(
        Request $request,
        ?Subclient $subclient = null
    ): array {

        $inherits =
            $request->boolean(
                'inherits_operational_rules'
            );

        return $request->validate([

            'client_id' => [
                'required',
                'exists:clients,id',
            ],

            'business_name' => [
                'required',
                'string',
                'max:255',

                Rule::unique(
                    'subclients',
                    'business_name'
                )
                    ->where(
                        fn($query) =>
                        $query->where(
                            'client_id',
                            $request->client_id
                        )
                    )
                    ->ignore(
                        $subclient?->id
                    ),
            ],

            'trade_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'identification_type' => [
                'nullable',

                Rule::in([
                    'RUC',
                    'CEDULA',
                    'PASAPORTE',
                    'OTRO',
                ]),
            ],

            'identification' => [
                'nullable',
                'string',
                'max:20',
            ],

            'contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            /*
             * REGLAS OPERATIVAS
             */

            'free_loading_hours' => [
                $inherits
                    ? 'nullable'
                    : 'required',

                'integer',
                'min:0',
                'max:999',
            ],

            'free_unloading_hours' => [
                $inherits
                    ? 'nullable'
                    : 'required',

                'integer',
                'min:0',
                'max:999',
            ],

            'service_time_start' => [
                $inherits
                    ? 'nullable'
                    : 'required',

                Rule::in([
                    'requested_time',
                    'arrival_time',
                ]),
            ],

            'standby_fraction_minutes' => [
                $inherits
                    ? 'nullable'
                    : 'required',

                'integer',
                'min:1',
                'max:1440',
            ],

            /*
             * CARGAS
             */

            'cargo_types' => [
                'nullable',
                'array',
            ],

            'cargo_types.*' => [
                'exists:cargo_types,id',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

        ], [

            'client_id.required' =>
            'Seleccione el cliente principal.',

            'business_name.required' =>
            'El nombre del subcliente es obligatorio.',

            'business_name.unique' =>
            'Ya existe un subcliente con este nombre para el cliente seleccionado.',

            'free_loading_hours.required' =>
            'Ingrese las horas libres de carga.',

            'free_unloading_hours.required' =>
            'Ingrese las horas libres de descarga.',

            'service_time_start.required' =>
            'Seleccione desde qué momento inicia el conteo.',

            'standby_fraction_minutes.required' =>
            'Ingrese la fracción utilizada para Stand-by.',

        ]);
    }

    private function validateCargoTypesForClient(
        int $clientId,
        array $cargoTypeIds
    ): void {

        if (empty($cargoTypeIds)) {
            return;
        }

        $validIds =
            DB::table(
                'client_cargo_types'
            )
            ->where(
                'client_id',
                $clientId
            )
            ->whereIn(
                'cargo_type_id',
                $cargoTypeIds
            )
            ->pluck(
                'cargo_type_id'
            )
            ->map(
                fn($id) => (int) $id
            )
            ->toArray();


        $requestedIds =
            collect($cargoTypeIds)
            ->map(
                fn($id) => (int) $id
            )
            ->unique()
            ->values()
            ->toArray();


        $invalid =
            array_diff(
                $requestedIds,
                $validIds
            );


        if (!empty($invalid)) {

            throw ValidationException::withMessages([

                'cargo_types' =>
                'Uno o más tipos de carga no están habilitados para el cliente principal.',

            ]);
        }
    }
}
