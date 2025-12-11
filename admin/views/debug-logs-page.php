<?php
/**
 * Debug Logs admin page.
 *
 * @package PolarSmtpMailer
 * @since 1.0.4
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get log data.
// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template variables.
$psm_logs = PSM_Debug_Logger::get_logs( 200 );
$psm_log_size = PSM_Debug_Logger::get_log_size();
?>

<div class="wrap ssm-wrap">
    <h1 class="ssm-page-title">
        <span class="dashicons dashicons-warning"></span>
        <?php esc_html_e( 'Debug Logs', 'polar-smtp-mailer' ); ?>
    </h1>

    <div class="ssm-card">
        <div class="ssm-card-header">
            <h2><?php esc_html_e( 'SMTP Debug Log', 'polar-smtp-mailer' ); ?></h2>
            <div class="ssm-card-actions">
                <span class="ssm-log-size">
                    <?php
                    printf(
                        /* translators: %s: Log file size */
                        esc_html__( 'Log size: %s', 'polar-smtp-mailer' ),
                        esc_html( $psm_log_size )
                    );
                    ?>
                </span>
                <form method="post" action="" style="display: inline;">
                    <?php wp_nonce_field( 'psm_clear_debug_logs', 'psm_debug_nonce' ); ?>
                    <button type="submit" name="psm_clear_logs" value="1" class="button button-secondary" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to clear all debug logs?', 'polar-smtp-mailer' ); ?>');">
                        <span class="dashicons dashicons-trash" style="vertical-align: text-top;"></span>
                        <?php esc_html_e( 'Clear Logs', 'polar-smtp-mailer' ); ?>
                    </button>
                </form>
                <button type="button" class="button button-secondary" onclick="location.reload();">
                    <span class="dashicons dashicons-update" style="vertical-align: text-top;"></span>
                    <?php esc_html_e( 'Refresh', 'polar-smtp-mailer' ); ?>
                </button>
            </div>
        </div>

        <div class="ssm-card-body">
            <?php if ( empty( $psm_logs ) ) : ?>
                <div class="ssm-notice ssm-notice-info">
                    <p><?php esc_html_e( 'No debug logs yet. Send a test email to generate logs.', 'polar-smtp-mailer' ); ?></p>
                </div>
            <?php else : ?>
                <div class="ssm-debug-log-container">
                    <pre class="ssm-debug-log"><?php echo esc_html( $psm_logs ); ?></pre>
                </div>
            <?php endif; ?>
        </div>

        <div class="ssm-card-footer">
            <p class="description">
                <?php esc_html_e( 'Debug logs show detailed SMTP communication. Disable debug mode in production.', 'polar-smtp-mailer' ); ?>
            </p>
        </div>
    </div>
</div>

<style>
.ssm-debug-log-container {
    max-height: 500px;
    overflow-y: auto;
    background: #1e1e1e;
    border-radius: 4px;
    padding: 15px;
}
.ssm-debug-log {
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    font-size: 12px;
    line-height: 1.5;
    color: #d4d4d4;
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
}
.ssm-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}
.ssm-card-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}
.ssm-log-size {
    color: #666;
    font-size: 13px;
}
.ssm-notice {
    padding: 12px 15px;
    border-left: 4px solid #0073aa;
    background: #f0f6fc;
}
.ssm-notice-info {
    border-color: #0073aa;
}
</style>
