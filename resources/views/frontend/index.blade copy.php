<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Integrated Medicare</title>
    <meta name="robots" content="noindex, follow" />
    <meta name="description" content="">

    <!-- Open Graph Meta Tags -->
    <meta property='og:image:width' content='1200' />
    <meta property='og:image:height' content='630' />
    <meta property='og:image:type' content='image/jpeg' />
    <meta property="og:title" content="{{ $meta['title'] ?? 'Integrated Medicare' }}" />
    <meta property="og:description" content="{{ $meta['description'] ?? 'Default description' }}" />
    <meta property="og:image" content="{{ isset($meta['image']) ? asset($meta['image']) : asset('img/logo_pos.jpg') }}" />
    <meta property="og:url" content="{{ $meta['url'] ?? url()->current() }}" />

    <!-- Twitter Meta Tags -->
    <meta name="twitter:title" content="{{ $meta['title'] ?? 'Integrated Medicare' }}" />
    <meta name="twitter:description" content="{{ $meta['description'] ?? 'Default description' }}" />
    <meta name="twitter:image" content="{{ isset($meta['image']) ? asset($meta['image']) : asset('img/logo_pos.jpg') }}" />

    <meta property="og:type" content="article" />
    <meta property="og:locale" content="en_US" />
    
    <!-- For Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image" />

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{-- For google console --}}
    <meta name="google-site-verification" content="RGmfIquDUhrW3OGjGynnE_ujaDOWWOmjKIAz3byyLwg"/>
    {{-- For Bing --}}
    <meta name="msvalidate.01" content="38F50C1E2D1820FFF7FAC272A5F0FAE5"/>
    <!-- Place favicon.png in the root directory -->
    {{-- <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon" /> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="shortcut icon" href="{{ asset('img/logo.jpg') }}" type="image/x-icon" />
    <link rel="stylesheet" href="{{asset('ui/frontend')}}/css/font-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('ui/frontend')}}/css/plugins.css">
    <link rel="stylesheet" href="{{asset('ui/frontend')}}/css/responsive.css">
    <link rel="stylesheet" href="{{asset('ui/frontend')}}/css/style.css">
    <link rel="stylesheet" href="{{asset('ui/frontend')}}/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/icomoon@1.0.0/style.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .MsoNormal hr{
            padding: 0;
            margin: 0;
            border-top: 1px solid #000 !important;
        }
        .ltn__blog-details-wrap ul li, .ltn__blog-details-wrap ol li {
            margin-top: 0rem !important;
        }
        .ltn__blog-details-wrap p.MsoNormal {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div id="app">

    </div>


    <script src="{{asset('ui/frontend')}}/js/plugins.js"></script>
    <!-- Main JS -->
    {{-- <script src="{{asset('ui/frontend')}}/js/main.js"></script> --}}
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4HN4E7XNQM"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-4HN4E7XNQM');
    </script>
</body>
</html>
