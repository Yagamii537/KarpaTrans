<?php

namespace App\Http\Controllers;

use App\Models\CargoType;
use App\Models\Client;
use App\Models\Location;
use App\Models\Plant;
use App\Models\Subclient;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    public function index(Request $request): View
    {
        $search =
            trim((string) $request->get('search'));

        $status =
            $request->get('status');

        $clientId =
            $request->get('client_id');

        $operationType =
            $request->get('operation_type');

        $workOrders = WorkOrder::query()
            ->with([
                'client',
                'subclient',
                'cargoType',
                'plant',
                'originLocation',
                'originPlant',
                'destinationLocation',
                'destinationPlant',
            ])
            ->when(
                $search !== '',
                function ($query) use ($search) {

                    $query->where(
                        function ($subquery) use ($search) {

                            $subquery
                                ->where(
                                    'work_order_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'booking_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'customer_order_number',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhereHas(
                                    'client',
                                    function ($clientQuery) use ($search) {

                                        $clientQuery->where(
                                            'business_name',
                                            'like',
                                            "%{$search}%"
                                        );
                                    }
                                );
                        }
                    );
                }
            )
            ->when(
                $status,
                fn($query) =>
                $query->where(
                    'status',
                    $status
                )
            )
            ->when(
                $clientId,
                fn($query) =>
                $query->where(
                    'client_id',
                    $clientId
                )
            )
            ->when(
                $operationType,
                fn($query) =>
                $query->where(
                    'operation_type',
                    $operationType
                )
            )
            ->orderByDesc('requested_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $clients = Client::query()
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get();

        return view(
            'work-orders.index',
            compact(
                'workOrders',
                'clients',
                'search',
                'status',
                'clientId',
                'operationType'
            )
        );
    }

    public function create(): View
    {
        return view(
            'work-orders.create',
            $this->formData()
        );
    }

    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $this->validateWorkOrder($request);

        $validated =
            $this->normalizeLocations(
                $validated
            );

        /*
         * Validamos que subcliente, carga y planta
         * realmente pertenezcan/configuren con el cliente.
         */
        $this->validateBusinessRelations(
            $validated
        );

        $validated['work_order_number'] =
            $this->generateWorkOrderNumber();

        $validated['created_by'] =
            Auth::id();

        $validated['updated_by'] =
            Auth::id();

        $workOrder = WorkOrder::create(
            $validated
        );

        return redirect()
            ->route(
                'work-orders.show',
                $workOrder
            )
            ->with(
                'success',
                'Orden de trabajo creada correctamente.'
            );
    }

    public function show(
        WorkOrder $workOrder
    ): View {

        $workOrder->load([
            'client',
            'subclient',
            'cargoType',
            'plant',
            'originLocation',
            'originPlant',
            'destinationLocation',
            'destinationPlant',
            'creator',
            'updater',
            'trips.activeAssignment.driver',
            'trips.activeAssignment.vehicle',
        ]);

        return view(
            'work-orders.show',
            compact('workOrder')
        );
    }

    public function edit(
        WorkOrder $workOrder
    ): View {

        return view(
            'work-orders.edit',
            array_merge(
                $this->formData(),
                compact('workOrder')
            )
        );
    }

    public function update(
        Request $request,
        WorkOrder $workOrder
    ): RedirectResponse {

        $validated =
            $this->validateWorkOrder(
                $request,
                $workOrder
            );

        $validated =
            $this->normalizeLocations(
                $validated
            );

        $this->validateBusinessRelations(
            $validated
        );

        $validated['updated_by'] =
            Auth::id();

        $workOrder->update(
            $validated
        );

        return redirect()
            ->route(
                'work-orders.show',
                $workOrder
            )
            ->with(
                'success',
                'Orden de trabajo actualizada correctamente.'
            );
    }

    public function destroy(
        WorkOrder $workOrder
    ): RedirectResponse {

        /*
         * Más adelante, cuando existan viajes,
         * aquí bloquearemos la eliminación
         * si existen viajes relacionados.
         */

        $workOrder->delete();

        return redirect()
            ->route('work-orders.index')
            ->with(
                'success',
                'Orden de trabajo eliminada correctamente.'
            );
    }

    /*
     |--------------------------------------------------------------------------
     | DATOS PARA FORMULARIO
     |--------------------------------------------------------------------------
     */

    private function formData(): array
    {
        return [

            'clients' => Client::query()
                ->where('is_active', true)
                ->orderBy('business_name')
                ->get(),

            'subclients' => Subclient::query()
                ->where('is_active', true)
                ->orderBy('business_name')
                ->get(),

            'cargoTypes' => CargoType::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),

            'plants' => Plant::query()
                ->where('is_active', true)
                ->with('client')
                ->orderBy('name')
                ->get(),

            'locations' => Location::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ];
    }

    /*
     |--------------------------------------------------------------------------
     | VALIDACIÓN
     |--------------------------------------------------------------------------
     */

    private function validateWorkOrder(
        Request $request,
        ?WorkOrder $workOrder = null
    ): array {

        return $request->validate([

            'client_id' => [
                'required',
                'exists:clients,id',
            ],

            'subclient_id' => [
                'nullable',
                'exists:subclients,id',
            ],

            'cargo_type_id' => [
                'nullable',
                'exists:cargo_types,id',
            ],

            'booking_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'customer_order_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'operation_type' => [
                'required',

                Rule::in([
                    'EXPORT',
                    'IMPORT',
                    'TRANSFER',
                    'OTHER',
                ]),
            ],

            'service_type' => [
                'required',

                Rule::in([
                    'TRANSPORT',
                    'POSITIONING',
                    'PICKUP',
                    'POSITIONING_PICKUP',
                    'TRANSFER',
                    'OTHER',
                ]),
            ],

            'plant_id' => [
                'nullable',
                'exists:plants,id',
            ],

            'origin_type' => [
                'required',

                Rule::in([
                    'LOCATION',
                    'PLANT',
                ]),
            ],

            'origin_location_id' => [
                'nullable',
                'exists:locations,id',
            ],

            'origin_plant_id' => [
                'nullable',
                'exists:plants,id',
            ],

            'destination_type' => [
                'required',

                Rule::in([
                    'LOCATION',
                    'PLANT',
                ]),
            ],

            'destination_location_id' => [
                'nullable',
                'exists:locations,id',
            ],

            'destination_plant_id' => [
                'nullable',
                'exists:plants,id',
            ],

            'requested_date' => [
                'required',
                'date',
            ],

            'requested_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'appointment_at' => [
                'nullable',
                'date',
            ],

            'requested_trips' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],

            'requested_container_type' => [
                'nullable',

                Rule::in([
                    'DRY',
                    'REEFER',
                    'OPEN_TOP',
                    'FLAT_RACK',
                    'TANK',
                    'OTHER',
                ]),
            ],

            'requested_container_size' => [
                'nullable',

                Rule::in([
                    '20FT',
                    '40FT',
                    '40HC',
                    '45FT',
                    'OTHER',
                ]),
            ],

            'cargo_description' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'estimated_weight_kg' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',

                Rule::in([
                    'PENDING',
                    'PLANNED',
                    'IN_PROGRESS',
                    'COMPLETED',
                    'CANCELLED',
                ]),
            ],

            'customer_reference' => [
                'nullable',
                'string',
                'max:150',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

        ], [

            'client_id.required' =>
            'Seleccione el cliente.',

            'operation_type.required' =>
            'Seleccione el tipo de operación.',

            'origin_type.required' =>
            'Seleccione el tipo de origen.',

            'destination_type.required' =>
            'Seleccione el tipo de destino.',

            'requested_date.required' =>
            'La fecha solicitada es obligatoria.',

            'requested_trips.min' =>
            'La orden debe solicitar al menos un viaje.',

        ]);
    }

    /*
     |--------------------------------------------------------------------------
     | NORMALIZAR ORIGEN / DESTINO
     |--------------------------------------------------------------------------
     */

    private function normalizeLocations(
        array $validated
    ): array {

        if (
            $validated['origin_type']
            === 'LOCATION'
        ) {
            $validated['origin_plant_id'] =
                null;
        }

        if (
            $validated['origin_type']
            === 'PLANT'
        ) {
            $validated['origin_location_id'] =
                null;
        }

        if (
            $validated['destination_type']
            === 'LOCATION'
        ) {
            $validated['destination_plant_id'] =
                null;
        }

        if (
            $validated['destination_type']
            === 'PLANT'
        ) {
            $validated['destination_location_id'] =
                null;
        }

        return $validated;
    }

    /*
     |--------------------------------------------------------------------------
     | REGLAS ENTRE CLIENTE / SUBCLIENTE / CARGA / PLANTA
     |--------------------------------------------------------------------------
     */

    private function validateBusinessRelations(
        array $validated
    ): void {

        $clientId =
            $validated['client_id'];

        /*
         * SUBCLIENTE
         */

        if (!empty($validated['subclient_id'])) {

            $valid =
                Subclient::query()
                ->where(
                    'id',
                    $validated['subclient_id']
                )
                ->where(
                    'client_id',
                    $clientId
                )
                ->exists();

            if (!$valid) {

                abort(
                    422,
                    'El subcliente seleccionado no pertenece al cliente.'
                );
            }
        }

        /*
         * PLANTA PRINCIPAL
         */

        if (!empty($validated['plant_id'])) {

            $valid =
                Plant::query()
                ->where(
                    'id',
                    $validated['plant_id']
                )
                ->where(
                    'client_id',
                    $clientId
                )
                ->exists();

            if (!$valid) {

                abort(
                    422,
                    'La planta seleccionada no pertenece al cliente.'
                );
            }
        }

        /*
         * ORIGEN PLANTA
         */

        if (
            $validated['origin_type']
            === 'PLANT'
            &&
            !empty($validated['origin_plant_id'])
        ) {

            $valid =
                Plant::query()
                ->where(
                    'id',
                    $validated['origin_plant_id']
                )
                ->where(
                    'client_id',
                    $clientId
                )
                ->exists();

            if (!$valid) {

                abort(
                    422,
                    'La planta de origen no pertenece al cliente.'
                );
            }
        }

        /*
         * DESTINO PLANTA
         */

        if (
            $validated['destination_type']
            === 'PLANT'
            &&
            !empty($validated['destination_plant_id'])
        ) {

            $valid =
                Plant::query()
                ->where(
                    'id',
                    $validated['destination_plant_id']
                )
                ->where(
                    'client_id',
                    $clientId
                )
                ->exists();

            if (!$valid) {

                abort(
                    422,
                    'La planta de destino no pertenece al cliente.'
                );
            }
        }

        /*
         * TIPO DE CARGA
         */

        if (!empty($validated['cargo_type_id'])) {

            $cargoAllowed =
                DB::table(
                    'client_cargo_types'
                )
                ->where(
                    'client_id',
                    $clientId
                )
                ->where(
                    'cargo_type_id',
                    $validated['cargo_type_id']
                )
                ->exists();

            if (!$cargoAllowed) {

                abort(
                    422,
                    'El tipo de carga no está configurado para este cliente.'
                );
            }

            /*
             * Si el subcliente tiene configuraciones
             * propias, también debe estar permitido.
             */

            if (!empty($validated['subclient_id'])) {

                $hasSpecificConfiguration =
                    DB::table(
                        'subclient_cargo_types'
                    )
                    ->where(
                        'subclient_id',
                        $validated['subclient_id']
                    )
                    ->exists();

                if (
                    $hasSpecificConfiguration
                ) {

                    $allowedForSubclient =
                        DB::table(
                            'subclient_cargo_types'
                        )
                        ->where(
                            'subclient_id',
                            $validated['subclient_id']
                        )
                        ->where(
                            'cargo_type_id',
                            $validated['cargo_type_id']
                        )
                        ->exists();

                    if (!$allowedForSubclient) {

                        abort(
                            422,
                            'El tipo de carga no está habilitado para este subcliente.'
                        );
                    }
                }
            }
        }
    }

    /*
     |--------------------------------------------------------------------------
     | NUMERACIÓN AUTOMÁTICA
     |--------------------------------------------------------------------------
     */

    private function generateWorkOrderNumber(): string
    {
        $year =
            now()->format('Y');

        $lastId =
            WorkOrder::withTrashed()->max('id')
            ?? 0;

        $next =
            $lastId + 1;

        return 'OT-'
            . $year
            . '-'
            . str_pad(
                $next,
                6,
                '0',
                STR_PAD_LEFT
            );
    }
}
