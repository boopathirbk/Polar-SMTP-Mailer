<?php
/**
 * Dashboard class.
 *
 * @package SimpleSmtpMail
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SSM_Dashboard class.
 */
class SSM_Dashboard {

    /**
     * Get dashboard stats.
     */
    public static function get_stats() {
        return array(
            'today' => SSM_DB::get_stats( 'today' ),
            'week'  => SSM_DB::get_stats( 'week' ),
            'month' => SSM_DB::get_stats( 'month' ),
            'all'   => SSM_DB::get_stats( 'all' ),
        );
    }

    /**
     * Get daily chart data.
     */
    public static function get_chart_data() {
        return SSM_DB::get_daily_stats( 30 );
    }

    /**
     * Get system status.
     */
    public static function get_system_status() {
        $host = get_option( 'ssm_smtp_host', '' );

        return array(
            'smtp_configured' => ! empty( $host ),
            'smtp_host'       => $host,
            'logging_enabled' => get_option( 'ssm_enable_logging', true ),
            'queue_enabled'   => get_option( 'ssm_enable_queue', false ),
            'queue_count'     => SSM_DB::get_queue_count(),
            'debug_mode'      => get_option( 'ssm_debug_mode', false ),
            'php_version'     => PHP_VERSION,
            'wp_version'      => get_bloginfo( 'version' ),
            'openssl'         => extension_loaded( 'openssl' ),
        );
    }

    /**
     * Get recent logs.
     */
    public static function get_recent_logs( $limit = 5 ) {
        return SSM_DB::get_logs( array( 'per_page' => $limit, 'page' => 1 ) );
    }
}
