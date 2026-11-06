@extends('layouts.app')

@section('title', 'Edit Promotion Product Template')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Edit Promotion Product Template: {{ $template->slug }}</h3>
                </div>
                <form enctype="multipart/form-data" action="{{ route('product-template.update', ['product_template' => encrypt_value($template->id)]) }}" class="form-horizontal" method="post">
                    @csrf
                    @method('PUT') {{-- IMPORTANT: Use PUT method for update --}}
                    
                    <div class="card-body">
                        
                        <div class="form-group row {{ $errors->has('products') ? 'has-error' :'' }}">
                            <label for="products" class="col-sm-2 col-form-label">Products <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="products[]" multiple="multiple" id="products" style="width: 100%;">
                                    @php
                                        // Get IDs of currently attached products
                                        $attachedProductIds = $template->details->pluck('product_id')->toArray();
                                    @endphp
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" 
                                            {{ in_array($product->id, old('products', $attachedProductIds)) ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('products')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-2 col-form-label">
                                <h5>Bundles</h5>
                            </div>
                            <div class="col-sm-10">
                                <div class="row">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="15%">Buy Quantity</th>
                                                <th width="20%">Offer Type</th>
                                                <th width="15%">Discount (%)</th>
                                                <th>Free Product</th>
                                                <th width="15%">Free Quantity</th>
                                                <th width="5%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="product-offer-rows">
                                            @php
                                                $pricingBundles = old('pricing_bundles', $template->bundles->toArray());
                                            @endphp
                                            
                                            @foreach ($pricingBundles as $index => $bundle)
                                            <tr>
                                                <td class="offer-details-cell">
                                                    <div class="form-group">
                                                        <input type="number" name="buy_quantity[]" value="{{ $bundle['buy_quantity'] ?? '' }}" class="form-control" min="1" placeholder="e.g., 1">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <select name="offer_type[]" class="form-control select2 offer_type" required>
                                                            <option value="">Select</option>
                                                            <option value="buy_x_free_y" {{ $bundle['offer_type'] == 'buy_x_free_y' ? 'selected' : '' }}>Buy X → Free Y</option>
                                                            <option value="buy_x_discount" {{ $bundle['offer_type'] == 'buy_x_discount' ? 'selected' : '' }}>Buy X → Discount</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                
                                                <td>
                                                    <div class="form-group">
                                                        <input type="number" name="discount_percent[]" value="{{ $bundle['discount_percent'] }}" class="form-control" step="0.01" min="0" placeholder="e.g., 10">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <select name="free_product[]" class="form-control select2">
                                                            <option value="">Select Product</option>
                                                            @foreach($products as $p)
                                                                <option value="{{ $p->id }}" {{ (isset($bundle['free_product_id']) && $bundle['free_product_id'] == $p->id) ? 'selected' : '' }}>{{ $p->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                
                                                <td class="offer-details-cell">
                                                    <div class="form-group">
                                                        <input type="number" name="free_quantity[]" value="{{ $bundle['free_quantity'] ?? '' }}" class="form-control" min="0" placeholder="e.g., 1">
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($index != 0)
                                                        <button type="button" class="btn btn-danger btn-sm remove-product-offer-row">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                    
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6">
                                                    <button type="button" class="btn btn-primary btn-sm" id="add-product-offer-row">
                                                        <i class="fas fa-plus"></i> Add Bundle Offer
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('product_combo_name') ? 'has-error' :'' }}">
                            <label for="product_combo_name" class="col-sm-2 col-form-label">Product/Combo Name<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('product_combo_name',$template->product_combo_name) }}" name="product_combo_name" class="form-control" id="product_combo_name" placeholder="e.g., Stress Relief Oil Combo Pack">
                                @error('product_combo_name')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row {{ $errors->has('slug') ? 'has-error' :'' }}">
                            <label for="slug" class="col-sm-2 col-form-label">URL Slug <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('slug', $template->slug) }}" name="slug" class="form-control" id="slug" placeholder="e.g., product-x-exclusive-offer">
                                @error('slug')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Page will be available at /lp/your-slug</small>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('promotion_image') ? 'has-error' :'' }}">
                            <label for="promotion_image" class="col-sm-2 col-form-label">Promotion Image <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                @if ($template->promotion_image)
                                    <div class="mb-2">
                                        <img src="{{ asset($template->promotion_image) }}" alt="Current Promotion Image" style="max-height: 100px;">
                                        <input type="hidden" name="old_promotion_image" value="{{ $template->promotion_image }}">
                                    </div>
                                @endif
                                <input type="file" name="promotion_image" class="form-control" id="promotion_image">
                                <small class="form-text text-muted">Leave blank to keep the existing image.</small>
                                @error('promotion_image')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('main_heading') ? 'has-error' :'' }}">
                            <label for="main_heading" class="col-sm-2 col-form-label">Main Heading <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('main_heading', $template->main_heading) }}" name="main_heading" class="form-control" id="main_heading" placeholder="Enter the main sales headline in Bangla">
                                @error('main_heading')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('video_url') ? 'has-error' :'' }}">
                            <label for="video_url" class="col-sm-2 col-form-label">Video Embed URL <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="url" value="{{ old('video_url', $template->video_url) }}" name="video_url" class="form-control" id="video_url" placeholder="e.g., https://www.youtube.com/embed/VIDEO_ID">
                                @error('video_url')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Use the YouTube embed link for the video section.</small>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('review_upper_area_title') ? 'has-error' :'' }}">
                            <label for="review_upper_area_title" class="col-sm-2 col-form-label">Review Upper Area Title<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('review_upper_area_title',$template->review_upper_area_title) }}" name="review_upper_area_title" class="form-control" id="review_upper_area_title">
                                @error('review_upper_area_title')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('benefits_title') ? 'has-error' :'' }}">
                            <label for="benefits_title" class="col-sm-2 col-form-label">Benefits Area Title<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('benefits_title',$template->benefits_title) }}" name="benefits_title" class="form-control" id="benefits_title" placeholder="Benefits Area Title">
                                @error('benefits_title')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div id="benefit-container">
                            @php
                                $benefits = old('benefits', $template->benefits ?? ['']);
                                if (is_string($benefits)) {
                                    $benefits = json_decode($benefits, true);
                                }
                                if (empty($benefits)) {
                                     $benefits = [''];
                                }
                            @endphp
                            @foreach ($benefits as $index => $benefit)
                                <div class="form-group row benefit-item">
                                    <label for="benefits" class="col-sm-2 col-form-label">Benefits</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="benefits[]" class="form-control" value="{{ old('benefits.' . $index, $benefit) }}" placeholder="e.g., স্মৃতি শক্তি বাড়ে">
                                    </div>
                                    <div class="col-sm-2 text-right">
                                        @if ($loop->iteration > 1)
                                            <button type="button" class="btn btn-danger btn-sm remove-field"><i class="fas fa-times"></i> Remove</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" class="btn btn-info btn-sm" id="add-benefit">
                                    <i class="fas fa-plus"></i> Add More Benefit
                                </button>
                            </div>
                        </div>

                        <div id="review-images-container">
                            @php
                                $reviewImages = old('review_images', $template->review_images ?? ['']);
                                if (is_string($reviewImages)) {
                                    $reviewImages = json_decode($reviewImages, true);
                                }
                                if (empty($reviewImages)) {
                                     $reviewImages = [''];
                                }
                            @endphp
                            @foreach ($reviewImages as $index => $imagePath)
                                <div class="form-group row review-image-item">
                                    <label for="review_images" class="col-sm-2 col-form-label">Review Images(500x500px)</label>
                                    <div class="col-sm-8">
                                        @if ($imagePath)
                                            <div class="mb-2">
                                                <img src="{{ asset($imagePath) }}" alt="Current Review Image" style="max-height: 50px;">
                                                <input type="hidden" name="existing_review_images[]" value="{{ $imagePath }}">
                                            </div>
                                        @endif
                                        <input type="file" name="review_images[]" class="form-control" accept="image/*">
                                        <small class="form-text text-muted">Leave blank to keep existing image or upload new.</small>
                                    </div>
                                    <div class="col-sm-2 text-right">
                                        @if ($loop->iteration > 1 || $imagePath)
                                            <button type="button" class="btn btn-danger btn-sm remove-field"><i class="fas fa-times"></i> Remove</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="row mb-2">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" class="btn btn-info btn-sm" id="add-review-image">
                                    <i class="fas fa-plus"></i> Add More Review Image
                                </button>
                            </div>
                        </div>

                        <div id="review-videos-container">
                            @php
                                $reviewVideos = old('review_videos', $template->review_videos ?? ['']);
                                if (is_string($reviewVideos)) {
                                    $reviewVideos = json_decode($reviewVideos, true);
                                }
                                if (empty($reviewVideos)) {
                                     $reviewVideos = [''];
                                }
                            @endphp
                            @foreach ($reviewVideos as $index => $videoUrl)
                                <div class="form-group row review-video-item">
                                    <label for="review_videos" class="col-sm-2 col-form-label">Review Video URL</label>
                                    <div class="col-sm-8">
                                        <input type="url" name="review_videos[]" class="form-control" value="{{ old('review_videos.' . $index, $videoUrl) }}" placeholder="e.g., https://www.youtube.com/embed/VIDEO_ID">
                                    </div>
                                    <div class="col-sm-2 text-right">
                                        @if ($loop->iteration > 1)
                                            <button type="button" class="btn btn-danger btn-sm remove-field"><i class="fas fa-times"></i> Remove</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" class="btn btn-info btn-sm" id="add-review-video">
                                    <i class="fas fa-plus"></i> Add More Review Video
                                </button>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('offer_area_title') ? 'has-error' :'' }}">
                            <label for="offer_area_title" class="col-sm-2 col-form-label">Offer Area Title<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('offer_area_title',$template->offer_area_title) }}" name="offer_area_title" class="form-control" id="offer_area_title" placeholder="Offer Area Title">
                                @error('offer_area_title')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('offer_area_sub_title') ? 'has-error' :'' }}">
                            <label for="offer_area_sub_title" class="col-sm-2 col-form-label">Offer Area SubTitle<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('offer_area_sub_title',$template->offer_area_sub_title) }}" name="offer_area_sub_title" class="form-control" id="offer_area_sub_title" placeholder="Offer Area SubTitle">
                                @error('offer_area_sub_title')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('is_show_certificate') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is Show Certificate <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline me-3">
                                    <input checked type="radio" id="is_show_certificate" name="is_show_certificate" value="1" {{ old('is_show_certificate',$template->is_show_certificate) == '1' ? 'checked' : '' }}>
                                    <label for="is_show_certificate">Show</label>
                                </div>
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inis_show_certificate" name="is_show_certificate" value="0" {{ old('is_show_certificate',$template->is_show_certificate) == '0' ? 'checked' : '' }}>
                                    <label for="inis_show_certificate">Hide</label>
                                </div>
                                @error('is_show_certificate')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('certificate_area_title') ? 'has-error' :'' }}">
                            <label for="certificate_area_title" class="col-sm-2 col-form-label">Certificate Area Title<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('certificate_area_title',$template->certificate_area_title) }}" name="certificate_area_title" class="form-control" id="certificate_area_title" placeholder="Certificate Area Title">
                                @error('certificate_area_title')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('certificate_area_subtitle') ? 'has-error' :'' }}">
                            <label for="certificate_area_subtitle" class="col-sm-2 col-form-label">Certificate Area SubTitle<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('certificate_area_subtitle',$template->certificate_area_subtitle) }}" name="certificate_area_subtitle" class="form-control" id="certificate_area_subtitle" placeholder="Certificate Area Sub Title">
                                @error('certificate_area_subtitle')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('certificate_image') ? 'has-error' :'' }}">
                            <label for="certificate_image" class="col-sm-2 col-form-label">Certificate Image </label>
                            <div class="col-sm-10">
                                <input type="file" value="{{ old('certificate_image') }}" name="certificate_image" class="form-control" id="certificate_image">
                                @error('certificate_image')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('certificate_area_bottom_title') ? 'has-error' :'' }}">
                            <label for="certificate_area_bottom_title" class="col-sm-2 col-form-label">Certificate Area Bottom Title<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('certificate_area_bottom_title',$template->certificate_area_bottom_title) }}" name="certificate_area_bottom_title" class="form-control" id="certificate_area_bottom_title" placeholder="Certificate Area Bottom Title">
                                @error('certificate_area_bottom_title')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                                                
                        <div class="form-group row">
                            <label for="end_date" class="col-sm-2 col-form-label">End Date <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="end_date" value="{{ old('end_date', date('d-m-Y',strtotime($template->end_date))) }}" placeholder="dd-mm-yyyy" class="form-control date-picker" id="end_date" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('is_free_delivery') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is Free Delivery <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline me-3">
                                    <input checked type="radio" id="is_free_delivery" name="is_free_delivery" value="1" {{ old('is_free_delivery', $template->is_free_delivery) == '1' ? 'checked' : '' }}>
                                    <label for="is_free_delivery">Yes</label>
                                </div>
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inis_free_delivery" name="is_free_delivery" value="0" {{ old('is_free_delivery', $template->is_free_delivery) == '0' ? 'checked' : '' }}>
                                    <label for="inis_free_delivery">No</label>
                                </div>
                                @error('is_free_delivery')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('status') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline me-3">
                                    <input type="radio" id="active" name="status" value="1" {{ old('status', $template->status) == '1' ? 'checked' : '' }}>
                                    <label for="active">Active</label>
                                </div>
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inactive" name="status" value="0" {{ old('status', $template->status) == '0' ? 'checked' : '' }}>
                                    <label for="inactive">Inactive</label>
                                </div>
                                @error('status')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary bg-gradient-primary btn-sm">Update Page</button>
                        <a href="{{ route('product-template.index') }}" class="btn btn-danger bg-gradient-danger btn-sm float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <template id="product-offer-row-template">
        <tr>
            <td class="offer-details-cell">
                <div class="form-group">
                    <input type="number" name="buy_quantity[]" class="form-control" min="1" placeholder="e.g., 1">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select name="offer_type[]" class="form-control select2 offer_type" required>
                        <option value="">Select</option>
                        <option value="buy_x_free_y">Buy X → Free Y</option>
                        <option value="buy_x_discount">Buy X → Discount</option>
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group">
                    <input type="number" name="discount_percent[]" value="0" class="form-control" step="0.01" min="0" placeholder="e.g., 10">
                </div>
            </td>
            <td>
                <div class="form-group">
                    <select name="free_product[]" class="form-control select2">
                        <option value="">Select Product</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            
            <td class="offer-details-cell">
                <div class="form-group">
                    <input type="number" name="free_quantity[]" class="form-control" min="0" placeholder="e.g., 1" value="0">
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-product-offer-row">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection
@section('script')
<script>
    $(document).ready(function() {

        $('#add-product-offer-row').on('click', function() {
            const template = $('#product-offer-row-template').html();
            $('#product-offer-rows').append(template);
            // Re-initialize select2 for the newly added row
            $('#product-offer-rows .select2').select2();
        });
        $('body').on('click', '.remove-product-offer-row', function() {
            $(this).closest('tr').remove();
        });


        // --- 0. Helper function to update bundle index numbers ---
        function updateBundleIndexes() {
            $('#pricing-container .pricing-item').each(function(index) {
                $(this).find('.bundle-index').text(index + 1);
                // Ensure the first item doesn't have a remove button
                if (index === 0) {
                    $(this).find('.remove-field-pricing').remove();
                } else {
                    // Add remove button if it's missing (for pre-loaded items)
                    if ($(this).find('.remove-field-pricing').length === 0) {
                        $(this).find('.col-md-6:last .col-sm-8').append(
                            `<button type="button" class="btn btn-danger btn-sm remove-field-pricing ms-2" style="height: 38px;"><i class="fas fa-times"></i></button>`
                        );
                    }
                }
            });
        }

        // --- 1. Dynamic Pricing Logic (Cloning the template) ---
        const $pricingContainer = $('#pricing-container');
        const $addPricingButton = $('#add-pricing-item');
        
        // Clone the structure of the *first* pricing item to use as a clean template
        const $templatePricingItem = $pricingContainer.find('.pricing-item').first().clone();
        $templatePricingItem.find('input').val(''); // Clear inputs
        // Ensure template includes the remove button structure
        $templatePricingItem.find('.col-md-6:last .col-sm-8').append(
            `<button type="button" class="btn btn-danger btn-sm remove-field-pricing ms-2" style="height: 38px;"><i class="fas fa-times"></i></button>`
        );
        $templatePricingItem.find('.col-12.mb-2').html(`<h5 class="text-primary">Bundle Offer #<span class="bundle-index"></span></h5>`);


        $addPricingButton.on('click', function() {
            const $newItem = $templatePricingItem.clone();
            $pricingContainer.append($newItem);
            updateBundleIndexes();
        });
        
        // --- 2. Benefit Logic ---
        const $benefitContainer = $('#benefit-container');
        const $addBenefitButton = $('#add-benefit');

        const $templateBenefitItem = $benefitContainer.find('.benefit-item').first().clone();
        $templateBenefitItem.find('.col-sm-8').after(`
            <div class="col-sm-2 text-right">
                <button type="button" class="btn btn-danger btn-sm remove-field">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
        `).end().find('input[type="text"]').val('');

        $addBenefitButton.on('click', function() {
            const $newItem = $templateBenefitItem.clone();
            $benefitContainer.append($newItem);
        });
        
        // --- 3. Review Image Logic ---
        const $reviewImageContainer = $('#review-images-container');
        const $addImageButton = $('#add-review-image');
        
        const $templateImageItem = $reviewImageContainer.find('.review-image-item').first().clone();
        $templateImageItem.find('input').val('');
        $templateImageItem.find('.mb-2').remove(); // Remove existing image preview
        $templateImageItem.find('input[name="existing_review_images[]"]').remove(); // Remove hidden field
        $templateImageItem.find('.col-sm-2.text-right').html(`
            <button type="button" class="btn btn-danger btn-sm remove-field">
                <i class="fas fa-times"></i> Remove
            </button>
        `);


        $addImageButton.on('click', function() {
            const $newItem = $templateImageItem.clone();
            $reviewImageContainer.append($newItem);
        });

        // --- 4. Review Video Logic ---
        const $reviewVideoContainer = $('#review-videos-container');
        const $addVideoButton = $('#add-review-video');

        const $templateVideoItem = $reviewVideoContainer.find('.review-video-item').first().clone();
        $templateVideoItem.find('.col-sm-2.text-right').html(`
            <button type="button" class="btn btn-danger btn-sm remove-field">
                <i class="fas fa-times"></i> Remove
            </button>
        `);
        $templateVideoItem.find('input[type="url"]').val('');


        $addVideoButton.on('click', function() {
            const $newItem = $templateVideoItem.clone();
            $reviewVideoContainer.append($newItem);
        });


        // --- 5. Global Remove Listener (Delegated Event) ---
        $('body').on('click', '.remove-field', function() {
            $(this).closest('.form-group.row').remove();
        });

        $('body').on('click', '.remove-field-pricing', function() {
            $(this).closest('.pricing-item').remove();
            updateBundleIndexes(); 
        });
        
        // --- 6. Initial setup ---
        updateBundleIndexes(); 
           
    });
</script>
@endsection