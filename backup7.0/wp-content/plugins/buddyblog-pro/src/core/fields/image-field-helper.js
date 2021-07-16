var bbl_image_uploader, $wrapper;

jQuery(function ($) {
	$(document).on('click', '.bbl-field-label-type-image-upload-btn', function (e) {

		e.preventDefault();

		var $this = $(this);
		$wrapper = $this.parents('.bbl-form-field-type-image-container');

		var oldFieldID = BuddyBlog_Pro.currentMediaFieldID;
		BuddyBlog_Pro.currentMediaFieldID = $wrapper.data('field-key');

		// If the uploader object has already been created, reopen the dialog.
		if (bbl_image_uploader) {

			if( oldFieldID == BuddyBlog_Pro.currentMediaFieldID ) {
				bbl_image_uploader.open();
				return;
			}
			// Note, for Ravi.
			bbl_image_uploader.detach();
		}

		// Extend the wp.media object.
		bbl_image_uploader = wp.media.frames.file_frame = wp.media({
			title: $this.data('uploader-title'),
			button: {
				text: $this.data('btn-title')
			},
			allowedTypes: ['image'],
			multiple: false
		});

		//When a file is selected, grab the URL and set it as the text field's value
		bbl_image_uploader.on('select', function () {
			var attachment = bbl_image_uploader.state().get('selection').first().toJSON();
			if( attachment.type !=='image' ) {
				return;
			}

			// console.log(attachment);
			$wrapper.find('input[type="hidden"]').val(attachment.id);
			$wrapper.find('.bbl-field-type-image-preview').html('<img src="' + attachment.url + '" /><a href="#" class="bbl-field-type-image-delete-btn">X</a>');
			BuddyBlog_Pro.currentMediaFieldID = null;
		});

		bbl_image_uploader.on('close', function () {
			BuddyBlog_Pro.currentMediaFieldID = null;
		});

		// Open the uploader dialog.
		bbl_image_uploader.open();

		return false;
	});

	$(document).on('click', '.bbl-field-type-image-delete-btn', function () {

		var $this = $(this);

		$wrapper = $this.parents('.bbl-form-field-type-image-container');

		$wrapper.find('input[type="hidden"]').val('');
		$wrapper.find('.bbl-field-type-image-preview').html('');

		return false;
	});
});
