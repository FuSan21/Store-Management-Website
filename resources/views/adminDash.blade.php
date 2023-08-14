@extends('layouts.main')

@section('main-section')
    <br>
    <br>
    <br>

    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-center text-gray-800">Admin Panel</h1>
        <div class="flex flex-wrap justify-around mt-4">
            <div class="bg-blue-200 rounded-lg p-4 w-40 text-center">
                <p class="text-xl font-semibold text-gray-800">Total customer</p>
                <p class="text-2xl font-bold text-gray-900">123</p>
            </div>
            <div class="bg-green-200 rounded-lg p-4 w-40 text-center">
                <p class="text-xl font-semibold text-gray-800">Total order</p>
                <p class="text-2xl font-bold text-gray-900">456</p>
            </div>
            <div class="bg-yellow-200 rounded-lg p-4 w-40 text-center">
                <p class="text-xl font-semibold text-gray-800">Total order to deliver</p>
                <p class="text-2xl font-bold text-gray-900">78</p>
            </div>
            <div class="bg-red-200 rounded-lg p-4 w-40 text-center">
                <p class="text-xl font-semibold text-gray-800">Total out of stock products</p>
                <p class="text-2xl font-bold text-gray-900">9</p>
            </div>
        </div>
        <table class="table-auto border-collapse border border-gray-300 mt-8 w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2">Who ordered</th>
                    <th class="border border-gray-300 px-4 py-2">Order number</th>
                    <th class="border border-gray-300 px-4 py-2">Total price</th>
                    <th class="border border-gray-300 px-4 py-2">Order status</th>
                    <th class="border border-gray-300 px-4 py-2">Button 1</th>
                    <th class="border border-gray-300 px-4 py-2">Button 2</th>
                    <th class="border border-gray-300 px-4 py-2">Button 3</th>
                </tr>
            </thead>
            <tbody>

                <tr>
                    <td class="border border-gray-300 px-4 py-2">Alice</td>
                    <td class="border border-gray-300 px-4 py-2">#123456</td>
                    <td class="border border-gray-300 px-4 py-2">$100.00</td>
                    <td class="border border-gray-300 px-4 py-2">Pending</td>
                    <td class="border border-gray-300 px-4 py-2"><button
                            class="bg-blue-500 hover:bg-blue-
            700 text-white font-bold py-
            2 px-
            4 rounded">View
                            order</button></td>
                    <td class="border border-gray-300 px-4 py-2"><button
                            class="bg-green-500 hover:bg-green-
            700 text-white font-bold py-
            2 px-
            4 rounded">Mark
                            as delivered</button></td>
                    <td class="border border-gray-300 px-4 py-2"><button
                            class="bg-red-500 hover:bg-red-
            700 text-white font-bold py-
            2 px-
            4 rounded">Delete
                            order</button></td>
            </tbody>
            <tbody>

                <tr>
                    <td class="border border-gray-300 px-4 py-2">Bob</td>
                    <td class="border border-gray-300 px-4 py-2">#123457</td>
                    <td class="border border-gray-300 px-4 py-2">$200.00</td>
                    <td class="border border-gray-300 px-4 py-2">Delivered</td>
                    <td class="border border-gray-300 px-4 py-2"><button
                            class="bg-blue-500 hover:bg-blue-
            700 text-white font-bold py-
            2 px-
            4 rounded">View
                            order</button></td>
                    <td class="border border-gray-300 px-4 py-2"><button
                            class="bg-green-500 hover:bg-green-
            700 text-white font-bold py-
            2 px-
            4 rounded">Mark
                            as delivered</button></td>
                    <td class="border border-gray-300 px-4 py-2"><button
                            class="bg-red-500 hover:bg-red-
            700 text-white font-bold py-
            2 px-
            4 rounded">Delete
                            order</button></td>
            </tbody>
            <tbody>

                <tr>
                    <td class="border border-gray-300 px-4 py-2">Charlie</td>
                    <td class="border border-gray-300 px-4 py-2">#123458</td>
                    <td class="border border-gray-300 px-4 py-2">$300.00</td>
                    <td class="border border-gray-300 px-4 py-2">Delivered</td>
                    <td class="border border-gray-300 px-4 py-2"><button
                            class="bg-blue-500 hover:bg-blue-
                700 text-white font-bold py-
                2 px-
                4 rounded">View
                            order</button></td>
                    <td class="border border-gray-300 px-4 py-2"><button
                            class="bg-green-500 hover:bg-green-
                700 text-white font-bold py-
                2 px-
                4 rounded">Mark
                            as delivered</button></td>
                    <td class="border border-gray-300 px-4 py-2"><button
                            class="bg-red-500 hover:bg-red-
                700 text-white font-bold py-
                2 px-
                4 rounded">Delete
                            order</button></td>
            </tbody>
            </tbody>


        </table>


        <div class="flex flex-wrap justify-around mt-8">
            <a href="#"
                class="
        bg-green-500 hover:bg-green-700
        text-white
        font-bold
        py-2
        px-4
        rounded-lg
      ">Add
                new product</a>


            <a href="#"
                class="
        bg-purple-500 hover:bg-purple-700
        text-white
        font-bold
        py-2
        px-4
        rounded-lg
      ">View
                all product list</a>


            <a href="#"
                class="
        bg-yellow-500 hover:bg-yellow-700
        text-white
        font-bold
        py-2
        px-4
        rounded-lg
      ">View
                all customer list</a>
        </div>

        <br>
        <br>
        <br>
        <br>
        <br>
    </div>
@endsection
