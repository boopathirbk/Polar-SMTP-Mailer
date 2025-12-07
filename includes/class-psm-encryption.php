<?php
/**
 * Encryption handler class.
 *
 * Provides secure encryption and decryption for sensitive data
 * like SMTP passwords using WordPress secret keys.
 *
 * @package PolarSmtpMailer
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Encryption class.
 *
 * @since 1.0.0
 */
class PSM_Encryption {

    /**
     * Encryption method.
     *
     * @var string
     */
    private static $method = 'aes-256-cbc';

    /**
     * Get the encryption key.
     *
     * Uses WordPress AUTH_KEY or generates a fallback key.
     *
     * @since 1.0.0
     * @return string The encryption key.
     */
    private static function get_key() {
        if ( defined( 'AUTH_KEY' ) && '' !== AUTH_KEY ) {
            return hash( 'sha256', AUTH_KEY . 'PSM_encryption' );
        }

        // Fallback key (less secure, but works).
        return hash( 'sha256', ABSPATH . 'PSM_fallback_key' );
    }

    /**
     * Get the initialization vector.
     *
     * Uses WordPress SECURE_AUTH_KEY or generates a fallback IV.
     *
     * @since 1.0.0
     * @return string The IV (16 bytes for AES-256-CBC).
     */
    private static function get_iv() {
        if ( defined( 'SECURE_AUTH_KEY' ) && '' !== SECURE_AUTH_KEY ) {
            return substr( hash( 'sha256', SECURE_AUTH_KEY . 'PSM_iv' ), 0, 16 );
        }

        // Fallback IV.
        return substr( hash( 'sha256', ABSPATH . 'PSM_fallback_iv' ), 0, 16 );
    }

    /**
     * Encrypt data.
     *
     * @since 1.0.0
     * @param string $data The data to encrypt.
     * @return string|false The encrypted data (base64 encoded) or false on failure.
     */
    public static function encrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }

        // Check if OpenSSL is available.
        if ( ! function_exists( 'openssl_encrypt' ) ) {
            // Return base64 encoded data as fallback (not secure, but allows functionality).
            // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
            return 'base64:' . base64_encode( $data );
        }

        $key = self::get_key();
        $iv  = self::get_iv();

        $encrypted = openssl_encrypt( $data, self::$method, $key, 0, $iv );

        if ( false === $encrypted ) {
            return false;
        }

        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
        return 'enc:' . base64_encode( $encrypted );
    }

    /**
     * Decrypt data.
     *
     * @since 1.0.0
     * @param string $data The encrypted data (base64 encoded).
     * @return string|false The decrypted data or false on failure.
     */
    public static function decrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }

        // Handle base64 fallback (from systems without OpenSSL).
        if ( 0 === strpos( $data, 'base64:' ) ) {
            // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
            return base64_decode( substr( $data, 7 ) );
        }

        // Handle unencrypted data (legacy or manual entry).
        if ( 0 !== strpos( $data, 'enc:' ) ) {
            return $data;
        }

        // Check if OpenSSL is available.
        if ( ! function_exists( 'openssl_decrypt' ) ) {
            return false;
        }

        $key  = self::get_key();
        $iv   = self::get_iv();
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
        $data = base64_decode( substr( $data, 4 ) );

        $decrypted = openssl_decrypt( $data, self::$method, $key, 0, $iv );

        return $decrypted;
    }

    /**
     * Check if a value is encrypted.
     *
     * @since 1.0.0
     * @param string $data The data to check.
     * @return bool True if encrypted, false otherwise.
     */
    public static function is_encrypted( $data ) {
        return ( 0 === strpos( $data, 'enc:' ) || 0 === strpos( $data, 'base64:' ) );
    }

    /**
     * Mask a value for display.
     *
     * @since 1.0.0
     * @param string $value The value to mask.
     * @param int    $visible Number of visible characters at start and end.
     * @return string Masked value.
     */
    public static function mask( $value, $visible = 2 ) {
        if ( empty( $value ) ) {
            return '';
        }

        $length = strlen( $value );

        if ( $length <= $visible * 2 ) {
            return str_repeat( '*', $length );
        }

        return substr( $value, 0, $visible ) . str_repeat( '*', $length - ( $visible * 2 ) ) . substr( $value, -$visible );
    }
}
