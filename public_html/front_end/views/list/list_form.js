$(function () {
    // District typeahead functionality
    var districtsArray = [];
    
    // Convert districtsData object to array for typeahead
    if (typeof districtsData !== 'undefined') {
        for (var id in districtsData) {
            districtsArray.push({
                id: id,
                name: districtsData[id]
            });
        }
    }
    
    // Initialize typeahead for district search
    if ($('.typeahead-district').length && districtsArray.length > 0) {
        var districtSearch = $('.typeahead-district').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'districts',
            display: 'name',
            source: function(query, syncResults, asyncResults) {
                var results = districtsArray.filter(function(district) {
                    return district.name.toLowerCase().indexOf(query.toLowerCase()) !== -1;
                });
                syncResults(results);
            },
            templates: {
                suggestion: function(data) {
                    return '<div>' + data.name + '</div>';
                }
            }
        });
        
        // Handle selection
        districtSearch.on('typeahead:select', function(ev, suggestion) {
            $('#district_id_field').val(suggestion.id);
        });
        
        // Handle clear/change - reset hidden field if empty
        districtSearch.on('typeahead:change', function(ev) {
            var currentValue = $(this).val();
            var found = false;
            for (var i = 0; i < districtsArray.length; i++) {
                if (districtsArray[i].name === currentValue) {
                    $('#district_id_field').val(districtsArray[i].id);
                    found = true;
                    break;
                }
            }
            if (!found && currentValue === '') {
                $('#district_id_field').val('');
            }
        });
    }

    var cropper = $('#picture').croppie({
        url: $php.image,
        enableExif: false,
        viewport: {
            width: 260,
            height: 260
        },
        boundary: {
            width: 300,
            height: 300
        },
        enableOrientation: true,

    });

    cropper.croppie('bind', {url: $php.image, zoom: 0});

    // Hide upload box if image is already loaded (update mode)
    if ($php.image && $php.image.length > 0) {
        $('#uploadBox').hide();
        $('#picture').show();
    }

    function readFile(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#picture').addClass('ready');
                $('#rotate').removeClass('d-none');
                // Hide upload box and show picture preview
                $('#uploadBox').hide();
                $('#picture').show();
                cropper.croppie('bind', {
                    url: e.target.result,
                    zoom: 0
                });
            };
            reader.readAsDataURL(input.files[0]);
        }
        else {
            alert("Sorry - you're browser doesn't have the required features.");
        }
    }

    $('#upload').on('change', function () {
        readFile(this);
    });

    // Drag and drop functionality for upload box
    var uploadBox = $('#uploadBox');
    var uploadInput = $('#upload')[0];

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
        uploadBox.on(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    // Highlight drop area when dragging over it
    ['dragenter', 'dragover'].forEach(function(eventName) {
        uploadBox.on(eventName, function() {
            uploadBox.addClass('dragover');
        });
    });

    // Remove highlight when dragging leaves or drops
    ['dragleave', 'drop'].forEach(function(eventName) {
        uploadBox.on(eventName, function() {
            uploadBox.removeClass('dragover');
        });
    });

    // Handle dropped files
    uploadBox.on('drop', function(e) {
        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            uploadInput.files = files;
            // Trigger change event
            var event = new Event('change', { bubbles: true });
            uploadInput.dispatchEvent(event);
        }
    });

    // Click on upload box triggers file input (but not when clicking on the label/button)
    uploadBox.on('click', function(e) {
        // Don't trigger if clicking on the label or its children (the actual button)
        if (e.target.closest('label')) {
            return; // Let the label handle its native click behavior
        }
        uploadInput.click();
    });

    $('#rotate').on('click', function () {
        cropper.croppie('rotate', -90);
    });

    cropper.on('update.croppie', function (ev, cropData) {
        cropper.croppie('result', {type: 'base64', size: {width: 800}, format: 'jpeg'}).then(function (data) {
            $("input[name='image_data']").val(data);
        });
    });

    function adjustReservedPosition() {
        $('.sold-out-image').each(function () {
            //gets the height of the h4 right above it
            let h4_height = $(this).parent().prev().height() + 25;
            //sets the top position of the sold out to the bottom of the h4
            $(this).css('top', h4_height + 'px');
        });
    }

    // TODO: Remove from code if nothing break [CS] 17/07/2024
    // $('.btn-delist').click(function (e) {
    //     e.preventDefault();
    //
    //     $.ajax({
    //         url: 'list/process_delist/' + $(this).data('listing_id'),
    //     }).done(function (new_status) {
    //         window.location.href = 'my_freestuff#previous';
    //     });
    // });

    $('.btn-submit-form').click(function (e) {
        e.preventDefault();
        $(this).attr('disabled',true);
        $('#list_form').submit();
    });

    $('.btn-cancel-edit').click(function (e) {
        e.preventDefault();
        window.location.href = $(this).data('return')
    });

    $('#list_form').formTools2({
        successMsg: false,
        onComplete: function (listing_id) {
            $('.btn-submit-form').attr('disabled', false);
        },
        onSuccess: function(listing_id) {
            if ($php.listing_url) {
                document.location = $php.listing_url;
            } else {
                document.location = "list/success/" + listing_id;
            }
        }
    });

    $('#list_form #listing_type input').change(function () {
        var val = $(this).val();
        $('.agree').toggleClass('d-none', true);
        $('#agree-' + val).toggleClass('d-none', false);
    });

});
