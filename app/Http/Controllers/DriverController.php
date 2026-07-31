<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));
        $status = $request->get('status');
        $licenseStatus = $request->get('license_status');

        $drivers = Driver::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('first_names', 'like', "%{$search}%")
                        ->orWhere('last_names', 'like', "%{$search}%")
                        ->orWhere('identification', 'like', "%{$search}%")
                        ->orWhere('license_number', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->when($licenseStatus === 'expired', function ($query) {
                $query->whereDate(
                    'license_expiration_date',
                    '<',
                    now()->toDateString()
                );
            })
            ->when($licenseStatus === 'expiring', function ($query) {
                $query
                    ->whereDate(
                        'license_expiration_date',
                        '>=',
                        now()->toDateString()
                    )
                    ->whereDate(
                        'license_expiration_date',
                        '<=',
                        now()->addDays(30)->toDateString()
                    );
            })
            ->when($licenseStatus === 'valid', function ($query) {
                $query->whereDate(
                    'license_expiration_date',
                    '>',
                    now()->addDays(30)->toDateString()
                );
            })
            ->orderBy('last_names')
            ->orderBy('first_names')
            ->paginate(10)
            ->withQueryString();

        return view('drivers.index', compact(
            'drivers',
            'search',
            'status',
            'licenseStatus'
        ));
    }

    public function create(): View
    {
        return view('drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateDriver($request);

        $validated['is_active'] = $request->boolean('is_active');

        $validated = $this->storeUploadedFiles(
            $request,
            $validated
        );

        Driver::create($validated);

        return redirect()
            ->route('drivers.index')
            ->with('success', 'Conductor registrado correctamente.');
    }

    public function show(Driver $driver): View
    {
        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver): View
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(
        Request $request,
        Driver $driver
    ): RedirectResponse {
        $validated = $this->validateDriver($request, $driver);

        $validated['is_active'] = $request->boolean('is_active');

        $validated = $this->storeUploadedFiles(
            $request,
            $validated,
            $driver
        );

        $driver->update($validated);

        return redirect()
            ->route('drivers.index')
            ->with('success', 'Conductor actualizado correctamente.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $this->deleteDriverFiles($driver);

        $driver->delete();

        return redirect()
            ->route('drivers.index')
            ->with('success', 'Conductor eliminado correctamente.');
    }

    private function validateDriver(
        Request $request,
        ?Driver $driver = null
    ): array {
        return $request->validate([
            'first_names' => [
                'required',
                'string',
                'max:150',
            ],

            'last_names' => [
                'required',
                'string',
                'max:150',
            ],

            'identification' => [
                'required',
                'string',
                'max:20',
                Rule::unique('drivers', 'identification')
                    ->ignore($driver?->id),
            ],

            'birth_date' => [
                'nullable',
                'date',
                'before:today',
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

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'license_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('drivers', 'license_number')
                    ->ignore($driver?->id),
            ],

            'license_type' => [
                'required',
                Rule::in([
                    'A',
                    'A1',
                    'B',
                    'C',
                    'C1',
                    'D',
                    'D1',
                    'E',
                    'E1',
                    'F',
                    'G',
                ]),
            ],

            'license_issue_date' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'license_expiration_date' => [
                'required',
                'date',
                'after:license_issue_date',
            ],

            'license_points' => [
                'nullable',
                'integer',
                'min:0',
                'max:30',
            ],

            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'emergency_contact_relationship' => [
                'nullable',
                'string',
                'max:100',
            ],

            'hire_date' => [
                'nullable',
                'date',
            ],

            'employee_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('drivers', 'employee_code')
                    ->ignore($driver?->id),
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'identification_document' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            'license_document' => [
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
            'first_names.required' => 'Los nombres son obligatorios.',
            'last_names.required' => 'Los apellidos son obligatorios.',
            'identification.required' => 'La cédula es obligatoria.',
            'identification.unique' => 'Ya existe un conductor con esta cédula.',
            'license_number.required' => 'El número de licencia es obligatorio.',
            'license_number.unique' => 'Ya existe un conductor con este número de licencia.',
            'license_type.required' => 'Seleccione el tipo de licencia.',
            'license_expiration_date.required' => 'La fecha de vencimiento es obligatoria.',
            'license_expiration_date.after' => 'La fecha de vencimiento debe ser posterior a la fecha de emisión.',
            'email.email' => 'Ingrese un correo electrónico válido.',
            'photo.image' => 'La fotografía debe ser una imagen.',
            'photo.max' => 'La fotografía no debe superar los 4 MB.',
        ]);
    }

    private function storeUploadedFiles(
        Request $request,
        array $validated,
        ?Driver $driver = null
    ): array {
        if ($request->hasFile('photo')) {
            if ($driver?->photo) {
                Storage::disk('public')->delete($driver->photo);
            }

            $validated['photo'] = $request
                ->file('photo')
                ->store('drivers/photos', 'public');
        }

        if ($request->hasFile('identification_document')) {
            if ($driver?->identification_document) {
                Storage::disk('public')
                    ->delete($driver->identification_document);
            }

            $validated['identification_document'] = $request
                ->file('identification_document')
                ->store('drivers/identifications', 'public');
        }

        if ($request->hasFile('license_document')) {
            if ($driver?->license_document) {
                Storage::disk('public')
                    ->delete($driver->license_document);
            }

            $validated['license_document'] = $request
                ->file('license_document')
                ->store('drivers/licenses', 'public');
        }

        return $validated;
    }

    private function deleteDriverFiles(Driver $driver): void
    {
        $files = [
            $driver->photo,
            $driver->identification_document,
            $driver->license_document,
        ];

        foreach ($files as $file) {
            if ($file) {
                Storage::disk('public')->delete($file);
            }
        }
    }
}
