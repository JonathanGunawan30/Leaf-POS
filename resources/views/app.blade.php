<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ondragstart="return false" onselectstart="return false">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Leaf POS</title>
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/leafPos1.svg') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->
        <link href="https://cdn.jsdelivr.net/npm/remixicon@3.7.0/fonts/remixicon.css" rel="stylesheet">
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
{{--        @include('lock') --}}
    </head>
    <body class="font-sans antialiased">
        @inertia
        <noscript>
            <h1>Please enable JavaScript to access this website !</h1>
            <p>Your IP Address : {{ request()->ip() }}</p>
            @if(request()->server('HTTP_X_FORWARDED_FOR'))
                <p>Proxy IP : {{ request()->server('HTTP_X_FORWARDED_FOR') }}</p>
             @endif
            <p>Device : {{ request()->header('User-Agent') }}</p>
        </noscript>
    </body>
</html>
