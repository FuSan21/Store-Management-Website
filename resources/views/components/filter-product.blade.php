<div id="filter-product">
    <div class="flex items-center justify-between ">
        <h1 class="text-2xl font-bold text-blue-600 dark:text-lightblue">Filter</h1>
    </div>
    <div
        class="{{-- hidden --}}divide-x divide-gray-300 md:grid-cols-3 lg:grid-cols-5 md:grid dark:divide-gray-700">
        <div class="p-1">
            <div class="flex items-center gap-2 mb-1 text-gray-600 dark:text-gray-400">
                <i class="fa-solid fa-bars"></i>
                Collection
            </div>
            <form>
                <div
                    class="overflow-x-auto max-h-40 scrollbar scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 hover:scrollbar-thumb-gray-500 dark:hover:scrollbar-thumb-gray-500 dark:scrollbar-track-gray-700 scrollbar-track-gray-200">
                    <a class="@if ($selectedFilter['collection'] == 'all') selected-label @endif flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out cursor-pointer"
                        href="{{ url('/shop/filtered?collection=' . 'all' . '&filter_type=' . $selectedFilter['filter_type'] . '&brand=' . $selectedFilter['brand'] . '&model=' . $selectedFilter['model'] . '&specification=' . $selectedFilter['specification']) }}">
                        All
                    </a>
                    @foreach ($collections as $collection)
                        <a class="@if ($selectedFilter['collection'] == $collection) selected-label @endif flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out cursor-pointer"
                            href="{{ url('/shop/filtered?collection=' . $collection . '&filter_type=' . $selectedFilter['filter_type'] . '&brand=' . $selectedFilter['brand'] . '&model=' . $selectedFilter['model'] . '&specification=' . $selectedFilter['specification']) }}">
                            {{ $collection }}
                        </a>
                    @endforeach
                </div>
            </form>
        </div>

        <div class="p-1">
            <div class="flex items-center gap-2 mb-1 text-gray-600 dark:text-gray-400">
                <i class="fa-solid fa-wrench"></i>
                Filter Type
            </div>
            <div
                class="overflow-x-auto max-h-40 scrollbar scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 hover:scrollbar-thumb-gray-500 dark:hover:scrollbar-thumb-gray-500 dark:scrollbar-track-gray-700 scrollbar-track-gray-200">
                @foreach ($filterTypes as $filterType)
                    <a class="@if ($selectedFilter['filter_type'] == $filterType) selected-label @endif flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out cursor-pointer"
                        href="{{ url('/shop/filtered?collection=' . $selectedFilter['collection'] . '&filter_type=' . $filterType . '&brand=' . $selectedFilter['brand'] . '&model=' . $selectedFilter['model'] . '&specification=' . $selectedFilter['specification']) }}">
                        {{ $filterType }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="p-1">
            <div class="flex items-center gap-2 mb-1 text-gray-600 dark:text-gray-400">
                <i class="fa-solid fa-copyright"></i>
                Car Brand
            </div>
            <div
                class="overflow-x-auto max-h-40 scrollbar scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 hover:scrollbar-thumb-gray-500 dark:hover:scrollbar-thumb-gray-500 dark:scrollbar-track-gray-700 scrollbar-track-gray-200">
                @foreach ($brands as $brand)
                    <a class="@if ($selectedFilter['brand'] == $brand) selected-label @endif flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out cursor-pointer"
                        href="{{ url('/shop/filtered?collection=' . $selectedFilter['collection'] . '&filter_type=' . $selectedFilter['filter_type'] . '&brand=' . $brand . '&model=' . $selectedFilter['model'] . '&specification=' . $selectedFilter['specification']) }}">
                        {{ $brand }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="p-1">
            <div class="flex items-center gap-2 mb-1 text-gray-600 dark:text-gray-400">
                <i class="fa-solid fa-car"></i>
                Model
            </div>
            <div
                class="overflow-x-auto max-h-40 scrollbar scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 hover:scrollbar-thumb-gray-500 dark:hover:scrollbar-thumb-gray-500 dark:scrollbar-track-gray-700 scrollbar-track-gray-200">
                @if (!isset($models))
                    <p class="p-2 text-sm font-semibold text-center text-gray-400 dark:text-gray-700">please select a
                        car brand</p>
                @else
                    @foreach ($models as $model)
                        <a class="@if ($selectedFilter['model'] == $model) selected-label @endif flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out cursor-pointer"
                            href="{{ url('/shop/filtered?collection=' . $selectedFilter['collection'] . '&filter_type=' . $selectedFilter['filter_type'] . '&brand=' . $selectedFilter['brand'] . '&model=' . $model . '&specification=' . $selectedFilter['specification']) }}">
                            {{ $model }}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="p-1">
            <div class="flex items-center gap-2 mb-1 text-gray-600 dark:text-gray-400">
                <i class="fa-solid fa-gear"></i>
                Specification
            </div>
            <div
                class="overflow-x-auto overflow-y-none max-h-40 scrollbar scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600 hover:scrollbar-thumb-gray-500 dark:hover:scrollbar-thumb-gray-500 dark:scrollbar-track-gray-700 scrollbar-track-gray-200">

                @if (!isset($specifications))
                    <p class="p-2 text-sm font-semibold text-center text-gray-400 dark:text-gray-700">please select a
                        Model
                    </p>
                @else
                    @foreach ($specifications as $specification)
                        <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out cursor-pointer"
                            href="#">
                            {{ $specification }}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @if (
        !(
            $selectedFilter['collection'] == 'all' &&
            $selectedFilter['filter_type'] == 'all' &&
            $selectedFilter['brand'] == 'all' &&
            $selectedFilter['model'] == 'all' &&
            $selectedFilter['specification'] == 'all'
        ))
        <div><a href="{{ url('/shop') }}"
                class="block w-full py-1 text-center text-white bg-blue-600 border rounded-b hover:bg-transparent hover:text-rose-500 transition">Clear</a>
        </div>
    @endif


    {{-- <div class="md:hidden flex-col w-full transition-all duration-300 ease-in-out max-h-[100vh] overflow-y-auto">
        <div
            class="flex flex-col flex-shrink-0 w-full border-r border-gray-200 bg-gradient-to-t from-white to-gray-100 dark:from-gray-900 dark:to-gray-800 dark:border-gray-700">
            <nav class="flex-grow pb-4 md:md:pb-0 md:overflow-y-auto">

                <ul>

                    <li x-data="{ showDrop: true }">
                        <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out justify-between cursor-pointer"
                            x-on:click="showDrop=!showDrop">
                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <i class="fa-solid fa-bars w-5 h-5 mr-3"></i>
                                Collection
                            </div>
                            <i x-show="!showDrop" class="fa-solid fa-caret-down w-4 h-4" style="display: none;"></i>
                            <i x-show="showDrop" class="fa-solid fa-caret-up w-4 h-4"></i>
                        </a>

                        <div x-show="showDrop" x-transition:enter="transition transform duration-300"
                            x-transition:enter-start="-translate-y-9 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition transform duration-300"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-9 opacity-0">
                            <a class="pl-3 pr-4 py-2 border-l-4 border-red-400 text-base text-red-600 bg-gray-100 dark:text-gray-300 dark:bg-gray-700 focus:outline-none focus:text-blue-600 focus:border-blue-800 transition duration-150 ease-in-out flex justify-start items-center pl-12 cursor-pointer"
                                href="https://bogramotors.co.uk/shop/all">
                                All
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                href="https://bogramotors.co.uk/shop/air-filter">
                                Air Filter
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                href="https://bogramotors.co.uk/shop/oil-filter">
                                Oil Filter
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                href="https://bogramotors.co.uk/shop/0w-30">
                                0w-30
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                href="https://bogramotors.co.uk/shop/5w-30">
                                5w-30
                            </a>

                        </div>
                    </li>


                    <li x-data="{ showDrop: false }">
                        <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out flex justify-between cursor-pointer"
                            @click="showDrop=!showDrop">
                            <div class="flex items-center">
                                <i class="fa-solid fa-wrench w-5 h-5 mr-3"></i>
                                Filter Type
                            </div>
                            <i x-show="!showDrop" class="fa-solid fa-caret-down w-4 h-4"></i>
                            <i x-show="showDrop" class="fa-solid fa-caret-up w-4 h-4" style="display: none;"></i>
                        </a>

                        <div x-show="showDrop" x-transition:enter="transition transform duration-300"
                            x-transition:enter-start="-translate-y-9 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition transform duration-300"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-9 opacity-0" style="display: none;">

                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(1)">
                                Polyurethane
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(2)">
                                Plastic Panel
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(3)">
                                Metal Cap
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(4)">
                                Plastic Cap
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(5)">
                                Eco Felt
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(6)">
                                Spin On
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(7)">
                                ECO type
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(8)">
                                Plastic Cap
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(11)">
                                4 liter
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(12)">
                                5 Liter
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(13)">
                                1 Liter
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(14)">
                                5 Liter
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(15)">
                                4 Liter
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectSubCats(16)">
                                1 Liter
                            </a>

                        </div>
                    </li>

                    <li x-data="{ showDrop: false }">
                        <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out flex justify-between cursor-pointer"
                            @click="showDrop=!showDrop">
                            <div class="flex items-center">
                                <i class="fa-solid fa-copyright w-5 h-5 mr-3"></i>
                                Car Brand
                            </div>

                            <svg x-show="!showDrop" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>

                            <svg x-show="showDrop" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 15l7-7 7 7"></path>
                            </svg>
                        </a>

                        <div x-show="showDrop" x-transition:enter="transition transform duration-300"
                            x-transition:enter-start="-translate-y-9 opacity-0"
                            x-transition:enter-end="translate-y-0 opacity-100"
                            x-transition:leave="transition transform duration-300"
                            x-transition:leave-start="translate-y-0 opacity-100"
                            x-transition:leave-end="-translate-y-9 opacity-0" style="display: none;">

                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(36)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09122022161629png"
                                        class="w-6 h-6" alt="">
                                    Audi
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(38)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_BMW.png"
                                        class="w-6 h-6" alt="">
                                    BMW
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(42)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-01272023121526png"
                                        class="w-6 h-6" alt="">
                                    Castrol
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(8)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09122022161453png"
                                        class="w-6 h-6" alt="">
                                    Citroen
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(24)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09122022161542png"
                                        class="w-6 h-6" alt="">
                                    Fiat
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(50)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_Ford-01282023131117png"
                                        class="w-6 h-6" alt="">
                                    Ford
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(44)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-01272023152858png"
                                        class="w-6 h-6" alt="">
                                    Fuchs
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(41)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_Honda-09122022161926png"
                                        class="w-6 h-6" alt="">
                                    Honda
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(29)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09132022160853png"
                                        class="w-6 h-6" alt="">
                                    Land Rover
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(10)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09122022161504png"
                                        class="w-6 h-6" alt="">
                                    Lexus
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(49)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_Mercedes-Benz-01282023131049png"
                                        class="w-6 h-6" alt="">
                                    Mercedes-Benz
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(37)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09122022161643png"
                                        class="w-6 h-6" alt="">
                                    Mitsubishi
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(47)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_Mobil-01272023130755png"
                                        class="w-6 h-6" alt="">
                                    Mobil
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(45)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_Motul-01272023121754png"
                                        class="w-6 h-6" alt="">
                                    Motul
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(32)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_Nissan.png"
                                        class="w-6 h-6" alt="">
                                    Nissan
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(43)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-01272023121546png"
                                        class="w-6 h-6" alt="">
                                    Petronas
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(39)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09132022155707png"
                                        class="w-6 h-6" alt="">
                                    Peugeot
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(48)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_Shell-01272023130814png"
                                        class="w-6 h-6" alt="">
                                    Shell
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(21)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09132022155158png"
                                        class="w-6 h-6" alt="">
                                    Toyota
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(46)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_VALVOLINE-01272023130730png"
                                        class="w-6 h-6" alt="">
                                    Valvoline
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(6)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09122022161441png"
                                        class="w-6 h-6" alt="">
                                    Vauxhall
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(33)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_Vaushall.png"
                                        class="w-6 h-6" alt="">
                                    Vauxhall
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(40)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09122022161718png"
                                        class="w-6 h-6" alt="">
                                    Volkswagen
                                </div>
                            </a>
                            <a class="flex justify-start items-center pl-3 pr-4 py-2 border-l-4 border-transparent text-base text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:bg-gray-700 hover:bg-gray-100 hover:border-blue-800 dark:hover:border-gray-500 focus:outline-none focus:text-gray-800 focus:border-blue-800 transition duration-150 ease-in-out pl-12 cursor-pointer"
                                wire:click="selectCar(12)">
                                <div class="flex items-center justify-start gap-5">
                                    <img src="https://bogramotors.co.uk/storage/uploads/logos/logo_-09122022161514png"
                                        class="w-6 h-6" alt="">
                                    Volvo
                                </div>
                            </a>


                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </div> --}}

</div>
