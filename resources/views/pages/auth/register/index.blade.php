@extends('layouts.app')

@section('content')
    <div class="w-full max-w-md">
        <h1 class="text-3xl font-bold mb-6 text-center">Registrarse</h1>

        {{-- Mostrar error global si existiera (por ejemplo credenciales inválidas) --}}
        @include('components.alerts-error')

        <form class="space-y-4"
              action="{{ route('register.perform') }}"
              method="POST"
              data-validate="true"
              novalidate
        >
            @csrf

            <div>
                <label class="text-sm font-medium" for="name">Nombre completo</label>
                <input
                    class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm
                           ring-offset-background placeholder:text-muted-foreground
                           focus-visible:outline-none focus-visible:ring-2
                           focus-visible:ring-ring focus-visible:ring-offset-2
                           disabled:cursor-not-allowed disabled:opacity-50
                           @error('name') border-red-500 @enderror
                    "
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                />
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium" for="email">Correo electrónico</label>
                <input
                    class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm
                           ring-offset-background placeholder:text-muted-foreground
                           focus-visible:outline-none focus-visible:ring-2
                           focus-visible:ring-ring focus-visible:ring-offset-2
                           disabled:cursor-not-allowed disabled:opacity-50
                           @error('password') border-red-500 @enderror
                    "
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                />
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium" for="password">Contraseña</label>
                <input
                    class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm
                           ring-offset-background placeholder:text-muted-foreground
                           focus-visible:outline-none focus-visible:ring-2
                           focus-visible:ring-ring focus-visible:ring-offset-2
                           disabled:cursor-not-allowed disabled:opacity-50
                           @error('password') border-red-500 @enderror
                    "
                    id="password"
                    name="password"
                    type="password"
                    required
                />
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-medium" for="confirmPassword">Confirmar contraseña</label>
                <input
                    class="flex h-10 w-full rounded-md border bg-background px-3 py-2 text-sm
                           ring-offset-background placeholder:text-muted-foreground
                           focus-visible:outline-none focus-visible:ring-2
                           focus-visible:ring-ring focus-visible:ring-offset-2
                           disabled:cursor-not-allowed disabled:opacity-50
                           @error('password_confirmation') border-red-500 @enderror
                    "
                    id="confirmPassword"
                    name="password_confirmation"
                    type="password"
                    required
                />
                @error('password_confirmation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                class="inline-flex items-center justify-center rounded-md text-sm font-medium
                       bg-primary text-primary-foreground hover:bg-primary/90
                       h-10 px-4 py-2 w-full"
                type="submit"
            >
                Registrarse
            </button>
        </form>

        <div class="shrink-0 bg-border h-[1px] w-full my-6"></div>
        <div class="space-y-4">
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"></path>
                    <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"></path>
                    <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"></path>
                    <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"></path>
                    <path fill="none" d="M1 1h22v22H1z"></path>
                </svg>
                Registrarse con Google
            </button>
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"></path>
                </svg>
                Registrarse con Facebook
            </button>
        </div>
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">¿Ya tienes una cuenta? <a href="/login" class="text-blue-600 hover:underline">Ingresar</a></p>
        </div>
    </div>

    {{-- Script para validación en tiempo real --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[data-validate="true"]');
            if (form) {
                // 1. Validación al hacer submit
                form.addEventListener('submit', function(e) {
                    let hasError = false;
                    const requiredInputs = form.querySelectorAll('input[required]');

                    requiredInputs.forEach((input) => {
                        // Elimina clase e imagen de error previos
                        removeValidationError(input);

                        if (!input.value.trim()) {
                            hasError = true;
                            showValidationError(input, 'Este campo es requerido');
                        }
                    });

                    if (hasError) {
                        e.preventDefault();
                    }
                });

                // 2. Limpiar error cuando el usuario escribe en el campo
                const allInputs = form.querySelectorAll('input');
                allInputs.forEach((input) => {
                    input.addEventListener('input', () => {
                        removeValidationError(input);
                    });
                });
            }

            // Función para quitar la clase y mensaje de error
            function removeValidationError(input) {
                input.classList.remove('border-red-500');
                const existingMsg = input.parentNode.querySelector('.js-error-empty');
                if (existingMsg) {
                    existingMsg.remove();
                }
            }

            // Función para mostrar error en un campo
            function showValidationError(input, message) {
                input.classList.add('border-red-500');
                const errorP = document.createElement('p');
                errorP.textContent = message;
                errorP.classList.add('text-red-500', 'text-sm', 'mt-1', 'js-error-empty');
                input.parentNode.appendChild(errorP);
            }
        });
    </script>
@endsection
