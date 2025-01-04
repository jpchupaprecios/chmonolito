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
                {{ //LIS }}
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
