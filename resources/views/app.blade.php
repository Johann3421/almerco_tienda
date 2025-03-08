<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-537S5MH7');</script>
<!-- End Google Tag Manager -->
        <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-F2801G6C90"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-F2801G6C90');
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="canonical" href="https://sekaitech.com.pe/" />
        <meta name="description" content="SEKAI TECH - Expertos en tecnología, ofrecemos los mejores productos tecnológicos con garantía y calidad.">
        <meta name="robots" content="index, follow">
        <meta property="og:title" content="SEKAI TECH - Tecnología de Vanguardia">
        <meta property="og:description" content="SEKAI TECH - Innovación y calidad en productos tecnológicos.">
        <meta property="og:url" content="https://sekaitech.com.pe/">
        <meta property="og:site_name" content="SEKAI TECH">
        <meta property="og:locale" content="es">
        <meta name="keywords" content="tecnología, productos tecnológicos, SEKAI TECH, innovación, calidad, servicio de garantía">

        <script type="application/ld+json">
            {
              "@context": "https://schema.org",
              "@type": "Organization",
              "name": "SEKAI TECH",
              "url": "https://sekaitech.com.pe/",
              "logo": "https://sekaitech.com.pe/path/to/logo.jpg",
              "sameAs": [
                "https://www.facebook.com/SekaiTech",
                "https://www.instagram.com/SekaiTech"
              ]
            }
        </script>

        <title inertia>{{ $page['props']['title'] ?? config('app.name', 'SEKAI TECH') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @if (app()->environment('local'))
            @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @else
            <script type="module" src="{{ asset('build/assets/app-BHPdqG9z.js') }}"></script>
        @endif

        @inertiaHead
        
    </head>
    <body class="font-sans antialiased">
        <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-537S5MH7"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
        @inertia
        <a href="https://wa.me/1234567890" class="whatsapp-float" id="whatsapp-icon" target="_blank">
    <i class="fab fa-whatsapp"></i>
</a>
    </body>
</html>

<script>
  document.addEventListener("DOMContentLoaded", function () {
      const whatsappIcon = document.getElementById("whatsapp-icon");
      const currentPath = window.location.pathname;

      // Verifica si la URL contiene la ruta específica
      if (!currentPath.includes("resources/js/pages/web/")) {
          whatsappIcon.style.display = "none"; // Oculta el icono si no está en la ruta permitida
      }
  });
</script>


<style>
  .whatsapp-float {
    position: fixed;
    width: 60px;
    height: 60px;
    bottom: 40px;
    right: 40px;
    background-color: #25d366;
    color: #FFF;
    border-radius: 50px;
    text-align: center;
    font-size: 30px;
    box-shadow: 2px 2px 3px #999;
    z-index: 100;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease-in-out;
}

.whatsapp-float:hover {
    background-color: #128C7E;
    transform: scale(1.1);
}

.whatsapp-float i {
    margin-top: 5px;
}
</style>
