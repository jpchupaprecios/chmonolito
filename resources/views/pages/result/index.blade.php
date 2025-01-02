@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 flex-grow">
        <h1 class="text-3xl font-bold mb-6">Resultados de búsqueda</h1>
        <div class="flex flex-col md:flex-row gap-8">
            @include('pages.result.components.filters')
            <div class="flex-grow">
                @include('pages.result.components.products')
                @include('pages.result.components.pagination')
            </div>
        </div>
    </div>
@endsection
