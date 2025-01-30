<div class="product-wrapper rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
    <div class="p-4">
        <div class="img-wrapper">
            <img
                alt="{{ $productData['title'] ?? 'Producto' }}"
                loading="lazy"
                width="200"
                height="200"
                decoding="async"
                class="rounded-lg mb-4 img-product"
                src="{{ $productData['image'] ?? 'https://dummyimage.com/200x200/000/fff&text=Producto' }}"
            >
        </div>
        <h3 class="font-semibold mb-2">{{ $productData['title'] ?? 'Sin título' }}</h3>
        <p class="text-gray-600 mb-2">${{ $productData['price'] ?? '0.00' }}</p>
        <p class="text-gray-600 mb-2">{{ $productData['brand'] ?? 'Desconocida' }}</p>
        <!-- Calificación, Botón "Ver más", etc. -->
        <a href="{{ url('product/' . ($productData['product_id'] . '/amazon/' . $productData['csi'] ?? "#")) }}">
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 h-10 px-4 py-2 w-full bg-[#d94551] hover:bg-[#b01721] text-white">
                Ver más
            </button>
        </a>
    </div>
</div>
