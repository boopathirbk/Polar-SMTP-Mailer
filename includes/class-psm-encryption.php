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
     * Generate a random initialization vector.
     *
     * @since 1.0.1
     * @return string Random IV (16 bytes for AES-256-CBC).
     */
    private static function generate_iv() {
        $iv_length = openssl_cipher_iv_length( self::$method );
        return openssl_random_pseudo_bytes( $iv_length );
    }

    /**
     * Get the legacy initialization vector for backward compatibility.
     *
     * Uses WordPress SECURE_AUTH_KEY or generates a fallback IV.
     *
     * @since 1.0.0
     * @deprecated 1.0.1 Use generate_iv() for new encryptions.
     * @return string The IV (16 bytes for AES-256-CBC).
     */
    private static function get_legacy_iv() {
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
        $iv  = self::generate_iv();

        $encrypted = openssl_encrypt( $data, self::$method, $key, OPENSSL_RAW_DATA, $iv );

        if ( false === $encrypted ) {
            return false;
        }

        // Prepend IV to encrypted data for secure storage (enc2: prefix for new format).
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
        return 'enc2:' . base64_encode( $iv . $encrypted );
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
        if ( 0 !== strpos( $data, 'enc:' ) && 0 !== strpos( $data, 'enc2:' ) ) {
            return $data;
        }

        // Check if OpenSSL is available.
        if ( ! function_exists( 'openssl_decrypt' ) ) {
            return false;
        }

        $key = self::get_key();

        // Handle new format with random IV (enc2:).
        if ( 0 === strpos( $data, 'enc2:' ) ) {
            // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
            $decoded = base64_decode( substr( $data, 5 ) );
            $iv_length = openssl_cipher_iv_length( self::$method );
            $iv = substr( $decoded, 0, $iv_length );
            $encrypted = substr( $decoded, $iv_length );

            return openssl_decrypt( $encrypted, self::$method, $key, OPENSSL_RAW_DATA, $iv );
        }

        // Handle legacy format with static IV (enc:).
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
        $decoded = base64_decode( substr( $data, 4 ) );

        return openssl_decrypt( $decoded, self::$method, $key, 0, self::get_legacy_iv() );
    }

    /**
     * Check if a value is encrypted.
     *
     * @since 1.0.0
     * @param string $data The data to check.
     * @return bool True if encrypted, false otherwise.
     */
    public static function is_encrypted( $data ) {
        return ( 0 === strpos( $data, 'enc:' ) || 0 === strpos( $data, 'enc2:' ) || 0 === strpos( $data, 'base64:' ) );
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
