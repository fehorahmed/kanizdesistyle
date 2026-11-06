<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $template->main_heading }}</title>
    @include('layouts.partial.__favicon')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <style>
        body { font-family: "Hind Siliguri", sans-serif; }
        /* আপনার আগের ডিজাইন স্টাইল */
        .bg-custom-dark-green { background-color: #c6277a; }
        .product-card { cursor: pointer; transition: 0.3s; border: 2px solid #eee; position: relative; border-radius: 12px; }
        .product-card:hover { border-color: #28a745; }
        
        /* সিলেক্টেড কার্ড স্টাইল */
        .product-card.selected { border-color: #28a745; background-color: #f8fff8; }
        
        /* চেকবক্স স্টাইল (আগের মতই ছোট ও ডান পাশে) */
        .product-checkbox { width: 20px; height: 20px; cursor: pointer; }
        
        /* কোয়ান্টিটি বাটন স্টাইল */
        .qty-control { display: flex; align-items: center; background: #f0f0f0; border-radius: 20px; padding: 2px 8px; width: fit-content; }
        .qty-btn { border: none; background: none; font-weight: bold; padding: 0 8px; cursor: pointer; }
        .qty-input { width: 35px; border: none; background: transparent; text-align: center; font-weight: bold; pointer-events: none; }
        
        .summary-item-img { width: 45px; height: 45px; object-fit: cover; border-radius: 5px; margin-right: 10px; border: 1px solid #ddd; }
        .faq-section { background-color: #f9f9f9; padding: 40px 0; }
        .swiper-container {
            width: 100%;
            max-width: 100%;
            overflow: hidden; /* এটি স্ক্রলবার আসা বন্ধ করবে */
            padding: 10px 5px; /* স্লাইডারের শ্যাডোর জন্য সামান্য প্যাডিং */
        }

        .swiper-wrapper {
            box-sizing: border-box;
        }
    </style>
</head>
<body>

    <header class="text-white text-center py-2 bg-white shadow-sm">
        <div class="container-fluid">
            <img src="{{ asset('img/logo.jpg') }}" height="60px" alt="Logo" />
        </div>
    </header>

    <section class="py-4 bg-custom-dark-green">
        <div class="container text-center text-white">
            <h2 class="mb-4">{{ $template->main_heading }}</h2>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="ratio ratio-16x9 shadow-lg mb-4" style="border: 5px solid #fff; border-radius: 20px; overflow: hidden;">
                        <iframe src="{{ $template->video_url }}?rel=0" allowfullscreen></iframe>
                    </div>
                    <a href="#order-section" class="btn btn-lg btn-danger fw-bold px-5 py-3 rounded-[10px] shadow">অর্ডার করতে নিচে যান</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            
            <div class="text-center bg-custom-header-green mx-auto mb-5" style="max-width: 800px;">
                <h3 class="mb-0 p-2">পণ্যের বর্ণনা</h3>
            </div>
            <div class="description">
                {!! $template->description !!}
            </div>
            <div class="text-center mt-5">
                <a href="#order-section" class="btn btn-lg btn-danger text-white fw-bold px-5 py-2" style="border-radius: 8px;">অর্ডার করুন</a>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light" style="background-color: #161446 !important;">
        <div class="container">
            
            <div class="text-center bg-custom-header-green mx-auto mb-5" style="max-width: 800px;">
                <h3 class="mb-0 p-2 text-white">{{ $template->review_upper_area_title }}</h3>
            </div>

            {{-- <h4 class="text-center mb-4 text-secondary">ক্রেতাদের ছবি রিভিউসমূহ</h4> --}}
            @if (count($template->review_images) > 0)
                <div class="row">
                    <div class="col-12"> <div class="swiper-container imageSwiper mb-5">
                            <div class="swiper-wrapper">
                                @foreach ($template->review_images as $image)
                                    <div class="swiper-slide">
                                        <img src="{{ asset($image) }}" class="img-fluid rounded shadow" alt="Customer Testimonial">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-3">
            @endif
            
            @if (count($template->review_videos) > 0)
                <h4 class="text-center mb-4 text-secondary text-white">ভিডিও রিভিউসমূহ</h4>
                
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
            @endif
            <div class="text-center mt-5">
                <a href="#order-section" class="btn btn-lg btn-danger text-white fw-bold px-5 py-2" style="border-radius: 8px;">অর্ডার করুন</a>
            </div>
        </div>
    </section>

    @if($template->is_faq_show && !empty($template->faqs))
    <section class="faq-section">
        <div class="container">
            <h3 class="text-center fw-bold mb-4">সাধারণ জিজ্ঞাসা (FAQ)</h3>
            <div class="accordion shadow-sm" id="faqAccordion">
                @php $faqs = is_string($template->faqs) ? json_decode($template->faqs, true) : $template->faqs; @endphp
                @foreach($faqs as $index => $faq)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                            {{ $faq['question'] }}
                        </button>
                    </h2>
                    <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">{{ $faq['answer'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <form id="checkoutForm" action="{{ route('promotion-template.order.submit', $template->id) }}" method="POST">
        @csrf
        <section id="order-section" class="py-5 bg-light">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
                            <h4 class="fw-bold mb-4 border-bottom pb-2 text-success"><i class="fas fa-shopping-basket me-2"></i>প্রোডাক্ট সিলেক্ট করুন</h4>
                            <div class="row g-3">
                                @foreach ($template->details as $index => $detail)
                                {{-- {{ dd($detail) }} --}}
                                
                                    @php
                                        $product = \App\Models\Product::find($detail->product_id);
                                        $regPrice = $product ? $product->selling_price : 0;
                                        $finalPrice = ($detail->discount_type == 'percentage') ? ($regPrice - ($regPrice * $detail->discount / 100)) : ($regPrice - $detail->discount);
                                        $itemImg = $detail->image ? asset($detail->image) : asset($template->promotion_image);
                                    @endphp
                                    
                                    <div class="col-12">
                                        <div class="product-card p-3 d-flex align-items-center justify-content-between {{ $index == 0 ? 'selected' : '' }}">
                                            <div class="d-flex align-items-center flex-grow-1">
                                                <input type="checkbox" name="product_ids[]" 
                                                    class="form-check-input product-checkbox me-3 ms-1" 
                                                    value="{{ $detail->product_id }}" 
                                                    data-price="{{ $finalPrice }}" 
                                                    data-name="{{ $detail->product->name ?? '' }}"
                                                    data-image="{{ $itemImg }}"
                                                    {{ $index == 0 ? 'checked' : '' }}>
                                                
                                                <img src="{{ $itemImg }}" width="60" class="rounded me-3 border">
                                                <div>
                                                    <h6 class="mb-0 fw-bold">{{ $detail->product->name ?? '' }}</h6>
                                                    <span class="text-success fw-bold">৳ {{ number_format($finalPrice, 2) }}</span>
                                                    @if($detail->discount > 0)
                                                        <del class="text-muted small ms-2">৳ {{ number_format($regPrice, 2) }}</del>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="qty-control">
                                                <button type="button" class="qty-btn btn-minus"><i class="fas fa-minus"></i></button>
                                                <input type="number" name="qty[{{ $detail->product_id }}]" class="qty-input" value="1" readonly>
                                                <button type="button" class="qty-btn btn-plus"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-white p-4 rounded-4 shadow-sm">
                            <h4 class="fw-bold mb-4 border-bottom pb-2 text-success"><i class="fas fa-user-edit me-2"></i>আপনার তথ্য দিন</h4>
                            <div class="mb-3">
                                <label class="form-label fw-bold">আপনার নাম *</label>
                                <input type="text" name="name" class="form-control form-control-lg" placeholder="নাম লিখুন" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">মোবাইল নাম্বার *</label>
                                <input type="tel" name="mobile" class="form-control form-control-lg" placeholder="017XXXXXXXX" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">পূর্ণ ঠিকানা *</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="জেলা, থানা, গ্রাম/রাস্তা" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">ডেলিভারি এরিয়া *</label>
                                <select name="delivery_charge" id="delivery_charge_select" class="form-select form-select-lg" required>
                                    @if($template->is_free_delivery)
                                        <option value="0" data-charge="0">সারাদেশে ডেলিভারি চার্জ ফ্রি (৳ ০)</option>
                                    @else
                                        <option value="60" data-charge="60">ঢাকার ভিতরে (৳ ৬০)</option>
                                        <option value="120" data-charge="120">ঢাকার বাইরে (৳ ১২০)</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="card border-0 shadow sticky-top" style="top: 20px; border-radius: 15px;">
                            <div class="card-header bg-dark text-white py-3" style="border-radius: 15px 15px 0 0;">
                                <h5 class="mb-0 text-center fw-bold">অর্ডার সামারি</h5>
                            </div>
                            <div class="card-body p-4">
                                <div id="summary-items" class="mb-3">
                                    </div>

                                <div class="d-flex justify-content-between mb-2 pt-3 border-top">
                                    <span>সাবটোটাল</span>
                                    <span class="fw-bold" id="subtotalDisplay">৳ ০</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>ডেলিভারি চার্জ</span>
                                    <span class="fw-bold" id="shippingDisplay">৳ ০</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fs-4 fw-bold text-success mb-4">
                                    <span>সর্বমোট</span>
                                    <span id="totalDisplay">৳ ০</span>
                                </div>
                                
                                <input type="hidden" name="sub_total" id="hiddenSub">
                                <input type="hidden" name="total" id="hiddenTotal">

                                <button type="submit" class="btn btn-danger w-100 py-3 fw-bold fs-5 shadow rounded-3" id="confirmBtn">
                                    অর্ডার কনফার্ম করুন
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            function updateSummary() {
                let subtotal = 0;
                let summaryHtml = '';
                let count = 0;

                $('.product-checkbox:checked').each(function() {
                    count++;
                    let $card = $(this).closest('.product-card');
                    $card.addClass('selected');

                    let price = parseFloat($(this).data('price'));
                    let name = $(this).data('name');
                    let img = $(this).data('image');
                    let qty = parseInt($card.find('.qty-input').val());
                    
                    let lineTotal = price * qty;
                    subtotal += lineTotal;
                    subtotal = Math.ceil(subtotal);
                    // সামারিতে আইটেম অ্যাড করা
                    summaryHtml += `
                        <div class="d-flex align-items-center justify-content-between mb-2 border-bottom pb-2">
                            <div class="d-flex align-items-center">
                                <img src="${img}" class="summary-item-img">
                                <div>
                                    <p class="mb-0 fw-bold small text-dark">${name}</p>
                                    <small class="text-muted">৳ ${price.toLocaleString()} x ${qty}</small>
                                </div>
                            </div>
                            <span class="fw-bold small">৳ ${lineTotal.toLocaleString()}</span>
                        </div>
                    `;
                });

                // আনসিলেক্টেড কার্ড থেকে ক্লাস রিমুভ
                $('.product-checkbox:not(:checked)').closest('.product-card').removeClass('selected');

                let shipping = parseFloat($('#delivery_charge_select option:selected').data('charge')) || 0;
                let total = subtotal + shipping;

                $('#summary-items').html(count > 0 ? summaryHtml : '<p class="text-center text-muted small">কোনো প্রোডাক্ট সিলেক্ট করা নেই</p>');
                $('#subtotalDisplay').text('৳ ' + subtotal.toLocaleString());
                $('#shippingDisplay').text('৳ ' + shipping.toLocaleString());
                $('#totalDisplay').text('৳ ' + total.toLocaleString());
                $('#confirmBtn').text('অর্ডার কনফার্ম করুন (৳ ' + total.toLocaleString() + ')');
                
                $('#hiddenSub').val(subtotal);
                $('#hiddenTotal').val(total);
                $('#confirmBtn').prop('disabled', count === 0);
            }

            // ইভেন্ট লিসেনারস
            $('.product-checkbox, #delivery_charge_select').on('change', updateSummary);

            $('.btn-plus').click(function() {
                let $input = $(this).siblings('.qty-input');
                $input.val(parseInt($input.val()) + 1);
                updateSummary();
            });

            $('.btn-minus').click(function() {
                let $input = $(this).siblings('.qty-input');
                let val = parseInt($input.val());
                if (val > 1) {
                    $input.val(val - 1);
                    updateSummary();
                }
            });

            // শুরুতে ক্যালকুলেশন রান করা
            updateSummary();
        });
    </script>
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