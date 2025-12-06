<?php
/**
 * Uninstall Simple SMTP Mail plugin.
 *
 * Removes all plugin data including database tables and options.
 *
 * @package SimpleSmtpMail
 * @since 1.0.0
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Clean up plugin data on uninstall.
 *
 * @since 1.0.0
 * @return void
 */
function ssm_uninstall() {
    global $wpdb;

    // Delete plugin options.
    $options = array(
        'ssm_smtp_host',
        'ssm_smtp_port',
        'ssm_smtp_encryption',
        'ssm_smtp_auth',
        'ssm_smtp_username',
        'ssm_smtp_password',
        'ssm_from_email',
        'ssm_from_name',
        'ssm_force_from_email',
        'ssm_force_from_name',
        'ssm_enable_logging',
        'ssm_log_retention_days',
        'ssm_enable_queue',
        'ssm_queue_batch_size',
        'ssm_queue_interval',
        'ssm_enable_backup_smtp',
        'ssm_backup_smtp_host',
        'ssm_backup_smtp_port',
        'ssm_backup_smtp_encryption',
        'ssm_backup_smtp_username',
        'ssm_backup_smtp_password',
        'ssm_debug_mode',
        'ssm_db_version',
        'ssm_privacy_exclude_content',
        'ssm_privacy_anonymize',
        'ssm_auth_failures',
    );

    foreach ( $options as $option ) {
        delete_option( $option );
    }

    // Drop custom database tables.
    $tables = array(
        $wpdb->prefix . 'ssm_email_logs',
        $wpdb->prefix . 'ssm_email_queue',
    );

    foreach ( $tables as $table ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
        $wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table ) );
    }

    // Clear scheduled events.
    wp_clear_scheduled_hook( 'ssm_process_email_queue' );
    wp_clear_scheduled_hook( 'ssm_cleanup_logs' );

    // Clear any transients.
    delete_transient( 'ssm_activation_redirect' );

    // Clear cache.
    wp_cache_flush();
}

// Run uninstall.
ssm_uninstall();
