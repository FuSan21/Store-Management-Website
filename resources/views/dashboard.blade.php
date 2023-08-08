@extends('layouts.main')

@section('main-section')
    < <div class="py-14 px-4 md:px-6 2xl:px-20 2xl:container 2xl:mx-auto">
        <div class="flex justify-start item-start space-y-2 flex-col">
            <h1 class="text-3xl dark:text-white lg:text-4xl font-semibold leading-7 lg:leading-9 text-gray-800"></h1>
            <p class="text-base dark:text-gray-300 font-medium leading-6 text-gray-600"></p>
        </div>
        <div
            class="mt-10 flex flex-col xl:flex-row jusitfy-center items-stretch w-full xl:space-x-8 space-y-4 md:space-y-6 xl:space-y-0">
            <div class="flex flex-col justify-start items-start w-full space-y-4 md:space-y-6 xl:space-y-8">
                <div
                    class="flex flex-col justify-start items-start dark:bg-gray-800 bg-gray-50 px-4 py-4 md:py-6 md:p-6 xl:p-8 w-full">
                    <p class="text-lg md:text-xl dark:text-white font-semibold leading-6 xl:leading-5 text-gray-800"></p>
                    <!DOCTYPE html>
                    <html lang="en">

                    <head>
                        <meta charset="UTF-8" />
                        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                        <title>Customer Dashboard</title>
                        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    </head>

                    <body class="bg-gray-100">
                        <div class="container mx-auto p-6">
                            <h1 class="text-3xl font-bold mb-4">Customer Dashboard</h1>
                            <div class="flex space-x-4 mb-4">
                                <div class="flex-1 bg-white rounded-lg p-4 shadow">
                                    <h2 class="text-lg font-semibold mb-2">Total Spending</h2>
                                    <p class="text-gray-600">$500</p>
                                </div>
                                <div class="flex-1 bg-white rounded-lg p-4 shadow">
                                    <h2 class="text-lg font-semibold mb-2">Total Orders</h2>
                                    <p class="text-gray-600">10</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow">
                                <h2 class="text-lg font-semibold mb-2">Last 5 Orders</h2>
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="py-2">Order Date</th>
                                            <th>Order Status</th>
                                            <th>Total Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="py-2">2023/08/01</td>
                                            <td>Delivered</td>
                                            <td>$50</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="py-2">2023/07/15</td>
                                            <td>Delivered</td>
                                            <td>$75</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="py-2">2023/06/30</td>
                                            <td>Delivered</td>
                                            <td>$60</td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="py-2">2023/06/01</td>
                                            <td>Delivered</td>
                                            <td>$40</td>
                                        </tr>
                                        <tr class="">
                                            <td class="py-2">2023/05/15</td>
                                            <td>Delivered</td>
                                            <td>$35</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </body>

                    </html>


                </div>
                <div
                    class="flex justify-center flex-col md:flex-row flex-col items-stretch w-full space-y-4 md:space-y-0 md:space-x-6 xl:space-x-8">
                    <div class="flex flex-col px-4 py-6 md:p-6 xl:p-8 w-full bg-gray-50 dark:bg-gray-800 space-y-6">
                        <h3 class="text-xl dark:text-white font-semibold leading-5 text-gray-800">Coupons Codes</h3>
                        <div
                            class="flex justify-center items-center w-full space-y-4 flex-col border-gray-200 border-b pb-4">
                            <div class="flex justify-between w-full">
                                <p class="text-base dark:text-white leading-4 text-gray-800">Student</p>
                                <p class="text-base dark:text-gray-300 leading-4 text-gray-600">5%</p>
                            </div>
                            <div class="flex justify-between items-center w-full">
                                <p class="text-base dark:text-white leading-4 text-gray-800">1yearUser</p>
                                <p class="text-base dark:text-gray-300 leading-4 text-gray-600">$8.00</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div
                class="bg-gray-50 dark:bg-gray-800 w-full xl:w-96 flex justify-between items-center md:items-start px-4 py-6 md:p-6 xl:p-8 flex-col">
                <h3 class="text-xl dark:text-white font-semibold leading-5 text-gray-800">Customer</h3>
                <div
                    class="flex flex-col md:flex-row xl:flex-col justify-start items-stretch h-full w-full md:space-x-6 lg:space-x-8 xl:space-x-0">
                    <div class="flex flex-col justify-start items-start flex-shrink-0">
                        <div
                            class="flex justify-center w-full md:justify-start items-center space-x-4 py-8 border-b border-gray-200">
                            <img src="https://i.ibb.co/5TSg7f6/Rectangle-18.png" alt="avatar" />
                            <div class="flex justify-start items-start flex-col space-y-2">
                                <p class="text-base dark:text-white font-semibold leading-4 text-left text-gray-800">David
                                    Kent</p>
                                <p class="text-sm dark:text-gray-300 leading-5 text-gray-600">10 Previous Orders</p>
                            </div>
                        </div>

                        <div
                            class="flex justify-center text-gray-800 dark:text-white md:justify-start items-center space-x-4 py-4 border-b border-gray-200 w-full">
                            <img class="dark:hidden"
                                src="https://tuk-cdn.s3.amazonaws.com/can-uploader/order-summary-3-svg1.svg" alt="email">
                            <img class="hidden dark:block"
                                src="https://tuk-cdn.s3.amazonaws.com/can-uploader/order-summary-3-svg1dark.svg"
                                alt="email">
                            <p class="cursor-pointer text-sm leading-5 ">david89@gmail.com</p>
                        </div>
                    </div>
                    <div class="flex justify-between xl:h-full items-stretch w-full flex-col mt-6 md:mt-0">
                        <div
                            class="flex justify-center md:justify-start xl:flex-col flex-col md:space-x-6 lg:space-x-8 xl:space-x-0 space-y-4 xl:space-y-12 md:space-y-0 md:flex-row items-center md:items-start">
                            <div
                                class="flex justify-center md:justify-start items-center md:items-start flex-col space-y-4 xl:mt-8">
                                <p
                                    class="text-base dark:text-white font-semibold leading-4 text-center md:text-left text-gray-800">
                                    Shipping Address</p>
                                <p
                                    class="w-48 lg:w-full dark:text-gray-300 xl:w-48 text-center md:text-left text-sm leading-5 text-gray-600">
                                    180 North King Street, Northhampton MA 1060</p>
                            </div>
                            <div
                                class="flex justify-center md:justify-start items-center md:items-start flex-col space-y-4">
                                <p
                                    class="text-base dark:text-white font-semibold leading-4 text-center md:text-left text-gray-800">
                                    Billing Address</p>
                                <p
                                    class="w-48 lg:w-full dark:text-gray-300 xl:w-48 text-center md:text-left text-sm leading-5 text-gray-600">
                                    180 North King Street, Northhampton MA 1060</p>
                            </div>
                        </div>
                        <div class="flex w-full justify-center items-center md:justify-start md:items-start">
                            <button
                                class="mt-6 md:mt-0 dark:border-white dark:hover:bg-gray-900 dark:bg-transparent dark:text-white py-5 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800 border border-gray-800 font-medium w-96 2xl:w-full text-base font-medium leading-4 text-gray-800">Edit
                                Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    @endsection
