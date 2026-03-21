<x-layouts.auth title="Sign in" subtitle="Track orders, reorder filters and check out faster.">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <x-field label="Email" name="email" type="email" autocomplete="email" required autofocus/>
        <div>
            <x-field label="Password" name="password" type="password" autocomplete="current-password" required/>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link mt-2 inline-block text-sm text-muted">Forgot your password?</a>
            @endif
        </div>
        <label class="flex items-center gap-2.5 text-sm"><input type="checkbox" name="remember" class="checkbox"> Keep me signed in</label>
        <button class="btn btn-primary btn-lg w-full">Sign in</button>
    </form>
    <p class="mt-8 text-center text-sm text-muted">New here? <a href="{{ route('register') }}" class="link text-ink">Create an account</a></p>
</x-layouts.auth>
