<!-- jQuery -->
<script src="{{ asset('themes/backend/plugins/jquery/jquery-3.5.1.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('themes/backend/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('themes/backend/plugins/select2/js/select2.full.min.js') }}"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="{{ asset('themes/backend/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
<!-- InputMask -->
<script src="{{ asset('themes/backend/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
<!-- date-range-picker -->
<script src="{{ asset('themes/backend/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- bootstrap color picker -->
<script src="{{ asset('themes/backend/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
<!-- Tempus dominus Bootstrap 4 -->
<script
    src="{{ asset('themes/backend/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('themes/backend/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Toastr -->
<script src="{{ asset('themes/backend/plugins/toastr/toastr.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('themes/backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script src="{{ asset('themes/backend/plugins/datatables-colreorder/js/dataTables.colReorder.min.js') }}"></script>

<script src="{{ asset('themes/backend/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>

<script src="{{ asset('themes/backend/dist/js/spectrum.js') }}"></script>

<!-- bootstrap datepicker -->
<script src="{{ asset('themes/backend/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('themes/backend/dist/js/sweetalert2@9.js') }}"></script>
<script src="{{ asset('themes/backend/jquery-ui-1.13.2.custom/jquery-ui.js') }}"></script>
<script src="{{ asset('themes/backend/month-picker/MonthPicker.min.js') }}"></script>
<script src="{{ asset('themes/backend/ui-date-picker/cdn.jsdelivr.net_gh_digitalBush_jquery.maskedinput@1.4.1_dist_jquery.maskedinput.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/1.2.0/jquery.magnific-popup.min.js" integrity="sha512-fCRpXk4VumjVNtE0j+OyOqzPxF1eZwacU3kN3SsznRPWHgMTSSo7INc8aY03KQDBWztuMo5KjKzCFXI/a5rVYQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var message = '{{ session('message') }}';
        var success = '{{ session('success') }}';
        var error = '{{ session('error') }}';
        var validateError = '{{ $errors->any() }}';

        if (!window.performance || window.performance.navigation.type != window.performance.navigation.TYPE_BACK_FORWARD) {
            if (message != '' || success != ''){
                if (message != ''){
                    $(document).Toasts('create', {
                        icon: 'fas fa-envelope fa-lg',
                        class: 'bg-success',
                        title: 'Success',
                        autohide: true,
                        delay: 5000,
                        body: message
                    })
                }
                if (success != ''){
                    $(document).Toasts('create', {
                        icon: 'fas fa-envelope fa-lg',
                        class: 'bg-success',
                        title: 'Success',
                        autohide: true,
                        delay: 5000,
                        body: success
                    })
                }
                // Play the notification sound
                let notificationSound = document.getElementById('notification-success-audio');
                if (notificationSound) {
                    notificationSound.play();
                }
            }


            if (error != ''){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error,
                })
                $(document).Toasts('create', {
                    icon: 'fas fa-envelope fa-lg',
                    class: 'bg-danger',
                    title: 'Error',
                    autohide: true,
                    delay: 5000,
                    body: error
                })
                // Play the notification sound
                let notificationSound = document.getElementById('notification-error-audio');
                if (notificationSound) {
                    notificationSound.play();
                }
            }
            if (validateError != ''){

                $(document).Toasts('create', {
                    icon: 'fas fa-envelope fa-lg',
                    class: 'bg-warning',
                    title: 'Error',
                    autohide: true,
                    delay: 5000,
                    body: 'Please fill up validate required fields.'
                })
                // Play the notification sound
                let notificationSound = document.getElementById('notification-error-audio');
                if (notificationSound) {
                    notificationSound.play();
                }
            }

        }

        //Date picker
        $(".date-picker").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
        });
        //Date picker
        $(".date-picker-before-disable").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            minDate: 0
        });



        $(".date-picker-after-disable").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            maxDate: 0  // 0 means today
        });
        //Date picker

        $('.month-picker').MonthPicker({
            Button: false,
        });
        $('.date-picker-fiscal-year').attr('readonly', true);
        $("#financial_year").change(function () {
            let FYear = $(this).val();
            if (FYear !== '') {
                fiscalYearDateRange(FYear)
            } else {
                $('.date-picker-fiscal-year').val(' ')
            }
        })
        $("#financial_year").trigger('change');
        //Initialize Select2 Elements
        $('.select2').select2()
        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        //Bootstrap Duallistbox
        $('.duallistbox').bootstrapDualListbox()
        //Colorpicker
        $('.my-colorpicker1').colorpicker()
        //color picker with addon
        $('.my-colorpicker2').colorpicker()
        $('.my-colorpicker2').on('colorpickerChange', function (event) {
            $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
        })
        $("input[data-bootstrap-switch]").each(function () {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        })


    })
    function fiscalYearDateRange(year) {

        $(".date-picker-fiscal-year").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            minDate: '01-07-' + year,
            maxDate: '30-06-' + (parseFloat(year) + 1)
        });
    }
    function dateInit() {
        $(".date-picker").datepicker({
            dateFormat: 'dd-mm-yy',
        });
    }
    function dateInitSelector(selector) {
        $('.'+selector).datepicker({
            dateFormat: 'dd-mm-yy',
        });
    }
     function select2Init() {
        $(".select2").select2();
    }
    function jsNumberFormat(yourNumber) {
        //Seperates the components of the number
        var n = yourNumber.toString().split(".");
        //Comma-fies the first part
        n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        //Combines the two sections
        return n.join(".");
    }
    function formSubmitConfirm(btnIdName) {
        $('body').on('click', '#' + btnIdName, function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure to save?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save It!'

            }).then((result) => {
                if (result.isConfirmed) {
                    $('form').submit();
                }
            })

        });
    }
    // Global AJAX success handler
    $(document).ajaxComplete(function(event, xhr, settings) {
        // Check if the request was successful (status code 200)
        if (xhr.status === 200) {
            let responseData = JSON.parse(xhr.responseText);
            let success = responseData.success;
            if(success == false){
                let notificationSound = document.getElementById('notification-error-audio');
                if (notificationSound) {
                    notificationSound.play();
                }
            }else if(success == true){
                let notificationSound = document.getElementById('notification-success-audio');
                if (notificationSound) {
                    notificationSound.play();
                }
            }
        }
    });
    // Error handler for AJAX requests
    $(document).ajaxError(function (event, xhr, settings, error) {
        preloaderToggle(false);
    });
    function preloaderToggle(condition){
        if(condition){
            $("#preloader").fadeIn();
            $('body').css('overflow','hidden');
        }else{
            $("#preloader").fadeOut();
            $('body').css('overflow','initial');
        }
    }
    function insidePreloaderToggle(condition){
        if(condition){
            $("#inside-preloader").fadeIn();
            $('body').css('overflow','hidden');
        }else{
            $("#inside-preloader").fadeOut();
            $('body').css('overflow','initial');
        }
    }
    function ajaxSuccessMessage(message){
        $(document).Toasts('create', {
            icon: 'fas fa-envelope fa-lg',
            class: 'bg-success',
            title: 'Success',
            autohide: true,
            delay: 5000,
            body: message
        })
        // Play the notification sound
        let notificationSound = document.getElementById('notification-success-audio');
        if (notificationSound) {
            notificationSound.play();
        }
    }
    function ajaxErrorMessage(message){
        $(document).Toasts('create', {
            icon: 'fas fa-envelope fa-lg',
            class: 'bg-warning',
            title: 'Error',
            autohide: true,
            delay: 5000,
            body: message
        })
        // Play the notification sound
        let notificationSound = document.getElementById('notification-error-audio');
        if (notificationSound) {
            notificationSound.play();
        }
    }
    function ajaxWarningMessage(message){
        $(document).Toasts('create', {
            icon: 'fas fa-envelope fa-lg',
            class: 'bg-warning',
            title: 'Error',
            autohide: true,
            delay: 5000,
            body: message
        })
        // Play the notification sound
        let notificationSound = document.getElementById('notification-error-audio');
        if (notificationSound) {
            notificationSound.play();
        }
    }
    function datatableButtons(){
        return [
            {
                "extend": "copy",
                "text": "<i class='fas fa-copy'></i> Copy",
                "className": "btn btn-primary bg-gradient-primary btn-sm"
            },{
                "extend": "csv",
                "text": "<i class='fas fa-file-csv'></i> Export to CSV",
                "className": "btn btn-primary bg-gradient-primary btn-sm"
            },
            {
                "extend": "excel",
                "text": "<i class='fas fa-file-excel'></i> Export to Excel",
                "className": "btn btn-primary bg-gradient-primary btn-sm"
            },

            {
                "extend": "print",
                "text": "<i class='fas fa-print'></i> Print",
                "className": "btn btn-primary bg-gradient-primary btn-sm"
            },
            {
                "extend": "colvis",
                "text": "<i class='fas fa-eye'></i> Column visibility",
                "className": "btn btn-primary bg-gradient-primary btn-sm"
            }
        ];
    }
</script>
<script>
    $(document).ready(function() {
        // Listen for changes in the dropdown
        $('#dashboard_year').on('change', function() {
            $('#dashboard_year_form').submit();
        });
    });
</script>
