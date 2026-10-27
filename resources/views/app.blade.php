<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/x-icon"
        href="{{ function_exists('get_setting') && get_setting('app_favicon') ? asset('storage/' . get_setting('app_favicon')) : asset('favicon.ico') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com/" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/perfect-scrollbar.min.css') }}" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/animate.css') }}" />

    <script>
        try {
            if (localStorage.getItem('menu') !== '"vertical"') {
                localStorage.setItem('menu', '"vertical"');
            }
        } catch (e) {}
    </script>

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>

<body class="relative overflow-x-hidden font-nunito text-sm font-normal antialiased vertical">
    @inertia
</body>

</html>
