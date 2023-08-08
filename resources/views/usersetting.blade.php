@extends('layouts.main')

@section('main-section')

<
<div class="py-14 px-4 md:px-6 2xl:px-20 2xl:container 2xl:mx-auto">
    <div class="flex justify-start item-start space-y-2 flex-col">
        <h1 class="text-3xl dark:text-white lg:text-4xl font-semibold leading-7 lg:leading-9 text-gray-800"></h1>
        <p class="text-base dark:text-gray-300 font-medium leading-6 text-gray-600"></p>
    </div>
    <div class="mt-10 flex flex-col xl:flex-row jusitfy-center items-stretch w-full xl:space-x-8 space-y-4 md:space-y-6 xl:space-y-0">
        <div class="flex flex-col justify-start items-start w-full space-y-4 md:space-y-6 xl:space-y-8">
            <div class="flex flex-col justify-start items-start dark:bg-gray-800 bg-gray-50 px-4 py-4 md:py-6 md:p-6 xl:p-8 w-full">
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
      <!DOCTYPE html>
      <html lang="en">
      <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
          <title>User Settings</title>
          <style>
              /* Additional styling for pop-up */
              .popup {
                  display: none;
              }

              .popup.active {
                  display: flex;
                  align-items: center;
                  justify-content: center;
                  position: fixed;
                  top: 0;
                  left: 0;
                  width: 100%;
                  height: 100%;
                  background-color: rgba(0, 0, 0, 0.5);
              }

              .popup-content {
                  background-color: white;
                  padding: 20px;
                  border-radius: 8px;
                  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                  max-width: 400px;
                  width: 100%;
              }
          </style>
      </head>
      <body class="bg-gray-100">
          <div class="container mx-auto p-8">
              <h1 class="text-2xl font-bold mb-4">User Settings</h1>

              <div class="bg-white rounded-lg p-6 shadow-md">
                  <div class="mb-4">
                      <h2 class="text-lg font-semibold">User Information</h2>
                      <p class="mt-2"><strong>Name:</strong> John Doe</p>
                      <p><strong>Address:</strong> 123 Main St, City, Country</p>
                      <p><strong>Phone Number:</strong> (123) 456-7890</p>
                      <p><strong>Email:</strong> john@example.com</p>
                      <button class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded" id="editButton">Edit Information</button>
                  </div>
              </div>
          </div>

          <!-- Pop-up for editing information -->
          <div class="popup" id="popup">
              <div class="popup-content">
                  <h2 class="text-lg font-semibold mb-4">Edit Information</h2>
                  <form class="space-y-4">
                      <div>
                          <label class="block font-semibold" for="newName">New Name</label>
                          <input class="w-full border rounded px-3 py-2" type="text" id="newName" name="newName" required>
                      </div>
                      <div>
                          <label class="block font-semibold" for="newAddress">New Address</label>
                          <input class="w-full border rounded px-3 py-2" type="text" id="newAddress" name="newAddress" required>
                      </div>
                      <div>
                          <label class="block font-semibold" for="newPhoneNumber">New Phone Number</label>
                          <input class="w-full border rounded px-3 py-2" type="tel" id="newPhoneNumber" name="newPhoneNumber" required>
                      </div>
                      <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded" type="submit">Save Changes</button>
                  </form>
              </div>
          </div>

          <script>
              const editButton = document.getElementById('editButton');
              const popup = document.getElementById('popup');

              editButton.addEventListener('click', () => {
                  popup.classList.add('active');
              });
          </script>
      </body>
      </html>

    </div>
  </body>
</html>





@endsection
