<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="next-size-adjust" content="">

        <link rel="stylesheet" href="/assets/styles/styles.css" data-precedence="next">
        <link rel="stylesheet" href="/assets/styles/layout.css" data-precedence="next">
        <link rel="stylesheet" href="/assets/styles/fonts.css" data-precedence="next">

        <title>{{ //APP.NAME }}</title>

        <style>.f_SW50ZXI{
                font-family: 'Roboto';
            }
        </style>
        <link rel="preload" href="https://via.placeholder.com/1200x500/666666/FFFFFF?text=Banner+1" as="image">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    </head>
    <body class="f_SW50ZXI">
    <script>
        let resultsC = null;
        let pData = {
            variants:[]
        };
        const differences = {};

        function fixData(){
            if(typeof differences.price !== "undefined" ){
                //selecciono el elemento con clase .product-data-price
                const priceElement = document.querySelector('.product-data-price');
                document.querySelector('.price-shimmer').style.display = 'none';
                // actualizo el contenido del elemento por el de differences.price
                priceElement.textContent = differences.price;
                console.log("price updated");
            }

            //lo mismo para el elemento img con clase .product-data-image cuando differences.image no sea undefined
            if(typeof differences.image !== "undefined" ){
                const imageElement = document.querySelector('.product-data-image');
                document.querySelector('.image-placeholder').style.display = 'none';
                imageElement.style.display = 'block';
                imageElement.src = differences.image;
                console.log("image updated");
            }

            //lo mismo pero para differences.title
            if(typeof differences.title !== "undefined" ){
                const titleElement = document.querySelector('.product-data-title');
                document.querySelector('.title-shimmer-wrapper').style.display = 'none';
                titleElement.textContent = differences.title;
                console.log("title updated");
            }

            if(typeof differences.variants !== "undefined" ){
                console.log("variants updated");
            }

            if (typeof differences.rating !== "undefined") {
                if (product.rating === 0) {
                    document.querySelector('.color-shimmer-options').style.display = 'none';
                    console.log("rating none");
                }else{
                    // Lógica para generar el HTML de las estrellas en JavaScript
                    const rating = parseFloat(differences.rating);
                    let fullStars = Math.floor(rating);
                    const decimalPart = rating - fullStars;
                    let halfStar = 0;

                    if (decimalPart > 0) {
                        if (decimalPart >= 0.6) {
                            fullStars++;
                        } else {
                            halfStar = 1;
                        }
                    }

                    if (fullStars > 5) {
                        fullStars = 5;
                        halfStar = 0;
                    }

                    const emptyStars = 5 - (fullStars + halfStar);

                    let starsHtml = "";

                    // Generar estrellas llenas
                    for (let i = 0; i < fullStars; i++) {
                        starsHtml += `<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>`;
                    }

                    // Generar media estrella si es necesario
                    if (halfStar) {
                        starsHtml += `<svg class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <defs>
                        <linearGradient id="half-star">
                            <stop offset="50%" stop-color="#facc15"/>
                            <stop offset="50%" stop-color="#d1d5db"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#half-star)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>`;
                    }

                    // Generar estrellas vacías
                    for (let i = 0; i < emptyStars; i++) {
                        starsHtml += `<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>`;
                    }

                    // Agregar el texto del rating
                    starsHtml += `<span class="ml-2 text-gray-600 product-data-rating">${rating}</span>`;

                    // Seleccionar el contenedor de las estrellas
                    const ratingStarsWrapper = document.querySelector('.rating-stars-wrapper');
                    document.querySelector('.rating-stars-wrapper').style.display = 'flex';

                    // Actualizar el contenido del contenedor con el nuevo HTML
                    ratingStarsWrapper.innerHTML = starsHtml;
                    console.log("rating updated");
                }
            }
        }

        function parsepDataC(pData, d) {

            // Comparar atributos principales
            const mainAttributes = ['title', 'image', 'price', 'rating'];
            mainAttributes.forEach(attr => {
                if (pData[attr] !== d[attr]) {
                    differences[attr] = d[attr];
                }
            });

            // Comparar variantes
            if (pData.variants && d.variants) {
                differences.variants = [];

                const pDataVariantsMap = new Map(pData.variants.map(v => [v.name, v]));
                const dVariantsMap = new Map(d.variants.map(v => [v.name, v]));

                // Recorrer las variantes de d
                d.variants.forEach(dVariant => {
                    const pDataVariant = pDataVariantsMap.get(dVariant.name);

                    if (!pDataVariant) {
                        // Si la variante no existe en pData, agregarla al tercer objeto
                        differences.variants.push(dVariant);
                    } else {
                        // Si la variante existe, comparar las options
                        const optionsDiff = dVariant.options.filter(dOption => {
                            return !pDataVariant.options.some(pOption => pOption.sku === dOption.sku);
                        });

                        if (optionsDiff.length > 0) {
                            // Si hay options diferentes, agregar la variante con las options que faltan
                            differences.variants.push({
                                ...dVariant,
                                options: optionsDiff
                            });
                        }
                    }
                });
            }

            fixData();
        }
    </script>
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
    <style>
        /* Animación de carga */
        @keyframes shimmer {
            0% { background-position: -200px 0; }
            100% { background-position: 200px 0; }
        }

        .skeleton {
            background: linear-gradient(90deg, #ececec 25%, #f5f5f5 50%, #ececec 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
            border-radius: 8px;
        }

        /* Estructura */
        .container-shimmer {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            display: grid;
            gap: 20px;
        }

        .image-placeholder {
            width: 100%;
            aspect-ratio: 1;
        }

        .thumbnail-shimmers-shimmer {
            display: flex;
            gap: 10px;
        }

        .thumbnail-shimmer {
            width: 80px;
            height: 80px;
        }

        .title-shimmer {
            width: 80%;
            height: 30px;
        }

        .rating-shimmer {
            width: 50%;
            height: 20px;
        }

        .price-shimmer {
            width: 30%;
            height: 25px;
        }

        .description-shimmer {
            width: 100%;
            height: 50px;
        }

        .color-shimmer-options {
            display: flex;
            gap: 10px;
        }

        .color-shimmer {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .button-shimmer {
            width: 100%;
            height: 40px;
            margin-top: 10px;
        }
    </style>
    <style style="">  :root {
            --font-sans: __variable_3a0388;
            --font-mono: __variable_c1e5c9;
        }
    </style>
    <div class="flex flex-col min-h-screen">
        {{ //MARQUEE }}

        <main class="flex-grow">
            {{ //HEADER }}
            {{ //CATEGORIES}}

            <div class="container mx-auto px-4 flex-grow">
                {{ //SEARCH}}
                {{ //CONTENT}}
