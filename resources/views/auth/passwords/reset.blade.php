{{-- resources/views/auth/passwords/reset.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="max-w-md mx-auto my-12">
        <h2 class="text-2xl font-bold mb-4 text-center">Restablecer Contraseña</h2>

        {{-- Si existe algún error general, se puede mostrar aquí, por ejemplo --}}
        @if($errors->any())
            <div class="mb-4 text-red-600">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            {{-- El token y el email se pasan ocultos; vienen en el enlace de reset --}}
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

            {{-- Nueva contraseña --}}
            <div>
                <label for="password" class="block text-sm font-medium mb-1">
                    Nueva Contraseña
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="w-full border rounded px-3 py-2
                       @error('password') border-red-500 @enderror"
                    required
                    autofocus
                >
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmar contraseña --}}
            <div>
                <label for="password-confirm" class="block text-sm font-medium mb-1">
                    Confirmar Contraseña
                </label>
                <input
                    id="password-confirm"
                    type="password"
                    name="password_confirmation"
                    class="w-full border rounded px-3 py-2"
                    required
                >
            </div>

            <button
                type="submit"
                class="inline-flex items-center justify-center
                   rounded-md text-sm font-medium bg-primary
                   text-white hover:bg-primary/90 h-10 px-4 py-2 w-full"
            >
                Restablecer Contraseña
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">
                Volver al Login
            </a>
        </div>
    </div>
@endsection
