/**
 * Digital Ocean Spaces Integration - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Test Connection Button Handler
         */
        $('#test-connection-btn').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var $status = $('#connection-status');

            // Get form field values
            var data = {
                action: 'do_spaces_test_connection',
                nonce: doSpacesAdmin.nonce,
                bucket: $('#bucket').val(),
                access_key: $('#access_key').val(),
                access_secret: $('#access_secret').val(),
                region: $('#region').val(),
                endpoint: $('#endpoint').val()
            };

            // Validate required fields
            var missingFields = [];
            if (!data.bucket) missingFields.push('Bucket Name');
            if (!data.access_key) missingFields.push('Access Key');
            if (!data.access_secret) missingFields.push('Access Secret');
            if (!data.region) missingFields.push('Region');
            if (!data.endpoint) missingFields.push('Endpoint URL');

            if (missingFields.length > 0) {
                $status
                    .removeClass('testing success')
                    .addClass('error')
                    .text('Please fill in all required fields: ' + missingFields.join(', '));
                return;
            }

            // Disable button and show loading state
            $button.prop('disabled', true);
            $status
                .removeClass('success error')
                .addClass('testing')
                .html('<span class="spinner is-active"></span>' + doSpacesAdmin.strings.testing);

            // Make AJAX request
            $.ajax({
                url: doSpacesAdmin.ajaxUrl,
                type: 'POST',
                data: data,
                timeout: 30000, // 30 second timeout
                success: function(response) {
                    $button.prop('disabled', false);

                    if (response.success) {
                        $status
                            .removeClass('testing error')
                            .addClass('success')
                            .html('&#10004; ' + response.data.message);
                    } else {
                        $status
                            .removeClass('testing success')
                            .addClass('error')
                            .html('&#10008; ' + response.data.message);
                    }
                },
                error: function(xhr, status, error) {
                    $button.prop('disabled', false);

                    var message = doSpacesAdmin.strings.error;

                    if (status === 'timeout') {
                        message += 'Request timed out. Please check your connection settings.';
                    } else if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        message += xhr.responseJSON.data.message;
                    } else {
                        message += 'An unexpected error occurred. Please try again.';
                    }

                    $status
                        .removeClass('testing success')
                        .addClass('error')
                        .html('&#10008; ' + message);
                }
            });
        });

        /**
         * Show/hide password toggle (optional enhancement)
         */
        var $secretField = $('#access_secret');
        if ($secretField.length) {
            var $toggleBtn = $('<button type="button" class="button button-secondary" style="margin-left: 5px;">Show</button>');

            $toggleBtn.on('click', function() {
                if ($secretField.attr('type') === 'password') {
                    $secretField.attr('type', 'text');
                    $toggleBtn.text('Hide');
                } else {
                    $secretField.attr('type', 'password');
                    $toggleBtn.text('Show');
                }
            });

            $secretField.after($toggleBtn);
        }

        /**
         * Auto-format path prefix
         */
        $('#path_prefix').on('blur', function() {
            var value = $(this).val().trim();
            // Remove leading slash, keep trailing slash
            value = value.replace(/^\/+/, '');
            $(this).val(value);
        });

        /**
         * Confirm before enabling if not configured
         */
        var $enabledCheckbox = $('#enabled');
        var originalEnabledState = $enabledCheckbox.is(':checked');

        $enabledCheckbox.on('change', function() {
            var isEnabled = $(this).is(':checked');

            // If enabling, check if all required fields are filled
            if (isEnabled && !originalEnabledState) {
                var bucket = $('#bucket').val();
                var accessKey = $('#access_key').val();
                var accessSecret = $('#access_secret').val();
                var region = $('#region').val();
                var endpoint = $('#endpoint').val();

                if (!bucket || !accessKey || !accessSecret || !region || !endpoint) {
                    if (!confirm('Some required fields are empty. Are you sure you want to enable the integration? It will not work until all required fields are configured.')) {
                        $(this).prop('checked', false);
                        return;
                    }
                }
            }
        });

        /**
         * Update CDN endpoint placeholder when API endpoint changes
         */
        function updateCdnPlaceholder() {
            var endpoint = $('#endpoint').val();
            if (endpoint) {
                var cdnEndpoint = endpoint.replace('.digitaloceanspaces.com', '.cdn.digitaloceanspaces.com');
                $('#cdn_endpoint').attr('placeholder', cdnEndpoint);

                // Update description with current value
                var $description = $('#auto-cdn-value');
                if ($description.length) {
                    $description.text(cdnEndpoint);
                }
            }
        }

        // Update on page load and when endpoint changes
        $('#endpoint').on('blur change', updateCdnPlaceholder);
        updateCdnPlaceholder();

        /**
         * Toggle CDN endpoint field visibility
         */
        $('#use_cdn').on('change', function() {
            if ($(this).is(':checked')) {
                $('#cdn-endpoint-row').show();
                $('#cdn_endpoint').prop('disabled', false);
                updateCdnPlaceholder(); // Update placeholder when showing
            } else {
                $('#cdn-endpoint-row').hide();
                $('#cdn_endpoint').prop('disabled', true);
            }
        }).trigger('change');

    });

})(jQuery);
