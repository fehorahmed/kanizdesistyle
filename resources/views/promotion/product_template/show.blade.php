<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->product_combo_name }}</title>
    @include('layouts.partial.__favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $template->product_combo_name }}">
    <meta property="og:description" content="{{ $template->product_combo_name }}">
    <meta property="og:image" content="{{ asset($template->promotion_image) }}">
    <meta property="og:image:secure_url" content="{{ asset($template->promotion_image) }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:type" content="image/jpeg">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url()->current() }}">
    <meta name="twitter:title" content="{{ $template->product_combo_name }}">
    <meta name="twitter:description" content="{{ $template->product_combo_name }}">
    <meta name="twitter:image" content="{{ asset($template->promotion_image) }}">

    <style>
        .bg-custom-dark-green {
            background-color: #214d24; /* A dark green shade */
        }
        .bg-custom-medium-green {
            background-color: #386f3a; /* A medium green shade */
        }
        /* Add custom font if needed */
        body {
            font-family: "Hind Siliguri", sans-serif;
        }
        .benifit-header {
            background-color: #45712F;
            margin: 0px 0px 0px 0px;
            padding: 16px 11px 15px 11px;
            border-style: solid;
            border-color: #FFFF00;
            border-radius: 22px 22px 22px 22px;
        }
        .benifit-list-item {
            font-size: 25px; 
        }

        /* Custom Green Header */
        .bg-custom-header-green {
            background-color: #386f3a; 
            color: white;
            padding: 10px 0;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        /* Styling for the video thumbnail wrapper (for the grid section) */
        .video-thumbnail-wrapper {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            cursor: pointer;
            margin-bottom: 15px; /* Spacing below video items */
        }

        .video-thumbnail-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .swiper-container {
            width: 100%; 
            overflow: hidden; 
        }
        .swiper-slide img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

    <header class="text-white text-center">
        <div class="container-fluid">
            <img src="{{ asset('img/logo.jpg') }}" height="60px" alt="Logo" />
        </div>
    </header>

    <section class="py-2 mt-2 bg-custom-dark-green">
        <div class="container">
            <div class="mt-2 text-center text-white">
                <h2 class="mb-3">{{ $template->main_heading }}</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-12 col-lg-12">
                    <div 
                        class="ratio ratio-16x9 shadow-lg mb-4" 
                        style="border-style: solid; border-color: #FFFFFF; border-radius: 32px;"
                    >
                        <iframe 
                            src="{{  $template->video_url }}?rel=0" 
                            title="YouTube video player" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            style="border-radius: 32px;"
                            allowfullscreen>
                        </iframe>
                    </div>
                    <div class="text-center">
                        <a href="#order-section" class="btn btn-lg btn-danger text-white fw-bold px-5">অর্ডার করুন</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="py-5">
        <div class="container">
            <div class="benifit-header text-center text-white mb-4">
                <h2>{{ $template->benefits_title }}</h2>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-4">
                    <ul class="list-unstyled">
                        @foreach ($template->benefits as $benefit)
                            <li class="mb-2 benifit-list-item">
                                <span class="me-2 text-success">
                                    <svg aria-hidden="true" class="e-font-icon-svg e-fas-check-circle" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor"><path d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path></svg>
                                </span> {{  $benefit }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
             <div class="text-center mt-4">
                <a href="#order-section" class="btn btn-danger text-white fw-bold px-5">অর্ডার করুন</a>
            </div>
        </div>
    </section>
    @php
        $totalRegularPrice = 0;
        foreach ($template->details as $key => $detail) {
            $inventory = \App\Models\PurchaseInventory::where('product_id', $detail['product_id'])->where('quantity','>',0)->first();
            $totalRegularPrice += $inventory ? $inventory->selling_price : 0;
            // dd($inventory);
        }
    @endphp
    {{-- <section class="bg-custom-dark-green py-5 text-center text-white">
        <div class="container">
            <h2 class="display-5 fw-bold mb-4 bangla-font-bold">
                {{ $template->product_combo_name }} এর পূর্বের মূল্য <del class="text-warning">{{ enToBn($totalRegularPrice) }}</del> টাকা
            </h2>
            <h1 class="display-3 fw-bold mb-4 bangla-font-bold">
                অফার মূল্য <span class="new-price-underline text-warning">৬৯৯ টাকা</span>
            </h1>
            <p class="lead mb-2 bangla-font-bold">
                ফ্রি হোম ডেলিভারি!!
            </p>
            <a href="order-section" class="btn btn-success btn-lg">অর্ডার করুন</a>
        </div>
    </section> --}}
    <section class="py-5 bg-light">
        <div class="container">
            
            <div class="text-center bg-custom-header-green mx-auto mb-5" style="max-width: 800px;">
                <h3 class="mb-0 p-2">{{ $template->review_upper_area_title }}</h3>
            </div>

            {{-- <h4 class="text-center mb-4 text-secondary">ক্রেতাদের ছবি রিভিউসমূহ</h4> --}}
            
            <div class="swiper-container imageSwiper mb-5">
                <div class="swiper-wrapper">
                    @foreach ($template->review_images as $image)
                        <div class="swiper-slide">
                            <img src="{{ asset($image) }}" class="img-fluid rounded shadow" alt="Customer Testimonial">
                        </div>
                    @endforeach
                </div>
                
            </div>
            <hr class="my-5">

            <h4 class="text-center mb-4 text-secondary">ভিডিও রিভিউসমূহ</h4>
            
                <div class="row g-4 justify-content-center">
                    @foreach ($template->review_videos as $video)
                        <div class="col-12 col-md-4 col-lg-4">
                            <div class="ratio ratio-16x9 rounded shadow-lg">
                                <iframe 
                                    src="{{ $video }}?rel=0" 
                                    title="YouTube video player 1" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                ></iframe>
                            </div>
                        </div>
                    @endforeach
                </div>
            <div class="text-center mt-5">
                <a href="#order-section" class="btn btn-lg btn-danger text-white fw-bold px-5 py-2" style="border-radius: 8px;">অর্ডার করুন</a>
            </div>
        </div>
    </section>
    <section class="py-4 bg-custom-medium-green text-white text-center">
        <div class="container">
            <h1 class="text-warning fw-bold mb-2">{{ $template->offer_area_title }}</h1>
            
            <div id="countdown-timer" class="row justify-content-center mb-3">
                <div class="col-3">
                    <div class="bg-dark p-2 rounded">
                        <span id="timer-days" class="d-block fs-3 fw-bold">00</span>
                        <span class="d-block small">দিন</span>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-dark p-2 rounded">
                        <span id="timer-hours" class="d-block fs-3 fw-bold">00</span>
                        <span class="d-block small">ঘন্টা</span>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-dark p-2 rounded">
                        <span id="timer-minutes" class="d-block fs-3 fw-bold">00</span>
                        <span class="d-block small">মিনিট</span>
                    </div>
                </div>
                <div class="col-3">
                    <div class="bg-dark p-2 rounded">
                        <span id="timer-seconds" class="d-block fs-3 fw-bold">00</span>
                        <span class="d-block small">সেকেন্ড</span>
                    </div>
                </div>
            </div>
            <h4 class="mb-3">{{ $template->offer_area_sub_title }}</h4>
            <a href="#order-section" class="btn btn-lg btn-danger text-white fw-bold px-5 py-3 shadow-lg">অর্ডার করুন</a>
        </div>
    </section>
    @if ($template->is_show_certificate == 1)
        <section class="py-5 bg-light">
            <div class="container">
                <h3 class="text-center mb-1 text-custom-dark-green">{{ $template->certificate_area_title }}</h3>
                <h1 class="text-center mb-4 text-custom-dark-green" style="color: #5AA721;">{{ $template->certificate_area_subtitle }}</h1>
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-6">
                        <img src="{{ asset($template->certificate_image) }}" class="img-fluid shadow-lg mb-4" alt="BCSIR Certificate">
                        <div class="text-center">
                            <a href="#order-section" class="btn btn-lg btn-danger text-white fw-bold px-5">অর্ডার করুন</a>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <p class="text-muted">{{ $template->certificate_area_bottom_title }}</p>
                </div>
            </div>
        </section>
    @endif
    
    <form id="checkoutForm" action="{{ route('product-template.order.submit', $template->id) }}" method="POST">
        @csrf
        <section id="order-section">
            <div class="container mb-5">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-12 p-0">
                        
                        <div class="order-header-black mb-4 text-center" style="background-color: #000000;padding: 16px 16px 16px 16px;border-radius: 27px 27px 27px 27px;">
                            <h2 style="font-size: 37px;font-weight: 600;line-height: 61px;color: #FFFFFF;">অর্ডার কনফার্ম করতে নিচের ফরমটি পূরণ করুন</h2>
                        </div>

                        <div class="mb-5">
                            <h5 class="fs-4 fw-semibold mb-3">সিলেক্ট করুন</h5>
                            <div class="row g-4">
                                @foreach ($template->bundles as $index => $bundle)
                                    @php
                                        $totalRegularPrice = $totalRegularPrice * $bundle->buy_quantity;
                                        $discountAmount = floor(($totalRegularPrice * $bundle->discount_percent) / 100);
                                    @endphp
                                    <div class="col-12 col-md-6">
                                        <label for="package-{{ $index+1 }}" class="position-relative d-block border rounded-3 p-4 shadow-sm cursor-pointer product-card" data-price="{{ $totalRegularPrice - $discountAmount }}" data-qty="{{  $bundle->quantity  }}">
                                            <input type="radio" id="package-{{ $index+1 }}" name="product_package" class="form-check-input position-absolute top-0 end-0 mt-3 me-3" value="{{ $bundle->id }}" {{ $index === 0 ? 'checked' : '' }}>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset($template->promotion_image) }}" alt="Stress Relief Oil" class="w-25 me-3 rounded">
                                                <div>
                                                    <p class="mb-1 fw-semibold fs-5">@if($bundle->buy_quantity > 1){{ enToBn($bundle->buy_quantity) }} টি @endif {{ $template->product_combo_name }}</p>
                                                    @if ($bundle->discount_percent > 0)
                                                        <p class="mb-0 fs-5 fw-bold">
                                                            <span class="text-decoration-line-through text-muted me-2 fs-6">৳ {{ enToBn(number_format($totalRegularPrice, 2)) }}</span>
                                                            <span class="text-green-primary fs-3">৳ {{ enToBn(number_format($totalRegularPrice - $discountAmount, 2)) }}</span>
                                                        </p>
                                                    @else
                                                        <p class="mb-0 fs-3 fw-bold text-green-primary">৳ {{ enToBn(number_format($totalRegularPrice, 2)) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- <span class="badge text-bg-danger position-absolute top-0 end-0 translate-middle-y mt-2 me-n2 py-2 px-3 fw-bold rounded-pill" style="transform: rotate(15deg);">
                                                @if ($template->is_free_delivery == 0)
                                                    @if($bundle->free_quantity > 0)
                                                        {{ enToBn($bundle->buy_quantity) }} টি কিনলে {{ enToBn($bundle->free_quantity) }} টি {{  $bundle->freeProduct->name ?? '' }} ফ্রি
                                                    @endif
                                                @else
                                                    ফ্রি ডেলিভারি
                                                @endif
                                                
                                            </span> --}}
                                        </label>
                                    </div>
                                @endforeach
                                                            
                            </div>
                        </div>

                        <div class="row g-4 px-3 px-md-0">
                            
                            <div class="col-12 col-lg-6"> 
                                <h5 class="mt-4 mb-3">অর্ডারের তথ্য</h5>
                                
                                <div class="mb-3">
                                    <label for="name" class="form-label required-label">আপনার নাম লিখুন *</label>
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="country" class="form-label">Bangladesh</label>
                                    <select class="form-select" id="country" name="country" disabled>
                                        <option>Bangladesh</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="address" class="form-label required-label">আপনার পূর্ণ ঠিকানা লিখুন *</label>
                                    <input type="text" class="form-control" name="address" id="address" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="mobile" class="form-label required-label">আপনার মোবাইল নাম্বার *</label>
                                    <input type="tel" class="form-control" name="mobile" id="mobile" required>
                                </div>
                                <h5 class="mt-0 mb-0">Shipping</h5>
                                <div class="mb-3">
                                    <label for="delivery_charge_select" class="form-label required-label">ডেলিভারি এরিয়া সিলেক্ট করুন *</label>
                                    
                                    <select class="form-select delivery-option" name="delivery_charge" id="delivery_charge_select" required>
                                        <option value="" disabled>ডেলিভারি এরিয়া নির্বাচন করুন</option>
                                        @if ($template->is_free_delivery == 0)
                                            <option 
                                                value="60" 
                                                data-charge="60"
                                                selected
                                            >
                                                ঢাকার ভিতরে (চার্জ: 60 টাকা)
                                            </option>
                                            <option 
                                                value="120" 
                                                data-charge="120"
                                            >
                                                ঢাকার বাইরে (চার্জ: 120 টাকা)
                                            </option>
                                        @else
                                            <option 
                                                value="0" 
                                                data-charge="0"
                                            >
                                                ফ্রি ডেলিভারি
                                            </option>
                                        @endif
                                        
                                    </select>
                                </div>
                                {{-- <h5 class="mt-0 mb-0">Shipping</h5>
                                <div class="mb-3">
                                    <label for="shipping" class="form-label">ডেলিভারি চার্জ ফ্রি:</label>
                                    <input type="text" class="form-control" name="shipping" id="shipping" value="ফ্রি" disabled>
                                </div> --}}
                                
                            </div>
                            
                            <div class="col-12 col-lg-6">
                                
                                <h5 class="mb-3">আপনার অর্ডার</h5>
                                <div class="p-3 bg-white border rounded shadow-sm">
                                    
                                    <div class="d-flex justify-content-between border-bottom pb-2">
                                        <span class="fw-bold">Product</span>
                                        <span class="fw-bold">Subtotal</span>
                                    </div>

                                    {{-- <div class="d-flex justify-content-between align-items-center py-2">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset($template->promotion_image) }}" alt="" class="me-2" style="width: 40px;">
                                            <span>{{ $template->product_combo_name }}</span>
                                        </div>
                                        <span>
                                            <span id="productQuantity">1</span> X <span id="productSubtotal"> ৳ 890.00</span>
                                        </span>
                                    </div> --}}
                                    <div class="d-flex justify-content-between align-items-center py-2">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset($template->promotion_image) }}" alt="" class="me-2" style="width: 40px;">
                                            <span id="productNameText">{{ $template->product_combo_name }}</span>
                                        </div>
                                        <div class="input-group" style="width: 120px;">
                                            <button class="btn btn-outline-secondary btn-sm" type="button" id="decrement-qty">-</button>
                                            <input 
                                                type="number" 
                                                class="form-control form-control-sm text-center" 
                                                name="package_quantity" 
                                                id="orderQuantityInput" 
                                                value="1" 
                                                min="1"
                                                required 
                                                readonly
                                                style="padding: .25rem 0;"
                                            >
                                            <button class="btn btn-outline-secondary btn-sm" type="button" id="increment-qty">+</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="sub_total" id="sub_total" />
                                    <input type="hidden" name="total" id="total" />
                                    <div class="d-flex justify-content-between py-2 border-top">
                                        <span>Subtotal</span>
                                        <span id="subtotalAmount">৳ 890.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-top">
                                        <span>ডেলিভারি চার্জ</span>
                                        <span id="shippingAmountDisplay">৳ 60.00</span> 
                                    </div>

                                    <div class="d-flex justify-content-between pt-2 border-top border-dark">
                                        <span class="fw-bold">Total</span>
                                        <span class="fw-bold fs-5 text-success" id="totalAmount">৳ 890.00</span>
                                    </div>
                                </div>

                                <button class="btn btn-danger btn-confirm-order mt-4" id="confirmOrderButton" type="submit" form="checkoutForm" style="border-radius: 0 0 8px 8px;">
                                    কনফার্ম অর্ডার ৳ 890.00
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </section>
    </form>
    
    <footer class="py-4 bg-dark text-white text-center">
        <div class="container">
            <p class="mb-0"><small>About Us | Contact Us | Privacy Policy | Terms & Conditions</small></p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        // 💡 PHP FIX: We use the explicit timezone format (P) to ensure the expiration time is accurate.
        const offerEndDateString = '{{ \Carbon\Carbon::parse($template->end_date)
                                        ->endOfDay()
                                        ->setTimezone(config('app.timezone'))
                                        ->format('Y-m-d\TH:i:sP') }}';

        const offerEndTime = new Date(offerEndDateString).getTime();

        // Get the display elements (Assuming you have elements for Days, e.g., <span id="timer-days">)
        const daysElement = document.getElementById('timer-days'); // NEW ELEMENT
        const hoursElement = document.getElementById('timer-hours');
        const minutesElement = document.getElementById('timer-minutes');
        const secondsElement = document.getElementById('timer-seconds');
        const timerSection = document.getElementById('countdown-timer');

        function updateCountdown() {
            const now = new Date().getTime();
            let distance = offerEndTime - now;

            // Calculations for time components
            if (distance > 0) {
                
                // 1. Calculate Days (Total milliseconds / milliseconds in a day)
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                
                // 2. Calculate remaining Hours (The remainder of the division by a day, divided by milliseconds in an hour)
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                
                // 3. Calculate remaining Minutes
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                
                // 4. Calculate remaining Seconds
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Function to add leading zero
                const pad = (num) => num < 10 ? '0' + num : num;
                
                // Display the results (Ensure these elements exist in your HTML)
                if (daysElement) { // Check if days element exists before setting text
                    daysElement.textContent = pad(days);
                }
                hoursElement.textContent = pad(hours);
                minutesElement.textContent = pad(minutes);
                secondsElement.textContent = pad(seconds);
                
            } else {
                // Timer expired
                clearInterval(timerInterval);
                if (daysElement) daysElement.textContent = '00';
                hoursElement.textContent = '00';
                minutesElement.textContent = '00';
                secondsElement.textContent = '00';
                
                // Handle timer expiration display
                if (timerSection) {
                    timerSection.innerHTML = '<span class="text-danger fw-bold fs-4">অফার শেষ হয়েছে!</span>';
                }
            }
        }

        // Update the countdown every 1 second
        const timerInterval = setInterval(updateCountdown, 1000);

        // Initial call
        updateCountdown(); 
    </script>
    <script>
        $(document).ready(function() {
            const $productCards = $('.product-card');
            const $productSubtotal = $('#productSubtotal'); 
            const $subtotalAmount = $('#subtotalAmount');
            const $totalAmount = $('#totalAmount');
            const $confirmOrderButton = $('#confirmOrderButton');
            const $orderQuantityInput = $('#orderQuantityInput'); 
            const $decrementButton = $('#decrement-qty'); 
            const $incrementButton = $('#increment-qty'); 
            const $shippingAmountDisplay = $('#shippingAmountDisplay');
            const $deliveryChargeSelect = $('#delivery_charge_select');

            let currentPackagePrice = 0; 

            function formatTaka(amount) {
                return '৳ ' + parseFloat(amount).toLocaleString('en-BD', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            
            $incrementButton.on('click', function() {
                let currentQty = parseInt($orderQuantityInput.val());
                $orderQuantityInput.val(currentQty + 1);
                updateOrderSummary();
            });

            $decrementButton.on('click', function() {
                let currentQty = parseInt($orderQuantityInput.val());
                if (currentQty > 1) {
                    $orderQuantityInput.val(currentQty - 1);
                    updateOrderSummary();
                }
            });

            $orderQuantityInput.on('change keyup', function() {
                if (parseInt($(this).val()) < 1) {
                    $(this).val(1);
                }
                updateOrderSummary();
            });

            function handlePackageChange() {
                const $selectedPackage = $('input[name="product_package"]:checked');
                if (!$selectedPackage.length) return; 

                const $card = $selectedPackage.closest('.product-card');
                
                currentPackagePrice = parseFloat($card.data('price'));
                
                $productSubtotal.text(formatTaka(currentPackagePrice));
                $orderQuantityInput.val(1);

                updateOrderSummary();
            }

            function updateOrderSummary() {
                const selectedOption = $('#delivery_charge_select option:selected');
                const selectedDeliveryCharge = parseFloat(selectedOption.data('charge')) || 0;
                
                const quantity = parseInt($orderQuantityInput.val()) || 1;
                
                const subTotalOrderPrice = (currentPackagePrice * quantity);
                const totalOrderPrice = (currentPackagePrice * quantity) + selectedDeliveryCharge;
                
                $shippingAmountDisplay.text(formatTaka(selectedDeliveryCharge));
                
                $subtotalAmount.text(formatTaka(subTotalOrderPrice));
                $totalAmount.text(formatTaka(totalOrderPrice));

                $confirmOrderButton.text(`কনফার্ম অর্ডার ${formatTaka(totalOrderPrice)}`);

                $('#sub_total').val(totalOrderPrice);
                $('#total').val(totalOrderPrice);

            }

            $productCards.find('input[name="product_package"]').on('change', handlePackageChange);
            $deliveryChargeSelect.on('change', updateOrderSummary);

            handlePackageChange();
        });
    </script>
    {{-- <script>
        $(document).ready(function() {
            // Select elements using jQuery selectors
            const $productCards = $('.product-card');
            const $productSubtotal = $('#productSubtotal');
            const $subtotalAmount = $('#subtotalAmount');
            const $totalAmount = $('#totalAmount');
            const $productNameQty = $('#productNameQty');
            const $confirmOrderButton = $('#confirmOrderButton');
            const $orderQuantityInput = $('#orderQuantityInput'); // NEW
            const $decrementButton = $('#decrement-qty'); // NEW
            const $incrementButton = $('#increment-qty'); // NEW

            let currentPackagePrice = 0;

            function formatTaka(amount) {
                return '৳ ' + parseFloat(amount).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function updateOrderSummary() {
                const $selectedPackage = $('input[name="product_package"]:checked');
                
                if (!$selectedPackage.length) return; // Exit if nothing is checked

                // Find the closest parent card element
                const $card = $selectedPackage.closest('.product-card');
                
                // Get data attributes using jQuery's .data() method
                const price = parseFloat($card.data('price'));
                const qty = parseInt($card.data('qty'));
                console.log(price);
                // --- Update Summary Values ---
                
                // Subtotal (same as total since shipping is assumed free)
                $productSubtotal.text(formatTaka(price));
                $subtotalAmount.text(formatTaka(price));
                $totalAmount.text(formatTaka(price));

                $('#sub_total').val(price);
                $('#total').val(price);
                
                // Update Product Name/Quantity text
                let nameText = "Stress Relief OIL";
                if (qty > 1) {
                    nameText = `২টি Stress Relief OIL (x${qty})`;
                } else {
                    nameText = `${nameText} (x${qty})`;
                }
                $productNameQty.text(nameText);

                $confirmOrderButton.text(`কনফার্ম অর্ডার ${formatTaka(price)}`);
            }
            $productCards.find('input[name="product_package"]').on('change', updateOrderSummary);

            // Initial load update
            updateOrderSummary();
        });
    </script> --}}
    <script>
        var imageSwiper = new Swiper(".imageSwiper", {
            slidesPerView: 1, // Default: 1 slide visible
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 3000, 
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            // Show 3 images at once on large screens
            breakpoints: {
                640: { 
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: { 
                    slidesPerView: 3, 
                    spaceBetween: 30,
                },
            }
        });
    </script>
</body>
</html>