<html class="__className_3a0388 __variable_3a0388 __variable_c1e5c9 __variable_9d7907" style="--font-sans:var(--font-geist-sans);--font-mono:var(--font-geist-mono);--font-serif:__fallback" lang="en">
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="next-size-adjust" content="">

    <link rel="stylesheet" href="/admin/assets/styles/styles.css" data-precedence="next">
    <link rel="stylesheet" href="/admin/assets/styles/layout.css" data-precedence="next">
    <link rel="stylesheet" href="/admin/assets/styles/fonts.css" data-precedence="next">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <style>.f_SW50ZXI{
            font-family: 'Roboto';
        }
    </style>
    <link rel="preload" href="https://via.placeholder.com/1200x500/666666/FFFFFF?text=Banner+1" as="image">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

</head>
<body data-testim-main-word-scripts-loaded="true">

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
<script src="/admin/assets/lite-runtime.js?v=1"></script><!--$-->
<style style="">  :root {
        --font-sans: __variable_3a0388;
        --font-mono: __variable_c1e5c9;
    }
</style>
<!--/$-->
<script src="/admin/assets/js/admin.js" async=""></script>
<script src="/_next/static/chunks/webpack-270ff552acd000ce.js" async=""></script><script src="/_next/static/chunks/4996-80d654afda2b1e3b.js" async=""></script><script src="/_next/static/chunks/9209-84ba6c5decf96078.js" async=""></script><script src="/_next/static/chunks/8774-973af8487de2042d.js" async=""></script><script src="/_next/static/chunks/7795-444f5e2ccba0a2f4.js" async=""></script><script src="/_next/static/chunks/1070-0487e6e62becdf57.js" async=""></script><script src="/_next/static/chunks/6447-f86fc81cdacd0153.js" async=""></script><script src="/_next/static/chunks/1037-7923c877fe1445a9.js" async=""></script><script src="/_next/static/chunks/2309-0133c534efc8a721.js" async=""></script><script src="/_next/static/chunks/app/(lite)/render/next/page-03b0782c59d3b94c.js" async=""></script><script>$RS=function(a,b){a=document.getElementById(a);b=document.getElementById(b);for(a.parentNode.removeChild(a);a.firstChild;)b.parentNode.insertBefore(a.firstChild,b);b.parentNode.removeChild(b)};$RS("S:1","P:1")</script><script>$RC=function(b,c,e){c=document.getElementById(c);c.parentNode.removeChild(c);var a=document.getElementById(b);if(a){b=a.previousSibling;if(e)b.data="$!",a.setAttribute("data-dgst",e);else{e=b.parentNode;a=b.nextSibling;var f=0;do{if(a&&8===a.nodeType){var d=a.data;if("/$"===d)if(0===f)break;else f--;else"$"!==d&&"$?"!==d&&"$!"!==d||f++}d=a.nextSibling;e.removeChild(a);a=d}while(a);for(;c.firstChild;)e.insertBefore(c.firstChild,a);b.data="$"}b._reactRetry&&b._reactRetry()}};$RC("B:0","S:0")</script>
<div></div>
<div class="flex min-h-screen">
    <div class="flex-1">
        <header class="py-4 px-4 sm:px-6 lg:px-8 bg-white shadow-sm">
            <div class="container mx-auto flex justify-between items-center">
                <div class="flex-1"></div>
                <a href="/" class="flex items-center justify-center flex-1"><img alt="Venia" loading="lazy" width="150" height="40" decoding="async" data-nimg="1" class="h-10 w-auto" src="https://staging.chupaprecios.com.mx/chupaprecioslogo-bLx.svg" style="color: transparent;"></a>
                <nav class="flex-1 flex justify-end">

                </nav>
            </div>
        </header>

        <div class="flex-1 space-y-4 p-8 pt-6">
            @yield('content')
        </div>
    </div>
</div>
<span id="recharts_measurement_span" aria-hidden="true" style="position: absolute; top: -20000px; left: 0px; padding: 0px; margin: 0px; border: none; white-space: pre; font-size: 12px; letter-spacing: normal;">$0</span>
</body>
</html>
