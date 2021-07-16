jQuery(document).ready(function($) {
	'use strict';
	/**
	 * Support Tab JS
	 */
	var acc = document.getElementsByClassName("bplock-accordion");
	var i;
	for (i = 0; i < acc.length; i++) {
		acc[i].onclick = function() {
			this.classList.toggle("active");
			var panel = this.nextElementSibling;
			if (panel.style.maxHeight) {
				panel.style.maxHeight = null;
			} else {
				panel.style.maxHeight = panel.scrollHeight + "px";
			}
		}
	}
	$(document).on('click', '.bplock-accordion', function() {
		return false;
	});

	$('#bplock-cpt-list').selectize({
		placeholder: "Select custom post types",
		plugins: ['remove_button']
	});

	var wp_pages = $('#bplock-pages-list').selectize({
		placeholder: "Select pages",
		plugins: ['remove_button']
	});
	if (wp_pages[0]) {
		var wp_pages_selectize = wp_pages[0].selectize;
	}

	$('#bplock-bp-components-list').selectize({
		placeholder: "Select buddypress components",
		plugins: ['remove_button']
	});

	$('.lr-form').click(function() {
		var option = $(this).data('id');

		if (option == 'custom_form') {
			$('.custom_form').show();
			$('#wp-bplock-locked-content-wrap').closest("tr").show();
			$('#logout_redirect_page').hide();
		} else if (option == 'page_redirect') {
			$('#logout_redirect_page').show();
			$('.custom_form').hide();
			$('#wp-bplock-locked-content-wrap').closest("tr").hide();
		} else {
			$('#wp-bplock-locked-content-wrap').closest("tr").show();
			$('.custom_form').hide();
			$('#logout_redirect_page').hide();
		}
	});

	$(document).on('click', '#bplock-select-all', function() {
		var pt_names = [], i;
		var pt_options = wp_pages_selectize.options;
		for (i in pt_options) {
			pt_names.push(pt_options[i]['value']);
		}
		wp_pages_selectize.setValue(pt_names);
	});
	$(document).on('click', '#bplock-unselect-all', function() {
		wp_pages_selectize.setValue([]);
	});
});
