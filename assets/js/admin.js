/**
 * Simple SMTP Mail Admin JavaScript
 *
 * @package SimpleSmtpMail
 */

(function ($) {
    'use strict';

    // Settings page
    const SSMSettings = {
        init: function () {
            this.bindEvents();
            this.toggleAuthFields();
            this.toggleQueueFields();
            this.toggleBackupFields();
        },

        bindEvents: function () {
            $('#ssm_smtp_auth').on('change', this.toggleAuthFields);
            $('[name="ssm_enable_queue"]').on('change', this.toggleQueueFields);
            $('#ssm_enable_backup_smtp').on('change', this.toggleBackupFields);
            $('#ssm_smtp_provider').on('change', function () { SSMSettings.onProviderChange(this, 'primary'); });
            $('#ssm_backup_smtp_provider').on('change', function () { SSMSettings.onProviderChange(this, 'backup'); });
            $('#ssm-test-connection').on('click', this.testConnection);
            $('.ssm-toggle-password').on('click', this.togglePassword);
        },

        toggleAuthFields: function () {
            const checked = $('#ssm_smtp_auth').is(':checked');
            $('.ssm-auth-field').toggle(checked);
        },

        toggleQueueFields: function () {
            const checked = $('[name="ssm_enable_queue"]').is(':checked');
            $('.ssm-queue-field').toggle(checked);
        },

        toggleBackupFields: function () {
            const checked = $('#ssm_enable_backup_smtp').is(':checked');
            $('.ssm-backup-field').toggle(checked);
        },

        togglePassword: function () {
            const input = $(this).prev('input');
            const icon = $(this).find('.dashicons');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
                $(this).attr('aria-label', ssm_ajax.strings.hide_password || 'Hide password');
            } else {
                input.attr('type', 'password');
                icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
                $(this).attr('aria-label', ssm_ajax.strings.show_password || 'Show password');
            }
        },

        onProviderChange: function (element, context) {
            const provider = $(element).val();
            const prefix = context === 'backup' ? '#ssm_backup_' : '#ssm_';
            const descId = context === 'backup' ? '#ssm-backup-provider-description' : '#ssm-provider-description';

            if (provider === 'custom') {
                $(prefix + 'smtp_host').val('').prop('readonly', false);
                $(prefix + 'smtp_port').val(587);
                $(prefix + 'smtp_encryption').val('tls');
                $(descId).text('');
                return;
            }

            $.ajax({
                url: ssm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ssm_get_provider',
                    nonce: ssm_ajax.nonce,
                    provider: provider
                },
                success: function (response) {
                    if (response.success && response.data.provider) {
                        const p = response.data.provider;
                        $(prefix + 'smtp_host').val(p.host);
                        $(prefix + 'smtp_port').val(p.port);
                        $(prefix + 'smtp_encryption').val(p.encryption);

                        if (p.help_text) {
                            $(descId).text(p.help_text);
                        } else {
                            $(descId).text('');
                        }
                    }
                }
            });
        },

        testConnection: function () {
            const $btn = $(this);
            const $result = $('.ssm-test-result');
            const password = $('#ssm_smtp_password').val();
            const isAuthEnabled = $('#ssm_smtp_auth').is(':checked');

            // Check if password is the masked placeholder (bullets)
            if (isAuthEnabled && password && /^[•]+$/.test(password)) {
                $result.addClass('error').html('✗ Please re-enter your password to test the connection. For security, saved passwords are masked and cannot be used for testing.');
                return;
            }

            $btn.prop('disabled', true).text(ssm_ajax.strings.testing);
            $result.removeClass('success error').text('');

            $.ajax({
                url: ssm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ssm_test_connection',
                    nonce: ssm_ajax.nonce,
                    host: $('#ssm_smtp_host').val(),
                    port: $('#ssm_smtp_port').val(),
                    encryption: $('#ssm_smtp_encryption').val(),
                    auth: isAuthEnabled ? 'true' : 'false',
                    username: $('#ssm_smtp_username').val(),
                    password: password
                },
                success: function (response) {
                    if (response.success) {
                        $result.addClass('success').text('✓ ' + response.data.message);
                    } else {
                        $result.addClass('error').text('✗ ' + response.data.message);
                    }
                },
                error: function () {
                    $result.addClass('error').text('✗ Connection failed');
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Test Connection');
                }
            });
        }
    };

    // Test Email page
    const SSMTestEmail = {
        init: function () {
            $('#ssm-test-email-form').on('submit', this.sendTestEmail);
        },

        sendTestEmail: function (e) {
            e.preventDefault();

            const $btn = $('#ssm-send-test');
            const $result = $('#ssm-test-result');
            const email = $('#ssm_test_email').val();

            $btn.prop('disabled', true);
            $btn.find('.dashicons').removeClass('dashicons-email-alt').addClass('dashicons-update spin');
            $result.hide().removeClass('success error');

            $.ajax({
                url: ssm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ssm_send_test_email',
                    nonce: ssm_ajax.nonce,
                    to: email
                },
                success: function (response) {
                    if (response.success) {
                        $result.addClass('success').html('<strong>Success!</strong> ' + response.data.message).show();
                    } else {
                        $result.addClass('error').html('<strong>Error:</strong> ' + response.data.message).show();
                    }
                },
                error: function () {
                    $result.addClass('error').html('<strong>Error:</strong> Failed to send test email').show();
                },
                complete: function () {
                    $btn.prop('disabled', false);
                    $btn.find('.dashicons').removeClass('dashicons-update spin').addClass('dashicons-email-alt');
                }
            });
        }
    };

    // Logs page
    const SSMLogs = {
        init: function () {
            this.bindEvents();
        },

        bindEvents: function () {
            $(document).on('click', '.ssm-view-log', this.viewLog);
            $(document).on('click', '.ssm-delete-log', this.deleteLog);
            $(document).on('click', '.ssm-resend-email', this.resendEmail);
            $(document).on('click', '.ssm-export-logs', this.exportLogs);
            $(document).on('click', '.ssm-modal-close, .ssm-modal-overlay', this.closeModal);
            $(document).on('keydown', this.handleKeydown);
        },

        handleKeydown: function (e) {
            if (e.key === 'Escape' && $('#ssm-log-modal').is(':visible')) {
                SSMLogs.closeModal();
            }
        },

        viewLog: function () {
            const id = $(this).data('id');

            $.ajax({
                url: ssm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ssm_view_log',
                    nonce: ssm_ajax.nonce,
                    id: id
                },
                success: function (response) {
                    if (response.success && response.data.log) {
                        const log = response.data.log;
                        $('#ssm-log-to').text(log.to_email);
                        $('#ssm-log-subject').text(log.subject || '(no subject)');
                        $('#ssm-log-status').html('<span class="ssm-status ssm-status-' + log.status + '">' + log.status.charAt(0).toUpperCase() + log.status.slice(1) + '</span>');
                        $('#ssm-log-provider').text(log.provider || '-');
                        $('#ssm-log-date').text(log.created_at);

                        if (log.error) {
                            $('#ssm-log-error').text(log.error);
                            $('#ssm-log-error-row').show();
                        } else {
                            $('#ssm-log-error-row').hide();
                        }

                        // Safely display message content - use text() for plain text or create iframe sandbox for HTML
                        const messageContainer = $('#ssm-log-message');
                        if (log.message.indexOf('<') !== -1 && log.message.indexOf('>') !== -1) {
                            // HTML content - use srcdoc iframe for sandboxed display
                            messageContainer.html('<iframe sandbox="" srcdoc="' + $('<div>').text(log.message).html().replace(/"/g, '&quot;') + '" style="width:100%;min-height:200px;border:none;"></iframe>');
                        } else {
                            // Plain text - safe to use text()
                            messageContainer.text(log.message);
                        }
                        $('#ssm-log-modal').show();
                        $('.ssm-modal-close').focus();
                    }
                }
            });
        },

        deleteLog: function () {
            if (!confirm(ssm_ajax.strings.confirm_delete)) {
                return;
            }

            const $btn = $(this);
            const id = $btn.data('id');
            const $row = $btn.closest('tr');

            $.ajax({
                url: ssm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ssm_delete_log',
                    nonce: ssm_ajax.nonce,
                    id: id
                },
                success: function (response) {
                    if (response.success) {
                        $row.fadeOut(300, function () { $(this).remove(); });
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        },

        resendEmail: function () {
            const $btn = $(this);
            const id = $btn.data('id');

            $btn.prop('disabled', true).text('Sending...');

            $.ajax({
                url: ssm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ssm_resend_email',
                    nonce: ssm_ajax.nonce,
                    id: id
                },
                success: function (response) {
                    if (response.success) {
                        alert(response.data.message);
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false).text('Resend');
                }
            });
        },

        exportLogs: function () {
            const format = $(this).data('format');
            const status = $('select[name="status"]').val() || '';

            $.ajax({
                url: ssm_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'ssm_export_logs',
                    nonce: ssm_ajax.nonce,
                    format: format,
                    status: status
                },
                success: function (response) {
                    if (response.success) {
                        const blob = new Blob([response.data.content], { type: response.data.mime });
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = response.data.filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    }
                }
            });
        },

        closeModal: function () {
            $('#ssm-log-modal').hide();
        }
    };

    // Initialize on document ready
    $(document).ready(function () {
        SSMSettings.init();
        SSMTestEmail.init();
        SSMLogs.init();
    });

})(jQuery);
