@extends('layouts.app')

@section('title', 'Editar subcliente | Karpan Logística')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-semibold mb-1">
                Editar subcliente
            </h4>

            <p class="text-muted mb-0">
                {{ $subclient->display_name }}
            </p>
        </div>

    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('subclients.update', $subclient) }}">

                @csrf
                @method('PUT')

                @include('subclients._form')

            </form>

        </div>
    </div>

@endsection
