<div class="my-8 flex justify-center">
    <!-- En tu formulario -->
    <form action="/results" class="flex w-full max-w-3xl" method="GET">
        <div class="relative flex-grow">
            <!-- Input de búsqueda -->
            <input
                type="search"
                name="q"
                placeholder="Buscar en la tienda..."
                class="w-full pr-20 flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm
             ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium
             file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none
             focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2
             disabled:cursor-not-allowed disabled:opacity-50"
            />

            <!-- CONTENEDOR DEL SELECT CUSTOM -->
            <div id="customSelect" class="absolute right-0 top-0 bottom-0 w-[120px]">

                <!-- BOTÓN (lo que se ve siempre) -->
                <button
                    id="selectButton"
                    type="button"
                    class="flex h-10 w-full items-center justify-between rounded-md border border-input
               bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground
               focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2
               disabled:cursor-not-allowed disabled:opacity-50 rounded-l-none"
                >
                    <span id="selectLabel">Amazon</span>
                    <!-- Flechita -->
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-chevron-down h-4 w-4 opacity-50"
                    >
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>

                <!-- LISTA DESPLEGABLE (inicialmente oculta con hidden) -->
                <div
                    id="selectOptions"
                    class="hidden absolute z-50 w-full bg-white border border-gray-200 rounded shadow-md mt-1"
                >
                    <ul>
                        <li
                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                            data-value="amazon"
                        >
                            Amazon
                        </li>
                        <li
                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                            data-value="ebay"
                        >
                            Ebay
                        </li>
                        <li
                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                            data-value="walmart"
                        >
                            Walmart
                        </li>
                        <li
                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                            data-value="homedepot"
                        >
                            Home Depot
                        </li>
                    </ul>
                </div>

                <!-- SELECT REAL (oculto) para enviar el valor en el form -->
                <select
                    id="hiddenSelect"
                    name="store"
                    class="hidden"
                >
                    <option value="amazon" selected>Amazon</option>
                    <option value="ebay">Ebay</option>
                    <option value="walmart">Walmart</option>
                    <option value="homedepot">Home Depot</option>
                </select>
            </div>
        </div>

        <!-- Botón de Submit -->
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-md text-sm font-medium
           ring-offset-background transition-colors focus-visible:outline-none
           focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2
           disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground
           hover:bg-primary/90 h-10 w-10 ml-2"
        >
            <!-- Icono de búsqueda -->
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24" height="24"
                viewBox="0 0 24 24"
                fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round"
                class="lucide lucide-search h-4 w-4"
            >
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </svg>
            <span class="sr-only">Buscar</span>
        </button>
    </form>

</div>
<script>
    // Referencias a los elementos
    const selectButton = document.getElementById('selectButton');
    const selectLabel = document.getElementById('selectLabel');
    const selectOptions = document.getElementById('selectOptions');
    const hiddenSelect = document.getElementById('hiddenSelect');

    // 1. Mostrar / Ocultar la lista al hacer click en el botón
    selectButton.addEventListener('click', () => {
        selectOptions.classList.toggle('hidden');
    });

    // 2. Al hacer click en alguna opción, actualizamos el label y el <select>
    selectOptions.addEventListener('click', (e) => {
        // Verifica si se hizo click en un <li>
        if (e.target.matches('li[data-value]')) {
            const value = e.target.getAttribute('data-value');
            const text = e.target.textContent;

            // Actualizamos el texto que se ve en el botón
            selectLabel.textContent = text;

            // Actualizamos el <select> oculto
            hiddenSelect.value = value;

            // Ocultamos el desplegable
            selectOptions.classList.add('hidden');
        }
    });

    // 3. (Opcional) Si quieres cerrar el dropdown cuando el usuario hace click fuera
    document.addEventListener('click', (e) => {
        // Si el click NO fue dentro de `selectButton` ni de `selectOptions`,
        // cerramos el dropdown (añadimos la clase hidden).
        if (
            !selectButton.contains(e.target) &&
            !selectOptions.contains(e.target)
        ) {
            selectOptions.classList.add('hidden');
        }
    });
</script>
