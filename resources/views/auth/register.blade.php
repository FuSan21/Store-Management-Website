<x-layouts.auth title="Create an account" subtitle="Save your addresses and follow every order to your door.">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <x-field label="Name" name="name" autocomplete="name" required autofocus/>
        <x-field label="Email" name="email" type="email" autocomplete="email" required/>
        <x-field label="Password" name="password" type="password" autocomplete="new-password" hint="At least 8 characters." required/>
        <x-field label="Repeat password" name="password_confirmation" type="password" autocomplete="new-password" required/>
        <button class="btn btn-primary btn-lg w-full">Create account</button>
    </form>
    <p class="mt-8 text-center text-sm text-muted">Already have an account? <a href="{{ route('login') }}" class="link text-ink">Sign in</a></p>
</x-layouts.auth>
