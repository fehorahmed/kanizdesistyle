<!DOCTYPE html>
<html class="" lang="en">
<head>
	<title>Kaniz's Desi Style</title>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, follow" />
    <meta name="description" content="Best computer products, laptops, and accessories in Bangladesh.">

    <!-- Open Graph Meta Tags -->
    <meta property='og:image:width' content='1200' />
    <meta property='og:image:height' content='630' />
    <meta property='og:image:type' content='image/jpeg' />
    <meta property="og:title" content="{{ $meta['title'] ?? 'Ecommerce' }}" />
    <meta property="og:description" content="{{ $meta['description'] ?? 'Best computer products, laptops, and accessories in Bangladesh.' }}" />
    <meta property="og:image" content="{{ isset($meta['image']) ? asset($meta['image']) : asset('img/logo.jpg') }}" />
    <meta property="og:url" content="{{ $meta['url'] ?? url()->current() }}" />

    <!-- Twitter Meta Tags -->
    <meta name="twitter:title" content="{{ $meta['title'] ?? 'Ecommerce' }}" />
    <meta name="twitter:description" content="{{ $meta['description'] ?? 'Best computer products, laptops, and accessories in Bangladesh.' }}" />
    <meta name="twitter:image" content="{{ isset($meta['image']) ? asset($meta['image']) : asset('img/logo.jpg') }}" />
    <meta name="twitter:card" content="summary_large_image" />

    <meta property="og:type" content="article" />
    <meta property="og:locale" content="en_US" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary-color: {{ $setting->primary_color ?? '#70c332' }};
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/all.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/animate.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/fontawesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/media.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/responsive.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/owl.carousel.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/logo.jpg') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('themes/frontend/assets/css/owl.theme.default.css') }}">
    
</head>
<body>

<div id="app"></div>

<!-- footer end -->
<a href="#" id="scroll">
    <i class="fas fa-arrow-up"></i>
</a>
	
<script  src="{{ asset('themes/frontend/assets/js/jquery-3.4.1.min.js') }}"></script>
<script  src="{{ asset('themes/frontend/assets/js/owl.carousel.js') }}"></script>
<script  src="{{ asset('themes/frontend/assets/js/bootstrap.min.js') }}"></script>
<script  src="{{ asset('themes/frontend/assets/js/popper.min.js') }}"></script>
<script  src="{{ asset('themes/frontend/assets/js/all.min.js') }}"></script>
<script  src="{{ asset('themes/frontend/assets/js/index.js') }}"></script>
<script  src="{{ asset('themes/frontend/assets/js/wow.min.js') }}"></script>
{{-- <script>
  
	var deadline = new Date("dec 31, 2022 15:37:25").getTime(); 
	  
	var x = setInterval(function() { 
	  
	var now = new Date().getTime(); 
	var t = deadline - now; 
	var days = Math.floor(t / (1000 * 60 * 60 * 24)); 
	var hours = Math.floor((t%(1000 * 60 * 60 * 24))/(1000 * 60 * 60)); 
	var minutes = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60)); 
	var seconds = Math.floor((t % (1000 * 60)) / 1000); 
	document.getElementById("day").innerHTML =days ; 
	document.getElementById("hour").innerHTML =hours; 
	document.getElementById("minute").innerHTML = minutes;  
	document.getElementById("second").innerHTML =seconds;  
	if (t < 0) { 
	        clearInterval(x); 
	        document.getElementById("demo").innerHTML = "TIME UP"; 
	        document.getElementById("day").innerHTML ='0'; 
	        document.getElementById("hour").innerHTML ='0'; 
	        document.getElementById("minute").innerHTML ='0' ;  
	        document.getElementById("second").innerHTML = '0'; } 
	}, 1000); 

</script>  --}}

</body>
</html>