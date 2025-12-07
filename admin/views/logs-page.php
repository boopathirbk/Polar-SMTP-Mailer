<?php
/**
 * Logs page template.
 *
 * @package PolarSmtpMailer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$logs_table = PSM_Logs::get_table();
$logs_table->process_bulk_action();
$logs_table->prepare_items();
?>

<div class="wrap ssm-wrap">
    <h1 class="ssm-page-title">
        <span class="dashicons dashicons-list-view"></span>
        <?php esc_html_e( 'Email Logs', 'polar-smtp-mailer' ); ?>
    </h1>
    <p class="description"><?php esc_html_e( 'View and manage your email sending history. You can search, filter, export, and resend emails from here.', 'polar-smtp-mailer' ); ?></p>

    <div class="ssm-logs-actions">
        <button type="button" class="button ssm-export-logs" data-format="csv">
            <span class="dashicons dashicons-download"></span>
            <?php esc_html_e( 'Export CSV', 'polar-smtp-mailer' ); ?>
        </button>
        <button type="button" class="button ssm-export-logs" data-format="json">
            <span class="dashicons dashicons-download"></span>
            <?php esc_html_e( 'Export JSON', 'polar-smtp-mailer' ); ?>
        </button>
    </div>

    <form method="post" class="ssm-logs-form">
        <input type="hidden" name="page" value="polar-smtp-mailer-logs">
        <?php wp_nonce_field( 'bulk-email-logs' ); ?>
        <?php $logs_table->search_box( __( 'Search', 'polar-smtp-mailer' ), 'ssm-search' ); ?>
        
        <div class="ssm-table-responsive">
            <?php $logs_table->display(); ?>
        </div>
    </form>
</div>

<!-- Email Preview Modal -->
<div id="ssm-log-modal" class="ssm-modal" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="ssm-modal-title">
    <div class="ssm-modal-overlay"></div>
    <div class="ssm-modal-content">
        <button type="button" class="ssm-modal-close" aria-label="<?php esc_attr_e( 'Close', 'polar-smtp-mailer' ); ?>">&times;</button>
        <h2 id="ssm-modal-title"><?php esc_html_e( 'Email Details', 'polar-smtp-mailer' ); ?></h2>
        <div class="ssm-modal-body">
            <table class="ssm-email-details">
                <tr>
                    <th><?php esc_html_e( 'To', 'polar-smtp-mailer' ); ?></th>
                    <td id="ssm-log-to"></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Subject', 'polar-smtp-mailer' ); ?></th>
                    <td id="ssm-log-subject"></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Status', 'polar-smtp-mailer' ); ?></th>
                    <td id="ssm-log-status"></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Provider', 'polar-smtp-mailer' ); ?></th>
                    <td id="ssm-log-provider"></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Date', 'polar-smtp-mailer' ); ?></th>
                    <td id="ssm-log-date"></td>
                </tr>
                <tr id="ssm-log-error-row" style="display:none;">
                    <th><?php esc_html_e( 'Error', 'polar-smtp-mailer' ); ?></th>
                    <td id="ssm-log-error" class="ssm-error-text"></td>
                </tr>
            </table>
            <div class="ssm-email-content">
                <h3><?php esc_html_e( 'Message Content', 'polar-smtp-mailer' ); ?></h3>
                <div id="ssm-log-message" class="ssm-message-preview"></div>
            </div>
        </div>
    </div>
</div>
