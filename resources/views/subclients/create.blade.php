@extends('layouts.app')

@section('title', 'Nuevo subcliente | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-semibold mb-1">
                Nuevo subcliente
            </h4>

            <p class="text-muted mb-0">
                Registre un subcliente asociado a un cliente principal.
            </p>
        </div>

    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('subclients.store') }}">

                @csrf

                @include('subclients._form')

            </form>

        </div>
    </div>

@endsection
