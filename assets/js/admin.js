/**
 * Polar SMTP Mailer Admin JavaScript
 *
 * @package PolarSmtpMailer
 */

// Define globally
var SSMSettings, SSMTestEmail, SSMLogs;

(function ($) {
    'use strict';

    // Settings page
    SSMSettings = {
        init: function () {
            this.bindEvents();
            this.toggleAuthFields();
            this.toggleQueueFields();
            this.toggleBackupFields();
        },

        bindEvents: function () {
            const self = this;

            $('#PSM_smtp_auth').on('change', this.toggleAuthFields);
            $('[name="PSM_enable_queue"]').on('change', this.toggleQueueFields);
            $('#PSM_enable_backup_smtp').on('change', this.toggleBackupFields);

            // Primary provider change - direct binding
            $('#PSM_smtp_provider').on('change', function () {
                self.onProviderChange(this, 'primary');
            });

            // Backup provider change - use event delegation for hidden elements
            $(document).on('change', '#PSM_backup_smtp_provider', function () {
                self.onProviderChange(this, 'backup');
            });

            $('#ssm-test-connection').on('click', this.testConnection);
            $('#ssm-test-backup-connection').on('click', function (e) {
                e.preventDefault();
                self.testConnection.call(this, 'backup');
            });
            $('.ssm-toggle-password').on('click', this.togglePassword);
        },

        toggleAuthFields: function () {
            const checked = $('#PSM_smtp_auth').is(':checked');
            $('.ssm-auth-field').toggle(checked);
        },

        toggleQueueFields: function () {
            const checked = $('[name="PSM_enable_queue"]').is(':checked');
            $('.ssm-queue-field').toggle(checked);
        },

        toggleBackupFields: function () {
            const checked = $('#PSM_enable_backup_smtp').is(':checked');
            $('.ssm-backup-field').toggle(checked);
        },

        togglePassword: function () {
            const input = $(this).prev('input');
            const icon = $(this).find('.dashicons');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('dashicons-visibility').addClass('dashicons-hidden');
                $(this).attr('aria-label', PSM_ajax.strings.hide_password || 'Hide password');
            } else {
                input.attr('type', 'password');
                icon.removeClass('dashicons-hidden').addClass('dashicons-visibility');
                $(this).attr('aria-label', PSM_ajax.strings.show_password || 'Show password');
            }
        },

        onProviderChange: function (element, context) {
            const provider = $(element).val();
            const prefix = context === 'backup' ? '#PSM_backup_' : '#PSM_';
            const descId = context === 'backup' ? '#ssm-backup-provider-description' : '#ssm-provider-description';

            if (provider === 'custom') {
                $(prefix + 'smtp_host').val('').prop('readonly', false);
                $(prefix + 'smtp_port').val(587);
                $(prefix + 'smtp_encryption').val('tls');
                $(descId).text('');
                return;
            }

            $.ajax({
                url: PSM_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'PSM_get_provider',
                    nonce: PSM_ajax.nonce,
                    provider: provider
                },
                success: function (response) {
                    if (response.success && response.data.provider) {
                        const p = response.data.provider;
                        // Set value AND force editable
                        $(prefix + 'smtp_host').val(p.host).prop('readonly', false);
                        $(prefix + 'smtp_port').val(p.port);
                        $(prefix + 'smtp_encryption').val(p.encryption);

                        if (p.help_text) {
                            $(descId).text(p.help_text);
                        } else {
                            $(descId).text('');
                        }
                    } else {
                        alert('Error: Could not load provider settings. ' + (response.data ? response.data.message : ''));
                    }
                },
                error: function (xhr, status, error) {
                    alert('Error loading provider: ' + error);
                }
            });
        },

        testConnection: function (type) {
            const isBackup = type === 'backup';
            const $btn = $(this);
            const $result = isBackup ? $('.ssm-backup-test-result') : $('.ssm-test-result'); // Ensure distinct result containers
            const prefix = isBackup ? '#PSM_backup_' : '#PSM_';

            const host = $(prefix + 'smtp_host').val();
            const port = $(prefix + 'smtp_port').val();
            const encryption = $(prefix + 'smtp_encryption').val();
            const username = $(prefix + 'smtp_username').val();
            const password = $(prefix + 'smtp_password').val();
            // Backup always assumes Auth is true if fields are filled, but for Primary checks checkbox
            const auth = isBackup ? 'true' : ($('#PSM_smtp_auth').is(':checked') ? 'true' : 'false');

            // Check if password is the masked placeholder (bullets)
            if (password && /^[•]+$/.test(password)) {
                $result.addClass('error').html('✗ Please re-enter your password to test the connection. For security, saved passwords are masked.');
                return;
            }

            $btn.prop('disabled', true).text(PSM_ajax.strings.testing);
            $result.removeClass('success error').text('');

            $.ajax({
                url: PSM_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'PSM_test_connection',
                    nonce: PSM_ajax.nonce,
                    host: host,
                    port: port,
                    encryption: encryption,
                    auth: auth,
                    username: username,
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
    SSMTestEmail = {
        init: function () {
            // Use .off().on() to prevent double binding
            $('#ssm-test-email-form').off('submit').on('submit', this.sendTestEmail);
        },

        sendTestEmail: function (e) {
            e.preventDefault();

            const $btn = $('#ssm-send-test');
            const $result = $('#ssm-test-result');
            const email = $('#PSM_test_email').val();

            $btn.prop('disabled', true);
            $btn.find('.dashicons').removeClass('dashicons-email-alt').addClass('dashicons-update spin');
            $result.hide().removeClass('success error');

            $.ajax({
                url: PSM_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'PSM_send_test_email',
                    nonce: PSM_ajax.nonce,
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
    SSMLogs = {
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

        viewLog: function (e) {
            e.preventDefault();
            const id = $(this).data('id');

            if (!id) {
                alert('Error: Invalid log ID');
                return;
            }

            $.ajax({
                url: PSM_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'PSM_view_log',
                    nonce: PSM_ajax.nonce,
                    id: id
                },
                success: function (response) {
                    if (response.success && response.data.log) {
                        const log = response.data.log;

                        // Build the modal content HTML with custom classes and font enforcement
                        let statusClass = 'ssm-status-' + log.status;
                        let statusText = log.status.charAt(0).toUpperCase() + log.status.slice(1);

                        // Main wrapper with plugin font family
                        let html = '<div class="ssm-view-log-content">';

                        // Info Grid Section - Server Details
                        html += '<div class="ssm-modal-info-grid">';
                        html += '<div class="ssm-modal-info-item"><span class="ssm-modal-info-label">To</span><span class="ssm-modal-info-value">' + SSMLogs.escapeHtml(log.to_email) + '</span></div>';
                        html += '<div class="ssm-modal-info-item"><span class="ssm-modal-info-label">From</span><span class="ssm-modal-info-value">' + SSMLogs.escapeHtml(log.from_email || 'N/A') + '</span></div>';
                        html += '<div class="ssm-modal-info-item"><span class="ssm-modal-info-label">Subject</span><span class="ssm-modal-info-value">' + SSMLogs.escapeHtml(log.subject || '(no subject)') + '</span></div>';
                        html += '<div class="ssm-modal-info-item"><span class="ssm-modal-info-label">Status</span><span class="ssm-modal-info-value"><span class="ssm-status ' + statusClass + '">' + statusText + '</span></span></div>';
                        html += '<div class="ssm-modal-info-item"><span class="ssm-modal-info-label">Provider</span><span class="ssm-modal-info-value">' + SSMLogs.escapeHtml(log.provider || 'Unknown') + '</span></div>';
                        html += '<div class="ssm-modal-info-item"><span class="ssm-modal-info-label">Date</span><span class="ssm-modal-info-value">' + SSMLogs.escapeHtml(log.created_at) + '</span></div>';
                        html += '</div>';

                        // Error message if present
                        if (log.error) {
                            html += '<div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 12px; margin-bottom: 20px; color: #991b1b;">';
                            html += '<strong style="display: block; margin-bottom: 4px;">⚠️ Error</strong>';
                            html += SSMLogs.escapeHtml(log.error);
                            html += '</div>';
                        }

                        // Message Content
                        html += '<h4 style="margin: 0 0 12px 0; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #646970; font-weight: 600;">Message Content</h4>';
                        html += '<div class="ssm-message-preview">';

                        // Check if HTML content
                        if (log.message && log.message.indexOf('<') !== -1 && log.message.indexOf('>') !== -1) {
                            const srcdoc = log.message.replace(/&/g, '&amp;').replace(/"/g, '&quot;');
                            html += '<iframe sandbox="" srcdoc="' + srcdoc + '" style="width:100%; min-height:300px; border:none; background:#fff;"></iframe>';
                        } else {
                            html += '<pre style="white-space: pre-wrap; word-wrap: break-word; margin: 0; font-family: monospace; font-size: 13px; line-height: 1.6;">' + SSMLogs.escapeHtml(log.message || '(no content)') + '</pre>';
                        }

                        html += '</div></div>';

                        // Update the ThickBox inline content
                        $('#psm-thickbox-content').html(html);

                        // Open ThickBox
                        tb_show('Email Details', '#TB_inline?width=650&height=500&inlineId=psm-thickbox-content');
                    } else {
                        alert('Error: ' + (response.data ? response.data.message : 'Failed to load log'));
                    }
                },
                error: function (xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });
        },

        escapeHtml: function (text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        deleteLog: function () {
            if (!confirm(PSM_ajax.strings.confirm_delete)) {
                return;
            }

            const $btn = $(this);
            const id = $btn.data('id');
            const $row = $btn.closest('tr');

            $.ajax({
                url: PSM_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'PSM_delete_log',
                    nonce: PSM_ajax.nonce,
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
                url: PSM_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'PSM_resend_email',
                    nonce: PSM_ajax.nonce,
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
                url: PSM_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'PSM_export_logs',
                    nonce: PSM_ajax.nonce,
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
