<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/styles/styles.css" data-precedence="next">
    <link rel="stylesheet" href="/assets/styles/layout.css" data-precedence="next">

    <meta name="next-size-adjust" content="">
    <title>v0</title>
    <style>@import url(https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap);</style>
    <style>@import url(https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600&display=swap);</style>
    <link href="https://fonts.googleapis.com/css2?family=Inter&amp;display=swap" rel="stylesheet">
    <style>.f_SW50ZXI{
            font-family: 'Inter';
        }
    </style>
    <link rel="preload" href="https://via.placeholder.com/1200x500/666666/FFFFFF?text=Banner+1" as="image">
</head>
<body class="f_SW50ZXI">
<style type="text/tailwindcss">@layer base {
        * {
            @apply border-border;
        }
        html {
            @apply scroll-smooth;
        }
        body {
            font-synthesis-weight: none;
            text-rendering: optimizeLegibility;
        }
    }
    @layer utilities {
        .step {
            counter-increment: step;
        }
        .step:before {
            @apply absolute w-9 h-9 bg-muted rounded-full font-mono font-medium text-center text-base inline-flex items-center justify-center -indent-px border-4 border-background;
            @apply ml-[-50px] mt-[-4px];
            content: counter(step);
        }
        .chunk-container {
            @apply shadow-none;
        }
        .chunk-container::after {
            content: '';
            @apply absolute -inset-4 shadow-xl rounded-xl border;
        }
    }
    @media (max-width: 640px) {
        .container {
            @apply px-4;
        }
    }
    .dark {
        /* Dark mode shadcn colors */
        --background: 240 10% 3.9%;
        --foreground: 0 0% 98%;
        --muted: 240 3.7% 15.9%;
        --muted-foreground: 240 5% 64.9%;
        --card: 240 10% 3.9%;
        --card-foreground: 0 0% 98%;
        --popover: 240 10% 3.9%;
        --popover-foreground: 0 0% 98%;
        --border: 240 3.7% 15.9%;
        --input: 240 3.7% 15.9%;
        --primary: 0 0% 98%;
        --primary-foreground: 240 5.9% 10%;
        --secondary: 240 3.7% 15.9%;
        --secondary-foreground: 0 0% 98%;
        --accent: 240 3.7% 15.9%;
        --accent-foreground: ;
        --destructive: 0 62.8% 30.6%;
        --destructive-foreground: 0 85.7% 97.3%;
        --warning: 35, 100%, 52%;
        --warning-foreground: 0 0% 9%;
        --ring: 240 3.7% 15.9%;
        --sidebar-background: 240 5.9% 10%;
        --sidebar-foreground: 240 4.8% 95.9%;
        --sidebar-primary: 224.3 76.3% 48%;
        --sidebar-primary-foreground: 0 0% 100%;
        --sidebar-accent: 240 3.7% 15.9%;
        --sidebar-accent-foreground: 240 4.8% 95.9%;
        --sidebar-border: 240 3.7% 15.9%;
        --sidebar-ring: 240 4.9% 83.9%;
    }
</style>
<style style="">  :root {
        --font-sans: __variable_3a0388;
        --font-mono: __variable_c1e5c9;
    }
