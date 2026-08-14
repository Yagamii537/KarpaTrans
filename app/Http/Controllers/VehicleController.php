<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));
        $status = $request->get('status');

        $vehicles = Vehicle::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('plate', 'like', "%{$search}%")
                        ->orWhere('internal_code', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('chassis_number', 'like', "%{$search}%")
                        ->orWhere('engine_number', 'like', "%{$search}%");
                });
            })
            ->when(
                $status,
                fn($query) =>
                $query->where('operational_status', $status)
            )
            ->orderBy('plate')
            ->paginate(10)
            ->withQueryString();

        return view('vehicles.index', compact(
            'vehicles',
            'search',
            'status'
        ));
    }

    public function create(): View
    {
        return view('vehicles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateVehicle($request);

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated = $this->saveFiles(
            $request,
            $validated
        );

        Vehicle::create($validated);

        return redirect()
            ->route('vehicles.index')
            ->with(
                'success',
                'Vehículo registrado correctamente.'
            );
    }

    public function show(Vehicle $vehicle): View
    {
        return view(
            'vehicles.show',
            compact('vehicle')
        );
    }

    public function edit(Vehicle $vehicle): View
    {
        return view(
            'vehicles.edit',
            compact('vehicle')
        );
    }

    public function update(
        Request $request,
        Vehicle $vehicle
    ): RedirectResponse {

        $validated =
            $this->validateVehicle(
                $request,
                $vehicle
            );

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated = $this->saveFiles(
            $request,
            $validated,
            $vehicle
        );

        $vehicle->update($validated);

        return redirect()
            ->route('vehicles.index')
            ->with(
                'success',
                'Vehículo actualizado correctamente.'
            );
    }

    public function destroy(
        Vehicle $vehicle
    ): RedirectResponse {

        /*
         * El vehículo ya no está ligado
         * permanentemente a ningún chasis.
         *
         * Las asignaciones se manejarán
         * posteriormente desde los viajes.
         */

        $this->deleteFiles($vehicle);

        $vehicle->delete();

        return redirect()
            ->route('vehicles.index')
            ->with(
                'success',
                'Vehículo eliminado correctamente.'
            );
    }

    private function validateVehicle(
        Request $request,
        ?Vehicle $vehicle = null
    ): array {

        return $request->validate([

            'plate' => [
                'required',
                'string',
                'max:15',

                Rule::unique(
                    'vehicles',
                    'plate'
                )->ignore($vehicle?->id),
            ],

            'internal_code' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique(
                    'vehicles',
                    'internal_code'
                )->ignore($vehicle?->id),
            ],

            'brand' => [
                'required',
                'string',
                'max:100',
            ],

            'model' => [
                'required',
                'string',
                'max:100',
            ],

            'year' => [
                'nullable',
                'integer',
                'min:1950',
                'max:' . (date('Y') + 1),
            ],

            'color' => [
                'nullable',
                'string',
                'max:50',
            ],

            'vehicle_type' => [
                'required',

                Rule::in([
                    'TRACTOCAMION',
                    'CAMION',
                    'CAMIONETA',
                    'OTRO',
                ]),
            ],

            'chassis_number' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'vehicles',
                    'chassis_number'
                )->ignore($vehicle?->id),
            ],

            'engine_number' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'vehicles',
                    'engine_number'
                )->ignore($vehicle?->id),
            ],

            'ownership_type' => [
                'required',

                Rule::in([
                    'PROPIO',
                    'ALQUILADO',
                    'TERCERO',
                ]),
            ],

            'owner_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'owner_identification' => [
                'nullable',
                'string',
                'max:20',
            ],

            'fuel_capacity' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'current_odometer' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            /*
             * PESOS Y MEDIDAS
             */

            'tare_weight_kg' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'max_weight_kg' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:tare_weight_kg',
            ],

            'length_m' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'width_m' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'height_m' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            /*
             * DOCUMENTACIÓN
             */

            'registration_expiration_date' => [
                'nullable',
                'date',
            ],

            'technical_review_expiration_date' => [
                'nullable',
                'date',
            ],

            'insurance_expiration_date' => [
                'nullable',
                'date',
            ],

            'operational_status' => [
                'required',

                Rule::in([
                    'AVAILABLE',
                    'ASSIGNED',
                    'MAINTENANCE',
                    'OUT_OF_SERVICE',
                ]),
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'registration_document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'insurance_document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'technical_review_document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

        ], [

            'plate.required' =>
            'La placa es obligatoria.',

            'plate.unique' =>
            'Ya existe un vehículo con esta placa.',

            'brand.required' =>
            'La marca es obligatoria.',

            'model.required' =>
            'El modelo es obligatorio.',

            'operational_status.required' =>
            'Seleccione el estado operativo.',

            'max_weight_kg.gte' =>
            'El peso máximo no puede ser menor que la tara.',

        ]);
    }

    private function saveFiles(
        Request $request,
        array $validated,
        ?Vehicle $vehicle = null
    ): array {

        $fields = [

            'photo' =>
            'vehicles/photos',

            'registration_document' =>
            'vehicles/registrations',

            'insurance_document' =>
            'vehicles/insurance',

            'technical_review_document' =>
            'vehicles/reviews',

        ];

        foreach ($fields as $field => $folder) {

            if ($request->hasFile($field)) {

                if ($vehicle?->{$field}) {

                    Storage::disk('public')
                        ->delete(
                            $vehicle->{$field}
                        );
                }

                $validated[$field] =
                    $request
                    ->file($field)
                    ->store(
                        $folder,
                        'public'
                    );
            }
        }

        return $validated;
    }

    private function deleteFiles(
        Vehicle $vehicle
    ): void {

        $files = [

            $vehicle->photo,

            $vehicle->registration_document,

            $vehicle->insurance_document,

            $vehicle->technical_review_document,

        ];

        foreach ($files as $file) {

            if ($file) {

                Storage::disk('public')
                    ->delete($file);
            }
        }
    }
}
