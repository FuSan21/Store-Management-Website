<x-layouts.auth title="Reset your password" subtitle="Enter your email and we will send a link to choose a new password.">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <x-field label="Email" name="email" type="email" autocomplete="email" required autofocus/>
        <button class="btn btn-primary btn-lg w-full">Email the reset link</button>
    </form>
    <p class="mt-8 text-center text-sm text-muted"><a href="{{ route('login') }}" class="link text-ink">Back to sign in</a></p>
</x-layouts.auth>
