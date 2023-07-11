<div
    class="px-2 py-3 md:px-6 md:py-4 bg-white border border-gray-300 dark:border-gray-700 dark:bg-gray-800 w-9/12 mx-auto backdrop-filter backdrop-blur-lg bg-opacity-30 dark:backdrop-filter dark:backdrop-blur-lg dark:bg-opacity-30">
    <h4 class="text-2xl font-bold dark:text-gray-300">Find your Product</h4>
    <form wire:submit.prevent="search">
        <select wire:model="carId" required=""
            class="block w-full mt-6 transition duration-300 ease-in-out bg-transparent border-transparent dark:text-gray-400 focus:border-red focus:outline-none focus:ring-transparent">
            <option disabled="" selected="" value="">Car Brand</option>
            <option value="36">Audi</option>
            <option value="38">BMW</option>
            <option value="42">Castrol</option>
        </select>
        <select wire:model="modelName"
            class="block w-full mt-6 transition duration-300 ease-in-out bg-transparent border-transparent dark:text-gray-400 focus:border-red focus:outline-none focus:ring-transparent">
            <option disabled="" selected="" value="">Model</option>
            <option value="453">EDGE</option>
            <option value="657">MAGNATEC</option>
        </select>

        <select wire:model="modelId"
            class="block w-full mt-6 transition duration-300 ease-in-out bg-transparent border-transparent dark:text-gray-400 focus:border-red focus:outline-none focus:ring-transparent">
            <option disabled="" selected="" value="-1">Specification</option>
            <option value="3563">Syntium 5000, Patrol, Petrol/Diesel, 2000-2023</option>
        </select>


        <select wire:model="catId" required=""
            class="block w-full mt-6 transition duration-300 ease-in-out bg-transparent border-transparent dark:text-gray-400 focus:border-red focus:outline-none focus:ring-transparent">
            <option value="">Category</option>
            <option value="1">Air Filter</option>
            <option value="2">Oil Filter</option>
        </select>

        <select wire:model="subId"
            class="block w-full mt-6 transition duration-300 ease-in-out bg-transparent border-transparent dark:text-gray-400 focus:border-red focus:outline-none focus:ring-transparent">
            <option value="">Sub-category (Optional)</option>
            <option value="2">Plastic Panel</option>
            <option value="3">Metal Cap</option>
            <option value="4">Plastic Cap</option>
        </select>

        <button type="submit"
            class="items-center px-4 py-4 bg-blue-600 border border-transparent font-semibold text-base text-white uppercase tracking-widest hover:bg-blue-500 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-300 block w-full mt-4">
            Search
        </button>
    </form>
</div>
