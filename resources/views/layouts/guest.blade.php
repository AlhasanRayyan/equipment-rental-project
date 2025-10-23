<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- Favicons (Optional, add your own paths if needed) --}}
        {{-- <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('assets/img/favicon/site.webmanifest') }}"> --}}

        <!-- Fonts (Breeze default, keep if you use them) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Breeze's default CSS/JS (يمكنك حذفها إذا لم تعد تستخدم Tailwind/Alpine.js) -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- This slot is for any additional head content from child views -->
        @isset($head)
            {{ $head }}
        @endisset
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{ $slot }}

        <!-- This slot is for any additional scripts from child views -->
        @isset($scripts)
            {{ $scripts }}
        @endisset
    </body>
</html>