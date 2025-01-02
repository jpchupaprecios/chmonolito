<div class="flex items-center space-x-2 mb-4">
    <!-- Botón “-” -->
    <button
        id="btnMinus"
        class="inline-flex items-center justify-center
           rounded-md text-sm font-medium ring-offset-background
           transition-colors focus-visible:outline-none
           focus-visible:ring-2 focus-visible:ring-ring
           focus-visible:ring-offset-2 disabled:pointer-events-none
           disabled:opacity-50 border border-input bg-background
           hover:bg-accent hover:text-accent-foreground h-10 w-10"
    >
        <span class="sr-only">Decrementar cantidad</span>
        <svg
            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        >
            <path d="M5 12h14"></path>
        </svg>
    </button>

    <!-- Input para cantidad -->
    <input
        id="quantityInput"
        class="flex h-10 rounded-md border border-input bg-background px-3 py-2 text-sm
           ring-offset-background file:border-0 file:bg-transparent
           file:text-sm file:font-medium file:text-foreground
           placeholder:text-muted-foreground focus-visible:outline-none
           focus-visible:ring-2 focus-visible:ring-ring
           focus-visible:ring-offset-2 disabled:cursor-not-allowed
           disabled:opacity-50 w-20 text-center"
        type="number"
        min="1"
        value="1"
    />

    <!-- Botón “+” -->
    <button
        id="btnPlus"
        class="inline-flex items-center justify-center
           rounded-md text-sm font-medium ring-offset-background
           transition-colors focus-visible:outline-none
           focus-visible:ring-2 focus-visible:ring-ring
           focus-visible:ring-offset-2 disabled:pointer-events-none
           disabled:opacity-50 border border-input bg-background
           hover:bg-accent hover:text-accent-foreground h-10 w-10"
    >
        <span class="sr-only">Incrementar cantidad</span>
        <svg
            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        >
            <path d="M5 12h14"></path>
            <path d="M12 5v14"></path>
        </svg>
    </button>
</div>
<script>
    const btnMinus = document.getElementById('btnMinus')
    const btnPlus = document.getElementById('btnPlus')
    const quantityInput = document.getElementById('quantityInput')

    // Función para actualizar el estado “disable” del botón “-”
    function updateMinusButton() {
        const currentVal = parseInt(quantityInput.value, 10) || 1
        btnMinus.disabled = (currentVal <= 1)
    }

    // Al cargar, verificar si debemos deshabilitar “-”
    updateMinusButton()

    // Botón “-”
    btnMinus.addEventListener('click', () => {
        let currentVal = parseInt(quantityInput.value, 10) || 1
        if (currentVal > 1) {
            currentVal--
            quantityInput.value = currentVal
            updateMinusButton()
        }
    })

    // Botón “+”
    btnPlus.addEventListener('click', () => {
        let currentVal = parseInt(quantityInput.value, 10) || 1
        currentVal++
        quantityInput.value = currentVal
        updateMinusButton()
    })

    // Si el usuario escribe manualmente en el input
    quantityInput.addEventListener('input', () => {
        let val = parseInt(quantityInput.value, 10)
        if (isNaN(val) || val < 1) {
            val = 1
        }
        quantityInput.value = val
        updateMinusButton()
    })
</script>
