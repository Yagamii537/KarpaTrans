@extends('layouts.app')

@section('title', 'Nueva orden de trabajo | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="fw-semibold mb-1">
                Nueva orden de trabajo
            </h4>

            <p class="text-muted mb-0">
                Registre el requerimiento recibido del cliente.
            </p>

        </div>

        <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary">

            <i class="ti ti-arrow-left me-1"></i>
            Regresar

        </a>

    </div>

    <div class="card">

        <div class="card-body">

            <form method="POST" action="{{ route('work-orders.store') }}">

                @csrf

                @include('work-orders._form')

            </form>

        </div>

    </div>

@endsection
