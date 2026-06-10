<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Autenticação') — Tallents RH</title>
    @include('layouts.partials.theme-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-gradient-to-br from-indigo-900 via-indigo-800 to-purple-900 flex items-center justify-center min-h-screen px-4">
    @yield('content')
    @stack('scripts')
</body>
</html>
