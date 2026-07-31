<header class="app-header">

    <nav class="navbar navbar-expand-lg navbar-light">

        <ul class="navbar-nav">

            <li class="nav-item d-block d-xl-none">

                <a class="nav-link sidebartoggler nav-icon-hover"
                   href="javascript:void(0)">

                    <i class="ti ti-menu-2"></i>

                </a>

            </li>

            <li class="nav-item">

                <a class="nav-link nav-icon-hover position-relative"
                   href="javascript:void(0)">

                    <i class="ti ti-bell-ringing"></i>

                    <span class="position-absolute rounded-circle bg-primary"
                          style="
                            width: 7px;
                            height: 7px;
                            top: 7px;
                            right: 5px;
                          ">
                    </span>

                </a>

            </li>

        </ul>

        <div class="navbar-collapse justify-content-end px-0">

            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

                <li class="nav-item dropdown">

                    <a class="nav-link nav-icon-hover"
                       href="javascript:void(0)"
                       id="drop2"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">

                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                             style="
                                width: 35px;
                                height: 35px;
                                font-weight: 600;
                             ">

                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}

                        </div>

                    </a>

                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up">

                        <div class="message-body">

                            <div class="px-3 py-2 border-bottom">

                                <strong>
                                    {{ Auth::user()->name ?? 'Usuario' }}
                                </strong>

                                <div class="text-muted small">
                                    {{ Auth::user()->email ?? '' }}
                                </div>

                            </div>

                            <a href="javascript:void(0)"
                               class="d-flex align-items-center gap-2 dropdown-item">

                                <i class="ti ti-user fs-6"></i>

                                <p class="mb-0 fs-3">
                                    Mi perfil
                                </p>

                            </a>

                            <form method="POST"
                                  action="{{ route('logout') }}">

                                @csrf

                                <button type="submit"
                                        class="btn btn-outline-primary mx-3 mt-2 d-block">

                                    Cerrar sesión

                                </button>

                            </form>

                        </div>

                    </div>

                </li>

            </ul>

        </div>

    </nav>

</header>
