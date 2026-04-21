/**
 * Digital Ocean Spaces Integration - Migration JavaScript
 */

(function($) {
    'use strict';

    var migration = {
        isRunning: false,
        isCancelled: false,

        init: function() {
            this.bindEvents();
            this.updateStatus();
        },

        bindEvents: function() {
            $('#start-migration-btn').on('click', $.proxy(this.startMigration, this));
            $('#cancel-migration-btn').on('click', $.proxy(this.cancelMigration, this));
            $('#reset-migration-btn').on('click', $.proxy(this.resetMigration, this));
        },

        updateStatus: function() {
            $.ajax({
                url: doSpacesMigration.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'do_spaces_get_migration_status',
                    nonce: doSpacesMigration.nonce
                },
                success: function(response) {
                    if (response.success) {
                        var data = response.data;
                        $('#migration-total').text(data.total);
                        $('#migration-migrated').text(data.migrated);
                        $('#migration-remaining').text(data.remaining);

                        if (data.total > 0) {
                            $('#migration-progress-bar').css('width', data.percentage + '%');
                            $('#migration-progress-text').text(data.percentage + '%');
                        }
                    }
                }
            });
        },

        startMigration: function(e) {
            e.preventDefault();

            if (this.isRunning) {
                return;
            }

            if (!confirm('This will upload all existing media files to Digital Ocean Spaces. Continue?')) {
                return;
            }

            this.isRunning = true;
            this.isCancelled = false;

            $('#start-migration-btn').prop('disabled', true);
            $('#cancel-migration-btn').prop('disabled', false).show();
            $('#migration-status').removeClass('success error').addClass('processing').text('Migration in progress...');

            this.processBatch();
        },

        processBatch: function() {
            if (this.isCancelled) {
                this.stopMigration('Migration cancelled by user.');
                return;
            }

            var self = this;

            $.ajax({
                url: doSpacesMigration.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'do_spaces_migrate_batch',
                    nonce: doSpacesMigration.nonce
                },
                timeout: 120000, // 120 second timeout per batch
                success: function(response) {
                    if (response.success) {
                        var data = response.data;

                        // Update progress
                        $('#migration-migrated').text(data.migrated);
                        $('#migration-remaining').text(data.total - data.migrated);
                        $('#migration-progress-bar').css('width', data.percentage + '%');
                        $('#migration-progress-text').text(data.percentage + '%');

                        // Log any per-file errors from this batch
                        if (data.error_details && data.error_details.length > 0) {
                            var errorMsg = 'Batch errors (' + data.error_details.length + '): ';
                            data.error_details.forEach(function(err) {
                                errorMsg += '\n  ID ' + err.id + ': ' + err.message;
                            });
                            console.warn(errorMsg);
                            $('#migration-status').text('Processing... (' + data.error_details.length + ' file(s) skipped in last batch)');
                        }

                        if (data.completed) {
                            self.stopMigration('Migration completed successfully! All files have been uploaded to Spaces.', 'success');
                        } else {
                            // Process next batch
                            setTimeout(function() {
                                self.processBatch();
                            }, 500);
                        }
                    } else {
                        self.stopMigration('Error: ' + response.data.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    var message = 'Migration failed: ';
                    if (status === 'timeout') {
                        message += 'Request timed out. Please try again.';
                    } else {
                        message += error;
                    }
                    self.stopMigration(message, 'error');
                }
            });
        },

        cancelMigration: function(e) {
            e.preventDefault();
            this.isCancelled = true;
        },

        stopMigration: function(message, type) {
            this.isRunning = false;
            this.isCancelled = false;

            $('#start-migration-btn').prop('disabled', false);
            $('#cancel-migration-btn').prop('disabled', true).hide();

            type = type || 'success';
            $('#migration-status')
                .removeClass('processing success error')
                .addClass(type)
                .text(message);

            this.updateStatus();
        },

        resetMigration: function(e) {
            e.preventDefault();

            if (!confirm('This will reset the migration status for all files. You will need to re-migrate everything. Continue?')) {
                return;
            }

            $.ajax({
                url: doSpacesMigration.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'do_spaces_reset_migration',
                    nonce: doSpacesMigration.nonce
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

    var svgFix = {
        init: function() {
            this.bindEvents();
            this.updateStatus();
        },

        bindEvents: function() {
            $('#fix-svg-btn').on('click', $.proxy(this.fixSvgs, this));
        },

        updateStatus: function() {
            $.ajax({
                url: doSpacesMigration.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'do_spaces_fix_svg_status',
                    nonce: doSpacesMigration.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#svg-fix-total').text(response.data.total);
                    }
                }
            });
        },

        fixSvgs: function(e) {
            e.preventDefault();

            if (!confirm('This will update the Content-Type metadata for all SVG files in Spaces. Continue?')) {
                return;
            }

            var $btn = $('#fix-svg-btn');
            var $status = $('#svg-fix-status');

            $btn.prop('disabled', true).text('Fixing...');
            $status.removeClass('success error').addClass('processing').text('Updating SVG Content-Types in Spaces...').show();

            $.ajax({
                url: doSpacesMigration.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'do_spaces_fix_svg_batch',
                    nonce: doSpacesMigration.nonce
                },
                timeout: 120000,
                success: function(response) {
                    $btn.prop('disabled', false).text('Fix SVG Content-Types');

                    if (response.success) {
                        var data = response.data;
                        var type = data.errors > 0 ? 'error' : 'success';
                        var message = data.message;

                        if (data.error_details && data.error_details.length > 0) {
                            message += '\n\nErrors:';
                            data.error_details.forEach(function(err) {
                                message += '\n  ID ' + err.id + ': ' + err.message;
                            });
                        }

                        $status.removeClass('processing success error').addClass(type).text(message);
                    } else {
                        $status.removeClass('processing success').addClass('error').text('Error: ' + response.data.message);
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).text('Fix SVG Content-Types');
                    var message = status === 'timeout' ? 'Request timed out. Please try again.' : error;
                    $status.removeClass('processing success').addClass('error').text('Failed: ' + message);
                }
            });
        }
    };

    $(document).ready(function() {
        migration.init();
        svgFix.init();
    });

})(jQuery);
