{{-- resources/views/auth/passwords/email.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto my-12">
        <h2 class="text-2xl font-bold mb-4 text-center">Recuperar contraseña</h2>

        <!-- Si el envío del correo de reseteo fue exitoso, se mostrará el mensaje "status" -->
        @if (session('status'))
            <div class="mb-4 text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <!-- Formulario para solicitar enlace de reseteo -->
        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium mb-1">
                    Correo electrónico
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    class="w-full border rounded px-3 py-2
                       @error('email') border-red-500 @enderror"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="inline-flex items-center justify-center
                   rounded-md text-sm font-medium bg-primary
                   text-white hover:bg-primary/90 h-10 px-4 py-2 w-full"
            >
                Enviar enlace de recuperación
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                Volver al Login
            </a>
        </div>
    </div>
@endsection
