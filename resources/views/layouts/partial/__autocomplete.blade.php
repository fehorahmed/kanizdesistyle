<script src="{{ asset('themes/backend/typeahead/bootstrap3-typeahead.min.js') }}"></script>
<script type="text/javascript">
    function barcodeAutoComplete(selector,path,inventory_type = null){
        $('#'+selector).typeahead({
            minLength: 2, // Minimum length to trigger the search
            source: function(query, process) {
                return $.get(path, { query: query,inventory_type: inventory_type }, function(data) {
                    // Convert numbers to strings
                    const codes = data.map(String); // Convert each number to a string
                    return process(codes); // Pass the array of strings to Typeahead
                });
            }
        });
    }
</script>
