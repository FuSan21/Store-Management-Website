<x-layouts.auth title="Confirm your email" subtitle="We sent a link to your inbox. Open it to confirm your address and receive order updates.">
    @if (session('status') === 'verification-link-sent')
        <p class="mb-6 rounded-lg bg-ok-soft px-4 py-3 text-sm text-ok">A new link is on its way.</p>
    @endif
    <div class="flex flex-wrap items-center gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-primary">Send the link again</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-ghost">Sign out</button>
        </form>
    </div>
</x-layouts.auth>
