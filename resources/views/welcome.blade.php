@extends('layouts.main')

@section('main-section')
    <div
        class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
        <div class="container pt-20">
            <x-find-product />
            <p class="my-6 text-4xl font-bold text-center text-red-700">New Arrivals</p>
            <div id="product-grid-1" class="container product-grid columns-3xs">
                <div class="mb-4 text-black font-bold">Engines</div>
                <hr>
                @foreach ($engines as $engine)
                    <x-product-grid :data="$engine" />
                @endforeach
            </div>

            <div id="product-grid-2" class="container product-grid columns-3xs">
                <div class="mb-4 text-black font-bold">Brakes</div>
                <hr>
                @foreach ($brakes as $brake)
                    <x-product-grid :data="$brake" />
                @endforeach

            </div>

            <div id="product-grid-3" class="container product-grid columns-3xs">
                <div class="mb-4 text-black font-bold">Suspensions</div>
                <hr>
                @foreach ($suspensions as $suspension)
                    <x-product-grid :data="$suspension" />
                @endforeach

            </div>
            <div id="product-grid-4" class="container product-grid columns-3xs">
                <div class="mb-4 text-black font-bold">Electricals</div>
                <hr>
                @foreach ($electricals as $electrical)
                    <x-product-grid :data="$electrical" />
                @endforeach

            </div>
        </div>
    </div>
@endsection
