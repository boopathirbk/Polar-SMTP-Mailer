<?php
/**
 * Logs page template.
 *
 * @package PolarSmtpMailer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$psm_logs_table = PSM_Logs::get_table();
$psm_logs_table->process_bulk_action();
$psm_logs_table->prepare_items();
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
        <?php $psm_logs_table->search_box( __( 'Search', 'polar-smtp-mailer' ), 'ssm-search' ); ?>
        
        <div class="ssm-table-responsive">
            <?php $psm_logs_table->display(); ?>
        </div>
    </form>
</div>

<!-- ThickBox Inline Content Container (hidden, used by WordPress ThickBox) -->
<div id="psm-thickbox-content" style="display:none;">
    <p><?php esc_html_e( 'Loading...', 'polar-smtp-mailer' ); ?></p>
</div>

