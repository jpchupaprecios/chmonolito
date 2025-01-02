@extends('layouts.app')

@section('content')
    <main class="flex-grow container mx-auto px-4 py-8">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm max-w-2xl mx-auto" data-v0-t="card">
            <div class="p-6">
                <h1 class="text-3xl font-bold text-center mb-6">¡Gracias por tu compra!</h1>
                <p class="text-xl text-center mb-6">Tu número de orden es: <span class="font-bold">445714</span></p>
                <div class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Resumen de la compra:</h2>
                    <div class="flex justify-between mb-2"><span>Camiseta Premium x2</span><span>$59.98</span></div>
                    <div class="flex justify-between mb-2"><span>Pantalón Vaquero x1</span><span>$49.99</span></div>
                    <div class="flex justify-between mb-2"><span>Zapatillas Deportivas x1</span><span>$79.99</span></div>
                    <div class="border-t pt-4 mt-4">
                        <div class="flex justify-between font-bold"><span>Total</span><span>$199.95</span></div>
                    </div>
                </div>
                <div class="text-center"><a href="/">Volver a la tienda</a></div>
            </div>
        </div>
    </main>
@endsection
