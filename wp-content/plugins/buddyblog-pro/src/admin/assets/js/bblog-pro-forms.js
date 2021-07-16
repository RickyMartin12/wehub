(function ($) {
    $(document).ready( function () {
        var $post_fields_meta_box = $('#bbl-post-fields-meta-box'),
            $post_fields_wrapper = $('#bbl-post-fields-wrapper'),
            $post_fields_section = $('#bbl-field-section-post-settings'),
            $tax_fields_section = $('#bbl-field-section-tax-settings'),
            $post_fields_sortable_wrapper = $('#bbl-posts-fields-list-section'),
            $custom_fields_meta_box = $('#bbl-cf-fields-meta-box'),
            $custom_fields_section = $('#bbl-cf-field-section-common'),
            $custom_fields_sortable_wrapper = $('#bbl-admin-form-cf-wrapper'),
            $custom_fields_sortable_section = $('#bbl-cf-fields-list-section'),
            form_id = $post_fields_wrapper.data('form-id'),
            $taxSelectorWrapper = $('#bbl-input-enabled-taxonomies-wrapper');

        makeCoreFieldsSortable();
        makeCustomFieldsSortable();
        enableShortcodeCopy();

        // for taxonomy enable /disable
        $tax_fields_section.on('change', '#bbl-input-enable-taxonomy', function() {

           if( $(this).val() == '0' ) {
                $('.bbl-row-enable-taxonomy').hide();
                $('.bbl-row-tax-settings-tax').removeClass('bbl-tax-visible').addClass('bbl-tax-hidden');
           } else {
               $('.bbl-row-enable-taxonomy').show();
                if( $taxSelectorWrapper.length && $taxSelectorWrapper.find(':checked').val()) {
                    var selectedTaxes = $taxSelectorWrapper.find(':checked').map( function(index, element) {
                        return $(this).val();
                    }).get();
                    for ( var i=0; i< selectedTaxes.length; i++ ) {
                        $('.bbl-row-tax-settings-'+selectedTaxes[i]).removeClass('bbl-tax-hidden').addClass('bbl-tax-visible');
                    }
                }
           }
        });
        $taxSelectorWrapper.on('click', 'input[type="checkbox"]', function () {
            $('.bbl-row-tax-settings-tax').removeClass('bbl-tax-visible').addClass('bbl-tax-hidden');
            var selectedTaxes = $taxSelectorWrapper.find(':checked').map( function(index, element) {
                return $(this).val();
            }).get();
            if(! selectedTaxes || ! selectedTaxes.length ) {
                return;
            }
            for ( var i=0; i< selectedTaxes.length; i++ ) {
                $('.bbl-row-tax-settings-'+selectedTaxes[i]).removeClass('bbl-tax-hidden').addClass('bbl-tax-visible');
            }
        });
        $('#bbl-post-field-type').on('change', function () {
            var current = $(this).val();
            $('.post-field-type-options').hide();
            $('.post-field-type-options-' + current).show();
        });


        // on new field add.
        $post_fields_meta_box.on('click', '#bbl-add-post-field', function () {
            hideFeedback();
            var currentFieldType = $('#bbl-post-field-type').val();
            var opts =  {
                    action: 'bblogpro_form_add_post_field',
                    form_id: form_id,
                    type:currentFieldType,
                    is_required: $('.bbl-col-post-field-is-required-options input:checked').val(),
                    placeholder:$('#bbl-post-field-placeholder').val(),
                    _wpnonce:BuddyBlog_Pro_Admin.nonce
                };
            var opts2 = $('.post-field-type-options-' + currentFieldType + ' :input').serialize();

            opts = $.param(opts) ;
            if (opts2.length) {
                opts = opts + '&' + opts2
            }
            $.post( ajaxurl, opts, function(response) {
                if( ! response.success ) {
                    //show error and return
                    showError($post_fields_wrapper, response.data.message );
                    return false;
                }

                $post_fields_sortable_wrapper.html(response.data.content );
                makeCoreFieldsSortable();
                showSuccess($post_fields_wrapper, response.data.message );
            });

            return false;
        });

        // on new field delete.
        $post_fields_meta_box.on('click', '.bbl-post-field-delete a', function () {
            hideFeedback();
            var field = $(this).parents('tr').data('field-type');
            $.post( ajaxurl, {
                'action': 'bblogpro_form_delete_post_field',
                'field': field,
                'form_id': form_id,
                '_wpnonce': BuddyBlog_Pro_Admin.nonce

            }, function(response) {
                if( !response.success ) {
                    //show error and return
                    showError($post_fields_sortable_wrapper, response.data.message );
                    return false;
                }

                $post_fields_sortable_wrapper.html(response.data.content );
                makeCoreFieldsSortable();
                showSuccess($post_fields_sortable_wrapper, response.data.message );
            });

            return false;
        });

        // on field change, update options.
        $custom_fields_meta_box.on('change', '#bbl-input-cf-field-type', function () {
            toggleCustomFieldOptions();
        });

        // on new field add.
        $custom_fields_meta_box.on('click', '#bbl-input-cf-add-custom-field', function () {
            hideFeedback();
            var data = {
                action: 'bblogpro_form_add_custom_field',
                form_id: form_id,
                type:$('#bbl-input-cf-field-type').val(),
                label:$('#bbl-input-cf-field-label').val(),
                key:$('#bbl-input-cf-field-key').val(),
                is_required:$('.bbl-col-cf-field-is-required input:checked').val(),
                default_value:$('#bbl-input-cf-field-default-value').val(),
                placeholder:$('#bbl-input-cf-field-placeholder').val(),
                _wpnonce: BuddyBlog_Pro_Admin.nonce
            };
            var options = $('select,input,textarea', '.bbl-cf-field-options-visible').serialize();

            $.post(ajaxurl,
                $.param(data) + '&'  + options,
                function (response) {
                    if ( !response.success ) {
                        //show error and return
                        showError($custom_fields_section, response.data.message);
                        return false;
                    }

                    $custom_fields_sortable_section.html(response.data.content);
                    makeCustomFieldsSortable();
                    showSuccess($custom_fields_section, response.data.message);
                });

            return false;
        });

        // on new field add.
        $custom_fields_meta_box.on('click', '.bbl-cf-delete a', function () {
            hideFeedback();
            var form_id = $(this).parents('.bbl-table').data('form-id');
            var field = $(this).parents('tr').data('field-key');
            $.post( ajaxurl, {
                'action': 'bblogpro_form_delete_custom_field',
                'field': field,
                'form_id': form_id,
                '_wpnonce': BuddyBlog_Pro_Admin.nonce

            }, function(response) {
                if( ! response.success ) {
                    //show error and return
                    showError($custom_fields_sortable_section, response.data.message );
                    return false;
                }

                $custom_fields_sortable_section.html(response.data.content );
                makeCoreFieldsSortable();
                showSuccess( $custom_fields_sortable_section, response.data.message )
            });

            return false;
        });

        // On add,

        // on sort.
        function sendSortedCoreFieldsList() {
            hideFeedback();

            $.post( ajaxurl, {
                'action': 'bblogpro_form_sort_post_fields',
                'fields': getSortedCoreFields(),
                'form_id': $post_fields_wrapper.data('form-id'),
                '_wpnonce': BuddyBlog_Pro_Admin.nonce

            }, function(response) {
                if( ! response.success ) {
                    showError($post_fields_sortable_wrapper, response.data.message );
                    return false;
                }

                $post_fields_sortable_wrapper.html(response.data.content );
                makeCoreFieldsSortable();
                showSuccess($post_fields_sortable_wrapper, response.data.message );
            });
        }

        function makeCoreFieldsSortable() {
            var $fields = $('#bbl-sortable-post-fields');

            $fields.sortable({
                update: function (event, ui) {
                    sendSortedCoreFieldsList();
                }
            });
            $fields.disableSelection();
        }


        function makeCustomFieldsSortable() {
            var $fields = $('#bbl-sortable-custom-fields');

            $fields.sortable({
                update: function (event, ui) {
                    sendSortedCustomFieldsList();
                }
            });
            $fields.disableSelection();
        }

        function enableShortcodeCopy() {
            new ClipboardJS('.bbl-shortcode-copy');
            $(document).on('click', '.bbl-shortcode-copy', function(evt){

                evt.preventDefault();
            });
        }

    // on sort.
    function sendSortedCustomFieldsList() {
        hideFeedback();
        $.post( ajaxurl, {
            'action': 'bblogpro_form_sort_custom_fields',
            'fields': getSortedCustomFields(),
            'form_id': $custom_fields_sortable_wrapper.data('form-id'),
            '_wpnonce': BuddyBlog_Pro_Admin.nonce

        }, function(response) {
            if( ! response.success ) {
                //show error and return
                showError($custom_fields_sortable_section, response.data.message );
                return false;
            }

            $custom_fields_sortable_section.html(response.data.content );
            makeCustomFieldsSortable();
            showSuccess( $custom_fields_sortable_section, response.data.message )
            // if we are here, It is a success.
            //add data.
        });
    }


    function getSortedCustomFields() {
        var val = [];
        $('.bbl-cf-selected-field').each(function () {
            val.push($(this).val());
        });

        return val;
    }

    function getSortedCoreFields() {
        var val = [];
        $('.bbl-post-field-selected-field').each(function () {
            val.push($(this).val());
        });

        return val;
    }
    });
    function hideFeedback() {
        $('.bbl-feedback-message').remove();
    }

    function showError( $container, message ) {
        $($container).prepend( '<div class="bbl-feedback-message bbl-error"><p>'+message+'</p></div>')
    }

    function showSuccess( $container, message ) {
        $($container).prepend( '<div class="bbl-feedback-message bbl-success"><p>'+message+'</p></div>')
    }


    function toggleCustomFieldOptions() {
        var current = $('#bbl-input-cf-field-type').val();
        $('.bbl-cf-field-options-extra').removeClass( 'bbl-cf-field-options-visible');// hide all.
        $('.bbl-cf-field-options-'+current).addClass('bbl-cf-field-options-visible');
    }
})(jQuery);
