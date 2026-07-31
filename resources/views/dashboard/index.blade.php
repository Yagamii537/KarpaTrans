@extends('layouts.app')

@section('title', 'Dashboard | Karpan Logística')

@section('content')

    <div class="row">

        <div class="col-12">

            <div class="card bg-primary-subtle shadow-none position-relative overflow-hidden mb-4">

                <div class="card-body px-4 py-3">

                    <div class="row align-items-center">

                        <div class="col-9">

                            <h4 class="fw-semibold mb-2">
                                Dashboard
                            </h4>

                            <p class="mb-0">
                                Bienvenido al Sistema de Gestión Logística de KARPAN TRANST S.A.
                            </p>

                        </div>

                        <div class="col-3 text-end">

                            <i class="ti ti-truck-delivery text-primary"
                               style="font-size: 65px;"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-3 col-md-6">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>

                            <p class="mb-1 text-muted">
                                Viajes programados
                            </p>

                            <h4 class="fw-semibold mb-0">
                                0
                            </h4>

                        </div>

                        <div class="bg-primary-subtle rounded-circle p-3">

                            <i class="ti ti-route text-primary fs-7"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>

                            <p class="mb-1 text-muted">
                                Viajes en ejecución
                            </p>

                            <h4 class="fw-semibold mb-0">
                                0
                            </h4>

                        </div>

                        <div class="bg-warning-subtle rounded-circle p-3">

                            <i class="ti ti-truck text-warning fs-7"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>

                            <p class="mb-1 text-muted">
                                Guías pendientes
                            </p>

                            <h4 class="fw-semibold mb-0">
                                0
                            </h4>

                        </div>

                        <div class="bg-danger-subtle rounded-circle p-3">

                            <i class="ti ti-file-description text-danger fs-7"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-3 col-md-6">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>

                            <p class="mb-1 text-muted">
                                Clientes activos
                            </p>

                            <h4 class="fw-semibold mb-0">
                                0
                            </h4>

                        </div>

                        <div class="bg-success-subtle rounded-circle p-3">

                            <i class="ti ti-users text-success fs-7"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-lg-8">

            <div class="card">

                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between mb-4">

                        <div>

                            <h5 class="card-title fw-semibold mb-1">
                                Operaciones recientes
                            </h5>

                            <p class="text-muted mb-0">
                                Últimos viajes registrados
                            </p>

                        </div>

                        <a href="javascript:void(0)"
                           class="btn btn-primary">

                            <i class="ti ti-plus me-1"></i>
                            Nuevo viaje

                        </a>

                    </div>

                    <div class="table-responsive">

                        <table class="table align-middle">

                            <thead>

                                <tr>

                                    <th>Booking</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Conductor</th>
                                    <th>Estado</th>

                                </tr>

                            </thead>

                            <tbody>

                                <tr>

                                    <td colspan="5"
                                        class="text-center py-5 text-muted">

                                        <i class="ti ti-truck-off fs-8 d-block mb-2"></i>

                                        Todavía no existen viajes registrados.

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card">

                <div class="card-body">

                    <h5 class="card-title fw-semibold">
                        Accesos rápidos
                    </h5>

                    <div class="d-grid gap-3 mt-4">

                        <a href="javascript:void(0)"
                           class="btn btn-outline-primary text-start">

                            <i class="ti ti-file-plus me-2"></i>
                            Crear orden de trabajo

                        </a>

                        <a href="javascript:void(0)"
                           class="btn btn-outline-primary text-start">

                            <i class="ti ti-user-plus me-2"></i>
                            Registrar cliente

                        </a>

                        <a href="javascript:void(0)"
                           class="btn btn-outline-primary text-start">

                            <i class="ti ti-steering-wheel me-2"></i>
                            Registrar conductor

                        </a>

                        <a href="javascript:void(0)"
                           class="btn btn-outline-primary text-start">

                            <i class="ti ti-truck me-2"></i>
                            Registrar vehículo

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
