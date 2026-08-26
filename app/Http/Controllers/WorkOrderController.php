<?php

namespace App\Http\Controllers;

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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WorkOrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTADO
    |--------------------------------------------------------------------------
    */

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


        $workOrders =
            WorkOrder::query()

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

                                ->orWhere(
                                    'customer_reference',
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
                                )

                                ->orWhereHas(
                                    'subclient',
                                    function ($subclientQuery) use ($search) {

                                        $subclientQuery->where(
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


        $clients =
            Client::query()
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


    /*
    |--------------------------------------------------------------------------
    | CREAR
    |--------------------------------------------------------------------------
    */

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
            $this->validateWorkOrder(
                $request
            );


        $validated =
            $this->normalizeLocations(
                $validated
            );


        /*
         * Cliente / Subcliente / Carga / Plantas.
         */
        $this->validateBusinessRelations(
            $validated
        );


        /*
         * Compatibilidad temporal con
         * el campo service_type anterior.
         */
        $validated['service_type'] =
            $this->legacyServiceType(
                $validated['service_modality']
            );


        /*
         * Resolvemos y congelamos
         * la regla Stand-by.
         */
        $validated =
            array_merge(
                $validated,
                $this->resolveStandbyRule(
                    $request,
                    $validated
                )
            );


        $validated['work_order_number'] =
            $this->generateWorkOrderNumber();


        $validated['created_by'] =
            Auth::id();

        $validated['updated_by'] =
            Auth::id();


        $workOrder =
            DB::transaction(
                fn() =>
                WorkOrder::create(
                    $validated
                )
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


    /*
    |--------------------------------------------------------------------------
    | MOSTRAR
    |--------------------------------------------------------------------------
    */

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

            'standbyOverrideUser',

            'trips.activeAssignment.driver',

            'trips.activeAssignment.vehicle',

            'trips.activeAssignment.chassis',

            'trips.activeAssignment.container',

        ]);


        return view(
            'work-orders.show',
            compact('workOrder')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDITAR
    |--------------------------------------------------------------------------
    */

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


        $validated['service_type'] =
            $this->legacyServiceType(
                $validated['service_modality']
            );


        /*
         * Volvemos a congelar la regla.
         *
         * Esto ocurre porque la OT todavía
         * está siendo modificada.
         *
         * Más adelante bloquearemos esta
         * modificación cuando existan
         * viajes ejecutados.
         */
        $validated =
            array_merge(
                $validated,
                $this->resolveStandbyRule(
                    $request,
                    $validated
                )
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


    /*
    |--------------------------------------------------------------------------
    | ELIMINAR
    |--------------------------------------------------------------------------
    */

    public function destroy(
        WorkOrder $workOrder
    ): RedirectResponse {

        if (
            $workOrder
            ->trips()
            ->exists()
        ) {

            return back()
                ->withErrors([

                    'delete' =>
                    'No se puede eliminar la orden porque tiene viajes relacionados.',

                ]);
        }


        $workOrder->delete();


        return redirect()
            ->route(
                'work-orders.index'
            )
            ->with(
                'success',
                'Orden de trabajo eliminada correctamente.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | DATOS DEL FORMULARIO
    |--------------------------------------------------------------------------
    */

    private function formData(): array
    {
        return [

            'clients' =>
            Client::query()

                ->where(
                    'is_active',
                    true
                )

                ->orderBy(
                    'business_name'
                )

                ->get(),


            'subclients' =>
            Subclient::query()

                ->where(
                    'is_active',
                    true
                )

                ->with('client')

                ->orderBy(
                    'business_name'
                )

                ->get(),


            'plants' =>
            Plant::query()

                ->where(
                    'is_active',
                    true
                )

                ->with('client')

                ->orderBy('name')

                ->get(),


            'locations' =>
            Location::query()

                ->where(
                    'is_active',
                    true
                )

                ->orderBy('name')

                ->get(),

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | VALIDACIÓN FORMULARIO
    |--------------------------------------------------------------------------
    */

    private function validateWorkOrder(
        Request $request,
        ?WorkOrder $workOrder = null
    ): array {

        return $request->validate([

            /*
             * CLIENTE
             */

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


            /*
             * REFERENCIAS
             */

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


            'customer_reference' => [
                'nullable',
                'string',
                'max:150',
            ],


            /*
             * OPERACIÓN
             */

            'operation_type' => [
                'required',

                Rule::in([
                    'EXPORT',
                    'IMPORT',
                    'TRANSFER',
                    'OTHER',
                ]),
            ],


            /*
             * MODALIDAD
             */

            'service_modality' => [
                'required',

                Rule::in([
                    'IMMEDIATE',
                    'POSITIONING',
                    'PICKUP',
                    'POSITIONING_PICKUP',
                ]),
            ],


            'plant_id' => [
                'nullable',
                'exists:plants,id',
            ],


            /*
             * ORIGEN
             */

            'origin_type' => [
                'required',

                Rule::in([
                    'LOCATION',
                    'PLANT',
                ]),
            ],


            'origin_location_id' => [
                'nullable',
                'required_if:origin_type,LOCATION',
                'exists:locations,id',
            ],


            'origin_plant_id' => [
                'nullable',
                'required_if:origin_type,PLANT',
                'exists:plants,id',
            ],


            /*
             * DESTINO
             */

            'destination_type' => [
                'required',

                Rule::in([
                    'LOCATION',
                    'PLANT',
                ]),
            ],


            'destination_location_id' => [
                'nullable',
                'required_if:destination_type,LOCATION',
                'exists:locations,id',
            ],


            'destination_plant_id' => [
                'nullable',
                'required_if:destination_type,PLANT',
                'exists:plants,id',
            ],


            /*
             * PLANIFICACIÓN
             */

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


            /*
             * STAND-BY
             */

            'standby_process_type' => [
                'required',

                Rule::in([
                    'LOAD',
                    'UNLOAD',
                ]),
            ],


            'standby_rule_overridden' => [
                'nullable',
                'boolean',
            ],


            'standby_override_free_hours' => [
                'nullable',
                'required_if:standby_rule_overridden,1',
                'integer',
                'min:0',
                'max:999',
            ],


            'standby_override_count_start_type' => [
                'nullable',
                'required_if:standby_rule_overridden,1',

                Rule::in([
                    'REQUESTED_TIME',
                    'ARRIVAL_TIME',
                ]),
            ],


            'standby_override_fraction_minutes' => [
                'nullable',
                'required_if:standby_rule_overridden,1',
                'integer',
                'min:1',
                'max:1440',
            ],


            'standby_override_reason' => [
                'nullable',
                'required_if:standby_rule_overridden,1',
                'string',
                'max:2000',
            ],


            /*
             * CONTENEDOR
             */

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


            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

        ], [

            'client_id.required' =>
            'Seleccione el cliente.',

            'service_modality.required' =>
            'Seleccione la modalidad del servicio.',

            'origin_location_id.required_if' =>
            'Seleccione la ubicación de origen.',

            'origin_plant_id.required_if' =>
            'Seleccione la planta de origen.',

            'destination_location_id.required_if' =>
            'Seleccione la ubicación de destino.',

            'destination_plant_id.required_if' =>
            'Seleccione la planta de destino.',

            'requested_date.required' =>
            'La fecha solicitada es obligatoria.',

            'standby_process_type.required' =>
            'Seleccione si la regla Stand-by corresponde a carga o descarga.',

            'standby_override_reason.required_if' =>
            'Debe indicar el motivo de la excepción de Stand-by.',

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZAR UBICACIONES
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
    | VALIDACIONES DE NEGOCIO
    |--------------------------------------------------------------------------
    */

    private function validateBusinessRelations(
        array $validated
    ): void {

        $clientId =
            (int)
            $validated['client_id'];


        /*
         * SUBCLIENTE
         */

        if (
            !empty($validated['subclient_id'])
        ) {

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

                ->where(
                    'is_active',
                    true
                )

                ->exists();


            if (!$valid) {

                throw ValidationException::withMessages([

                    'subclient_id' =>
                    'El subcliente seleccionado no pertenece al cliente.',

                ]);
            }
        }


        /*
         * PLANTA PRINCIPAL
         */

        if (
            !empty($validated['plant_id'])
        ) {

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

                ->where(
                    'is_active',
                    true
                )

                ->exists();


            if (!$valid) {

                throw ValidationException::withMessages([

                    'plant_id' =>
                    'La planta principal no pertenece al cliente.',

                ]);
            }
        }


        /*
         * PLANTA ORIGEN
         */

        if (
            $validated['origin_type']
            === 'PLANT'
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

                ->where(
                    'is_active',
                    true
                )

                ->exists();


            if (!$valid) {

                throw ValidationException::withMessages([

                    'origin_plant_id' =>
                    'La planta de origen no pertenece al cliente.',

                ]);
            }
        }


        /*
         * PLANTA DESTINO
         */

        if (
            $validated['destination_type']
            === 'PLANT'
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

                ->where(
                    'is_active',
                    true
                )

                ->exists();


            if (!$valid) {

                throw ValidationException::withMessages([

                    'destination_plant_id' =>
                    'La planta de destino no pertenece al cliente.',

                ]);
            }
        }


        /*
         * TIPO DE CARGA
         */

        if (
            !empty($validated['cargo_type_id'])
        ) {

            $cargoTypeId =
                (int)
                $validated['cargo_type_id'];


            $allowedForClient =
                DB::table(
                    'client_cargo_types'
                )

                ->where(
                    'client_id',
                    $clientId
                )

                ->where(
                    'cargo_type_id',
                    $cargoTypeId
                )

                ->exists();


            if (!$allowedForClient) {

                throw ValidationException::withMessages([

                    'cargo_type_id' =>
                    'El tipo de carga no está habilitado para este cliente.',

                ]);
            }


            if (
                !empty($validated['subclient_id'])
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
                        $cargoTypeId
                    )

                    ->exists();


                if (!$allowedForSubclient) {

                    throw ValidationException::withMessages([

                        'cargo_type_id' =>
                        'El tipo de carga no está habilitado para este subcliente.',

                    ]);
                }
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | RESOLVER REGLA STAND-BY
    |--------------------------------------------------------------------------
    */

    private function resolveStandbyRule(
        Request $request,
        array $validated
    ): array {

        $process =
            $validated['standby_process_type'];


        $isOverride =
            $request->boolean(
                'standby_rule_overridden'
            );


        /*
         * EXCEPCIÓN MANUAL
         */

        if ($isOverride) {

            return [

                'standby_free_hours' =>
                (int)
                $request->input(
                    'standby_override_free_hours'
                ),

                'standby_count_start_type' =>
                $request->input(
                    'standby_override_count_start_type'
                ),

                'standby_fraction_minutes' =>
                (int)
                $request->input(
                    'standby_override_fraction_minutes'
                ),

                'standby_rule_source' =>
                'OVERRIDE',

                'standby_rule_overridden' =>
                true,

                'standby_override_reason' =>
                $request->input(
                    'standby_override_reason'
                ),

                'standby_override_by' =>
                Auth::id(),
            ];
        }


        /*
         * Primero buscamos configuración
         * efectiva del subcliente.
         */

        $subclient =
            null;


        if (
            !empty($validated['subclient_id'])
        ) {

            $subclient =
                Subclient::with('client')
                ->find(
                    $validated['subclient_id']
                );
        }


        if ($subclient) {

            /*
             * Hereda.
             */

            if (
                $subclient
                ->inherits_operational_rules
            ) {

                $client =
                    $subclient->client;

                return $this
                    ->buildRuleFromEntity(
                        $client,
                        $process,
                        'CLIENT'
                    );
            }


            /*
             * Reglas propias.
             */

            return $this
                ->buildRuleFromEntity(
                    $subclient,
                    $process,
                    'SUBCLIENT'
                );
        }


        /*
         * Sin subcliente:
         * usar cliente.
         */

        $client =
            Client::findOrFail(
                $validated['client_id']
            );


        return $this
            ->buildRuleFromEntity(
                $client,
                $process,
                'CLIENT'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | CREAR SNAPSHOT DESDE CLIENTE / SUBCLIENTE
    |--------------------------------------------------------------------------
    */

    private function buildRuleFromEntity(
        $entity,
        string $process,
        string $source
    ): array {

        $freeHours =
            $process === 'UNLOAD'

            ? (int) (
                $entity
                ->free_unloading_hours
                ?? 0
            )

            : (int) (
                $entity
                ->free_loading_hours
                ?? 0
            );


        $countStart =
            match ($entity->service_time_start) {

                'arrival_time' =>
                'ARRIVAL_TIME',

                default =>
                'REQUESTED_TIME',
            };


        return [

            'standby_free_hours' =>
            $freeHours,

            'standby_count_start_type' =>
            $countStart,

            'standby_fraction_minutes' =>
            (int) (
                $entity
                ->standby_fraction_minutes
                ?? 30
            ),

            'standby_rule_source' =>
            $source,

            'standby_rule_overridden' =>
            false,

            'standby_override_reason' =>
            null,

            'standby_override_by' =>
            null,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | COMPATIBILIDAD service_type
    |--------------------------------------------------------------------------
    */

    private function legacyServiceType(
        string $modality
    ): string {

        return match ($modality) {

            'POSITIONING' =>
            'POSITIONING',

            'PICKUP' =>
            'PICKUP',

            'POSITIONING_PICKUP' =>
            'POSITIONING_PICKUP',

            default =>
            'TRANSPORT',
        };
    }


    /*
    |--------------------------------------------------------------------------
    | NUMERACIÓN
    |--------------------------------------------------------------------------
    */

    private function generateWorkOrderNumber(): string
    {
        $year =
            now()->format('Y');


        $lastId =
            WorkOrder::withTrashed()
            ->max('id')
            ?? 0;


        return 'OT-'
            . $year
            . '-'
            . str_pad(
                $lastId + 1,
                6,
                '0',
                STR_PAD_LEFT
            );
    }
}
