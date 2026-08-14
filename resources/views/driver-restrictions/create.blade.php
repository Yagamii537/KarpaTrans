@extends('layouts.app')

@section('title', 'Nueva restricción | Karpan Logística')

@section('content')

    <div class="mb-4">
        <h4 class="fw-semibold mb-1">
            Nueva restricción de conductor
        </h4>

        <p class="text-muted mb-0">
            Registre restricciones de acceso o retorno.
        </p>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('driver-restrictions.store') }}">

                @csrf

                @include('driver-restrictions._form')

            </form>

        </div>
    </div>

@endsection
