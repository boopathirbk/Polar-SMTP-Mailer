<?php
/**
 * Settings class.
 *
 * @package PolarSmtpMailer
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Settings class.
 */
class PSM_Settings {

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
        register_setting( 'PSM_settings', 'PSM_smtp_provider', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'PSM_settings', 'PSM_smtp_host', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'PSM_settings', 'PSM_smtp_port', array( 'sanitize_callback' => 'absint' ) );
        register_setting( 'PSM_settings', 'PSM_smtp_encryption', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'PSM_settings', 'PSM_smtp_auth', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'PSM_settings', 'PSM_smtp_username', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'PSM_settings', 'PSM_smtp_password', array( 'sanitize_callback' => array( $this, 'sanitize_smtp_password' ) ) );

        // From Settings.
        register_setting( 'PSM_settings', 'PSM_from_email', array( 'sanitize_callback' => 'sanitize_email' ) );
        register_setting( 'PSM_settings', 'PSM_from_name', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'PSM_settings', 'PSM_force_from_email', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'PSM_settings', 'PSM_force_from_name', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );

        // Logging Settings.
        register_setting( 'PSM_settings', 'PSM_enable_logging', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'PSM_settings', 'PSM_log_retention_days', array( 'sanitize_callback' => 'absint' ) );

        // Queue Settings.
        register_setting( 'PSM_settings', 'PSM_enable_queue', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'PSM_settings', 'PSM_queue_batch_size', array( 'sanitize_callback' => 'absint' ) );
        register_setting( 'PSM_settings', 'PSM_queue_interval', array( 'sanitize_callback' => 'absint' ) );

        // Backup SMTP Settings.
        register_setting( 'PSM_settings', 'PSM_enable_backup_smtp', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'PSM_settings', 'PSM_backup_smtp_provider', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'PSM_settings', 'PSM_backup_smtp_host', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'PSM_settings', 'PSM_backup_smtp_port', array( 'sanitize_callback' => 'absint' ) );
        register_setting( 'PSM_settings', 'PSM_backup_smtp_encryption', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'PSM_settings', 'PSM_backup_smtp_username', array( 'sanitize_callback' => 'sanitize_text_field' ) );
        register_setting( 'PSM_settings', 'PSM_backup_smtp_password', array( 'sanitize_callback' => array( $this, 'sanitize_backup_password' ) ) );

        // Debug Settings.
        register_setting( 'PSM_settings', 'PSM_debug_mode', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );

        // Privacy Settings (GDPR Compliance).
        register_setting( 'PSM_settings', 'PSM_privacy_exclude_content', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        register_setting( 'PSM_settings', 'PSM_privacy_anonymize', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
        
        // Uninstall Settings.
        register_setting( 'PSM_settings', 'PSM_delete_data_on_uninstall', array( 'sanitize_callback' => 'rest_sanitize_boolean' ) );
    }

    /**
     * Password placeholder constant.
     * Using a unique prefix to avoid collision with real passwords.
     *
     * @var string
     */
    const PASSWORD_PLACEHOLDER = '••••••••••••';
    const PASSWORD_UNCHANGED_TOKEN = '__PSM_UNCHANGED__';

    /**
     * Sanitize and encrypt SMTP password.
     *
     * @since 1.0.0
     * @param string $value Password value.
     * @return string Encrypted password or existing value.
     */
    public function sanitize_smtp_password( $value ) {
        // If value is the placeholder or unchanged token, return existing option.
        if ( self::PASSWORD_PLACEHOLDER === $value || self::PASSWORD_UNCHANGED_TOKEN === $value ) {
            return get_option( 'PSM_smtp_password' );
        }
        return $this->sanitize_password_common( $value );
    }

    /**
     * Sanitize and encrypt Backup SMTP password.
     *
     * @since 1.0.0
     * @param string $value Password value.
     * @return string Encrypted password or existing value.
     */
    public function sanitize_backup_password( $value ) {
        // If value is the placeholder or unchanged token, return existing option.
        if ( self::PASSWORD_PLACEHOLDER === $value || self::PASSWORD_UNCHANGED_TOKEN === $value ) {
            return get_option( 'PSM_backup_smtp_password' );
        }
        return $this->sanitize_password_common( $value );
    }

    /**
     * Common password sanitization.
     *
     * @since 1.0.0
     * @param string $value Password value.
     * @return string Encrypted password or empty string.
     */
    private function sanitize_password_common( $value ) {
        if ( empty( $value ) ) {
            return '';
        }
        if ( PSM_Encryption::is_encrypted( $value ) ) {
            return $value;
        }
        return PSM_Encryption::encrypt( $value );
    }

    /**
     * Get all current settings.
     */
    public static function get_settings() {
        // Get raw password values.
        $smtp_password = get_option( 'PSM_smtp_password', '' );
        $backup_smtp_password = get_option( 'PSM_backup_smtp_password', '' );

        // For display purposes, show placeholder if password exists (don't show encrypted value).
        $smtp_password_display = ! empty( $smtp_password ) ? '••••••••••••' : '';
        $backup_password_display = ! empty( $backup_smtp_password ) ? '••••••••••••' : '';

        return array(
            'smtp_provider'          => get_option( 'PSM_smtp_provider', 'custom' ),
            'smtp_host'              => get_option( 'PSM_smtp_host', '' ),
            'smtp_port'              => get_option( 'PSM_smtp_port', 587 ),
            'smtp_encryption'        => get_option( 'PSM_smtp_encryption', 'tls' ),
            'smtp_auth'              => get_option( 'PSM_smtp_auth', true ),
            'smtp_username'          => get_option( 'PSM_smtp_username', '' ),
            'smtp_password'          => $smtp_password_display,
            'from_email'             => get_option( 'PSM_from_email', get_option( 'admin_email' ) ),
            'from_name'              => get_option( 'PSM_from_name', get_bloginfo( 'name' ) ),
            'force_from_email'       => get_option( 'PSM_force_from_email', false ),
            'force_from_name'        => get_option( 'PSM_force_from_name', false ),
            'enable_logging'         => get_option( 'PSM_enable_logging', true ),
            'log_retention_days'     => get_option( 'PSM_log_retention_days', 30 ),
            'enable_queue'           => get_option( 'PSM_enable_queue', false ),
            'queue_batch_size'       => get_option( 'PSM_queue_batch_size', 10 ),
            'queue_interval'         => get_option( 'PSM_queue_interval', 5 ),
            'enable_backup_smtp'     => get_option( 'PSM_enable_backup_smtp', false ),
            'backup_smtp_provider'   => get_option( 'PSM_backup_smtp_provider', 'custom' ),
            'backup_smtp_host'       => get_option( 'PSM_backup_smtp_host', '' ),
            'backup_smtp_port'       => get_option( 'PSM_backup_smtp_port', 587 ),
            'backup_smtp_encryption' => get_option( 'PSM_backup_smtp_encryption', 'tls' ),
            'backup_smtp_username'   => get_option( 'PSM_backup_smtp_username', '' ),
            'backup_smtp_password'   => $backup_password_display,
            'debug_mode'             => get_option( 'PSM_debug_mode', false ),
            'delete_data_on_uninstall' => get_option( 'PSM_delete_data_on_uninstall', false ),
        );
    }
}

new PSM_Settings();
