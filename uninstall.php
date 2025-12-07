<?php
/**
 * Uninstall Polar SMTP Mailer plugin.
 *
 * Removes all plugin data including database tables and options.
 *
 * @package PolarSmtpMailer
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
function PSM_uninstall() {
    // Check if user has opted to delete data.
    if ( ! get_option( 'PSM_delete_data_on_uninstall', false ) ) {
        return;
    }

    global $wpdb;

    // Delete plugin options.
    $options = array(
        'PSM_smtp_provider',
        'PSM_smtp_host',
        'PSM_smtp_port',
        'PSM_smtp_encryption',
        'PSM_smtp_auth',
        'PSM_smtp_username',
        'PSM_smtp_password',
        'PSM_from_email',
        'PSM_from_name',
        'PSM_force_from_email',
        'PSM_force_from_name',
        'PSM_enable_logging',
        'PSM_log_retention_days',
        'PSM_enable_queue',
        'PSM_queue_batch_size',
        'PSM_queue_interval',
        'PSM_enable_backup_smtp',
        'PSM_backup_smtp_provider',
        'PSM_backup_smtp_host',
        'PSM_backup_smtp_port',
        'PSM_backup_smtp_encryption',
        'PSM_backup_smtp_username',
        'PSM_backup_smtp_password',
        'PSM_debug_mode',
        'PSM_db_version',
        'PSM_privacy_exclude_content',
        'PSM_privacy_anonymize',
        'PSM_auth_failures',
        'PSM_delete_data_on_uninstall',
    );

    foreach ( $options as $option ) {
        delete_option( $option );
    }

    // Drop custom database tables.
    $tables = array(
        $wpdb->prefix . 'PSM_email_logs',
        $wpdb->prefix . 'PSM_email_queue',
    );

    foreach ( $tables as $table ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        $wpdb->query( "DROP TABLE IF EXISTS $table" );
    }

    // Clear scheduled events.
    wp_clear_scheduled_hook( 'PSM_process_email_queue' );
    wp_clear_scheduled_hook( 'PSM_cleanup_logs' );

    // Clear any transients.
    delete_transient( 'PSM_activation_redirect' );

    // Clear cache.
    wp_cache_flush();
}

// Run uninstall.
PSM_uninstall();
