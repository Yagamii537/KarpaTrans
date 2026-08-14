@extends('layouts.app')

@section('title', 'Detalle del contenedor | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                {{ $container->container_number }}
            </h4>

            <p class="text-muted mb-0">
                Información e historial del contenedor.
            </p>

        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('containers.index') }}" class="btn btn-light">

                <i class="ti ti-arrow-left me-1"></i>
                Regresar

            </a>

            <a href="{{ route('containers.edit', $container) }}" class="btn btn-primary">

                <i class="ti ti-edit me-1"></i>
                Editar

            </a>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-4">

            <div class="card">

                <div class="card-body text-center">

                    <i class="ti ti-package text-primary" style="font-size:110px;"></i>

                    <h4 class="mt-2">
                        {{ $container->container_number }}
                    </h4>

                    <p class="text-muted">

                        {{ $container->container_size }}
                        ·
                        {{ $container->type_label }}

                    </p>

                    <span class="badge bg-primary-subtle text-primary">

                        {{ $container->operational_status_label }}

                    </span>

                </div>

            </div>

        </div>

        <div class="col-lg-8">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Información general
                    </h5>

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Tamaño
                            </small>

                            {{ $container->container_size }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Tipo
                            </small>

                            {{ $container->type_label }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Estado de carga
                            </small>

                            {{ $container->load_status_label }}

                        </div>

                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Ubicación actual
                            </small>

                            {{ $container->currentLocation?->name ?: 'Sin ubicación' }}

                        </div>

                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Naviera
                            </small>

                            {{ $container->shipping_line ?: 'No registrada' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Sello
                            </small>

                            {{ $container->seal_number ?: 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Tara
                            </small>

                            {{ $container->tare_weight_kg ? number_format((float) $container->tare_weight_kg, 2) . ' kg' : 'No registrada' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Peso bruto máximo
                            </small>

                            {{ $container->max_gross_weight_kg
                                ? number_format((float) $container->max_gross_weight_kg, 2) . ' kg'
                                : 'No registrado' }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card">

        <div class="card-body">

            <h5 class="fw-semibold mb-4">
                Historial de movimientos
            </h5>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Fecha</th>
                            <th>Movimiento</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Carga</th>
                            <th>Usuario</th>
                            <th>Observación</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($container->movements
                                as $movement)
                            <tr>

                                <td>

                                    {{ $movement->movement_at->format('d/m/Y H:i') }}

                                </td>

                                <td>

                                    <span class="badge bg-primary-subtle text-primary">

                                        {{ $movement->movement_type_label }}

                                    </span>

                                </td>

                                <td>

                                    {{ $movement->fromLocation?->name ?: '-' }}

                                </td>

                                <td>

                                    {{ $movement->toLocation?->name ?: '-' }}

                                </td>

                                <td>

                                    {{ match ($movement->load_status) {
                                        'FULL' => 'Lleno',
                                        'EMPTY' => 'Vacío',
                                        default => 'No definido',
                                    } }}

                                </td>

                                <td>

                                    {{ $movement->creator?->name ?: 'Sistema' }}

                                </td>

                                <td>

                                    {{ $movement->notes ?: '-' }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-4 text-muted">

                                    No existen movimientos registrados.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection
