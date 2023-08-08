@extends('layouts.main')

@section('main-section')
    <br>
    <br>
    <div class="py-12">
        <div class="hidden sm:flex flex-col justify-start items-start">
            <div class="pl-4 lg:px-10 2xl:px-20 flex flex-row justify-center items-end space-x-4">
                <h1 class="text-4xl font-semibold leading-9 text-gray-800  dark:text-white">Wishlist</h1>
                <p class="text-base leading-4 text-gray-600 dark:text-white pb-1">(12 Items)</p>
            </div>
            <table class="w-full mt-16 whitespace-nowrap">
                <thead aria-label="table heading"
                    class="w-full h-16 text-left py-6 bg-gray-50 border-gray-200 dark:bg-gray-800 border-b">
                    <tr>
                        <th class="text-base font-medium leading-4 text-gray-600 dark:text-white 2xl:pl-20 pl-4 lg:pl-10">
                            YOUR PRODUCT</th>
                        <th class="text-base font-medium leading-4 text-gray-600 dark:text-white pl-6 lg:pl-20 2xl:pl-52">
                            DESCRIPTION</th>
                        <th class="text-base font-medium leading-4 text-gray-600 dark:text-white pl-6 lg:pl-20 2xl:pl-52">
                            PRICE</th>
                        <th class="text-base font-medium leading-4 text-gray-600 dark:text-white pl-6 lg:pl-20 2xl:pl-52">
                            MORE OPTIONS</th>
                        <th
                            class="text-base font-medium leading-4 text-gray-600 dark:text-white 2xl:pl-28 2xl:pr-20 pr-4 lg:pr-10">
                        </th>
                    </tr>
                </thead>
                <tbody class="w-full text-left">
                    <tr class="border-gray-200 border-b">
                        <th>
                            <a
                                href="https://www.shutterstock.com/image-vector/demo-cursor-icon-presentation-billboard-600w-318562925.jpg">
                                <img class="my-10 pl-4 lg:pl-10 2xl:pl-20"
                                    src="https://www.shutterstock.com/image-vector/demo-cursor-icon-presentation-billboard-600w-318562925.jpg"
                                    alt="shoe" width="300" height="300" style="object-fit: cover;">
                            </a>
                        </th>
                        <th class="mt-10 text-base font-medium leading-4 text-gray-600 pl-6 lg:pl-20 2xl:pl-52">
                            <p class="text-base leading-4 text-gray-800 dark:text-white">Car parts</p>
                        </th>
                        <th class="my-10 pl-6 lg:pl-20 2xl:pl-52">
                            <p class="dark:text-white">$90</p>
                        </th>
                        <th class="my-10 text-base font-medium leading-4 text-gray-600 pl-6 lg:pl-20 2xl:pl-52">
                            <a href="javascript:void(0)"
                                class="hover:underline text-base font-medium leading-none text-gray-800 dark:text-white focus:outline-none focus:underline">View
                                details</a>
                        </th>
                        <th class="my-10 pl-4 lg:pl-12 2xl:pl-28 pr-4 2xl:pr-20">
                            <button
                                class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-800 text-base leading-none text-red-600 hover:text-red-800">
                                <p>Remove Item</p>
                            </button>
                        </th>
                    </tr>

                    <tr class="border-gray-200 border-b">
                        <th><a
                                href="https://www.shutterstock.com/image-vector/demo-cursor-icon-presentation-billboard-600w-318562925.jpg">
                                <img class="my-10 pl-4 lg:pl-10 2xl:pl-20"
                                    src="https://www.shutterstock.com/image-vector/demo-cursor-icon-presentation-billboard-600w-318562925.jpg"
                                    alt="shoe" width="300" height="300" style="object-fit: cover;">
                            </a></th>
                        <th class="my-10 text-base font-medium leading-4 text-gray-600 pl-6 lg:pl-20 2xl:pl-52">
                            <p class="text-base leading-4 text-gray-800 dark:text-white">Car parts</p>
                        </th>
                        <th class="mt-10 pl-6 lg:pl-20 2xl:pl-52">
                            <p class="dark:text-white">$90</p>
                        </th>
                        <th class="my-10 text-base font-medium leading-4 text-gray-600 pl-6 lg:pl-20 2xl:pl-52">
                            <a href="javascript:void(0)"
                                class="hover:underline text-base font-medium leading-none text-gray-800 dark:text-white focus:outline-none focus:underline">View
                                details</a>
                        </th>
                        <th class="mt-10 pl-4 lg:pl-12 2xl:pl-28 pr-4 2xl:pr-20">
                            <button
                                class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-800 text-base leading-none text-red-600 hover:text-red-800">
                                <p>Remove Item</p>
                            </button>
                        </th>
                    </tr>

                    <tr class="border-gray-200 border-b">
                        <th><a
                                href="https://www.shutterstock.com/image-vector/demo-cursor-icon-presentation-billboard-600w-318562925.jpg">
                                <img class="my-10 pl-4 lg:pl-10 2xl:pl-20"
                                    src="https://www.shutterstock.com/image-vector/demo-cursor-icon-presentation-billboard-600w-318562925.jpg"
                                    alt="shoe" width="300" height="300" style="object-fit: cover;">
                            </a></th>
                        <th class="my-10 text-base font-medium leading-4 text-gray-600 pl-6 lg:pl-20 2xl:pl-52">
                            <p class="text-base leading-4 text-gray-800 dark:text-white">Car parts</p>
                        </th>
                        <th class="my-10 pl-6 lg:pl-20 2xl:pl-52">
                            <p class="dark:text-white">$90</p>
                        </th>
                        <th class="my-10 text-base font-medium leading-4 text-gray-600 pl-6 lg:pl-20 2xl:pl-52">
                            <a href="javascript:void(0)"
                                class="hover:underline text-base font-medium leading-none text-gray-800 dark:text-white focus:outline-none focus:underline">View
                                details</a>
                        </th>
                        <th class="my-10 pl-4 lg:pl-12 2xl:pl-28 pr-4 2xl:pr-20">
                            <button
                                class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-800 text-base leading-none text-red-600 hover:text-red-800">
                                <p>Remove Item</p>
                            </button>
                        </th>
                    </tr>

                    <tr class="border-gray-200 border-b">
                        <th><a
                                href="https://www.shutterstock.com/image-vector/demo-cursor-icon-presentation-billboard-600w-318562925.jpg">
                                <img class="my-10 pl-4 lg:pl-10 2xl:pl-20"
                                    src="https://www.shutterstock.com/image-vector/demo-cursor-icon-presentation-billboard-600w-318562925.jpg"
                                    alt="shoe" width="300" height="300" style="object-fit: cover;">
                            </a></th>
                        <th class="my-10 text-base font-medium leading-4 text-gray-600 pl-6 lg:pl-20 2xl:pl-52">
                            <p class="text-base leading-4 text-gray-800 dark:text-white">Car parts</p>
                        </th>
                        <th class="my-10 pl-6 lg:pl-20 2xl:pl-52">
                            <p class="dark:text-white">$90</p>
                        </th>
                        <th class="my-10 text-base font-medium leading-4 text-gray-600 pl-6 lg:pl-20 2xl:pl-52">
                            <a href="javascript:void(0)"
                                class="hover:underline text-base font-medium leading-none text-gray-800 dark:text-white focus:outline-none focus:underline">View
                                detail</a>
                        </th>
                        <th class="my-10 pl-4 lg:pl-12 2xl:pl-28 pr-4 2xl:pr-20">
                            <button
                                class="focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-800 text-base leading-none text-red-600 hover:text-red-800">
                                <p>Remove Item</p>
                            </button>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>



    </div>
@endsection
