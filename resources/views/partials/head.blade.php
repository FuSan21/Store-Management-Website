<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="{{ $description ?? config('store.tagline') }}">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0b0b0c" media="(prefers-color-scheme: dark)">
<title>{{ $title ? $title.' · ' : '' }}{{ config('store.name') }}</title>
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<script>
    // Apply the saved theme before first paint.
    (() => {
        const mode = localStorage.getItem('theme') ?? 'system';
        const dark = mode === 'dark' || (mode === 'system' && matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('dark', dark);
    })();
</script>
@fonts
@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
@livewireScriptConfig
