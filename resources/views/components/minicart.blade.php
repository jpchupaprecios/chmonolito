<!-- Botón del carrito + Dropdown -->
<div class="relative inline-block text-left" id="cartDropdown">
    <!-- Botón que abre/cierra el dropdown -->
    <button
        id="cartButton"
        class="inline-flex items-center justify-center rounded-md text-sm font-medium
           ring-offset-background transition-colors focus-visible:outline-none
           focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2
           disabled:pointer-events-none disabled:opacity-50 hover:bg-accent
           hover:text-accent-foreground h-10 w-10 relative"
        type="button"
        aria-label="Carrito de compras"
    >
        <!-- Ícono de carrito -->
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24" height="24" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-shopping-cart h-6 w-6"
        >
            <circle cx="8" cy="21" r="1"></circle>
            <circle cx="19" cy="21" r="1"></circle>
            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78
               a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12">
            </path>
        </svg>

        <!-- Badge con el número de items en el carrito -->
        <div
            id="cartBadge"
            class="inline-flex items-center rounded-full border font-semibold
             transition-colors focus:outline-none focus:ring-2 focus:ring-ring
             focus:ring-offset-2 border-transparent bg-destructive text-destructive-foreground
             hover:bg-destructive/80 absolute -top-2 -right-2 px-2 py-1 text-xs"
        >
            0
        </div>
    </button>

    <!-- Contenido del dropdown: inicialmente oculto con "hidden" -->
    <div
        id="cartMenu"
        class="hidden absolute z-50 w-80 right-0 bg-white border border-gray-200
           rounded shadow-md"
    >
        <div class="p-4">
            <h2 class="text-lg font-semibold mb-4">Carrito de Compras</h2>

            <!-- Aquí inyectaremos los items con JS -->
            <div id="cartContent"></div>

            <!-- Alerta si el carrito está vacío -->
            <p id="cartEmptyMsg" class="text-gray-500 hidden">
                Tu carrito está vacío
            </p>

            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between mb-4">
                    <span class="font-semibold">Total:</span>
                    <span id="cartTotalValue">$0.00</span>
                </div>
                <button
                    id="btnVerCarrito"
                    class="w-full bg-[#d94551] hover:bg-[#b01721] text-white py-2 rounded"
                >
                    Ver Carrito
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    // 1. Nuestro "estado" global del carrito
    let cartItems = [
        { id: 1, name: 'Camiseta Premium', price: 29.99, image: 'https://dummyimage.com/80x80/000/fff&text=Camiseta', quantity: 1 },
        { id: 2, name: 'Pantalón Vaquero', price: 49.99, image: 'https://dummyimage.com/80x80/000/fff&text=Pantalón', quantity: 1 },
        { id: 3, name: 'Zapatillas Deportivas', price: 79.99, image: 'https://dummyimage.com/80x80/000/fff&text=Zapatillas', quantity: 1 },
    ];

    // 2. Seleccionamos elementos del DOM
    const cartButton      = document.getElementById('cartButton');
    const cartMenu        = document.getElementById('cartMenu');
    const cartBadge       = document.getElementById('cartBadge');
    const cartContent     = document.getElementById('cartContent');
    const cartEmptyMsg    = document.getElementById('cartEmptyMsg');
    const cartTotalValue  = document.getElementById('cartTotalValue');
    const btnVerCarrito   = document.getElementById('btnVerCarrito');

    // 3. Función para recalcular y renderizar el contenido del carrito
    function renderCart() {
// Si no hay items, mostramos el mensaje de "Carrito vacío" y ocultamos la lista
        if (cartItems.length === 0) {
            cartContent.innerHTML = '';
            cartEmptyMsg.classList.remove('hidden');
        } else {
            cartEmptyMsg.classList.add('hidden');
// Generamos el HTML para cada producto
            let html = '';
            cartItems.forEach((item) => {
                html += `
      <div class="flex items-center mb-4">
        <img
          src="${item.image}"
          alt="${item.name}"
          width="50"
          height="50"
          class="rounded-md mr-4"
        />
        <div class="flex-grow">
          <h3 class="text-sm font-medium">${item.name}</h3>
          <p class="text-sm text-gray-500">$${item.price.toFixed(2)}</p>
          <div class="flex items-center mt-1">
            <!-- Botón restar -->
            <button
              class="h-6 w-6 border rounded btn-decrease-qty"
              data-itemid="${item.id}"
            >
              –
            </button>
            <span class="mx-2 text-sm">${item.quantity}</span>
            <!-- Botón sumar -->
            <button
              class="h-6 w-6 border rounded btn-increase-qty"
              data-itemid="${item.id}"
            >
              +
            </button>
          </div>
        </div>
        <!-- Botón eliminar -->
        <button
          class="h-8 w-8 text-gray-400 hover:text-gray-600 btn-remove-item"
          data-itemid="${item.id}"
        >
          <!-- Ícono de basura -->
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 h-4 w-4"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" x2="10" y1="11" y2="17"></line><line x1="14" x2="14" y1="11" y2="17"></line></svg>
        </button>
      </div>
    `;
            });
            cartContent.innerHTML = html;
        }

// Calculamos el total multiplicando precio * cantidad
        const total = cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
        cartTotalValue.textContent = '$' + total.toFixed(2);

// Calculamos la cantidad total de productos (sumando quantities)
        const totalItems = cartItems.reduce((acc, cur) => acc + cur.quantity, 0);
        if (totalItems > 0) {
            cartBadge.textContent = totalItems;
            cartBadge.classList.remove('hidden');
        } else {
            cartBadge.classList.add('hidden');
        }
    }

    // 4. Funciones para manejar cantidad y eliminación
    function updateQuantity(itemId, delta) {
        cartItems = cartItems.map((item) => {
            if (item.id === Number(itemId)) {
                // nueva cantidad
                const newQty = item.quantity + delta;
                // si al restar queda 0 o menos, lo manejamos aparte
                return { ...item, quantity: newQty < 1 ? 1 : newQty };
            }
            return item;
        });
        renderCart();
    }

    function removeItem(itemId) {
        cartItems = cartItems.filter((item) => item.id !== Number(itemId));
        renderCart();
    }

    // 5. Al hacer click en un botón (+ / - / eliminar), delegamos en cartContent
    cartContent.addEventListener('click', (e) => {
// Botón de sumar
        if (e.target.classList.contains('btn-increase-qty')) {
            const itemId = e.target.dataset.itemid;
            updateQuantity(itemId, 1);
        }
// Botón de restar
        else if (e.target.classList.contains('btn-decrease-qty')) {
            const itemId = e.target.dataset.itemid;
            updateQuantity(itemId, -1);
        }
// Botón eliminar
        else if (e.target.classList.contains('btn-remove-item')) {
            const itemId = e.target.dataset.itemid;
            removeItem(itemId);
        }
    });

    // 6. Mostrar/ocultar el menú al hacer click en el carrito
    cartButton.addEventListener('click', () => {
        cartMenu.classList.toggle('hidden');
    });

    // 7. Redirigir al carrito (simulando router.push('/carrito'))
    btnVerCarrito.addEventListener('click', () => {
        window.location.href = '/cart';
    });

    // 8. Render inicial
    renderCart();

</script>
