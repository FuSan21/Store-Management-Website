<x-layouts.account title="Profile & security">
    <div class="max-w-2xl space-y-6">
        <form method="POST" action="{{ route('account.profile.update') }}" class="card space-y-5 p-6">
            @csrf @method('PATCH')
            <div>
                <h2 class="text-lg font-semibold">Your details</h2>
                <p class="mt-1 text-sm text-muted">Changing your email asks you to confirm the new one.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field label="Name" name="name" :value="$user->name" autocomplete="name" required/>
                <x-field label="Email" name="email" type="email" :value="$user->email" autocomplete="email" required/>
            </div>
            <button class="btn btn-primary">Save details</button>
        </form>

        <form method="POST" action="{{ route('account.profile.password') }}" class="card space-y-5 p-6">
            @csrf @method('PUT')
            <h2 class="text-lg font-semibold">Password</h2>
            <x-field label="Current password" name="current_password" type="password" bag="password" autocomplete="current-password" required/>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-field label="New password" name="password" type="password" bag="password" autocomplete="new-password" hint="At least 8 characters." required/>
                <x-field label="Repeat new password" name="password_confirmation" type="password" bag="password" autocomplete="new-password" required/>
            </div>
            <button class="btn btn-primary">Change password</button>
        </form>

        <div class="card p-6">
            <h2 class="text-lg font-semibold">Appearance</h2>
            <div class="mt-4 inline-flex rounded-lg border border-line p-1" x-data role="radiogroup" aria-label="Colour theme">
                @foreach (['light' => 'Light', 'dark' => 'Dark', 'system' => 'Match device'] as $mode => $label)
                    <button type="button" role="radio" @click="$store.theme.set('{{ $mode }}')" :aria-checked="$store.theme.mode === '{{ $mode }}'"
                        :class="$store.theme.mode === '{{ $mode }}' ? 'bg-ink text-bg' : 'text-muted hover:text-ink'"
                        class="rounded-md px-3.5 py-1.5 text-sm font-medium transition-colors">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        @unless ($user->isAdmin())
            <form method="POST" action="{{ route('account.profile.destroy') }}" class="card space-y-4 border-accent/40 p-6" x-data x-confirm="Delete your account? Your order history stays with the store but you will no longer be able to sign in.">
                @csrf @method('DELETE')
                <div>
                    <h2 class="text-lg font-semibold">Delete account</h2>
                    <p class="mt-1 text-sm text-muted">Removes your profile, addresses, cart, wishlist and reviews.</p>
                </div>
                <x-field label="Confirm with your password" name="password" type="password" bag="deletion" autocomplete="current-password" class="max-w-sm" required/>
                <button class="btn btn-danger">Delete my account</button>
            </form>
        @endunless
    </div>
</x-layouts.account>
