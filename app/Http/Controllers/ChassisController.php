<?php

namespace App\Http\Controllers;

use App\Models\Chassis;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ChassisController extends Controller
{
    public function index(Request $request): View
    {
        $search =
            trim((string) $request->get('search'));

        $status =
            $request->get('status');

        $chassisList = Chassis::query()
            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        function ($subquery) use ($search) {

                            $subquery
                                ->where(
                                    'code',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'plate',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'serial_number',
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
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view(
            'chassis.index',
            compact(
                'chassisList',
                'search',
                'status'
            )
        );
    }

    public function create(): View
    {
        return view('chassis.create');
    }

    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $this->validateChassis($request);

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['supports_20ft'] =
            $request->boolean('supports_20ft');

        $validated['supports_40ft'] =
            $request->boolean('supports_40ft');

        $validated['supports_reefer'] =
            $request->boolean('supports_reefer');

        $validated =
            $this->saveFiles(
                $request,
                $validated
            );

        Chassis::create($validated);

        return redirect()
            ->route('chassis.index')
            ->with(
                'success',
                'Chasis registrado correctamente.'
            );
    }

    public function show(
        Chassis $chassis
    ): View {

        return view(
            'chassis.show',
            compact('chassis')
        );
    }

    public function edit(
        Chassis $chassis
    ): View {

        return view(
            'chassis.edit',
            compact('chassis')
        );
    }

    public function update(
        Request $request,
        Chassis $chassis
    ): RedirectResponse {

        $validated =
            $this->validateChassis(
                $request,
                $chassis
            );

        $validated['is_active'] =
            $request->boolean('is_active');

        $validated['supports_20ft'] =
            $request->boolean('supports_20ft');

        $validated['supports_40ft'] =
            $request->boolean('supports_40ft');

        $validated['supports_reefer'] =
            $request->boolean('supports_reefer');

        $validated =
            $this->saveFiles(
                $request,
                $validated,
                $chassis
            );

        $chassis->update($validated);

        return redirect()
            ->route('chassis.index')
            ->with(
                'success',
                'Chasis actualizado correctamente.'
            );
    }

    public function destroy(
        Chassis $chassis
    ): RedirectResponse {

        $this->deleteFiles($chassis);

        $chassis->delete();

        return redirect()
            ->route('chassis.index')
            ->with(
                'success',
                'Chasis eliminado correctamente.'
            );
    }

    private function validateChassis(
        Request $request,
        ?Chassis $chassis = null
    ): array {

        return $request->validate([

            'code' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'chassis',
                    'code'
                )->ignore($chassis?->id),
            ],

            'plate' => [
                'nullable',
                'string',
                'max:20',

                Rule::unique(
                    'chassis',
                    'plate'
                )->ignore($chassis?->id),
            ],

            'chassis_type' => [
                'required',

                Rule::in([
                    'PORTACONTENEDOR',
                    'EXTENSIBLE',
                    'PLATAFORMA',
                    'CAMA_BAJA',
                    'OTRO',
                ]),
            ],

            'brand' => [
                'nullable',
                'string',
                'max:100',
            ],

            'model' => [
                'nullable',
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

            'serial_number' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'chassis',
                    'serial_number'
                )->ignore($chassis?->id),
            ],

            'axles' => [
                'nullable',
                'integer',
                'min:1',
                'max:10',
            ],

            'maximum_capacity_tons' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'registration_expiration_date' => [
                'nullable',
                'date',
            ],

            'technical_review_expiration_date' => [
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

            'code.required' =>
            'El código del chasis es obligatorio.',

            'code.unique' =>
            'Ya existe un chasis con este código.',

            'chassis_type.required' =>
            'Seleccione el tipo de chasis.',

        ]);
    }

    private function saveFiles(
        Request $request,
        array $validated,
        ?Chassis $chassis = null
    ): array {

        $fields = [

            'photo' =>
            'chassis/photos',

            'registration_document' =>
            'chassis/registrations',

            'technical_review_document' =>
            'chassis/reviews',

        ];

        foreach ($fields as $field => $folder) {

            if ($request->hasFile($field)) {

                if ($chassis?->{$field}) {

                    Storage::disk('public')
                        ->delete(
                            $chassis->{$field}
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
        Chassis $chassis
    ): void {

        $files = [

            $chassis->photo,

            $chassis->registration_document,

            $chassis->technical_review_document,

        ];

        foreach ($files as $file) {

            if ($file) {

                Storage::disk('public')
                    ->delete($file);
            }
        }
    }
}
