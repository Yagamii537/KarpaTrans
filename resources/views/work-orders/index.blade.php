@extends('layouts.app')

@section('title', 'Órdenes de trabajo | Karpan Logística')

@section('content')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                Órdenes de trabajo
            </h4>

            <p class="text-muted mb-0">
                Requerimientos de servicio solicitados por clientes.
            </p>

        </div>

        <a href="{{ route('work-orders.create') }}" class="btn btn-primary">

            <i class="ti ti-plus me-1"></i>
            Nueva orden

        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <form method="GET" class="row g-2 mb-4">

                <div class="col-md-3">

                    <input type="text" name="search" class="form-control" placeholder="Orden, booking o cliente"
                        value="{{ $search }}">

                </div>

                <div class="col-md-3">

                    <select name="client_id" class="form-select">

                        <option value="">
                            Todos los clientes
                        </option>

                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" @selected($clientId == $client->id)>

                                {{ $client->business_name }}

                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="col-md-2">

                    <select name="operation_type" class="form-select">

                        <option value="">
                            Todas las operaciones
                        </option>

                        <option value="EXPORT" @selected($operationType === 'EXPORT')>
                            Exportación
                        </option>

                        <option value="IMPORT" @selected($operationType === 'IMPORT')>
                            Importación
                        </option>

                        <option value="TRANSFER" @selected($operationType === 'TRANSFER')>
                            Transferencia
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <select name="status" class="form-select">

                        <option value="">
                            Todos los estados
                        </option>

                        <option value="PENDING" @selected($status === 'PENDING')>
                            Pendiente
                        </option>

                        <option value="PLANNED" @selected($status === 'PLANNED')>
                            Planificada
                        </option>

                        <option value="IN_PROGRESS" @selected($status === 'IN_PROGRESS')>
                            En ejecución
                        </option>

                        <option value="COMPLETED" @selected($status === 'COMPLETED')>
                            Completada
                        </option>

                        <option value="CANCELLED" @selected($status === 'CANCELLED')>
                            Cancelada
                        </option>

                    </select>

                </div>

                <div class="col-auto">

                    <button class="btn btn-outline-primary">

                        <i class="ti ti-search"></i>

                    </button>

                </div>

                <div class="col-auto">

                    <a href="{{ route('work-orders.index') }}" class="btn btn-light">

                        Limpiar

                    </a>

                </div>

            </form>

            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Orden</th>
                            <th>Cliente</th>
                            <th>Operación</th>
                            <th>Ruta</th>
                            <th>Fecha</th>
                            <th>Viajes</th>
                            <th>Estado</th>
                            <th class="text-end">
                                Acciones
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse ($workOrders as $workOrder)
                            <tr>

                                <td>

                                    <div class="fw-semibold">

                                        {{ $workOrder->work_order_number }}

                                    </div>

                                    <small class="text-muted">

                                        Booking:
                                        {{ $workOrder->booking_number ?: '-' }}

                                    </small>

                                </td>

                                <td>

                                    <div>
                                        {{ $workOrder->client->business_name }}
                                    </div>

                                    @if ($workOrder->subclient)
                                        <small class="text-muted">

                                            {{ $workOrder->subclient->business_name }}

                                        </small>
                                    @endif

                                </td>

                                <td>

                                    <span class="badge bg-primary-subtle text-primary">

                                        {{ $workOrder->operation_type_label }}

                                    </span>

                                    <small class="d-block mt-1">

                                        {{ $workOrder->service_type_label }}

                                    </small>

                                </td>

                                <td>

                                    <div>
                                        {{ $workOrder->origin_name }}
                                    </div>

                                    <small class="text-muted">

                                        →

                                        {{ $workOrder->destination_name }}

                                    </small>

                                </td>

                                <td>

                                    {{ $workOrder->requested_date->format('d/m/Y') }}

                                    @if ($workOrder->requested_time)
                                        <small class="d-block text-muted">

                                            {{ substr($workOrder->requested_time, 0, 5) }}

                                        </small>
                                    @endif

                                </td>

                                <td>

                                    <span class="badge bg-info-subtle text-info">

                                        {{ $workOrder->requested_trips }}

                                    </span>

                                </td>

                                <td>

                                    <span
                                        class="badge
                                    @if ($workOrder->status === 'COMPLETED') bg-success-subtle text-success
                                    @elseif ($workOrder->status === 'CANCELLED')
                                        bg-danger-subtle text-danger
                                    @elseif ($workOrder->status === 'IN_PROGRESS')
                                        bg-warning-subtle text-warning
                                    @else
                                        bg-primary-subtle text-primary @endif">

                                        {{ $workOrder->status_label }}

                                    </span>

                                </td>

                                <td class="text-end">

                                    <a href="{{ route('work-orders.show', $workOrder) }}"
                                        class="btn btn-sm btn-outline-secondary">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                    <a href="{{ route('work-orders.edit', $workOrder) }}"
                                        class="btn btn-sm btn-outline-primary">

                                        <i class="ti ti-edit"></i>

                                    </a>

                                    <form method="POST" action="{{ route('work-orders.destroy', $workOrder) }}"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                          '¿Eliminar esta orden?'
                                      )">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger">

                                            <i class="ti ti-trash"></i>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="text-center py-5 text-muted">

                                    <i class="ti ti-file-text fs-8 d-block mb-2"></i>

                                    No existen órdenes de trabajo.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                {{ $workOrders->links() }}

            </div>

        </div>

    </div>

@endsection
