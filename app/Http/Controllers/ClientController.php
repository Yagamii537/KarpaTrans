<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));

        $clients = Client::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('business_name', 'like', "%{$search}%")
                        ->orWhere('trade_name', 'like', "%{$search}%")
                        ->orWhere('identification', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', compact('clients', 'search'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateClient($request);

        $validated['is_active'] = $request->boolean('is_active');

        Client::create($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente registrado correctamente.');
    }

    public function show(Client $client): View
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $this->validateClient($request, $client);

        $validated['is_active'] = $request->boolean('is_active');

        $client->update($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }

    private function validateClient(Request $request, ?Client $client = null): array
    {
        return $request->validate([
            'business_name' => [
                'required',
                'string',
                'max:255',
            ],

            'trade_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'identification_type' => [
                'required',
                Rule::in(['RUC', 'CEDULA', 'PASAPORTE']),
            ],

            'identification' => [
                'required',
                'string',
                'max:20',
                Rule::unique('clients', 'identification')
                    ->ignore($client?->id),
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

            'secondary_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'free_loading_hours' => [
                'required',
                'integer',
                'min:0',
                'max:240',
            ],

            'free_unloading_hours' => [
                'required',
                'integer',
                'min:0',
                'max:240',
            ],

            'service_time_start' => [
                'required',
                Rule::in([
                    'requested_time',
                    'arrival_time',
                ]),
            ],

            'standby_fraction_minutes' => [
                'required',
                'integer',
                'min:0',
                'max:59',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ], [
            'business_name.required' => 'La razón social es obligatoria.',
            'identification_type.required' => 'Seleccione el tipo de identificación.',
            'identification.required' => 'La identificación es obligatoria.',
            'identification.unique' => 'Ya existe un cliente con esta identificación.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'free_loading_hours.required' => 'Ingrese las horas libres de carga.',
            'free_unloading_hours.required' => 'Ingrese las horas libres de descarga.',
            'service_time_start.required' => 'Seleccione desde cuándo inicia el conteo.',
            'standby_fraction_minutes.required' => 'Ingrese la fracción para stand-by.',
        ]);
    }
}
