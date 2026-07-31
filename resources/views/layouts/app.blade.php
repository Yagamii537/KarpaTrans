<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>@yield('title', 'Karpan Logística')</title>

    <link rel="shortcut icon"
          type="image/png"
          href="{{ asset('modernize/assets/images/logos/favicon.png') }}">

    <link rel="stylesheet"
          href="{{ asset('modernize/assets/css/styles.min.css') }}">

    <style>
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            min-height: 100%;
        }

        body {
            background-color: #f6f9fc;
        }

        #main-wrapper,
        .page-wrapper {
            margin: 0 !important;
            padding: 0 !important;
            top: 0 !important;
            min-height: 100vh;
        }

        /*
         * No colocar margin-left ni padding-top manual.
         * Modernize ya posiciona el sidebar, header y contenido.
         */
        .body-wrapper {
            padding: 0 !important;
        }

        .app-header {
            margin-top: 0 !important;
            top: 0 !important;
        }

        .left-sidebar {
            margin-top: 0 !important;
            top: 0 !important;
        }

        .body-wrapper > .container-fluid {
            max-width: 100%;
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="page-wrapper"
         id="main-wrapper"
         data-layout="vertical"
         data-navbarbg="skin6"
         data-sidebartype="full"
         data-sidebar-position="fixed"
         data-header-position="fixed">

        @include('partials.sidebar')

        <div class="body-wrapper">

            @include('partials.header')

            <div class="container-fluid">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show"
                         role="alert">

                        {{ session('success') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert">
                        </button>
                    </div>
                @endif

                @yield('content')

            </div>

        </div>

    </div>

    <script src="{{ asset('modernize/assets/libs/jquery/dist/jquery.min.js') }}"></script>

    <script src="{{ asset('modernize/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>

    <script src="{{ asset('modernize/assets/js/sidebarmenu.js') }}"></script>

    <script src="{{ asset('modernize/assets/js/app.min.js') }}"></script>

    @stack('scripts')

</body>

</html>
