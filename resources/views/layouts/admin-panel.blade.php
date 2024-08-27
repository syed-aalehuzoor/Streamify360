<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="bg-accent" x-data="{openSidebar: true}">  
        @livewire('admin-header')
        
        <div class="flex justify-center gap-2 m-1 sm:m-10 flex-row">
            <x-admin-navigation />
            
            <!-- Page Content -->
            <div class="flex flex-col w-full sm:w-3/5">

                <main class="h-full overflow-y-auto">
                    {{ $slot }}    
                </main>

            </div>

                @stack('modals')
                @livewireScripts

        </div>
    </body>
</html>
