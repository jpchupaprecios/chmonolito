<div class="mt-8" id="tabsContainer">
    <!-- Botones -->
    <div class="inline-flex h-10 items-center justify-center
              rounded-md bg-muted p-1 text-muted-foreground"
         id="tabsHeader"
    >
        <!-- Botón Descripción -->
        <button
            id="tabBtnDescription"
            class="inline-flex items-center justify-center
             whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium
             focus-visible:outline-none focus-visible:ring-2
             focus-visible:ring-ring focus-visible:ring-offset-2
             data-[active=true]:bg-background data-[active=true]:text-foreground
             data-[active=true]:shadow-sm"
            data-tab-target="description"
            data-active="true"
        >
            Descripción
        </button>
        <!-- Botón Características -->
        <button
            id="tabBtnCharacteristics"
            class="inline-flex items-center justify-center
             whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium
             focus-visible:outline-none focus-visible:ring-2
             focus-visible:ring-ring focus-visible:ring-offset-2
             data-[active=true]:bg-background data-[active=true]:text-foreground
             data-[active=true]:shadow-sm"
            data-tab-target="characteristics"
            data-active="false"
        >
            Características
        </button>
    </div>

    <!-- Panel Descripción -->
    <div
        id="tabPanelDescription"
        data-tab-content="description"
        class="mt-2"
        style="display: block;"
    >
        <!-- aquí tu contenido de descripción -->
        <p>Esta camiseta premium está hecha con 100% algodón orgánico...</p>
        <div class="mt-4">
            <img
                src="https://dummyimage.com/600x600/000/fff&text=Producto%201"
                alt="Camiseta Premium"
                class="rounded-lg"
            />
        </div>
    </div>

    <!-- Panel Características -->
    <div
        id="tabPanelCharacteristics"
        data-tab-content="characteristics"
        class="mt-2"
        style="display: none;" >
        <ul class="list-disc pl-5 space-y-2">
            <li>Material: 100% Algodón Orgánico</li>
            <li>Peso: 180g/m²</li>
            <li>Cuello redondo reforzado</li>
            <li>Costuras dobles en mangas y dobladillo</li>
            <li>Lavable a máquina</li>
            <li>Fabricado éticamente</li>
        </ul>
    </div>
</div>
<script>
    // Obtenemos referencia al contenedor y a los botones
    const tabsContainer = document.getElementById('tabsContainer')
    const tabButtons = tabsContainer.querySelectorAll('[data-tab-target]')
    const tabPanels = tabsContainer.querySelectorAll('[data-tab-content]')

    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const target = btn.getAttribute('data-tab-target')

            // 1. Marcamos todos los botones como inactivos
            tabButtons.forEach((b) => {
                b.setAttribute('data-active', 'false')
            })
            // 2. Ocultamos todos los paneles
            tabPanels.forEach((panel) => {
                panel.style.display = 'none'
            })
            // 3. Activamos el botón clicado
            btn.setAttribute('data-active', 'true')

            // 4. Mostramos el panel correspondiente
            const panelToShow = tabsContainer.querySelector(`[data-tab-content="${target}"]`)
            if (panelToShow) {
                panelToShow.style.display = 'block'
            }
        })
    })
</script>
