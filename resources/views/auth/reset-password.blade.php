<x-layouts.auth title="Choose a new password">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <x-field label="Email" name="email" type="email" :value="$request->email" autocomplete="email" required/>
        <x-field label="New password" name="password" type="password" autocomplete="new-password" required autofocus/>
        <x-field label="Repeat new password" name="password_confirmation" type="password" autocomplete="new-password" required/>
        <button class="btn btn-primary btn-lg w-full">Save password</button>
    </form>
</x-layouts.auth>
