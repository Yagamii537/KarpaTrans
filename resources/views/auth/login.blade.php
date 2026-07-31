<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>Iniciar sesión | Karpan Logística</title>

    <link rel="shortcut icon"
          type="image/png"
          href="{{ asset('modernize/assets/images/logos/favicon.png') }}">

    <link rel="stylesheet"
          href="{{ asset('modernize/assets/css/styles.min.css') }}">
</head>

<body>

    <div class="page-wrapper"
         id="main-wrapper"
         data-layout="vertical"
         data-navbarbg="skin6"
         data-sidebartype="full"
         data-sidebar-position="fixed"
         data-header-position="fixed">

        <div class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">

            <div class="d-flex align-items-center justify-content-center w-100">

                <div class="row justify-content-center w-100">

                    <div class="col-md-8 col-lg-6 col-xxl-4">

                        <div class="card mb-0 shadow">

                            <div class="card-body">

                                <div class="text-center mb-4">

                                    <h2 class="fw-bolder text-primary mb-1">
                                        KARPAN TRANST S.A.
                                    </h2>

                                    <p class="text-muted mb-0">
                                        Sistema de Gestión Logística
                                    </p>

                                </div>

                                <p class="text-center mb-4">
                                    Ingrese sus credenciales para continuar
                                </p>

                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                @if ($errors->any())
                                    <div class="alert alert-danger">

                                        @foreach ($errors->all() as $error)
                                            <div>{{ $error }}</div>
                                        @endforeach

                                    </div>
                                @endif

                                <form method="POST"
                                      action="{{ route('login.authenticate') }}">

                                    @csrf

                                    <div class="mb-3">

                                        <label for="email"
                                               class="form-label">
                                            Correo electrónico
                                        </label>

                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               id="email"
                                               name="email"
                                               value="{{ old('email') }}"
                                               autocomplete="email"
                                               autofocus
                                               required>

                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    <div class="mb-4">

                                        <label for="password"
                                               class="form-label">
                                            Contraseña
                                        </label>

                                        <input type="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               autocomplete="current-password"
                                               required>

                                        @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mb-4">

                                        <div class="form-check">

                                            <input class="form-check-input primary"
                                                   type="checkbox"
                                                   id="remember"
                                                   name="remember"
                                                   value="1">

                                            <label class="form-check-label text-dark"
                                                   for="remember">
                                                Recordarme
                                            </label>

                                        </div>

                                    </div>

                                    <button type="submit"
                                            class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">

                                        Iniciar sesión

                                    </button>

                                </form>

                                <div class="text-center">

                                    <small class="text-muted">
                                        Karpan Logística © {{ date('Y') }}
                                    </small>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="{{ asset('modernize/assets/libs/jquery/dist/jquery.min.js') }}"></script>

    <script src="{{ asset('modernize/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

</body>

</html>
