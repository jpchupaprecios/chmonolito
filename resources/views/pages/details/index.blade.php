@extends('layouts.app')

@section('content')
    <div class="grid md:grid-cols-2 gap-8">
        <div>
            <div class="relative aspect-square mb-4"><img alt="Camiseta Premium" loading="lazy" decoding="async" data-nimg="fill" class="rounded-lg" src="https://dummyimage.com/600x600/000/fff&text=Producto%201" style="position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;"></div>
            <div class="flex space-x-2">
                <div class="relative w-20 h-20"><img alt="Camiseta Premium thumbnail 1" loading="lazy" decoding="async" data-nimg="fill" class="rounded-md" src="https://dummyimage.com/100x100/000/fff&text=Thumbnail%201" style="position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;"></div>
                <div class="relative w-20 h-20"><img alt="Camiseta Premium thumbnail 2" loading="lazy" decoding="async" data-nimg="fill" class="rounded-md" src="https://dummyimage.com/100x100/000/fff&text=Thumbnail%202" style="position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;"></div>
                <div class="relative w-20 h-20"><img alt="Camiseta Premium thumbnail 3" loading="lazy" decoding="async" data-nimg="fill" class="rounded-md" src="https://dummyimage.com/100x100/000/fff&text=Thumbnail%203" style="position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;"></div>
            </div>
        </div>
        <div>
            <h1 class="text-3xl font-bold mb-2">Camiseta Premium</h1>
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>
                <span class="ml-2 text-gray-600">(4/5)</span>
            </div>
            <p class="text-xl font-bold mb-4">$29.99</p>
            <p class="mb-4">Una camiseta cómoda y elegante para cualquier ocasión.</p>
            @include('pages.details.components.color-options')
            @include('pages.details.components.selects')
            @include('pages.details.components.quantity-controls')
            <div class="flex items-center space-x-2">
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-10 px-4 py-2 flex-grow">Añadir al Carrito</button>
                @include('pages.details.components.fav')
                </button>
            </div>
        </div>
    </div>


    @include('pages.details.components.extra-data-tabs')
@endsection
