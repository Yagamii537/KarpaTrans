<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Driver;
use App\Models\DriverRestriction;
use App\Models\Location;
use App\Models\Plant;
use App\Models\Subclient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DriverRestrictionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->get('search'));

        $restrictions = DriverRestriction::query()
            ->with([
                'driver',
                'client',
                'subclient',
                'plant',
                'location',
                'creator',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subquery) use ($search) {
                    $subquery
                        ->where('reason', 'like', "%{$search}%")
                        ->orWhereHas('driver', function ($q) use ($search) {
                            $q->where('first_names', 'like', "%{$search}%")
                                ->orWhere('last_names', 'like', "%{$search}%")
                                ->orWhere('identification', 'like', "%{$search}%");
                        })
                        ->orWhereHas('client', function ($q) use ($search) {
                            $q->where('business_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('driver-restrictions.index', compact(
            'restrictions',
            'search'
        ));
    }

    public function create(): View
    {
        return view('driver-restrictions.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRestriction($request);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = Auth::id();

        if ($validated['restriction_type'] === 'INDEFINITE') {
            $validated['end_date'] = null;
        }

        DriverRestriction::create($validated);

        return redirect()
            ->route('driver-restrictions.index')
            ->with('success', 'Restricción registrada correctamente.');
    }

    public function edit(DriverRestriction $driverRestriction): View
    {
        return view(
            'driver-restrictions.edit',
            array_merge(
                $this->formData(),
                compact('driverRestriction')
            )
        );
    }

    public function update(
        Request $request,
        DriverRestriction $driverRestriction
    ): RedirectResponse {
        $validated = $this->validateRestriction($request);

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['restriction_type'] === 'INDEFINITE') {
            $validated['end_date'] = null;
        }

        $driverRestriction->update($validated);

        return redirect()
            ->route('driver-restrictions.index')
            ->with('success', 'Restricción actualizada correctamente.');
    }

    public function destroy(
        DriverRestriction $driverRestriction
    ): RedirectResponse {
        $driverRestriction->delete();

        return redirect()
            ->route('driver-restrictions.index')
            ->with('success', 'Restricción eliminada correctamente.');
    }

    private function formData(): array
    {
        return [
            'drivers' => Driver::where('is_active', true)
                ->orderBy('last_names')
                ->get(),

            'clients' => Client::where('is_active', true)
                ->orderBy('business_name')
                ->get(),

            'subclients' => Subclient::where('is_active', true)
                ->orderBy('business_name')
                ->get(),

            'plants' => Plant::where('is_active', true)
                ->orderBy('name')
                ->get(),

            'locations' => Location::where('is_active', true)
                ->orderBy('name')
                ->get(),
        ];
    }

    private function validateRestriction(Request $request): array
    {
        return $request->validate([
            'driver_id' => [
                'required',
                'exists:drivers,id',
            ],

            'client_id' => [
                'nullable',
                'exists:clients,id',
            ],

            'subclient_id' => [
                'nullable',
                'exists:subclients,id',
            ],

            'plant_id' => [
                'nullable',
                'exists:plants,id',
            ],

            'location_id' => [
                'nullable',
                'exists:locations,id',
            ],

            'reason' => [
                'required',
                'string',
                'max:2000',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],

            'restriction_type' => [
                'required',
                Rule::in([
                    'TEMPORARY',
                    'INDEFINITE',
                ]),
            ],

            'action_type' => [
                'required',
                Rule::in([
                    'BLOCK',
                    'WARNING',
                ]),
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ]);
    }
}
