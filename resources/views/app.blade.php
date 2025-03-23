<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" ondragstart="return false" onselectstart="return false">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Leaf POS</title>
        <link rel="shortcut icon" type="image/png" href="{{ asset('images/com.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
        <script>
            document.addEventListener('contextmenu', event => event.preventDefault());
            
            document.onkeydown = function(e) {
                if (e.keyCode === 123 || // F12
                    (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
                    (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
                    (e.ctrlKey && e.keyCode === 85)) { // Ctrl+U
                    alert("Don't inspect if you don't want to be blocked!");
                    return false;
                }
            };
            // DevTools Detection
            let devtools = function() {};
            devtools.toString = function() {
                alert("Don't inspect if you don't want to be blocked!");
                return '';
            }
            console.log('%c', devtools);
            // Disable source view
            document.onkeypress = function (event) {
                event = (event || window.event);
                if (event.keyCode == 123) {
                    alert("Don't inspect if you don't want to be blocked!");
                    return false;
                }
            }
        </script>
    </head>
    <body class="font-sans antialiased">
        @inertia
        <noscript>
            <h1>Please enable JavaScript to access this website.</h1>
        </noscript>
    </body>
</html>