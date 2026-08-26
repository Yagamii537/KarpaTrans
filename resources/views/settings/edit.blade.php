@extends('layouts.app')

@section('title', 'Configuración | Karpan Logística')

@section('content')

    {{-- ========================================================= --}}
    {{-- MENSAJES --}}
    {{-- ========================================================= --}}

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">

            <i class="ti ti-circle-check me-1"></i>

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    @if ($errors->any())

        <div class="alert alert-danger">

            <div class="fw-semibold mb-2">

                Se encontraron errores:

            </div>

            @foreach ($errors->all() as $error)
                <div>
                    {{ $error }}
                </div>
            @endforeach

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- ENCABEZADO --}}
    {{-- ========================================================= --}}

    <div class="mb-4">

        <h4 class="fw-semibold mb-1">

            Configuración

        </h4>

        <p class="text-muted mb-0">

            Parámetros generales de Karpan Transt.

        </p>

    </div>


    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">

        @csrf

        @method('PUT')


        {{-- ========================================================= --}}
        {{-- EMPRESA --}}
        {{-- ========================================================= --}}

        <div class="card">

            <div class="card-body">

                <h5 class="fw-semibold mb-1">

                    Datos de empresa

                </h5>

                <p class="text-muted mb-4">

                    Información general de la empresa.

                </p>


                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Razón social *

                        </label>

                        <input type="text" name="company_name" class="form-control"
                            value="{{ old('company_name', $settings->company_name) }}" required>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Nombre comercial

                        </label>

                        <input type="text" name="trade_name" class="form-control"
                            value="{{ old('trade_name', $settings->trade_name) }}">

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            RUC

                        </label>

                        <input type="text" name="ruc" class="form-control" value="{{ old('ruc', $settings->ruc) }}">

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Teléfono

                        </label>

                        <input type="text" name="phone" class="form-control"
                            value="{{ old('phone', $settings->phone) }}">

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">

                            Email

                        </label>

                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $settings->email) }}">

                    </div>


                    <div class="col-12 mb-3">

                        <label class="form-label">

                            Dirección

                        </label>

                        <textarea name="address" rows="2" class="form-control">{{ old('address', $settings->address) }}</textarea>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Logo

                        </label>

                        <input type="file" name="logo" class="form-control" accept="image/*">

                        <small class="text-muted">

                            PNG o JPG. Máximo 2 MB.

                        </small>

                    </div>


                    @if ($settings->logo_path)
                        <div class="col-md-6 mb-3">

                            <label class="form-label d-block">

                                Logo actual

                            </label>

                            <img src="{{ asset('storage/' . $settings->logo_path) }}" alt="Logo"
                                style="max-height:80px;">

                        </div>
                    @endif

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- OPERACIÓN --}}
        {{-- ========================================================= --}}

        <div class="card">

            <div class="card-body">

                <h5 class="fw-semibold mb-1">

                    Parámetros generales

                </h5>

                <p class="text-muted mb-4">

                    Configuración global de operación y alertas.

                </p>


                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Moneda

                        </label>

                        <select name="currency" class="form-select">

                            <option value="USD" @selected(old('currency', $settings->currency) === 'USD')>

                                USD - Dólar estadounidense

                            </option>

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Zona horaria

                        </label>

                        <select name="timezone" class="form-select">

                            <option value="America/Guayaquil" @selected(old('timezone', $settings->timezone) === 'America/Guayaquil')>

                                America/Guayaquil

                            </option>

                        </select>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Alerta de documentos

                        </label>

                        <div class="input-group">

                            <input type="number" name="document_alert_days" min="0" max="365"
                                class="form-control"
                                value="{{ old('document_alert_days', $settings->document_alert_days) }}">

                            <span class="input-group-text">

                                días antes

                            </span>

                        </div>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Alerta de licencias

                        </label>

                        <div class="input-group">

                            <input type="number" name="license_alert_days" min="0" max="365"
                                class="form-control"
                                value="{{ old('license_alert_days', $settings->license_alert_days) }}">

                            <span class="input-group-text">

                                días antes

                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- NUMERACIONES --}}
        {{-- ========================================================= --}}

        <div class="card">

            <div class="card-body">

                <h5 class="fw-semibold mb-1">

                    Numeraciones

                </h5>

                <p class="text-muted mb-4">

                    Prefijos que utiliza el sistema.

                </p>


                <div class="row">

                    <div class="col-md-3 mb-3">

                        <label class="form-label">

                            Ordenes

                        </label>

                        <input type="text" name="work_order_prefix" class="form-control"
                            value="{{ old('work_order_prefix', $settings->work_order_prefix) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label">

                            Viajes

                        </label>

                        <input type="text" name="trip_prefix" class="form-control"
                            value="{{ old('trip_prefix', $settings->trip_prefix) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label">

                            Transferencias

                        </label>

                        <input type="text" name="transfer_prefix" class="form-control"
                            value="{{ old('transfer_prefix', $settings->transfer_prefix) }}">

                    </div>


                    <div class="col-md-3 mb-3">

                        <label class="form-label">

                            Liquidaciones

                        </label>

                        <input type="text" name="settlement_prefix" class="form-control"
                            value="{{ old('settlement_prefix', $settings->settlement_prefix) }}">

                    </div>

                </div>


                <div class="alert alert-warning mb-0">

                    <i class="ti ti-alert-circle me-1"></i>

                    Por ahora estos valores quedan preparados.
                    En el siguiente ajuste conectaremos las numeraciones
                    automáticas para que Viajes, OT, Transferencias y
                    Liquidaciones utilicen estos prefijos.

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- ECONÓMICO --}}
        {{-- ========================================================= --}}

        <div class="card">

            <div class="card-body">

                <h5 class="fw-semibold mb-1">

                    Parámetros económicos

                </h5>

                <p class="text-muted mb-4">

                    Valores generales para cálculos futuros.

                </p>


                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            IVA

                        </label>

                        <div class="input-group">

                            <input type="number" step="0.01" min="0" max="100" name="vat_percentage"
                                class="form-control" value="{{ old('vat_percentage', $settings->vat_percentage) }}">

                            <span class="input-group-text">

                                %

                            </span>

                        </div>

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Decimales

                        </label>

                        <select name="decimal_places" class="form-select">

                            @foreach ([0, 1, 2, 3, 4] as $decimals)
                                <option value="{{ $decimals }}" @selected((int) old('decimal_places', $settings->decimal_places) === $decimals)>

                                    {{ $decimals }}

                                </option>
                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- GUARDAR --}}
        {{-- ========================================================= --}}

        <div class="d-flex justify-content-end mb-4">

            <button type="submit" class="btn btn-primary">

                <i class="ti ti-device-floppy me-1"></i>

                Guardar configuración

            </button>

        </div>

    </form>

@endsection