</style>
<div class="flex flex-col min-h-screen">
    @include('components.marquee')
    <main class="flex-grow">
        @include('components.header')
        @include('components.categories')
        <div class="container mx-auto px-4 flex-grow">
            @include('components.search')
            <div class="w-full h-[500px] mb-8">
                <div class="relative w-full h-full rounded-lg overflow-hidden">
                    <div aria-roledescription="carousel" class="relative w-full h-full" role="region">
                        <div class="overflow-hidden" style="transform: translate3d(0px, 0px, 0px);">
                            <div class="flex -ml-4 h-full" style="transform: translate3d(0px, 0px, 0px);">
                                <div aria-roledescription="slide" class="min-w-0 shrink-0 grow-0 pl-4 basis-full h-full" role="group" style="transform: translate3d(0px, 0px, 0px);">
                                    <div class="w-full h-full">
                                        <img alt="Banner promocional 1" width="1200" height="500" decoding="async" data-nimg="1" class="w-full h-full object-cover" src="https://via.placeholder.com/1200x500/666666/FFFFFF?text=Banner+1" style="color: transparent;">
                                        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col justify-center items-start p-10">
                                            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">Bienvenido a TuTienda</h1>
                                            <p class="text-xl text-white mb-6">Descubre nuestras increíbles ofertas</p>
                                            <button class="inline-flex items-center justify-center text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-11 rounded-md px-8">Comprar Ahora</button>
                                        </div>
                                    </div>
                                </div>
                                <div aria-roledescription="slide" class="min-w-0 shrink-0 grow-0 pl-4 basis-full h-full" role="group">
                                    <div class="w-full h-full">
                                        <img alt="Banner promocional 2" loading="lazy" width="1200" height="500" decoding="async" data-nimg="1" class="w-full h-full object-cover" src="https://via.placeholder.com/1200x500/666666/FFFFFF?text=Banner+2" style="color: transparent;">
                                        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col justify-center items-start p-10">
                                            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">Bienvenido a TuTienda</h1>
                                            <p class="text-xl text-white mb-6">Descubre nuestras increíbles ofertas</p>
                                            <button class="inline-flex items-center justify-center text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-11 rounded-md px-8">Comprar Ahora</button>
                                        </div>
                                    </div>
                                </div>
                                <div aria-roledescription="slide" class="min-w-0 shrink-0 grow-0 pl-4 basis-full h-full" role="group">
                                    <div class="w-full h-full">
                                        <img alt="Banner promocional 3" loading="lazy" width="1200" height="500" decoding="async" data-nimg="1" class="w-full h-full object-cover" src="https://via.placeholder.com/1200x500/666666/FFFFFF?text=Banner+3" style="color: transparent;">
                                        <div class="absolute inset-0 bg-black bg-opacity-50 flex flex-col justify-center items-start p-10">
                                            <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">Bienvenido a TuTienda</h1>
                                            <p class="text-xl text-white mb-6">Descubre nuestras increíbles ofertas</p>
                                            <button class="inline-flex items-center justify-center text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-11 rounded-md px-8">Comprar Ahora</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground absolute h-8 w-8 rounded-full top-1/2 -translate-y-1/2 left-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4">
                                <path d="m12 19-7-7 7-7"></path>
                                <path d="M19 12H5"></path>
                            </svg>
                            <span class="sr-only">Previous slide</span>
                        </button>
                        <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 border border-input bg-background hover:bg-accent hover:text-accent-foreground absolute h-8 w-8 rounded-full top-1/2 -translate-y-1/2 right-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right h-4 w-4">
                                <path d="M5 12h14"></path>
                                <path d="m12 5 7 7-7 7"></path>
                            </svg>
                            <span class="sr-only">Next slide</span>
                        </button>
                    </div>
                </div>
            </div>
            <section class="my-12">
                <h2 class="text-3xl font-bold mb-6">Productos Destacados</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                        <div class="p-4">
                            <img alt="Producto 1" loading="lazy" width="200" height="200" decoding="async" data-nimg="1" class="rounded-lg mb-4" src="https://dummyimage.com/200x200/000/fff&text=Producto 1" style="color: transparent;">
                            <h3 class="font-semibold mb-2">Producto 1</h3>
                            <p class="text-gray-600 mb-4">$99.99</p>
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-10 px-4 py-2 w-full">Añadir al Carrito</button>
                        </div>
                    </div>
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                        <div class="p-4">
                            <img alt="Producto 2" loading="lazy" width="200" height="200" decoding="async" data-nimg="1" class="rounded-lg mb-4" src="https://dummyimage.com/200x200/000/fff&text=Producto 2" style="color: transparent;">
                            <h3 class="font-semibold mb-2">Producto 2</h3>
                            <p class="text-gray-600 mb-4">$99.99</p>
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-10 px-4 py-2 w-full">Añadir al Carrito</button>
                        </div>
                    </div>
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                        <div class="p-4">
                            <img alt="Producto 3" loading="lazy" width="200" height="200" decoding="async" data-nimg="1" class="rounded-lg mb-4" src="https://dummyimage.com/200x200/000/fff&text=Producto 3" style="color: transparent;">
                            <h3 class="font-semibold mb-2">Producto 3</h3>
                            <p class="text-gray-600 mb-4">$99.99</p>
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-10 px-4 py-2 w-full">Añadir al Carrito</button>
                        </div>
                    </div>
                    <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                        <div class="p-4">
                            <img alt="Producto 4" loading="lazy" width="200" height="200" decoding="async" data-nimg="1" class="rounded-lg mb-4" src="https://dummyimage.com/200x200/000/fff&text=Producto 4" style="color: transparent;">
                            <h3 class="font-semibold mb-2">Producto 4</h3>
                            <p class="text-gray-600 mb-4">$99.99</p>
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-10 px-4 py-2 w-full">Añadir al Carrito</button>
                        </div>
                    </div>
                </div>
            </section>
            <section class="my-12">
                <h2 class="text-3xl font-bold mb-6">Categorías Populares</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="/category/electrónica" class="group">
                        <div class="relative h-40 rounded-lg overflow-hidden">
                            <img alt="Electrónica" loading="lazy" width="300" height="160" decoding="async" data-nimg="1" class="group-hover:scale-110 transition-transform duration-200" src="https://dummyimage.com/300x160/000/fff&text=Electrónica" style="color: transparent;">
                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                <h3 class="text-white text-xl font-semibold">Electrónica</h3>
                            </div>
                        </div>
                    </a>
                    <a href="/category/ropa" class="group">
                        <div class="relative h-40 rounded-lg overflow-hidden">
                            <img alt="Ropa" loading="lazy" width="300" height="160" decoding="async" data-nimg="1" class="group-hover:scale-110 transition-transform duration-200" src="https://dummyimage.com/300x160/000/fff&text=Ropa" style="color: transparent;">
                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                <h3 class="text-white text-xl font-semibold">Ropa</h3>
                            </div>
                        </div>
                    </a>
                    <a href="/category/hogar" class="group">
                        <div class="relative h-40 rounded-lg overflow-hidden">
                            <img alt="Hogar" loading="lazy" width="300" height="160" decoding="async" data-nimg="1" class="group-hover:scale-110 transition-transform duration-200" src="https://dummyimage.com/300x160/000/fff&text=Hogar" style="color: transparent;">
                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                <h3 class="text-white text-xl font-semibold">Hogar</h3>
                            </div>
                        </div>
                    </a>
                    <a href="/category/deportes" class="group">
                        <div class="relative h-40 rounded-lg overflow-hidden">
                            <img alt="Deportes" loading="lazy" width="300" height="160" decoding="async" data-nimg="1" class="group-hover:scale-110 transition-transform duration-200" src="https://dummyimage.com/300x160/000/fff&text=Deportes" style="color: transparent;">
                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                <h3 class="text-white text-xl font-semibold">Deportes</h3>
                            </div>
                        </div>
                    </a>
                </div>
            </section>
            <section class="my-12 bg-gray-100 rounded-lg p-8">
                <h2 class="text-3xl font-bold mb-6">Ofertas Especiales</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold mb-2">¡50% de descuento en Electrónica!</h3>
                            <p class="text-gray-600 mb-4">Válido hasta agotar existencias.</p>
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">Ver Ofertas</button>
                        </div>
                        <img alt="50% de descuento" loading="lazy" width="100" height="100" decoding="async" data-nimg="1" class="ml-4" src="https://dummyimage.com/100x100/000/fff&text=Electrónica" style="color: transparent;">
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold mb-2">Envío Gratis en Pedidos +$50</h3>
                            <p class="text-gray-600 mb-4">En todos los productos.</p>
                            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">Comprar Ahora</button>
                        </div>
                        <img alt="Envío gratis" loading="lazy" width="100" height="100" decoding="async" data-nimg="1" class="ml-4" src="https://dummyimage.com/100x100/000/fff&text=Envío" style="color: transparent;">
                    </div>
                </div>
            </section>
        </div>
    </main>
    @include('components.footer')
</div>

</body>
</html>
