/**
 * Digital Ocean Spaces Integration - Metadata Repair JavaScript
 */

(function($) {
    'use strict';

    var repair = {
        isRunning: false,
        isCancelled: false,

        init: function() {
            this.bindEvents();
            this.updateStatus();
        },

        bindEvents: function() {
            $('#start-repair-btn').on('click', $.proxy(this.startRepair, this));
            $('#cancel-repair-btn').on('click', $.proxy(this.cancelRepair, this));
            $('#reset-repair-btn').on('click', $.proxy(this.resetRepair, this));
        },

        updateStatus: function() {
            $.ajax({
                url: doSpacesRepair.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'do_spaces_get_repair_status',
                    nonce: doSpacesRepair.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $('#repair-broken').text(data.broken);
                        $('#repair-repaired').text(data.repaired);

                        var total = data.broken + data.repaired;
                        if (total > 0) {
                            var pct = Math.round((data.repaired / total) * 100);
                            $('#repair-progress-bar').css('width', pct + '%');
                            $('#repair-progress-text').text(pct + '%');
                        }

                        // Disable start button if nothing to repair
                        if (data.broken === 0) {
                            $('#start-repair-btn').prop('disabled', true);
                        }
                    }
                }
            });
        },

        startRepair: function(e) {
            e.preventDefault();

            if (this.isRunning) {
                return;
            }

            if (!confirm('This will download images from Spaces, regenerate thumbnails, and re-upload them. Continue?')) {
                return;
            }

            this.isRunning = true;
            this.isCancelled = false;

            $('#start-repair-btn').prop('disabled', true);
            $('#cancel-repair-btn').prop('disabled', false).show();
            $('#repair-status').removeClass('success error').addClass('processing').text('Repair in progress...');

            this.processBatch();
        },

        processBatch: function() {
            if (this.isCancelled) {
                this.stopRepair('Repair cancelled by user.');
                return;
            }

            var self = this;

            $.ajax({
                url: doSpacesRepair.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'do_spaces_repair_batch',
                    nonce: doSpacesRepair.nonce
                },
                timeout: 180000,
                success: function(response) {
                    if (response.success) {
                        var data = response.data;

                        $('#repair-broken').text(data.broken);
                        $('#repair-repaired').text(data.repaired);

                        var total = data.broken + data.repaired;
                        if (total > 0) {
                            var pct = Math.round((data.repaired / total) * 100);
                            $('#repair-progress-bar').css('width', pct + '%');
                            $('#repair-progress-text').text(pct + '%');
                        }

                        if (data.error_details && data.error_details.length > 0) {
                            var errorMsg = 'Batch errors (' + data.error_details.length + '): ';
                            data.error_details.forEach(function(err) {
                                errorMsg += '\n  ID ' + err.id + ': ' + err.message;
                            });
                            console.warn(errorMsg);
                            $('#repair-status').text('Processing... (' + data.error_details.length + ' image(s) skipped in last batch)');
                        }

                        if (data.completed) {
                            self.stopRepair('Repair completed! All images have been fixed.', 'success');
                        } else if (data.broken === 0) {
                            self.stopRepair('Repair completed! All images have been fixed.', 'success');
                        } else {
                            setTimeout(function() {
                                self.processBatch();
                            }, 500);
                        }
                    } else {
                        self.stopRepair('Error: ' + response.data.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    var message = 'Repair failed: ';
                    if (status === 'timeout') {
                        message += 'Request timed out. Please try again.';
                    } else {
                        message += error;
                    }
                    self.stopRepair(message, 'error');
                }
            });
        },

        cancelRepair: function(e) {
            e.preventDefault();
            this.isCancelled = true;
        },

        stopRepair: function(message, type) {
            this.isRunning = false;
            this.isCancelled = false;

            $('#start-repair-btn').prop('disabled', false);
            $('#cancel-repair-btn').prop('disabled', true).hide();

            type = type || 'success';
            $('#repair-status')
                .removeClass('processing success error')
                .addClass(type)
                .text(message);

            this.updateStatus();
        },

        resetRepair: function(e) {
            e.preventDefault();

            if (!confirm('This will reset the repair status so all broken images can be re-scanned. Continue?')) {
                return;
            }

            $.ajax({
                url: doSpacesRepair.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'do_spaces_reset_repair',
                    nonce: doSpacesRepair.nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                }
            });
        }
    };

    $(document).ready(function() {
        if ($('#start-repair-btn').length) {
            repair.init();
        }
    });

})(jQuery);
