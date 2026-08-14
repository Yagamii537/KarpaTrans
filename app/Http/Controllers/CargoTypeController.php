<?php

namespace App\Http\Controllers;

use App\Models\CargoType;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CargoTypeController extends Controller
{
    public function index(Request $request): View
    {
        $search =
            trim((string) $request->get('search'));

        $cargoTypes = CargoType::query()
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

    public function create(): View
    {
        $clients = Client::query()
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        return view(
            'cargo-types.create',
            compact('clients')
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {
        $validated =
            $this->validateCargoType($request);

        $validated['is_active'] =
            $request->boolean('is_active');

        $cargoType =
            CargoType::create($validated);

        $cargoType
            ->clients()
            ->sync(
                $request->input(
                    'clients',
                    []
                )
            );

        return redirect()
            ->route('cargo-types.index')
            ->with(
                'success',
                'Tipo de carga registrado correctamente.'
            );
    }

    public function show(
        CargoType $cargoType
    ): View {
        $cargoType->load([
            'clients',
            'subclients.client',
        ]);

        return view(
            'cargo-types.show',
            compact('cargoType')
        );
    }

    public function edit(
        CargoType $cargoType
    ): View {
        $clients = Client::query()
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        $cargoType->load('clients');

        return view(
            'cargo-types.edit',
            compact(
                'cargoType',
                'clients'
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

        $validated['is_active'] =
            $request->boolean('is_active');

        $cargoType->update($validated);

        $cargoType
            ->clients()
            ->sync(
                $request->input(
                    'clients',
                    []
                )
            );

        return redirect()
            ->route('cargo-types.index')
            ->with(
                'success',
                'Tipo de carga actualizado correctamente.'
            );
    }

    public function destroy(
        CargoType $cargoType
    ): RedirectResponse {
        /*
         * Para preservar historial futuro,
         * lo eliminamos de forma lógica.
         */
        $cargoType->delete();

        return redirect()
            ->route('cargo-types.index')
            ->with(
                'success',
                'Tipo de carga eliminado correctamente.'
            );
    }

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
                )->ignore($cargoType?->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique(
                    'cargo_types',
                    'code'
                )->ignore($cargoType?->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'clients' => [
                'nullable',
                'array',
            ],

            'clients.*' => [
                'exists:clients,id',
            ],
        ]);
    }
}
