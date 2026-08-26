<aside class="left-sidebar">

    <div>

        <div class="brand-logo d-flex align-items-center justify-content-between">

            <a href="{{ route('dashboard') }}" class="text-nowrap logo-img text-decoration-none">

                <div class="d-flex flex-column">

                    <span class="fw-bold text-primary" style="font-size: 20px; line-height: 1.2;">

                        KARPAN TRANST

                    </span>

                    <small class="text-muted" style="font-size: 12px;">

                        Gestión Logística

                    </small>

                </div>

            </a>

            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer">

                <i class="ti ti-x fs-8"></i>

            </div>

        </div>

        <nav class="sidebar-nav scroll-sidebar">

            <ul id="sidebarnav">

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">PRINCIPAL</span>
                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">

                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>

                        <span class="hide-menu">
                            Dashboard
                        </span>

                    </a>

                </li>

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">DATOS MAESTROS</span>
                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link {{ request()->routeIs('clients.*') ? 'active' : '' }}"
                        href="{{ route('clients.index') }}">

                        <span>
                            <i class="ti ti-user"></i>
                        </span>

                        <span class="hide-menu">
                            Clientes
                        </span>

                    </a>

                </li>


                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('subclients.*') ? 'active' : '' }}"
                        href="{{ route('subclients.index') }}">

                        <span>
                            <i class="ti ti-users"></i>
                        </span>

                        <span class="hide-menu">
                            Subclientes
                        </span>

                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('cargo-types.*') ? 'active' : '' }}"
                        href="{{ route('cargo-types.index') }}">

                        <span>
                            <i class="ti ti-package"></i>
                        </span>

                        <span class="hide-menu">
                            Tipos de carga
                        </span>

                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}"
                        href="{{ route('drivers.index') }}">

                        <span>
                            <i class="ti ti-steering-wheel"></i>
                        </span>

                        <span class="hide-menu">
                            Conductores
                        </span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('driver-restrictions.*') ? 'active' : '' }}"
                        href="{{ route('driver-restrictions.index') }}">

                        <span>
                            <i class="ti ti-alert-triangle"></i>
                        </span>

                        <span class="hide-menu">
                            Restricciones
                        </span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}"
                        href="{{ route('vehicles.index') }}">

                        <span>
                            <i class="ti ti-truck"></i>
                        </span>

                        <span class="hide-menu">
                            Vehículos
                        </span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('chassis.*') ? 'active' : '' }}"
                        href="{{ route('chassis.index') }}">

                        <span>
                            <i class="ti ti-tool"></i>
                        </span>

                        <span class="hide-menu">
                            Chasis
                        </span>
                    </a>
                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link {{ request()->routeIs('containers.*') ? 'active' : '' }}"
                        href="{{ route('containers.index') }}">

                        <span>
                            <i class="ti ti-box"></i>
                        </span>

                        <span class="hide-menu">
                            Contenedores
                        </span>

                    </a>

                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('locations.*') ? 'active' : '' }}"
                        href="{{ route('locations.index') }}">

                        <span>
                            <i class="ti ti-map-pin"></i>
                        </span>

                        <span class="hide-menu">
                            Ubicaciones
                        </span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('plants.*') ? 'active' : '' }}"
                        href="{{ route('plants.index') }}">

                        <span>
                            <i class="ti ti-building-factory"></i>
                        </span>

                        <span class="hide-menu">
                            Plantas
                        </span>
                    </a>
                </li>



                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">OPERACIONES</span>
                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}"
                        href="{{ route('work-orders.index') }}">

                        <span>
                            <i class="ti ti-clipboard"></i>
                        </span>

                        <span class="hide-menu">
                            Órdenes de trabajo
                        </span>

                    </a>

                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link {{ request()->routeIs('trips.*') ? 'active' : '' }}"
                        href="{{ route('trips.index') }}">

                        <span>
                            <i class="ti ti-route"></i>
                        </span>

                        <span class="hide-menu">
                            Viajes
                        </span>

                    </a>

                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link" href="javascript:void(0)">

                        <span>
                            <i class="ti ti-arrows-transfer-down"></i>
                        </span>

                        <span class="hide-menu">
                            Transferencias
                        </span>

                    </a>

                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link" href="{{ route('standby.index') }}">

                        <span>

                            <i class="ti ti-clock-hour-4"></i>

                        </span>


                        <span class="hide-menu">

                            Stand-by

                        </span>

                    </a>

                </li>

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">DOCUMENTOS</span>
                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link" href="javascript:void(0)">

                        <span>
                            <i class="ti ti-file-certificate"></i>
                        </span>

                        <span class="hide-menu">
                            Guías de remisión
                        </span>

                    </a>

                </li>

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">ADMINISTRACIÓN</span>
                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link" href="javascript:void(0)">

                        <span>
                            <i class="ti ti-currency-dollar"></i>
                        </span>

                        <span class="hide-menu">
                            Costos y liquidaciones
                        </span>

                    </a>

                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link" href="javascript:void(0)">

                        <span>
                            <i class="ti ti-chart-bar"></i>
                        </span>

                        <span class="hide-menu">
                            Reportes
                        </span>

                    </a>

                </li>

                <li class="sidebar-item">

                    <a class="sidebar-link" href="javascript:void(0)">

                        <span>
                            <i class="ti ti-settings"></i>
                        </span>

                        <span class="hide-menu">
                            Configuración
                        </span>

                    </a>

                </li>

            </ul>

        </nav>

    </div>

</aside>
