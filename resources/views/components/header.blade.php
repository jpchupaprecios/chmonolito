<!-- resources/views/components/header.blade.php (o donde prefieras) -->
<header class="py-4 px-4 sm:px-6 lg:px-8 bg-white shadow-sm">
    <div class="container mx-auto flex justify-between items-center">
        <div class="flex-1"></div>
        <!-- Logo o Nombre de tu sitio -->
        <a href="/" class="flex items-center justify-center">
            <img
                alt="Venia"
                loading="lazy"
                width="150"
                height="40"
                decoding="async"
                class="h-10 w-auto"
                src="https://staging.chupaprecios.com.mx/chupaprecioslogo-bLx.svg"
            />
        </a>

        <!-- Navegación derecha -->
        <nav class="flex-1 flex justify-end">
            <ul class="flex items-center space-x-4">
                @auth
                    <!-- SI el usuario está logueado -->

                    <!-- Dropdown de Usuario -->
                    <li class="relative" id="userDropdownContainer">
                        <button
                            id="userDropdownButton"
                            class="inline-flex items-center justify-center
                                   rounded-md text-sm font-medium ring-offset-background
                                   transition-colors focus-visible:outline-none
                                   focus-visible:ring-2 focus-visible:ring-ring
                                   focus-visible:ring-offset-2
                                   disabled:pointer-events-none disabled:opacity-50
                                   hover:bg-accent hover:text-accent-foreground
                                   h-10 w-10 relative"
                            aria-label="Menú de usuario"
                        >
                            <!-- Icono de Usuario -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24" height="24"
                                viewBox="0 0 24 24"
                                fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-user h-5 w-5"
                            >
                                <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M4 21v-2a4 4 0 0 1 3-3.87"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </button>
                        <!-- Menu desplegable (dropdown) -->

                        <div  style="position: absolute;" data-radix-popper-content-wrapper="" dir="ltr" style="left: 0px; top: 0px; transform: translate(990px, 96px); min-width: max-content; --radix-popper-transform-origin: 100% 0px; z-index: 50; --radix-popper-available-width: 1118px; --radix-popper-available-height: 870px; --radix-popper-anchor-width: 40px; --radix-popper-anchor-height: 40px;">
                            <div id="userDropdownMenu" data-side="bottom" data-align="end" role="menu" aria-orientation="vertical" data-state="open"
                                 data-radix-menu-content="" dir="ltr" id="radix-:rb:" aria-labelledby="radix-:ra:"
                                 class="hidden z-50 min-w-[8rem] overflow-hidden rounded-md border bg-popover p-1 text-popover-foreground shadow-md data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-95 data-[side=bottom]:slide-in-from-top-2 data-[side=left]:slide-in-from-right-2 data-[side=right]:slide-in-from-left-2 data-[side=top]:slide-in-from-bottom-2" tabindex="-1" data-orientation="vertical" style="outline: none; --radix-dropdown-menu-content-transform-origin: var(--radix-popper-transform-origin); --radix-dropdown-menu-content-available-width: var(--radix-popper-available-width); --radix-dropdown-menu-content-available-height: var(--radix-popper-available-height); --radix-dropdown-menu-trigger-width: var(--radix-popper-anchor-width); --radix-dropdown-menu-trigger-height: var(--radix-popper-anchor-height); pointer-events: auto;">
                                <!-- “Mi cuenta” link -->
                                <a href="/mi-cuenta"
                                   role="menuitem"
                                   class="relative flex cursor-default select-none items-center gap-2 rounded-sm
          px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent
          focus:text-accent-foreground"
                                   tabindex="-1"
                                >
                                    Mi cuenta
                                </a>

                                <!-- Botón Salir: en lugar de un div, usa un formulario con POST -->
                                <form
                                    action="{{ route('logout') }}"
                                    method="POST"
                                    role="menuitem"
                                    class="relative flex cursor-default select-none items-center gap-2 rounded-sm
         px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent
         focus:text-accent-foreground"
                                    tabindex="-1"
                                    style="margin: 0;"
                                >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-2 w-full text-left"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24" height="24"
                                            viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-log-out mr-2 h-4 w-4"
                                        >
                                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2
               0 0 1 2-2h4"
                                            ></path>
                                            <polyline points="16 17 21 12 16 7"></polyline>
                                            <line x1="21" x2="9" y1="12" y2="12"></line>
                                        </svg>
                                        <span>Salir</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>

                    <!-- MiniCart -->
                    <li>
                        @include('components.minicart')
                    </li>

                @else
                    <!-- SI el usuario NO está logueado -->

                    <li><a href="/login">Ingresar</a></li>
                    <li><a href="/register">Registrarse</a></li>
                    <li>
                        @include('components.minicart')
                    </li>

                @endauth
            </ul>
        </nav>
    </div>
</header>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userDropdownButton = document.getElementById('userDropdownButton');
        const userDropdownMenu = document.getElementById('userDropdownMenu');

        if (userDropdownButton && userDropdownMenu) {
            // Al hacer click en el icono de usuario, alternamos la clase .hidden
            userDropdownButton.addEventListener('click', () => {
                userDropdownMenu.classList.toggle('hidden');
            });

            // Opcional: cerrar el dropdown si se hace click fuera
            document.addEventListener('click', (e) => {
                if (!userDropdownButton.contains(e.target) &&
                    !userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                }
            });
        }
    });
</script>
