<?php
/**
 * Dashboard class.
 *
 * @package PolarSmtpMailer
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Dashboard class.
 */
class PSM_Dashboard {

    /**
     * Get dashboard stats.
     */
    public static function get_stats() {
        return array(
            'today' => PSM_DB::get_stats( 'today' ),
            'week'  => PSM_DB::get_stats( 'week' ),
            'month' => PSM_DB::get_stats( 'month' ),
            'all'   => PSM_DB::get_stats( 'all' ),
        );
    }

    /**
     * Get daily chart data.
     */
    public static function get_chart_data() {
        return PSM_DB::get_daily_stats( 30 );
    }

    /**
     * Get system status.
     */
    public static function get_system_status() {
        $host = get_option( 'PSM_smtp_host', '' );

        return array(
            'smtp_configured' => ! empty( $host ),
            'smtp_host'       => $host,
            'logging_enabled' => get_option( 'PSM_enable_logging', true ),
            'queue_enabled'   => get_option( 'PSM_enable_queue', false ),
            'queue_count'     => PSM_DB::get_queue_count(),
            'debug_mode'      => get_option( 'PSM_debug_mode', false ),
            'php_version'     => PHP_VERSION,
            'wp_version'      => get_bloginfo( 'version' ),
            'openssl'         => extension_loaded( 'openssl' ),
        );
    }

    /**
     * Get recent logs.
     */
    public static function get_recent_logs( $limit = 5 ) {
        return PSM_DB::get_logs( array( 'per_page' => $limit, 'page' => 1 ) );
    }
}
