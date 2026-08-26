@extends('layouts.app')

@section('title', 'Viajes | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                Viajes
            </h4>

            <p class="text-muted mb-0">
                Planificación y ejecución de operaciones.
            </p>

        </div>


        <a href="{{ route('trips.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>

            Nuevo viaje

        </a>

    </div>


    <div class="card">

        <div class="card-body">

            {{-- ========================================================= --}}
            {{-- FILTROS --}}
            {{-- ========================================================= --}}

            <form method="GET" action="{{ route('trips.index') }}" class="row g-2 mb-4">

                <div class="col-md-5">

                    <input type="text" name="search" class="form-control"
                        placeholder="Viaje, booking, cliente o subcliente" value="{{ $search }}">

                </div>


                <div class="col-md-3">

                    <select name="status" class="form-select">

                        <option value="">
                            Todos los estados
                        </option>


                        @foreach ([
            'PENDING' => 'Pendiente',
            'ASSIGNED' => 'Asignado',
            'IN_TRANSIT' => 'En tránsito',
            'AT_DESTINATION' => 'En destino',
            'COMPLETED' => 'Completado',
            'CANCELLED' => 'Cancelado',
        ] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>

                                {{ $label }}

                            </option>
                        @endforeach

                    </select>

                </div>


                <div class="col-auto">

                    <button type="submit" class="btn btn-outline-primary">

                        <i class="ti ti-search me-1"></i>

                        Buscar

                    </button>

                </div>


                <div class="col-auto">

                    <a href="{{ route('trips.index') }}" class="btn btn-light">

                        Limpiar

                    </a>

                </div>

            </form>


            {{-- ========================================================= --}}
            {{-- TABLA --}}
            {{-- ========================================================= --}}

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>
                                Viaje
                            </th>

                            <th>
                                Servicio
                            </th>

                            <th>
                                Etapa
                            </th>

                            <th>
                                Cliente
                            </th>

                            <th>
                                Ruta
                            </th>

                            <th>
                                Programación
                            </th>

                            <th>
                                Recursos
                            </th>

                            <th>
                                Estado
                            </th>

                            <th class="text-end">
                                Acciones
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($trips as $trip)

                            @php

                                /*
                                 * Modalidad visible.
                                 */
                                $modalityLabel = $trip->workOrder?->service_modality_label ?: 'No definida';

                                /*
                                 * Número de etapa.
                                 */
                                $stageProgress = 'Etapa única';

                                if ($trip->workOrder?->service_modality === 'POSITIONING_PICKUP') {
                                    if ($trip->service_stage === 'POSITIONING') {
                                        $stageProgress = 'Etapa 1 de 2';
                                    } elseif ($trip->service_stage === 'PICKUP') {
                                        $stageProgress = 'Etapa 2 de 2';
                                    }
                                }

                                /*
                                 * Verificar si Retiro está bloqueado.
                                 */
                                $waitingPositioning = false;

                                if (
                                    $trip->service_stage === 'PICKUP' &&
                                    $trip->workOrder?->service_modality === 'POSITIONING_PICKUP'
                                ) {
                                    $positioning = $trips->getCollection()->first(function ($candidate) use ($trip) {
                                        return $candidate->work_order_id === $trip->work_order_id &&
                                            (int) $candidate->service_number === (int) $trip->service_number &&
                                            $candidate->service_stage === 'POSITIONING';
                                    });

                                    if ($positioning && $positioning->status !== 'COMPLETED') {
                                        $waitingPositioning = true;
                                    }
                                }

                                /*
                                 * Recursos actuales o utilizados.
                                 */
                                $assignment =
                                    $trip->activeAssignment ??
                                    ($trip->status === 'COMPLETED'
                                        ? $trip->assignments->sortByDesc('assigned_at')->first()
                                        : null);
                            @endphp


                            <tr>

                                {{-- ================================================= --}}
                                {{-- VIAJE --}}
                                {{-- ================================================= --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{ $trip->trip_number }}

                                    </div>


                                    <small class="text-muted d-block">

                                        {{ $trip->workOrder?->work_order_number ?: '-' }}

                                    </small>

                                </td>


                                {{-- ================================================= --}}
                                {{-- SERVICIO --}}
                                {{-- ================================================= --}}

                                <td>

                                    <div class="fw-semibold">

                                        Servicio #{{ $trip->service_number ?: 1 }}

                                    </div>


                                    <small class="text-muted d-block">

                                        {{ $modalityLabel }}

                                    </small>

                                </td>


                                {{-- ================================================= --}}
                                {{-- ETAPA --}}
                                {{-- ================================================= --}}

                                <td>

                                    <div class="mb-1">

                                        <span class="badge {{ $trip->service_stage_badge_class }}">

                                            {{ $trip->service_stage_label }}

                                        </span>

                                    </div>


                                    <small class="text-muted d-block">

                                        {{ $stageProgress }}

                                    </small>


                                    @if ($waitingPositioning)
                                        <small class="text-warning d-block mt-1">

                                            <i class="ti ti-lock me-1"></i>

                                            Esperando Posición

                                        </small>
                                    @endif

                                </td>


                                {{-- ================================================= --}}
                                {{-- CLIENTE --}}
                                {{-- ================================================= --}}

                                <td>

                                    <div>

                                        {{ $trip->client_name_snapshot ?: '-' }}

                                    </div>


                                    @if ($trip->subclient_name_snapshot)
                                        <small class="text-muted d-block">

                                            {{ $trip->subclient_name_snapshot }}

                                        </small>
                                    @endif

                                </td>


                                {{-- ================================================= --}}
                                {{-- RUTA --}}
                                {{-- ================================================= --}}

                                <td>

                                    <div>

                                        {{ $trip->origin_name_snapshot ?: '-' }}

                                    </div>


                                    <small class="text-muted d-block">

                                        <i class="ti ti-arrow-right me-1"></i>

                                        {{ $trip->destination_name_snapshot ?: '-' }}

                                    </small>

                                </td>


                                {{-- ================================================= --}}
                                {{-- PROGRAMACIÓN --}}
                                {{-- ================================================= --}}

                                <td>

                                    @if ($trip->scheduled_start_at)
                                        <div>

                                            {{ $trip->scheduled_start_at->format('d/m/Y') }}

                                        </div>


                                        <small class="text-muted">

                                            {{ $trip->scheduled_start_at->format('H:i') }}

                                        </small>
                                    @else
                                        <span class="text-muted">

                                            Sin programar

                                        </span>
                                    @endif

                                </td>


                                {{-- ================================================= --}}
                                {{-- RECURSOS --}}
                                {{-- ================================================= --}}

                                <td>

                                    @if ($assignment)
                                        <div class="small">

                                            <i class="ti ti-user me-1"></i>

                                            {{ $assignment->driver?->full_name ?: '-' }}

                                        </div>


                                        <small class="text-muted d-block">

                                            <i class="ti ti-truck me-1"></i>

                                            {{ $assignment->vehicle?->plate ?: '-' }}

                                        </small>


                                        @if ($assignment->container)
                                            <small class="text-muted d-block">

                                                <i class="ti ti-box me-1"></i>

                                                {{ $assignment->container->container_number }}

                                            </small>
                                        @endif


                                        @if ($trip->status === 'COMPLETED')
                                            <small class="text-success d-block mt-1">

                                                Recursos utilizados

                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">

                                            Sin asignar

                                        </span>
                                    @endif

                                </td>


                                {{-- ================================================= --}}
                                {{-- ESTADO --}}
                                {{-- ================================================= --}}

                                <td>

                                    <span
                                        class="badge
                                        @if ($trip->status === 'COMPLETED') bg-success-subtle text-success

                                        @elseif ($trip->status === 'CANCELLED')
                                            bg-danger-subtle text-danger

                                        @elseif ($trip->status === 'IN_TRANSIT')
                                            bg-warning-subtle text-warning

                                        @elseif ($trip->status === 'AT_DESTINATION')
                                            bg-info-subtle text-info

                                        @elseif ($trip->status === 'ASSIGNED')
                                            bg-primary-subtle text-primary

                                        @else
                                            bg-light text-dark @endif">

                                        {{ $trip->status_label }}

                                    </span>


                                    @if ($waitingPositioning && $trip->status === 'PENDING')
                                        <small class="text-warning d-block mt-1">

                                            Bloqueado temporalmente

                                        </small>
                                    @endif

                                </td>


                                {{-- ================================================= --}}
                                {{-- ACCIONES --}}
                                {{-- ================================================= --}}

                                <td class="text-end">

                                    <div class="d-inline-flex gap-1">

                                        <a href="{{ route('trips.show', $trip) }}" class="btn btn-sm btn-outline-secondary"
                                            title="Ver viaje">

                                            <i class="ti ti-eye"></i>

                                        </a>


                                        @if (!in_array($trip->status, ['COMPLETED', 'CANCELLED']))
                                            <a href="{{ route('trips.edit', $trip) }}"
                                                class="btn btn-sm btn-outline-primary" title="Editar planificación">

                                                <i class="ti ti-edit"></i>

                                            </a>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="text-center py-5 text-muted">

                                    <i class="ti ti-truck fs-8 d-block mb-2"></i>

                                    No existen viajes registrados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ========================================================= --}}
            {{-- PAGINACIÓN --}}
            {{-- ========================================================= --}}

            <div class="mt-3">

                {{ $trips->links() }}

            </div>

        </div>

    </div>

@endsection
