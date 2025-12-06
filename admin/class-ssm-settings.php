<?php
/**
 * Settings class.
 *
 * @package SimpleSmtpMail
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SSM_Settings class.
 */
class SSM_Settings {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Register settings.
     */
    public function register_settings() {
        // SMTP Settings.
        register_setting( 'ssm_settings', 'ssm_smtp_provider', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ssm_settings', 'ssm_smtp_host', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ssm_settings', 'ssm_smtp_port', array( 'sanitize_callback' => 'absint' ) );
        register_setting( 'ssm_settings', 'ssm_smtp_encryption', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ssm_settings', 'ssm_smtp_auth', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'ssm_settings', 'ssm_smtp_username', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ssm_settings', 'ssm_smtp_password', array( 'sanitize_callback' => array( $this, 'sanitize_password' ) ) );

        // From Settings.
        register_setting( 'ssm_settings', 'ssm_from_email', array( 'sanitize_callback' => 'sanitize_email' ) );
        register_setting( 'ssm_settings', 'ssm_from_name', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ssm_settings', 'ssm_force_from_email', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'ssm_settings', 'ssm_force_from_name', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );

        // Logging Settings.
        register_setting( 'ssm_settings', 'ssm_enable_logging', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'ssm_settings', 'ssm_log_retention_days', array( 'sanitize_callback' => 'absint' ) );

        // Queue Settings.
        register_setting( 'ssm_settings', 'ssm_enable_queue', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'ssm_settings', 'ssm_queue_batch_size', array( 'sanitize_callback' => 'absint' ) );
        register_setting( 'ssm_settings', 'ssm_queue_interval', array( 'sanitize_callback' => 'absint' ) );

        // Backup SMTP Settings.
        register_setting( 'ssm_settings', 'ssm_enable_backup_smtp', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'ssm_settings', 'ssm_backup_smtp_host', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ssm_settings', 'ssm_backup_smtp_port', array( 'sanitize_callback' => 'absint' ) );
        register_setting( 'ssm_settings', 'ssm_backup_smtp_encryption', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ssm_settings', 'ssm_backup_smtp_username', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'ssm_settings', 'ssm_backup_smtp_password', array( 'sanitize_callback' => array( $this, 'sanitize_password' ) ) );

        // Debug Settings.
        register_setting( 'ssm_settings', 'ssm_debug_mode', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );

        // Privacy Settings (GDPR Compliance).
        register_setting( 'ssm_settings', 'ssm_privacy_exclude_content', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'ssm_settings', 'ssm_privacy_anonymize', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
    }

    /**
     * Sanitize and encrypt password.
     */
    public function sanitize_password( $value ) {
        if ( empty( $value ) ) {
            return '';
        }
        if ( SSM_Encryption::is_encrypted( $value ) ) {
            return $value;
        }
        return SSM_Encryption::encrypt( $value );
    }

    /**
     * Get all current settings.
     */
    public static function get_settings() {
        return array(
            'smtp_provider'          => get_option( 'ssm_smtp_provider', 'custom' ),
            'smtp_host'              => get_option( 'ssm_smtp_host', '' ),
            'smtp_port'              => get_option( 'ssm_smtp_port', 587 ),
            'smtp_encryption'        => get_option( 'ssm_smtp_encryption', 'tls' ),
            'smtp_auth'              => get_option( 'ssm_smtp_auth', true ),
            'smtp_username'          => get_option( 'ssm_smtp_username', '' ),
            'smtp_password'          => get_option( 'ssm_smtp_password', '' ),
            'from_email'             => get_option( 'ssm_from_email', get_option( 'admin_email' ) ),
            'from_name'              => get_option( 'ssm_from_name', get_bloginfo( 'name' ) ),
            'force_from_email'       => get_option( 'ssm_force_from_email', false ),
            'force_from_name'        => get_option( 'ssm_force_from_name', false ),
            'enable_logging'         => get_option( 'ssm_enable_logging', true ),
            'log_retention_days'     => get_option( 'ssm_log_retention_days', 30 ),
            'enable_queue'           => get_option( 'ssm_enable_queue', false ),
            'queue_batch_size'       => get_option( 'ssm_queue_batch_size', 10 ),
            'queue_interval'         => get_option( 'ssm_queue_interval', 5 ),
            'enable_backup_smtp'     => get_option( 'ssm_enable_backup_smtp', false ),
            'backup_smtp_host'       => get_option( 'ssm_backup_smtp_host', '' ),
            'backup_smtp_port'       => get_option( 'ssm_backup_smtp_port', 587 ),
            'backup_smtp_encryption' => get_option( 'ssm_backup_smtp_encryption', 'tls' ),
            'backup_smtp_username'   => get_option( 'ssm_backup_smtp_username', '' ),
            'backup_smtp_password'   => get_option( 'ssm_backup_smtp_password', '' ),
            'debug_mode'             => get_option( 'ssm_debug_mode', false ),
        );
    }
}

new SSM_Settings();
