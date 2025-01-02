<div class="mb-4" id="colorSection">
    <h3 class="font-semibold mb-2">Color:</h3>
    <div class="flex space-x-2">
        <!-- Botón color Blanco -->
        <button
            type="button"
            class="color-button w-8 h-8 rounded-full border-2 focus:outline-none
             focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
             border-gray-300"
            style="background-color: white;"
            data-color="Blanco"
            aria-label="Blanco"
        ></button>

        <!-- Botón color Negro -->
        <button
            type="button"
            class="color-button w-8 h-8 rounded-full border-2 focus:outline-none
             focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
             border-gray-300"
            style="background-color: black;"
            data-color="Negro"
            aria-label="Negro"
        ></button>

        <!-- Botón color Azul -->
        <button
            type="button"
            class="color-button w-8 h-8 rounded-full border-2 focus:outline-none
             focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
             border-gray-300"
            style="background-color: blue;"
            data-color="Azul"
            aria-label="Azul"
        ></button>

        <!-- Botón color Rojo -->
        <button
            type="button"
            class="color-button w-8 h-8 rounded-full border-2 focus:outline-none
             focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
             border-gray-300"
            style="background-color: red;"
            data-color="Rojo"
            aria-label="Rojo"
        ></button>
    </div>

    <!-- (Opcional) INPUT HIDDEN para enviar el color elegido en un form -->
    <input
        type="hidden"
        id="colorInput"
        name="color"
        value=""
    />
</div>
<script>
    // Tomamos todos los botones de color
    const colorButtons = document.querySelectorAll('.color-button');
    // Tomamos el input oculto (si lo usamos)
    const hiddenColorInput = document.getElementById('colorInput');

    // Función que marca un botón como seleccionado
    function setSelectedColor(button) {
        // 1. Quitamos el “anillo” (ring) de todos los botones
        colorButtons.forEach((btn) => {
            btn.classList.remove('ring-2', 'ring-offset-2', 'ring-blue-500');
        });
        // 2. Agregamos el anillo al botón clicado
        button.classList.add('ring-2', 'ring-offset-2', 'ring-blue-500');

        // 3. Actualizamos el valor del input oculto
        if (hiddenColorInput) {
            hiddenColorInput.value = button.dataset.color;
        }
    }

    // Asignamos el evento click a cada botón
    colorButtons.forEach((btn, index) => {
        btn.addEventListener('click', () => {
            setSelectedColor(btn);
        });
    });

    // (Opcional) Seleccionar por defecto el primer color,
    // o cualquier lógica inicial que quieras.
    if (colorButtons.length > 0) {
        setSelectedColor(colorButtons[0]);
    }
</script>
