<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\ContainerMovement;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContainerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));
        $status = $request->get('status');
        $size = $request->get('size');

        $containers = Container::query()
            ->with('currentLocation')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('container_number', 'like', "%{$search}%")
                        ->orWhere('seal_number', 'like', "%{$search}%")
                        ->orWhere('shipping_line', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                $query->where('operational_status', $status);
            })
            ->when($size, function ($query) use ($size) {
                $query->where('container_size', $size);
            })
            ->orderBy('container_number')
            ->paginate(10)
            ->withQueryString();

        return view('containers.index', compact(
            'containers',
            'search',
            'status',
            'size'
        ));
    }

    public function create(): View
    {
        $locations = $this->locations();

        return view(
            'containers.create',
            compact('locations')
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateContainer($request);

        $validated['is_active'] =
            $request->boolean('is_active');

        $container = Container::create($validated);

        if ($container->current_location_id) {
            ContainerMovement::create([
                'container_id' => $container->id,
                'from_location_id' => null,
                'to_location_id' => $container->current_location_id,
                'movement_type' => 'INITIAL',
                'movement_at' => now(),
                'load_status' => $container->load_status,
                'seal_number' => $container->seal_number,
                'notes' => 'Ubicación inicial del contenedor.',
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('containers.index')
            ->with(
                'success',
                'Contenedor registrado correctamente.'
            );
    }

    public function show(Container $container): View
    {
        $container->load([
            'currentLocation',
            'movements.fromLocation',
            'movements.toLocation',
            'movements.creator',
        ]);

        return view(
            'containers.show',
            compact('container')
        );
    }

    public function edit(Container $container): View
    {
        $locations = $this->locations();

        return view(
            'containers.edit',
            compact(
                'container',
                'locations'
            )
        );
    }

    public function update(
        Request $request,
        Container $container
    ): RedirectResponse {

        $validated = $this->validateContainer(
            $request,
            $container
        );

        $validated['is_active'] =
            $request->boolean('is_active');

        $oldLocation =
            $container->current_location_id;

        $newLocation =
            $validated['current_location_id'] ?? null;

        $oldLoadStatus =
            $container->load_status;

        $container->update($validated);

        if ($oldLocation != $newLocation) {

            ContainerMovement::create([
                'container_id' => $container->id,
                'from_location_id' => $oldLocation,
                'to_location_id' => $newLocation,
                'movement_type' => 'TRANSFER',
                'movement_at' => now(),
                'load_status' => $container->load_status,
                'seal_number' => $container->seal_number,
                'notes' => 'Cambio de ubicación registrado desde la ficha del contenedor.',
                'created_by' => Auth::id(),
            ]);
        } elseif (
            $oldLoadStatus !== $container->load_status
        ) {

            ContainerMovement::create([
                'container_id' => $container->id,
                'from_location_id' => $container->current_location_id,
                'to_location_id' => $container->current_location_id,
                'movement_type' => 'OTHER',
                'movement_at' => now(),
                'load_status' => $container->load_status,
                'seal_number' => $container->seal_number,
                'notes' => 'Cambio del estado de carga del contenedor.',
                'created_by' => Auth::id(),
            ]);
        }

        return redirect()
            ->route('containers.index')
            ->with(
                'success',
                'Contenedor actualizado correctamente.'
            );
    }

    public function destroy(
        Container $container
    ): RedirectResponse {

        $container->delete();

        return redirect()
            ->route('containers.index')
            ->with(
                'success',
                'Contenedor eliminado correctamente.'
            );
    }

    private function locations()
    {
        return Location::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function validateContainer(
        Request $request,
        ?Container $container = null
    ): array {

        return $request->validate([

            'container_number' => [
                'required',
                'string',
                'max:30',
                Rule::unique(
                    'containers',
                    'container_number'
                )->ignore($container?->id),
            ],

            'container_type' => [
                'required',
                Rule::in([
                    'DRY',
                    'REEFER',
                    'OPEN_TOP',
                    'FLAT_RACK',
                    'TANK',
                    'OTHER',
                ]),
            ],

            'container_size' => [
                'required',
                Rule::in([
                    '20FT',
                    '40FT',
                    '40HC',
                    '45FT',
                    'OTHER',
                ]),
            ],

            'load_status' => [
                'required',
                Rule::in([
                    'EMPTY',
                    'FULL',
                    'UNKNOWN',
                ]),
            ],

            'operational_status' => [
                'required',
                Rule::in([
                    'AVAILABLE',
                    'ASSIGNED',
                    'IN_TRANSIT',
                    'AT_CLIENT',
                    'AT_PORT',
                    'AT_DEPOT',
                    'MAINTENANCE',
                    'OUT_OF_SERVICE',
                ]),
            ],

            'current_location_id' => [
                'nullable',
                'exists:locations,id',
            ],

            'seal_number' => [
                'nullable',
                'string',
                'max:50',
            ],

            'tare_weight_kg' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'max_gross_weight_kg' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:tare_weight_kg',
            ],

            'shipping_line' => [
                'nullable',
                'string',
                'max:255',
            ],

            'last_inspection_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

        ], [

            'container_number.required' =>
            'El número del contenedor es obligatorio.',

            'container_number.unique' =>
            'Ya existe un contenedor con este número.',

            'max_gross_weight_kg.gte' =>
            'El peso bruto máximo no puede ser menor que la tara.',

        ]);
    }
}
