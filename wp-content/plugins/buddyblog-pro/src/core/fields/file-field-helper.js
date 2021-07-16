jQuery(function ($) {
    var bbl_file_uploader, $wrapper;
    $(document).on('click', '.bbl-field-label-type-file-upload-btn', function (e) {

        e.preventDefault();

        var $this = $(this);

        $wrapper = $this.parents('.bbl-form-field-type-file-container');

        var oldFieldID = BuddyBlog_Pro.currentMediaFieldID;
        BuddyBlog_Pro.currentMediaFieldID = $wrapper.data('field-key');

        // If the uploader object has already been created, reopen the dialog.
        if (bbl_file_uploader) {

            if( oldFieldID == BuddyBlog_Pro.currentMediaFieldID ) {
                bbl_file_uploader.open();
                return;
            }
            // Note, for Ravi.
            bbl_file_uploader.detach();
        }
        var allowedTypes = $this.data('allowed-types');
        if ( !allowedTypes) {
            allowedTypes = '*';//['*']
        }
        allowedTypes = allowedTypes.split(',');
        // Extend the wp.media object.
        bbl_file_uploader = wp.media.frames.file_frame = wp.media({
            title: $this.data('uploader-title'),
            button: {
                text: $this.data('btn-title')
            },
            library: {
                type: allowedTypes
            },
            allowedTypes: allowedTypes,
            multiple: false
        });

        //When a file is selected, grab the URL and set it as the text field's value
        bbl_file_uploader.on('select', function () {
            var attachment = bbl_file_uploader.state().get('selection').first().toJSON();
            var mime = attachment.mime, matched = false;
            for ( var i =0; i< allowedTypes.length; i++ ) {
                if( mime === allowedTypes[i]) {
                    matched = true;
                    break;
                }
            }
            if( !matched ) {
                return;
            }

            $wrapper.find('input[type="hidden"]').val(attachment.id);
            $wrapper.find('.bbl-field-type-file-preview').html('<a href="' + attachment.url + '">' +attachment.filename +'</a><a href="#" class="bbl-field-type-file-delete-btn">X</a>');
            BuddyBlog_Pro.currentMediaFieldID = null;
        });

        bbl_file_uploader.on('close', function () {
            BuddyBlog_Pro.currentMediaFieldID = null;
        });

        // Open the uploader dialog.
        bbl_file_uploader.open();

        return false;
    });

    $(document).on('click', '.bbl-field-type-file-delete-btn', function () {

        var $this = $(this);

        $wrapper = $this.parents('.bbl-form-field-type-file-container');

        $wrapper.find('input[type="hidden"]').val('');
        $wrapper.find('.bbl-field-type-file-preview').html('');

        return false;
    });
});
