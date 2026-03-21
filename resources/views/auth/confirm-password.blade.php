<x-layouts.auth title="Confirm your password" subtitle="This is a protected area. Enter your password to continue.">
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <x-field label="Password" name="password" type="password" autocomplete="current-password" required autofocus/>
        <button class="btn btn-primary btn-lg w-full">Confirm</button>
    </form>
</x-layouts.auth>
