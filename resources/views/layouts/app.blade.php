<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="next-size-adjust" content="">

        <link rel="stylesheet" href="/assets/styles/styles.css" data-precedence="next">
        <link rel="stylesheet" href="/assets/styles/layout.css" data-precedence="next">
        <link rel="stylesheet" href="/assets/styles/fonts.css" data-precedence="next">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <style>.f_SW50ZXI{
                font-family: 'Roboto';
            }
        </style>
        <link rel="preload" href="https://via.placeholder.com/1200x500/666666/FFFFFF?text=Banner+1" as="image">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    </head>
    <body class="f_SW50ZXI">
    <div id="loading-wrapper" style="
    height: 100%;
    width: 100%;
    background: black;
    opacity: 0.3;
    position: absolute;
    z-index: 99999;
"></div>
    <style type="text/tailwindcss">@layer base {
            * {
                @apply border-border;
            }
            html {
                @apply scroll-smooth;
            }
            body {
                font-synthesis-weight: none;
                text-rendering: optimizeLegibility;
            }
        }
        @layer utilities {
            .step {
                counter-increment: step;
            }
            .step:before {
                @apply absolute w-9 h-9 bg-muted rounded-full font-mono font-medium text-center text-base inline-flex items-center justify-center -indent-px border-4 border-background;
                @apply ml-[-50px] mt-[-4px];
                content: counter(step);
            }
            .chunk-container {
                @apply shadow-none;
            }
            .chunk-container::after {
                content: '';
                @apply absolute -inset-4 shadow-xl rounded-xl border;
            }
        }
        @media (max-width: 640px) {
            .container {
                @apply px-4;
            }
        }
        .dark {
            /* Dark mode shadcn colors */
            --background: 240 10% 3.9%;
            --foreground: 0 0% 98%;
            --muted: 240 3.7% 15.9%;
            --muted-foreground: 240 5% 64.9%;
            --card: 240 10% 3.9%;
            --card-foreground: 0 0% 98%;
            --popover: 240 10% 3.9%;
            --popover-foreground: 0 0% 98%;
            --border: 240 3.7% 15.9%;
            --input: 240 3.7% 15.9%;
            --primary: 0 0% 98%;
            --primary-foreground: 240 5.9% 10%;
            --secondary: 240 3.7% 15.9%;
            --secondary-foreground: 0 0% 98%;
            --accent: 240 3.7% 15.9%;
            --accent-foreground: ;
            --destructive: 0 62.8% 30.6%;
            --destructive-foreground: 0 85.7% 97.3%;
            --warning: 35, 100%, 52%;
            --warning-foreground: 0 0% 9%;
            --ring: 240 3.7% 15.9%;
            --sidebar-background: 240 5.9% 10%;
            --sidebar-foreground: 240 4.8% 95.9%;
            --sidebar-primary: 224.3 76.3% 48%;
            --sidebar-primary-foreground: 0 0% 100%;
            --sidebar-accent: 240 3.7% 15.9%;
            --sidebar-accent-foreground: 240 4.8% 95.9%;
            --sidebar-border: 240 3.7% 15.9%;
            --sidebar-ring: 240 4.9% 83.9%;
        }
    </style>
    <style style="">  :root {
            --font-sans: __variable_3a0388;
            --font-mono: __variable_c1e5c9;
        }
    </style>
    <div class="flex flex-col min-h-screen">


        @if(!request()->is('checkout') && !request()->is('login') && !request()->is('register'))
            @include('components.marquee')
        @endif

        <main class="flex-grow">
            @if(request()->is('checkout'))
                @include('components.header-checkout')
            @elseif(request()->is('login') || request()->is('register'))
                @include('components.header-login')
            @else
                @include('components.header')
                @include('components.categories')
            @endif

            @if(!request()->is('checkout') && !request()->is('login') && !request()->is('register') && !request()->is('success'))
                    <div class="container mx-auto px-4 flex-grow">
                        @include('components.search')
                        @yield('content')
                    </div>
            @else
                    @if(request()->is('login') || request()->is('register'))
                        <div class="container mx-auto px-4 flex-grow">
                            <main class="container mx-auto px-4 py-8 flex-grow flex items-center justify-center">
                                @yield('content')
                            </main>
                        </div>

                    @else
                        <div class="container mx-auto px-4 flex-grow">
                            @yield('content')
                        </div>
                    @endif

            @endif


        </main>
        @include('components.footer')
    </div>
    </body>
</html>
