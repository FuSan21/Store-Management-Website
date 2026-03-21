@props(['code', 'title', 'body'])
<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.head', ['title' => $title, 'description' => null])
</head>
<body class="grid min-h-dvh place-items-center bg-bg px-5 text-ink">
    <main class="max-w-md text-center">
        <a href="{{ url('/') }}" class="inline-block" aria-label="Home"><x-logo/></a>
        <p class="mt-10 text-sm font-semibold text-accent tabular-nums">Error {{ $code }}</p>
        <h1 class="mt-2 text-3xl font-semibold sm:text-4xl">{{ $title }}</h1>
        <p class="mt-3 text-muted">{{ $body }}</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ url('/') }}" class="btn btn-primary">Go to the shop</a>
            <a href="{{ url('/contact') }}" class="btn btn-secondary">Contact us</a>
        </div>
    </main>
</body>
</html>
