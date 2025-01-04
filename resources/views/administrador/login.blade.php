@extends('layouts.administrador.login')

@section('content')
    <div class="flex min-h-screen items-center justify-center bg-gray-100">
        <div class="w-full max-w-md space-y-8 rounded-lg bg-white p-6 shadow-md">

            {{-- Mensaje de error --}}
            @if(session('error'))
                <div class="mb-4 text-red-600">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('showLogoutLink'))
                <div class="mb-4">
                    <a
                        href="{{ route('logout.admin') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="text-sm font-semibold text-blue-600 hover:underline"
                    >
                        Salir
                    </a>
                    <form id="logout-form" action="{{ route('logout.admin') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            @endif

            <div class="text-center">
                <h1 class="text-3xl font-bold">Iniciar Sesión</h1>
                <p class="mt-2 text-sm text-gray-600">Ingrese sus credenciales para acceder al dashboard</p>
            </div>

            <form action="{{ route('administrador.login.perform') }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label for="admin_email" class="text-sm font-medium">Correo Electrónico</label>
                    <input
                        id="admin_email"
                        name="email"
                        type="email"
                        required
                        class="w-full rounded-md border px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-500"
                        value="{{ old('email') }}"
                    />
                </div>

                <div class="space-y-2">
                    <label for="admin_password" class="text-sm font-medium">Contraseña</label>
                    <input
                        id="admin_password"
                        name="password"
                        type="password"
                        required
                        class="w-full rounded-md border px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-500"
                    />
                </div>

                <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full" type="submit">Iniciar Sesión</button>
            </form>
        </div>
    </div>
@endsection
