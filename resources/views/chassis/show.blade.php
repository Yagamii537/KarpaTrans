@extends('layouts.app')

@section('title', 'Detalle del chasis | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                {{ $chassis->display_name }}
            </h4>

            <p class="text-muted mb-0">
                Información completa del chasis.
            </p>

        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('chassis.index') }}" class="btn btn-light">

                <i class="ti ti-arrow-left me-1"></i>
                Regresar

            </a>

            <a href="{{ route('chassis.edit', $chassis) }}" class="btn btn-primary">

                <i class="ti ti-edit me-1"></i>
                Editar

            </a>

        </div>

    </div>

    <div class="card">

        <div class="card-body">

            <div class="row">

                <div class="col-lg-4 text-center">

                    @if ($chassis->photo)
                        <img src="{{ asset('storage/' . $chassis->photo) }}" class="img-fluid rounded"
                            style="max-height:250px;">
                    @else
                        <i class="ti ti-tool text-primary" style="font-size:130px;"></i>
                    @endif

                </div>

                <div class="col-lg-8">

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Código
                            </small>

                            {{ $chassis->code }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Placa
                            </small>

                            {{ $chassis->plate ?: 'No registrada' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Tipo
                            </small>

                            {{ $chassis->chassis_type }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Marca
                            </small>

                            {{ $chassis->brand ?: 'No registrada' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Modelo
                            </small>

                            {{ $chassis->model ?: 'No registrado' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Año
                            </small>

                            {{ $chassis->year ?: 'No registrado' }}

                        </div>

                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Número de serie
                            </small>

                            {{ $chassis->serial_number ?: 'No registrado' }}

                        </div>

                        <div class="col-md-3 mb-3">

                            <small class="text-muted d-block">
                                Ejes
                            </small>

                            {{ $chassis->axles ?: 'No registrado' }}

                        </div>

                        <div class="col-md-3 mb-3">

                            <small class="text-muted d-block">
                                Capacidad
                            </small>

                            {{ $chassis->maximum_capacity_tons ? $chassis->maximum_capacity_tons . ' t' : 'No registrada' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Contenedor 20'
                            </small>

                            {{ $chassis->supports_20ft ? 'Sí' : 'No' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Contenedor 40'
                            </small>

                            {{ $chassis->supports_40ft ? 'Sí' : 'No' }}

                        </div>

                        <div class="col-md-4 mb-3">

                            <small class="text-muted d-block">
                                Refrigerado
                            </small>

                            {{ $chassis->supports_reefer ? 'Sí' : 'No' }}

                        </div>

                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Estado operativo
                            </small>

                            {{ $chassis->operational_status_label }}

                        </div>

                        <div class="col-md-6 mb-3">

                            <small class="text-muted d-block">
                                Registro
                            </small>

                            {{ $chassis->is_active ? 'Activo' : 'Inactivo' }}

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
