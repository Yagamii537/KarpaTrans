<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\CargoType;
use App\Models\Subclient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SubclientController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));
        $clientId = $request->get('client_id');

        $subclients = Subclient::query()
            ->with(['client', 'cargoTypes'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('business_name', 'like', "%{$search}%")
                        ->orWhere('trade_name', 'like', "%{$search}%")
                        ->orWhere('identification', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%");
                });
            })
            ->when($clientId, function ($query) use ($clientId) {
                $query->where('client_id', $clientId);
            })
            ->orderBy('business_name')
            ->paginate(10)
            ->withQueryString();

        $clients = Client::query()
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        return view('subclients.index', compact(
            'subclients',
            'clients',
            'search',
            'clientId'
        ));
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

        return view('subclients.create', compact(
            'clients',
            'cargoTypes'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSubclient($request);

        $validated['is_active'] =
            $request->boolean('is_active');

        $subclient = Subclient::create($validated);

        $cargoTypes = $request->input(
            'cargo_types',
            []
        );

        $subclient->cargoTypes()->sync($cargoTypes);

        return redirect()
            ->route('subclients.index')
            ->with(
                'success',
                'Subcliente registrado correctamente.'
            );
    }

    public function show(Subclient $subclient): View
    {
        $subclient->load([
            'client',
            'cargoTypes',
        ]);

        return view(
            'subclients.show',
            compact('subclient')
        );
    }

    public function edit(Subclient $subclient): View
    {
        $clients = Client::query()
            ->where('is_active', true)
            ->orWhere('id', $subclient->client_id)
            ->orderBy('business_name')
            ->get();

        /*
         * Solo permitimos seleccionar tipos de carga
         * configurados previamente para el cliente.
         */
        $cargoTypes = CargoType::query()
            ->whereHas(
                'clients',
                fn($query) =>
                $query->where(
                    'clients.id',
                    $subclient->client_id
                )
            )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $subclient->load('cargoTypes');

        return view('subclients.edit', compact(
            'subclient',
            'clients',
            'cargoTypes'
        ));
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

        $subclient->update($validated);

        $cargoTypes = $request->input(
            'cargo_types',
            []
        );

        $subclient->cargoTypes()->sync($cargoTypes);

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
        /*
         * No borramos tipos de carga.
         * Solo eliminamos sus relaciones actuales.
         */
        $subclient->cargoTypes()->detach();

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
                    ->ignore($subclient?->id),
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
        ]);
    }
}
