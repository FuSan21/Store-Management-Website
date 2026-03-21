<x-layouts.account title="Addresses">
    <x-slot:actions>
        <a href="{{ route('account.addresses.create') }}" class="btn btn-primary"><x-icon name="plus" :size="16"/> Add address</a>
    </x-slot:actions>

    @if ($addresses->isEmpty())
        <x-empty-state icon="map-pin" title="No saved addresses" body="Save your home or workshop address to check out in one step.">
            <a href="{{ route('account.addresses.create') }}" class="btn btn-primary">Add an address</a>
        </x-empty-state>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($addresses as $address)
                <article class="card flex flex-col p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-semibold">{{ $address->label }}</h2>
                        @if ($address->is_default_shipping)<span class="rounded-full bg-surface px-2 py-0.5 text-xs">Default delivery</span>@endif
                        @if ($address->is_default_billing)<span class="rounded-full bg-surface px-2 py-0.5 text-xs">Default billing</span>@endif
                    </div>
                    <p class="mt-3 text-sm text-muted">{{ $address->recipient }}<br>{{ $address->line }}<br>{{ $address->city }}</p>
                    @if ($address->phone)<p class="mt-1 text-sm text-muted">{{ $address->phone }}</p>@endif
                    <div class="mt-auto flex gap-2 pt-5">
                        <a href="{{ route('account.addresses.edit', $address) }}" class="btn btn-secondary btn-sm"><x-icon name="pencil" :size="14"/> Edit</a>
                        <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" x-data x-confirm="Remove this address?">
                            @csrf @method('DELETE')
                            <button class="btn btn-ghost btn-sm text-muted hover:text-accent"><x-icon name="trash" :size="14"/> Remove</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-layouts.account>
