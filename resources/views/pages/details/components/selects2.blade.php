<div class="mb-6">
    <h3 class="font-semibold mb-2"><!-- select_name -->:</h3>
    <!-- CONTENEDOR DEL SELECT CUSTOM -->
    <div class="relative inline-block w-[180px]" id="sizeSelectContainer">

        <!-- BOTÓN que se ve siempre -->
        <button
            id="sizeSelectButton"
            type="button"
            class="flex h-10 w-full items-center justify-between
             rounded-md border border-input bg-background px-3 py-2 text-sm
             ring-offset-background focus:outline-none focus:ring-2
             focus:ring-ring focus:ring-offset-2"
        >
            <span id="sizeSelectLabel">Seleccionar</span>
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="24" height="24"
                 viewBox="0 0 24 24"
                 fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round"
                 stroke-linejoin="round"
                 class="lucide lucide-chevron-down h-4 w-4 opacity-50"
            >
                <path d="m6 9 6 6 6-6"></path>
            </svg>
        </button>

        <!-- LISTA DESPLEGABLE con las tallas -->
        <div
            id="sizeOptions"
            class="hidden absolute z-50 w-full bg-white border border-gray-200
             rounded shadow-md mt-1"
        >
            <ul>
                <!-- lis -->
                <!--<li class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-size="XS">XS</li>
                <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-size="S">S</li>
                <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-size="M">M</li>
                <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-size="L">L</li>
                <li class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-size="XL">XL</li>-->
            </ul>
        </div>

        <!-- SELECT REAL (OCULTO) PARA EL FORMULARIO -->
        <select
            id="hiddenSizeSelect"
            name="size"
            class="hidden"
        >
            <!-- options -->
            <!--<option value="">Selecciona un talle</option>
            <option value="XS">XS</option>
            <option value="S">S</option>
            <option value="M">M</option>
            <option value="L">L</option>
            <option value="XL">XL</option>-->
        </select>
    </div>
</div>
