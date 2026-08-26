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

        $status =
            $request->get('status');


        $vehicles =
            Vehicle::query()

            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        function ($subquery) use ($search) {

                            $subquery
                                ->where(
                                    'plate',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'internal_code',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'brand',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'model',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'chassis_number',
                                    'like',
                                    "%{$search}%"
                                )

                                ->orWhere(
                                    'engine_number',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
                }
            )

            ->when(
                $status,
                fn($query) =>
                $query->where(
                    'operational_status',
                    $status
                )
            )

            ->orderBy('plate')

            ->paginate(10)

            ->withQueryString();


        return view(
            'vehicles.index',
            compact(
                'vehicles',
                'search',
                'status'
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
            'vehicles.create'
        );
    }


    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $this->validateVehicle(
                $request
            );


        $validated['is_active'] =
            $request->boolean(
                'is_active'
            );


        /*
         * Compatibilidad temporal.
         *
         * Si max_weight_kg todavía existe
         * en la BD, guardaremos aquí el
         * peso bruto.
         */
        if (
            !empty($validated['gross_weight_kg'])
        ) {

            $validated['max_weight_kg'] =
                $validated['gross_weight_kg'];
        }


        $validated =
            $this->saveFiles(
                $request,
                $validated
            );


        Vehicle::create(
            $validated
        );


        return redirect()
            ->route(
                'vehicles.index'
            )
            ->with(
                'success',
                'Vehículo registrado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DETALLE
    |--------------------------------------------------------------------------
    */

    public function show(
        Vehicle $vehicle
    ): View {

        $vehicle->load([
            'assignments.trip',
        ]);


        return view(
            'vehicles.show',
            compact('vehicle')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

    public function edit(
        Vehicle $vehicle
    ): View {

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
            $request->boolean(
                'is_active'
            );


        if (
            !empty($validated['gross_weight_kg'])
        ) {

            $validated['max_weight_kg'] =
                $validated['gross_weight_kg'];
        }


        $validated =
            $this->saveFiles(
                $request,
                $validated,
                $vehicle
            );


        $vehicle->update(
            $validated
        );


        return redirect()
            ->route(
                'vehicles.index'
            )
            ->with(
                'success',
                'Vehículo actualizado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Vehicle $vehicle
    ): RedirectResponse {

        /*
         * Si ya tiene historial de
         * asignaciones no debemos eliminarlo.
         */
        if (
            $vehicle
            ->assignments()
            ->exists()
        ) {

            return back()
                ->withErrors([

                    'delete' =>
                    'No se puede eliminar el vehículo porque tiene historial de viajes o asignaciones.',

                ]);
        }


        $this->deleteFiles(
            $vehicle
        );


        $vehicle->delete();


        return redirect()
            ->route(
                'vehicles.index'
            )
            ->with(
                'success',
                'Vehículo eliminado correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN
    |--------------------------------------------------------------------------
    */

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
                )
                    ->ignore(
                        $vehicle?->id
                    ),
            ],


            'internal_code' => [
                'nullable',
                'string',
                'max:50',

                Rule::unique(
                    'vehicles',
                    'internal_code'
                )
                    ->ignore(
                        $vehicle?->id
                    ),
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
                'max:' . (
                    date('Y') + 1
                ),
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
                )
                    ->ignore(
                        $vehicle?->id
                    ),
            ],


            'engine_number' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'vehicles',
                    'engine_number'
                )
                    ->ignore(
                        $vehicle?->id
                    ),
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
             * DATOS TÉCNICOS
             */

            'tare_weight_kg' => [
                'nullable',
                'numeric',
                'min:0',
            ],


            'gross_weight_kg' => [
                'nullable',
                'numeric',
                'min:0',
                'gte:tare_weight_kg',
            ],


            'max_load_capacity_kg' => [
                'nullable',
                'numeric',
                'min:0',
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


            'axles' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],


            'volume_m3' => [
                'nullable',
                'numeric',
                'min:0',
            ],


            /*
             * DOCUMENTOS
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

            'vehicle_type.required' =>
            'Seleccione el tipo de vehículo.',

            'ownership_type.required' =>
            'Seleccione el tipo de propiedad.',

            'gross_weight_kg.gte' =>
            'El peso bruto no puede ser menor que la tara.',

            'operational_status.required' =>
            'Seleccione el estado operativo.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | ARCHIVOS
    |--------------------------------------------------------------------------
    */

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


        foreach (
            $fields
            as $field => $folder
        ) {

            if (
                !$request->hasFile(
                    $field
                )
            ) {
                continue;
            }


            if (
                $vehicle
                &&
                $vehicle->{$field}
            ) {

                Storage::disk(
                    'public'
                )->delete(
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


        return $validated;
    }


    private function deleteFiles(
        Vehicle $vehicle
    ): void {

        foreach (
            [
                $vehicle->photo,
                $vehicle->registration_document,
                $vehicle->insurance_document,
                $vehicle->technical_review_document,
            ] as $file
        ) {

            if ($file) {

                Storage::disk(
                    'public'
                )->delete(
                    $file
                );
            }
        }
    }
}
