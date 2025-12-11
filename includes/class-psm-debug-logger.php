<?php
/**
 * Debug Logger class.
 *
 * Handles debug logging to a custom file.
 *
 * @package PolarSmtpMailer
 * @since 1.0.4
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Debug_Logger class.
 *
 * @since 1.0.4
 */
class PSM_Debug_Logger {

    /**
     * Log file path.
     *
     * @var string
     */
    private static $log_file = null;

    /**
     * Get log file path.
     *
     * @since 1.0.4
     * @return string Log file path.
     */
    public static function get_log_file() {
        if ( null === self::$log_file ) {
            $upload_dir = wp_upload_dir();
            $log_dir = $upload_dir['basedir'] . '/psm-debug';

            // Create directory if it doesn't exist.
            if ( ! file_exists( $log_dir ) ) {
                wp_mkdir_p( $log_dir );
                // Add .htaccess to protect log files.
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
                file_put_contents( $log_dir . '/.htaccess', 'deny from all' );
                // Add index.php to prevent directory listing.
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
                file_put_contents( $log_dir . '/index.php', '<?php // Silence is golden.' );
            }

            self::$log_file = $log_dir . '/debug.log';
        }

        return self::$log_file;
    }

    /**
     * Check if debug mode is enabled.
     *
     * @since 1.0.4
     * @return bool
     */
    public static function is_enabled() {
        return (bool) get_option( 'PSM_debug_mode', false );
    }

    /**
     * Log a debug message.
     *
     * @since 1.0.4
     * @param string $message Message to log.
     * @param string $level   Log level (debug, info, warning, error).
     * @return bool True on success.
     */
    public static function log( $message, $level = 'debug' ) {
        if ( ! self::is_enabled() ) {
            return false;
        }

        $log_file = self::get_log_file();
        $timestamp = wp_date( 'Y-m-d H:i:s' );
        $level = strtoupper( $level );

        $log_entry = sprintf(
            "[%s] [%s] %s\n",
            $timestamp,
            $level,
            $message
        );

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
        return (bool) file_put_contents( $log_file, $log_entry, FILE_APPEND | LOCK_EX );
    }

    /**
     * Log SMTP debug output.
     *
     * @since 1.0.4
     * @param string $str   Debug string from PHPMailer.
     * @param int    $level Debug level.
     * @return void
     */
    public static function log_smtp( $str, $level ) {
        self::log( "SMTP [$level]: " . trim( $str ), 'debug' );
    }

    /**
     * Get log contents.
     *
     * @since 1.0.4
     * @param int $lines Number of lines to return (0 = all).
     * @return string Log contents.
     */
    public static function get_logs( $lines = 100 ) {
        $log_file = self::get_log_file();

        if ( ! file_exists( $log_file ) ) {
            return '';
        }

        if ( 0 === $lines ) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
            return file_get_contents( $log_file );
        }

        // Get last N lines efficiently.
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
        $file = fopen( $log_file, 'r' );
        if ( ! $file ) {
            return '';
        }

        $result = array();
        // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition
        while ( ( $line = fgets( $file ) ) !== false ) {
            $result[] = $line;
            if ( count( $result ) > $lines ) {
                array_shift( $result );
            }
        }
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
        fclose( $file );

        return implode( '', $result );
    }

    /**
     * Clear log file.
     *
     * @since 1.0.4
     * @return bool True on success.
     */
    public static function clear_logs() {
        $log_file = self::get_log_file();

        if ( file_exists( $log_file ) ) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
            return unlink( $log_file );
        }

        return true;
    }

    /**
     * Get log file size.
     *
     * @since 1.0.4
     * @return string Human readable file size.
     */
    public static function get_log_size() {
        $log_file = self::get_log_file();

        if ( ! file_exists( $log_file ) ) {
            return '0 B';
        }

        return size_format( filesize( $log_file ) );
    }
}
