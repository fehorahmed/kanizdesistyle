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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <style>
        body { font-family: "Hind Siliguri", sans-serif; background-color: #fdfdfd; }
        .bg-custom-dark-green { background-color: #214d24; }
        .product-card-multi { border: 2px solid #eee; transition: 0.3s; cursor: pointer; position: relative; }
        .product-card-multi:hover { border-color: #28a745; }
        .product-checkbox { width: 25px; height: 25px; cursor: pointer; z-index: 10; }
        .faq-section { background-color: #f8f9fa; padding: 60px 0; }
        .accordion-button:not(.collapsed) { background-color: #e8f5e9; color: #2e7d32; }
        .sticky-summary { position: sticky; top: 20px; z-index: 100; }
        .check-badge { display: none; position: absolute; top: -10px; left: -10px; background: #28a745; color: white; border-radius: 50%; padding: 5px; width: 30px; height: 30px; text-align: center; }
    </style>
</head>
<body>

    <header class="bg-white shadow-sm py-2 text-center">
        <div class="container">
            <img src="{{ asset('img/logo.jpg') }}" height="60px" alt="Logo">
        </div>
    </header>

    <section class="py-5 bg-custom-dark-green text-white text-center">
        <div class="container">
            <h1 class="fw-bold mb-4">{{ $template->main_heading }}</h1>
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="ratio ratio-16x9 shadow-lg rounded-4 overflow-hidden border border-4 border-white">
                        <iframe src="{{ $template->video_url }}" allowfullscreen></iframe>
                    </div>
                    <div class="mt-4">
                        <a href="#order-section" class="btn btn-danger btn-lg px-5 py-3 fw-bold rounded-pill shadow">অর্ডার করতে নিচে যান</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($template->is_faq_show && !empty($template->faqs))
    <section class="faq-section">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">সচরাচর জিজ্ঞাসিত প্রশ্নাবলী</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        @php $faqs = is_string($template->faqs) ? json_decode($template->faqs, true) : $template->faqs; @endphp
                        @foreach($faqs as $index => $faq)
                        <div class="accordion-item mb-3 border-0 shadow-sm">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                                    {{ $faq['question'] }}
                                </button>
                            </h2>
                            <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body bg-white text-muted">
                                    {{ $faq['answer'] }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <section class="py-5">
        <div class="container text-center">
            <h3 class="mb-5 fw-bold p-3 bg-success text-white rounded d-inline-block">{{ $template->review_upper_area_title }}</h3>
            
            <div class="swiper-container imageSwiper mb-5">
                <div class="swiper-wrapper">
                    @php $reviewImages = is_string($template->review_images) ? json_decode($template->review_images, true) : $template->review_images; @endphp
                    @foreach (($reviewImages ?? []) as $img)
                        <div class="swiper-slide">
                            <img src="{{ asset($img) }}" class="img-fluid rounded-3 shadow-sm" alt="Review">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <form action="{{ route('promotion-template.order.submit', $template->id) }}" method="POST" id="checkoutForm">
        @csrf
        <section id="order-section" class="py-5 bg-light">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
                            <h4 class="fw-bold mb-4 border-bottom pb-2 text-success"><i class="fas fa-shopping-basket me-2"></i>প্রোডাক্ট সিলেক্ট করুন</h4>
                            <div class="row g-3">
                                @foreach ($template->details as $index => $detail)
                                    @php
                                        $inventory = \App\Models\PurchaseInventory::where('product_id', $detail->product_id)->first();
                                        $regPrice = $inventory ? $inventory->selling_price : 0;
                                        $finalPrice = ($detail->discount_type == 'percentage') ? ($regPrice - ($regPrice * $detail->discount / 100)) : ($regPrice - $detail->discount);
                                        $img = $detail->image ? asset($detail->image) : asset($template->promotion_image);
                                    @endphp
                                    <div class="col-12">
                                        <label class="product-card-multi p-3 rounded-3 d-flex align-items-center w-100 mb-0" for="p-{{ $index }}">
                                            <div class="check-badge"><i class="fas fa-check"></i></div>
                                            <input type="checkbox" name="product_ids[]" id="p-{{ $index }}" 
                                                class="form-check-input product-checkbox me-3" 
                                                value="{{ $detail->product_id }}" 
                                                data-price="{{ $finalPrice }}" 
                                                data-name="{{ $detail->product->name ?? '' }}"
                                                {{ $index == 0 ? 'checked' : '' }}>
                                            
                                            <img src="{{ $img }}" width="70" height="70" class="rounded object-fit-cover me-3">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0 fw-bold">{{ $detail->product->name ?? '' }}</h6>
                                                <span class="text-success fw-bold fs-5">৳ {{ number_format($finalPrice, 0) }}</span>
                                                @if($detail->discount > 0)
                                                    <del class="text-muted small ms-2">৳ {{ number_format($regPrice, 0) }}</del>
                                                @endif
                                            </div>
                                        </label>
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
                        <div class="sticky-summary">
                            <div class="card border-0 rounded-4 shadow-lg overflow-hidden">
                                <div class="card-header bg-dark text-white py-3">
                                    <h5 class="mb-0 text-center fw-bold">অর্ডার সামারি</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <p class="text-muted mb-1">সিলেক্ট করা প্রোডাক্টসমূহ:</p>
                                        <div id="selectedProductsList" class="fw-bold text-dark"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3 border-top pt-3">
                                        <span>প্রোডাক্ট মোট</span>
                                        <span class="fw-bold" id="subtotalDisplay">৳ ০</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span>পরিমাণ</span>
                                        <div class="input-group input-group-sm w-50">
                                            <button class="btn btn-outline-dark" type="button" id="qtyMinus">-</button>
                                            <input type="number" name="quantity" id="qtyInput" class="form-control text-center" value="1" readonly>
                                            <button class="btn btn-outline-dark" type="button" id="qtyPlus">+</button>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>ডেলিভারি চার্জ</span>
                                        <span class="fw-bold" id="shippingDisplay">৳ ০</span>
                                    </div>
                                    <div class="d-flex justify-content-between fs-4 fw-bold border-top pt-3 text-success">
                                        <span>মোট টাকা</span>
                                        <span id="totalDisplay">৳ ০</span>
                                    </div>
                                    
                                    <input type="hidden" name="sub_total" id="hiddenSub">
                                    <input type="hidden" name="total" id="hiddenTotal">

                                    <button type="submit" class="btn btn-danger w-100 py-3 mt-4 fw-bold fs-5 rounded-3 shadow" id="confirmBtn">
                                        অর্ডার কনফার্ম করুন
                                    </button>
                                </div>
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
            function updateOrder() {
                let productSubtotal = 0;
                let names = [];

                $('.product-checkbox:checked').each(function() {
                    let price = parseFloat($(this).data('price')) || 0;
                    let name = $(this).data('name');
                    productSubtotal += price;
                    names.push(name);
                    
                    $(this).closest('.product-card-multi').css('border-color', '#28a745').css('background', '#f1f8f1');
                    $(this).closest('.product-card-multi').find('.check-badge').show();
                });

                $('.product-checkbox:not(:checked)').each(function() {
                    $(this).closest('.product-card-multi').css('border-color', '#eee').css('background', 'white');
                    $(this).closest('.product-card-multi').find('.check-badge').hide();
                });

                let qty = parseInt($('#qtyInput').val());
                let shipping = parseFloat($('#delivery_charge_select option:selected').data('charge')) || 0;
                
                let finalSub = productSubtotal * qty;
                let finalTotal = finalSub + shipping;

                // Update UI
                $('#selectedProductsList').html(names.length > 0 ? names.join(' <br> ') : '<span class="text-danger">কোনো প্রোডাক্ট সিলেক্ট করা নেই</span>');
                $('#subtotalDisplay').text('৳ ' + finalSub.toLocaleString());
                $('#shippingDisplay').text('৳ ' + shipping.toLocaleString());
                $('#totalDisplay').text('৳ ' + finalTotal.toLocaleString());
                $('#confirmBtn').text('অর্ডার কনফার্ম করুন (৳ ' + finalTotal.toLocaleString() + ')');
                
                // Set hidden values
                $('#hiddenSub').val(finalSub);
                $('#hiddenTotal').val(finalTotal);

                // Disable button if no product selected
                $('#confirmBtn').prop('disabled', names.length === 0);
            }

            $('.product-checkbox, #delivery_charge_select').on('change', updateOrder);
            
            $('#qtyPlus').click(function() {
                $('#qtyInput').val(parseInt($('#qtyInput').val()) + 1);
                updateOrder();
            });

            $('#qtyMinus').click(function() {
                let q = parseInt($('#qtyInput').val());
                if(q > 1) { $('#qtyInput').val(q - 1); updateOrder(); }
            });

            new Swiper(".imageSwiper", {
                slidesPerView: 1,
                spaceBetween: 15,
                loop: true,
                autoplay: { delay: 3000 },
                breakpoints: { 768: { slidesPerView: 3 } }
            });

            updateOrder(); // Initial run
        });
    </script>
</body>
</html>