<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Gestión')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @php
        use Carbon\Carbon;

        $empleado = auth('empleado')->user();
        $cliente = auth('cliente')->user();
        $roles = [];

        if ($empleado) {
            $contratosActivos = $empleado->contratos->filter(function ($contrato) {
                return is_null($contrato->end_date) || Carbon::parse($contrato->end_date)->greaterThanOrEqualTo(now());
            });

            $roles = $contratosActivos->flatMap(function ($contrato) {
                return optional($contrato->cargo)->roles->pluck('name') ?? collect();
            })->unique()->toArray();
        }
    @endphp

    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">Sistema de Inventario, Ventas y Gestión de Servicios</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegación">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">

                    {{-- Siempre visible --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? '' : 'active' }}" href="{{ route('home') }}">
                            Inicio
                        </a>
                    </li>

                    {{-- Solo clientes autenticados --}}
                    @if($cliente)
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('surveys*.*') ? '' : 'active' }}" 
                            href="{{ route('surveys-answer.index') }}">
                                Encuestas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.catalog') ? '' : 'active' }}" 
                            href="{{ route('products.catalog') }}">
                                Catálogo
                            </a>
                        </li>

                    @endif

                    {{-- Empleado con rol "Técnico" --}}
                    @if($empleado && (in_array('Técnico', $roles)))
                        
                    @endif

                    {{-- Empleado con rol "Administrador" o "Coordinador" --}}
                    @if($empleado && (in_array('Administrador', $roles) || in_array('Coordinador', $roles)))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('products.*') ? '' : 'active' }}" 
                            href="{{ route('products.index') }}">
                                Productos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('providers.*') ? '' : 'active' }}" 
                            href="{{ route('providers.index') }}">
                                Proveedores
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('surveys*.*') ? '' : 'active' }}" href="#" id="menuEncuestas" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Encuestas
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menuEncuestas">
                                <li><a class="dropdown-item" href="{{ route('surveys.index') }}">Administrar Definiciones de Encuestas</a></li>
                                <li><a class="dropdown-item" href="{{ route('surveys-assign.index') }}">Asignar Encuestas</a></li>
                            </ul>
                        </li>
                    @endif

                    {{-- Empleado con rol "Administrador" o "Coordinador" o "Vendedor" --}}
                    @if($empleado && (in_array('Administrador', $roles) || in_array('Coordinador', $roles) || in_array('Vendedor', $roles) ))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clients.*') ? '' : 'active' }}" 
                            href="{{ route('clients.index') }}">
                                Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('purchases.*') ? '' : 'active' }}" 
                            href="{{ route('purchases.index') }}">
                                Compras
                            </a>
                        </li>
                    @endif

                    {{-- Solo para Vendedor --}}
                    @if($empleado && in_array('Vendedor', $roles))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('sales.*') ? '' : 'active' }}" 
                            href="{{ route('sales.index') }}">
                                Ventas
                            </a>
                        </li>
                    @endif

                    {{-- Solo para Administradores --}}
                    @if($empleado && in_array('Administrador', $roles))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ (request()->routeIs('positions.*') || request()->routeIs('employees.*') ) ? '' : 'active' }}" href="#" id="menuAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Gestión de Empleados
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="menuAdmin">
                                <li><a class="dropdown-item" href="{{ route('positions.index') }}">Cargos</a></li>
                                <li><a class="dropdown-item" href="{{ route('employees.index') }}">Empleados</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>


                <ul class="navbar-nav ms-auto">
                    @if($empleado)
                        <li class="nav-item text-white me-3">
                            Empleado: <strong>{{ $empleado->name }}</strong>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-light">Cerrar sesión</button>
                            </form>
                        </li>
                    @elseif($cliente)
                        <li class="nav-item text-white me-3">
                            Cliente: <strong>{{ $cliente->name }}</strong>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-sm btn-outline-light">Cerrar sesión</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="btn btn-sm btn-outline-light" href="{{ route('login') }}">Iniciar sesión</a>
                        </li>
                    @endif
                </ul>

            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')  {{-- 👈 sección para scripts específicos de cada vista --}}
</body>
</html>
