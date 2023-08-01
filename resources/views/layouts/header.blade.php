<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <TITLE>Featured Parts</TITLE>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.7.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('/font-awesome_6.4.0_css_all.min.css') }}" />

    <!-- Styles -->
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/plain.css') }}" rel="stylesheet">
</head>

<body class="antialiased">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <div class="fixed top-0 z-30 w-full">
        <nav class="bg-white border-gray-200">
            <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                <a href="/" class="flex items-center">
                    <img src="{{ asset('/demo-logo.png') }}" class="h-8 mr-3" alt="StoreManagementWebsite Logo" />
                    <span class="self-center text-2xl font-semibold whitespace-nowrap">Vehicle Accessory Shop</span>
                </a>

                <div class="flex items-center md:order-2">
                    <button type="button"
                        class="flex mr-3 text-sm bg-slate-400 rounded-full focus:ring-4 focus:ring-gray-300"
                        id="cart-button">
                        <span class="sr-only">Open Cart</span>
                        <!-- component -->
                        <div class="text-l w-10 h-10 p-2">
                            <i class="fa-solid fa-cart-shopping fa-xl"></i>
                        </div>
                    </button>
                    <button type="button"
                        class="flex mr-3 text-sm bg-slate-400 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300"
                        id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown"
                        data-dropdown-placement="bottom">
                        <span class="sr-only">Open user menu</span>
                        <!-- component -->
                        <div class="text-l w-10 h-10 p-2">
                            <i class="fa-regular fa-user fa-xl"></i>
                        </div>
                        {{-- <img class="w-8 h-8 rounded-full" src="{{ asset('/demo-logo.png') }}" alt="user photo"> --}}
                    </button>
                    <!-- Dropdown menu -->
                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow"
                        id="user-dropdown">
                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900">@auth
                                    {{ @Auth::user()->name }}
                                @else
                                    Guest
                                @endauth
                            </span>
                            <span class="block text-sm  text-gray-500 truncate">@auth
                                    {{ @Auth::user()->email }}
                                @else
                                    No Email
                                @endauth
                            </span>
                        </div>
                        <ul class="py-2" aria-labelledby="user-menu-button">
                            @auth
                                <li>
                                    <a href="{{ url('/dashboard') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                                </li>
                                <li>
                                    <a href="{{ url('/orders') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Orders</a>
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf

                                        <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </li>
                            @else
                                <li>
                                    <a href="{{ route('login') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign
                                        in</a>
                                </li>
                                <li>
                                    <a href="{{ route('register') }}"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Register</a>
                                </li>
                            @endauth
                        </ul>
                    </div>
                    <button data-collapse-toggle="navbar-user" type="button"
                        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 "
                        aria-controls="navbar-user" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 17 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M1 1h15M1 7h15M1 13h15" />
                        </svg>
                    </button>
                </div>
                <div class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
                    <ul
                        class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 md:mt-0 md:border-0 md:bg-white">
                        <li>
                            <a href="{{ url('/') }}"
                                class="block py-2 pl-3 pr-4 text-white bg-blue-700 rounded md:bg-transparent md:text-blue-700 md:p-0"
                                aria-current="page">Home</a>
                        </li>
                        <li>
                            <a href="{{ url('/shop') }}"
                                class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Shop</a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">Contact</a>
                        </li>
                        <li>
                            <a href="#"
                                class="block py-2 pl-3 pr-4 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-blue-700 md:p-0">About
                                Us</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    </div>
