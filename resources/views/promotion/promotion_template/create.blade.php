@extends('layouts.app')

@section('title', 'Create Promotion Template')
@section('style')
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.css" />
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Promotion Template</h3>
                </div>
                <form enctype="multipart/form-data" action="{{ route('promotion-template.store') }}" class="form-horizontal" method="post">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-2 col-form-label">
                                <h5>Product wise Discount</h5>
                            </div>
                            <div class="col-sm-10">
                                <p class="text-muted small mb-2">প্রতিটি প্রডাক্ট একবারই সিলেক্ট করতে পারবেন। একই প্রডাক্ট একাধিক রোতে দিতে পারবেন না। প্রতিটি প্রডাক্টের জন্য আলাদা ইমেজ (ঐচ্ছিক) দিতে পারবেন।</p>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="28%">Product <span class="text-danger">*</span></th>
                                            <th width="22%">Product Image</th>
                                            <th width="18%">Discount Type <span class="text-danger">*</span></th>
                                            <th width="18%">Discount <span class="text-danger">*</span></th>
                                            <th width="9%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="promotion-detail-rows">
                                        @php
                                            // ওল্ড ভ্যালুগুলো সংগ্রহ করা
                                            $oldProductIds = old('detail_product_id');
                                            $oldDiscountTypes = old('discount_type');
                                            $oldDiscounts = old('discount');
                                            
                                            // কয়টি রো দেখাতে হবে তা নির্ধারণ করা (কমপক্ষে ১টি)
                                            $rowCount = $oldProductIds ? count($oldProductIds) : 1;
                                        @endphp
                                    
                                        @for($i = 0; $i < $rowCount; $i++)
                                            <tr class="promotion-detail-row">
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <select name="detail_product_id[]" class="form-control select2 detail-product" required>
                                                            <option value="">Select Product</option>
                                                            @foreach($products as $p)
                                                                <option value="{{ $p->id }}" 
                                                                    {{ (isset($oldProductIds[$i]) && $oldProductIds[$i] == $p->id) ? 'selected' : '' }}>
                                                                    {{ $p->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <input type="file" name="detail_image[]" class="form-control form-control-sm detail-image" accept="image/*">
                                                        <small class="text-muted">Optional</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <select name="discount_type[]" class="form-control select2 discount-type" required>
                                                            <option value="percentage" {{ (isset($oldDiscountTypes[$i]) && $oldDiscountTypes[$i] == 'percentage') ? 'selected' : '' }}>Percentage (%)</option>
                                                            <option value="fixed" {{ (isset($oldDiscountTypes[$i]) && $oldDiscountTypes[$i] == 'fixed') ? 'selected' : '' }}>Fixed Amount (৳)</option>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group mb-0">
                                                        <input type="number" name="discount[]" class="form-control discount-value" step="0.01" min="0" placeholder="0" 
                                                            value="{{ $oldDiscounts[$i] ?? 0 }}" required>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($i > 0)
                                                        <button type="button" class="btn btn-danger btn-sm remove-promotion-detail-row"><i class="fas fa-times"></i></button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <button type="button" class="btn btn-primary btn-sm" id="add-promotion-detail-row">
                                                    <i class="fas fa-plus"></i> Add Row
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                                @error('detail_product_id')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('promotion_image') ? 'has-error' :'' }}">
                            <label for="promotion_image" class="col-sm-2 col-form-label">Promotion Image <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="file" value="{{ old('promotion_image') }}" name="promotion_image" class="form-control" id="promotion_image">
                                @error('promotion_image')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('main_heading') ? 'has-error' :'' }}">
                            <label for="main_heading" class="col-sm-2 col-form-label">Main Heading <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('main_heading') }}" name="main_heading" class="form-control" id="main_heading" placeholder="Enter the main sales headline">
                                @error('main_heading')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('slug') ? 'has-error' :'' }}">
                            <label for="slug" class="col-sm-2 col-form-label">URL Slug <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('slug') }}" name="slug" class="form-control" id="slug" placeholder="e.g., product-x-exclusive-offer">
                                @error('slug')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Page will be available at /lp/your-slug</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">ভিডিওর ধরন</label>
                            <div class="col-sm-10">
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input video-type" type="radio" name="video_source" id="source_link" value="link" checked>
                                    <label class="form-check-label" for="source_link">YouTube Link</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input video-type" type="radio" name="video_source" id="source_upload" value="upload">
                                    <label class="form-check-label" for="source_upload">Upload Video File</label>
                                </div>
                            </div>
                        </div>
                        
                        <div id="link_field" class="form-group row {{ $errors->has('video_url') ? 'has-error' :'' }}">
                            <label for="video_url" class="col-sm-2 col-form-label">Video Embed URL <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="url" value="{{ old('video_url') }}" name="video_url" class="form-control" id="video_url" placeholder="https://www.youtube.com/embed/VIDEO_ID">
                                @error('video_url')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div id="upload_field" class="form-group row {{ $errors->has('video_file') ? 'has-error' :'' }}" style="display:none;">
                            <label for="video_file" class="col-sm-2 col-form-label">Upload Video <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="file" name="video_file" class="form-control" id="video_file" accept="video/*">
                                @error('video_file')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Max file size: 50MB (mp4, webm recommended)</small>
                            </div>
                        </div>

                        {{-- <div class="form-group row {{ $errors->has('video_url') ? 'has-error' :'' }}">
                            <label for="video_url" class="col-sm-2 col-form-label">Video Embed URL <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="url" value="{{ old('video_url') }}" name="video_url" class="form-control" id="video_url" placeholder="e.g., https://www.youtube.com/embed/VIDEO_ID">
                                @error('video_url')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Use the YouTube embed link for the video section.</small>
                            </div>
                        </div> --}}

                        <div class="form-group row {{ $errors->has('review_upper_area_title') ? 'has-error' :'' }}">
                            <label for="review_upper_area_title" class="col-sm-2 col-form-label">Review Upper Area Title</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('review_upper_area_title','আলহামদুলিল্লাহ অনেকেই ব্যবহার করে উপকৃত হয়েছেন') }}" name="review_upper_area_title" class="form-control" id="review_upper_area_title">
                                @error('review_upper_area_title')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div id="review-images-container">
                            <div class="form-group row review-image-item">
                                <label for="review_images" class="col-sm-2 col-form-label">Review Images(500x500px)</label>
                                <div class="col-sm-10">
                                    <input type="file" name="review_images[]" class="form-control" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" class="btn btn-info btn-sm" id="add-review-image">
                                    <i class="fas fa-plus"></i> Add More Review Image
                                </button>
                            </div>
                        </div>

                        <div id="review-videos-container">
                            <div class="form-group row review-video-item">
                                <label for="review_videos" class="col-sm-2 col-form-label">Review Video URL</label>
                                <div class="col-sm-10">
                                    <input type="url" name="review_videos[]" class="form-control" placeholder="e.g., https://www.youtube.com/embed/VIDEO_ID">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" class="btn btn-info btn-sm" id="add-review-video">
                                    <i class="fas fa-plus"></i> Add More Review Video
                                </button>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('offer_area_title') ? 'has-error' :'' }}">
                            <label for="offer_area_title" class="col-sm-2 col-form-label">Offer Area Title</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('offer_area_title','অফার অফার অফার !') }}" name="offer_area_title" class="form-control" id="offer_area_title" placeholder="Offer Area Title">
                                @error('offer_area_title')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('offer_area_sub_title') ? 'has-error' :'' }}">
                            <label for="offer_area_sub_title" class="col-sm-2 col-form-label">Offer Area SubTitle</label>
                            <div class="col-sm-10">
                                <input type="text" value="{{ old('offer_area_sub_title','আর নিতে পারবে 3 জন') }}" name="offer_area_sub_title" class="form-control" id="offer_area_sub_title" placeholder="Offer Area SubTitle">
                                @error('offer_area_sub_title')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('is_faq_show') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is FAQ Show</label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline me-3">
                                    <input checked type="radio" id="is_faq_show" name="is_faq_show" value="1" {{ old('is_faq_show') == '1' ? 'checked' : '' }}>
                                    <label for="is_faq_show">Yes</label>
                                </div>
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="is_faq_hide" name="is_faq_show" value="0" {{ old('is_faq_show') == '0' ? 'checked' : '' }}>
                                    <label for="is_faq_hide">No</label>
                                </div>
                                @error('is_faq_show')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                <h5>FAQs (Optional)</h5>
                            </label>
                            <div class="col-sm-10">
                                <div id="faq-container">
                                    @php
                                        // ১. প্রথমে চেক করো ওল্ড ভ্যালু আছে কিনা, না থাকলে ডাটাবেস থেকে ডিকোড করো
                                        $oldQuestions = old('faq_questions');
                                        $oldAnswers = old('faq_answers');
                                        
                                        // ওল্ড ভ্যালু থাকলে সেটাকে লুপ করার জন্য সাজানো
                                        $displayFaqs = [];
                                        if ($oldQuestions) {
                                            foreach ($oldQuestions as $key => $q) {
                                                $displayFaqs[] = ['question' => $q, 'answer' => $oldAnswers[$key] ?? ''];
                                            }
                                        }
                                    @endphp
                        
                                    {{-- যদি কোনো ডাটা না থাকে (নতুন ফর্মের মতো), তবে অন্তত একটি খালি রো দেখাবে --}}
                                    @if(empty($displayFaqs))
                                        <div class="card card-outline card-info faq-item mb-3">
                                            <div class="card-header">
                                                <h3 class="card-title">FAQ Item</h3>
                                                <div class="card-tools">
                                                    <button type="button" class="btn btn-tool remove-faq text-danger" style="display:none;">
                                                        <i class="fas fa-times"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <input type="text" name="faq_questions[]" class="form-control mb-2" placeholder="Question">
                                                    <textarea name="faq_answers[]" class="form-control" rows="2" placeholder="Answer"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        @foreach($displayFaqs as $index => $faq)
                                            <div class="card card-outline card-info faq-item mb-3">
                                                <div class="card-header">
                                                    <h3 class="card-title">FAQ Item</h3>
                                                    <div class="card-tools">
                                                        <button type="button" class="btn btn-tool remove-faq text-danger" 
                                                            style="{{ $loop->first && count($displayFaqs) == 1 ? 'display:none;' : '' }}">
                                                            <i class="fas fa-times"></i> Remove
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="form-group">
                                                        <input type="text" name="faq_questions[]" class="form-control mb-2" 
                                                            placeholder="Question" value="{{ $faq['question'] }}">
                                                        <textarea name="faq_answers[]" class="form-control" rows="2" 
                                                            placeholder="Answer">{{ $faq['answer'] }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <button type="button" class="btn btn-info btn-sm" id="add-faq">
                                    <i class="fas fa-plus"></i> Add More FAQ
                                </button>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="end_date" class="col-sm-2 col-form-label">End Date <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="end_date" placeholder="dd-mm-yyyy" value="{{ old('end_date',date('d-m-Y')) }}" class="form-control date-picker" id="end_date" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">Decription </label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="description" id="editor">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('is_free_delivery') ? 'has-error' :'' }}">
                            <label class="col-sm-2 col-form-label">Is Free Delivery</label>
                            <div class="col-sm-10">
                                <div class="icheck-success d-inline me-3">
                                    <input checked type="radio" id="is_free_delivery" name="is_free_delivery" value="1" {{ old('is_free_delivery') == '1' ? 'checked' : '' }}>
                                    <label for="is_free_delivery">Yes</label>
                                </div>
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inis_free_delivery" name="is_free_delivery" value="0" {{ old('is_free_delivery') == '0' ? 'checked' : '' }}>
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
                                    <input checked type="radio" id="active" name="status" value="1" {{ old('status') == '1' ? 'checked' : '' }}>
                                    <label for="active">Active</label>
                                </div>
                                <div class="icheck-danger d-inline">
                                    <input type="radio" id="inactive" name="status" value="0" {{ old('status') == '0' ? 'checked' : '' }}>
                                    <label for="inactive">Inactive</label>
                                </div>
                                @error('status')
                                <span class="help-block text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary bg-gradient-primary btn-sm">Save Page</button>
                        <a href="{{ route('promotion-template.index') }}" class="btn btn-danger bg-gradient-danger btn-sm float-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <template id="promotion-detail-row-template">
        <tr class="promotion-detail-row">
            <td>
                <div class="form-group mb-0">
                    <select name="detail_product_id[]" class="form-control select2 detail-product" required>
                        <option value="">Select Product</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group mb-0">
                    <input type="file" name="detail_image[]" class="form-control form-control-sm detail-image" accept="image/*">
                    <small class="text-muted">Optional</small>
                </div>
            </td>
            <td>
                <div class="form-group mb-0">
                    <select name="discount_type[]" class="form-control select2 discount-type" required>
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (৳)</option>
                    </select>
                </div>
            </td>
            <td>
                <div class="form-group mb-0">
                    <input type="number" name="discount[]" class="form-control discount-value" step="0.01" min="0" placeholder="0" value="0" required>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-promotion-detail-row"><i class="fas fa-times"></i></button>
            </td>
        </tr>
    </template>

    <template id="faq-template">
        <div class="card card-outline card-info faq-item mb-3">
            <div class="card-header">
                <h3 class="card-title">FAQ Item</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool remove-faq text-danger">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <input type="text" name="faq_questions[]" class="form-control mb-2" placeholder="Question">
                    <textarea name="faq_answers[]" class="form-control" rows="2" placeholder="Answer"></textarea>
                </div>
            </div>
        </div>
    </template>
@endsection
@section('script')

<script src="https://cdn.ckeditor.com/ckeditor5/43.0.0/ckeditor5.umd.js"></script>
<script>
    const {
        ClassicEditor,
        Essentials,
        Bold,
        Italic,
        Underline,      // Free
        Strikethrough,  // Free
        Code,           // Free
        Subscript,      // Free
        Superscript,    // Free
        Paragraph,
        Heading,        // Free (H1, H2, H3)
        List,           // Bulleted & Numbered
        Link,
        BlockQuote,     // Block Quote
        Table,
        TableToolbar,
        TableProperties,      // Free (Table color, border)
        TableCellProperties,  // Free (Cell color, padding)
        Alignment,      // Free (Left, Right, Center, Justify)
        Image,
        ImageToolbar,
        ImageCaption,
        ImageStyle,
        ImageUpload,
        ImageResize,
        SimpleUploadAdapter,
        MediaEmbed,     // Free (Youtube, Vimeo link embed)
        Indent,         // Free
        IndentBlock,    // Free
        Font            // Free (Font size, color, background)
    } = CKEDITOR;

    ClassicEditor
        .create(document.querySelector('#editor'), {
            plugins: [
                Essentials, Paragraph, Heading, Bold, Italic, Underline, Strikethrough,
                Code, Subscript, Superscript, List, Link, BlockQuote, 
                Table, TableToolbar, TableProperties, TableCellProperties,
                Alignment, Image, ImageToolbar, ImageCaption, ImageStyle, 
                ImageUpload, ImageResize, SimpleUploadAdapter, MediaEmbed,
                Indent, IndentBlock, Font
            ],
            toolbar: [
                'undo', 'redo', '|', 
                'heading', '|', 
                'bold', 'italic', 'underline', 'strikethrough', 'code', '|',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                'alignment', '|',
                'bulletedList', 'numberedList', 'outdent', 'indent', '|',
                'link', 'uploadImage', 'insertTable', 'blockQuote', 'mediaEmbed'
            ],
            table: {
                contentToolbar: [
                    'tableColumn', 'tableRow', 'mergeTableCells', 
                    'tableProperties', 'tableCellProperties' // Advanced Free Features
                ]
            },
            image: {
                toolbar: [
                    'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', 
                    '|', 'toggleImageCaption', 'imageTextAlternative'
                ],
                resizeUnit: '%'
            },
            simpleUpload: {
                uploadUrl: "{{ route('admin.ckeditor.upload', ['_token' => csrf_token() ]) }}",
            }
        })
        .then(editor => {
            console.log('All free features initialized');
        })
        .catch(error => {
            console.error(error);
        });
</script>
<script>
    $(document).ready(function() {
        $('.video-type').on('change', function() {
            if ($(this).val() === 'link') {
                $('#link_field').show();
                $('#upload_field').hide();
                $('#video_file').val(''); // ফাইল ক্লিয়ার করা
            } else {
                $('#link_field').hide();
                $('#upload_field').show();
                $('#video_url').val(''); // লিঙ্ক ক্লিয়ার করা
            }
        });

        // Get all product options HTML for re-use
        var productOptionsHtml = $('#promotion-detail-rows .detail-product').first().find('option').clone();

        function updateProductDropdowns() {
            $('#promotion-detail-rows .detail-product').each(function() {
                var $sel = $(this);
                var currentVal = $sel.val();
                var selectedInOthers = [];
                $('#promotion-detail-rows .detail-product').not($sel).each(function() {
                    var v = $(this).val();
                    if (v) selectedInOthers.push(v);
                });
                $sel.find('option').each(function() {
                    var optVal = $(this).attr('value');
                    if (!optVal) { $(this).prop('disabled', false); return; }
                    $(this).prop('disabled', selectedInOthers.indexOf(optVal) !== -1);
                });
            });
        }

        $('#add-promotion-detail-row').on('click', function() {
            var template = $('#promotion-detail-row-template').html();
            $('#promotion-detail-rows').append(template);
            $('#promotion-detail-rows .select2').select2();
            updateProductDropdowns();
        });
        $('body').on('click', '.remove-promotion-detail-row', function() {
            $(this).closest('tr').remove();
            updateProductDropdowns();
        });
        $('body').on('change', '.detail-product', function() {
            updateProductDropdowns();
        });


        const $reviewImageContainer = $('#review-images-container');
        const $addImageButton = $('#add-review-image');
        const $initialImageItem = $reviewImageContainer.find('.review-image-item').first().clone();
        $initialImageItem.find('.col-sm-10').removeClass('col-sm-10').addClass('col-sm-8').after(`
            <div class="col-sm-2 text-right">
                <button type="button" class="btn btn-danger btn-sm remove-field">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
        `);
        $addImageButton.on('click', function() {
            const $newItem = $initialImageItem.clone();
            $newItem.find('input[type="file"]').val('');
            $reviewImageContainer.append($newItem);
        });

        const $reviewVideoContainer = $('#review-videos-container');
        const $addVideoButton = $('#add-review-video');
        const $initialVideoItem = $reviewVideoContainer.find('.review-video-item').first().clone();
        $initialVideoItem.find('.col-sm-10').removeClass('col-sm-10').addClass('col-sm-8').after(`
            <div class="col-sm-2 text-right">
                <button type="button" class="btn btn-danger btn-sm remove-field">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
        `);
        $addVideoButton.on('click', function() {
            const $newItem = $initialVideoItem.clone();
            $newItem.find('input[type="url"]').val('');
            $reviewVideoContainer.append($newItem);
        });

        $('body').on('click', '.remove-field', function() {
            $(this).closest('.form-group.row').remove();
        });

        // Add More FAQ using Template
        $('#add-faq').on('click', function() {
            var template = $('#faq-template').html();
            $('#faq-container').append(template);
        });

        // Remove FAQ Item
        $('body').on('click', '.remove-faq', function() {
            // অন্তত একটি রো যাতে থাকে সেটি নিশ্চিত করতে চাইলে (ঐচ্ছিক)
            if ($('.faq-item').length > 1) {
                $(this).closest('.faq-item').remove();
            } else {
                alert('At least one FAQ item is required.');
            }
        });

    });
</script>
@endsection
