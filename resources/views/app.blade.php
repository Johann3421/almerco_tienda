<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="canonical" href="https://grupoalmerco.com.pe/" />
        <meta name="description" content="Somos una empresa especializada en el comercio de productos tecnológicos de las mejores marcas, ofreciendo garantía y un servicio de calidad.">
        <meta name="robots" content="index, follow, nocache">
        <meta property="og:title" content="Grupo Almerco">
        <meta property="og:description" content="Somos una empresa especializada en el comercio de productos tecnológicos de las mejores marcas, ofreciendo garantía y un servicio de calidad.">
        <meta property="og:url" content="https://grupoalmerco.com.pe/">
        <meta property="og:site_name" content="Grupo Almerco">
        <meta property="og:locale" content="es">
        <meta name="keywords" content="tecnología, productos tecnológicos, venta de tecnología, Grupo Almerco, marcas reconocidas, productos de calidad">

        <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "Organization",
              "name": "Grupo Almerco",
              "url": "https://grupoalmerco.com.pe/",
              "logo": "https://grupoalmerco.com.pe/path/to/logo.jpg",
              "sameAs": [
                "https://www.facebook.com/GrupoAlmerco",
                "https://www.instagram.com/GrupoAlmerco"
              ]
            }
        </script>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
