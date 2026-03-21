<x-layouts.account :title="$address->exists ? 'Edit address' : 'New address'">
    <form method="POST" action="{{ $address->exists ? route('account.addresses.update', $address) : route('account.addresses.store') }}" class="card max-w-2xl space-y-5 p-6">
        @csrf
        @if ($address->exists) @method('PUT') @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="Label" name="label" :value="$address->label" placeholder="Home, Office, Workshop" required/>
            <x-field label="Recipient" name="recipient" :value="$address->recipient ?? auth()->user()->name" autocomplete="name" required/>
        </div>
        <x-field label="Street and area" name="line" :value="$address->line" autocomplete="street-address" placeholder="Road 27, Dhanmondi" required/>
        <div class="grid gap-5 sm:grid-cols-2">
            <x-field label="City" name="city" :value="$address->city" autocomplete="address-level2" placeholder="Dhaka" required/>
            <x-field label="Mobile number" name="phone" type="tel" :value="$address->phone" autocomplete="tel" placeholder="01712345678" hint="The courier calls this number."/>
        </div>
        <div class="space-y-2.5">
            <label class="flex items-center gap-2.5 text-sm"><input type="checkbox" name="is_default_shipping" value="1" class="checkbox" @checked(old('is_default_shipping', $address->is_default_shipping))> Use as my default delivery address</label>
            <label class="flex items-center gap-2.5 text-sm"><input type="checkbox" name="is_default_billing" value="1" class="checkbox" @checked(old('is_default_billing', $address->is_default_billing))> Use as my default billing address</label>
        </div>
        <div class="flex gap-3 pt-2">
            <button class="btn btn-primary">Save address</button>
            <a href="{{ route('account.addresses.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</x-layouts.account>
