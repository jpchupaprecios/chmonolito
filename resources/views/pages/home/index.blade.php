@extends('layouts.app')

@section('content')
    @include('pages.home.components.banner')
    @include('pages.home.components.featured')
    @include('pages.home.components.categories')
    @include('pages.home.components.offers')
@endsection
