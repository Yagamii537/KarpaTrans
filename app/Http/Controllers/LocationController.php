<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));
        $type = $request->get('type');
        $status = $request->get('status');

        $locations = Location::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('province', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->when($status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('locations.index', compact(
            'locations',
            'search',
            'type',
            'status'
        ));
    }

    public function create(): View
    {
        return view('locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateLocation($request);

        $validated['receives_empty_containers'] =
            $request->boolean('receives_empty_containers');

        $validated['receives_full_containers'] =
            $request->boolean('receives_full_containers');

        $validated['requires_appointment'] =
            $request->boolean('requires_appointment');

        $validated['is_active'] =
            $request->boolean('is_active');

        Location::create($validated);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación registrada correctamente.');
    }

    public function show(Location $location): View
    {
        return view('locations.show', compact('location'));
    }

    public function edit(Location $location): View
    {
        return view('locations.edit', compact('location'));
    }

    public function update(
        Request $request,
        Location $location
    ): RedirectResponse {
        $validated = $this->validateLocation($request, $location);

        $validated['receives_empty_containers'] =
            $request->boolean('receives_empty_containers');

        $validated['receives_full_containers'] =
            $request->boolean('receives_full_containers');

        $validated['requires_appointment'] =
            $request->boolean('requires_appointment');

        $validated['is_active'] =
            $request->boolean('is_active');

        $location->update($validated);

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación actualizada correctamente.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()
            ->route('locations.index')
            ->with('success', 'Ubicación eliminada correctamente.');
    }

    private function validateLocation(
        Request $request,
        ?Location $location = null
    ): array {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('locations', 'code')
                    ->ignore($location?->id),
            ],

            'type' => [
                'required',
                Rule::in([
                    'PORT',
                    'DEPOT',
                    'YARD',
                    'WAREHOUSE',
                    'EXTERNAL_PLANT',
                    'WORKSHOP',
                    'CUSTOMER_LOCATION',
                    'OTHER',
                ]),
            ],

            'city' => [
                'nullable',
                'string',
                'max:150',
            ],

            'province' => [
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

            'opening_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'closing_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'code.unique' => 'Ya existe una ubicación con este código.',
            'type.required' => 'Seleccione el tipo de ubicación.',
            'address.required' => 'La dirección es obligatoria.',
            'email.email' => 'Ingrese un correo electrónico válido.',
        ]);
    }
}
