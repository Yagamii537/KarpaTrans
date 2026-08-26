@extends('layouts.app')

@section('title', 'Tipo de carga | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                {{ $cargoType->name }}
            </h4>

            <p class="text-muted mb-0">
                Configuración del tipo de carga
            </p>

        </div>


        <div class="d-flex gap-2">

            <a href="{{ route('cargo-types.index') }}" class="btn btn-light">

                <i class="ti ti-arrow-left me-1"></i>
                Regresar

            </a>


            <a href="{{ route('cargo-types.edit', $cargoType) }}" class="btn btn-primary">

                <i class="ti ti-edit me-1"></i>
                Editar

            </a>

        </div>

    </div>


    <div class="row">

        <div class="col-lg-5">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Información
                    </h5>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Nombre
                        </small>

                        <strong>
                            {{ $cargoType->name }}
                        </strong>

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Código
                        </small>

                        {{ $cargoType->code ?: '-' }}

                    </div>


                    <div class="mb-3">

                        <small class="text-muted d-block">
                            Estado
                        </small>

                        @if ($cargoType->is_active)
                            <span class="badge bg-success-subtle text-success">
                                Activo
                            </span>
                        @else
                            <span class="badge bg-danger-subtle text-danger">
                                Inactivo
                            </span>
                        @endif

                    </div>


                    <div>

                        <small class="text-muted d-block">
                            Descripción
                        </small>

                        {{ $cargoType->description ?: 'Sin descripción' }}

                    </div>

                </div>

            </div>

        </div>


        <div class="col-lg-7">

            <div class="card">

                <div class="card-body">

                    <h5 class="fw-semibold mb-4">
                        Clientes permitidos
                    </h5>


                    @forelse (
                                        $cargoType->clients
                                        as $client
                                    )

                        <div class="border rounded p-3 mb-3">

                            <div class="fw-semibold">
                                {{ $client->business_name }}
                            </div>


                            @php

                                $subclients = $cargoType->subclients->where('client_id', $client->id);

                            @endphp


                            @if ($subclients->isNotEmpty())
                                <div class="mt-2">

                                    <small class="text-muted d-block mb-1">
                                        Subclientes:
                                    </small>


                                    @foreach ($subclients as $subclient)
                                        <span class="badge bg-light text-dark border me-1">

                                            {{ $subclient->business_name }}

                                        </span>
                                    @endforeach

                                </div>
                            @else
                                <small class="text-muted d-block mt-2">

                                    Sin subclientes específicos.

                                </small>
                            @endif

                        </div>

                    @empty

                        <div class="alert alert-warning mb-0">

                            Este tipo de carga no está
                            asociado a clientes.

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

@endsection
