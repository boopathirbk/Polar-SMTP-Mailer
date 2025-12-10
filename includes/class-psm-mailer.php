<?php
/**
 * Mailer class.
 *
 * Handles SMTP configuration and email sending via PHPMailer.
 *
 * @package PolarSmtpMailer
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Mailer class.
 *
 * @since 1.0.0
 */
#[AllowDynamicProperties]
class PSM_Mailer {

    /**
     * Whether SMTP is enabled.
     *
     * @var bool
     */
    private $enabled = false;

    /**
     * SMTP settings.
     *
     * @var array
     */
    private $settings = array();

    /**
     * Current email data for logging.
     *
     * @var array
     */
    private $current_email = array();

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->load_settings();
        $this->init_hooks();
    }

    /**
     * Load SMTP settings.
     *
     * @since 1.0.0
     * @return void
     */
    private function load_settings() {
        $this->settings = array(
            'host'           => get_option( 'PSM_smtp_host', '' ),
            'port'           => (int) get_option( 'PSM_smtp_port', 587 ),
            'encryption'     => get_option( 'PSM_smtp_encryption', 'tls' ),
            'auth'           => (bool) get_option( 'PSM_smtp_auth', true ),
            'username'       => get_option( 'PSM_smtp_username', '' ),
            'password'       => get_option( 'PSM_smtp_password', '' ),
            'from_email'     => get_option( 'PSM_from_email', get_option( 'admin_email' ) ),
            'from_name'      => get_option( 'PSM_from_name', get_bloginfo( 'name' ) ),
            'force_from_email' => (bool) get_option( 'PSM_force_from_email', false ),
            'force_from_name'  => (bool) get_option( 'PSM_force_from_name', false ),
            'debug_mode'     => (bool) get_option( 'PSM_debug_mode', false ),
        );

        // Decrypt password if encrypted.
        if ( ! empty( $this->settings['password'] ) && PSM_Encryption::is_encrypted( $this->settings['password'] ) ) {
            $decrypted = PSM_Encryption::decrypt( $this->settings['password'] );
            
            // Handle decryption failure (e.g., if AUTH_KEY changed).
            if ( false === $decrypted ) {
                $this->settings['password'] = '';
                // Log warning once per request to avoid log spam.
                if ( ! defined( 'PSM_DECRYPTION_WARNING_LOGGED' ) ) {
                    define( 'PSM_DECRYPTION_WARNING_LOGGED', true );
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                    error_log( 'Polar SMTP Mailer: Password decryption failed. This may happen if AUTH_KEY changed. Please re-enter your SMTP password in settings.' );
                }
            } else {
                $this->settings['password'] = $decrypted;
            }
        }

        // Check if SMTP is configured.
        $this->enabled = ! empty( $this->settings['host'] );
    }

    /**
     * Initialize hooks.
     *
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        // Hook into PHPMailer configuration.
        add_action( 'phpmailer_init', array( $this, 'configure_phpmailer' ), 10, 1 );

        // Hook before mail is sent to capture data.
        add_filter( 'wp_mail', array( $this, 'capture_mail_data' ), 10, 1 );

        // Hook for mail failure.
        add_action( 'wp_mail_failed', array( $this, 'handle_mail_failed' ), 10, 1 );
    }

    /**
     * Configure PHPMailer with SMTP settings.
     *
     * @since 1.0.0
     * @param PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer instance.
     * @return void
     */
    public function configure_phpmailer( $phpmailer ) {
        if ( ! $this->enabled ) {
            return;
        }

        try {
            // Set mailer to SMTP.
            $phpmailer->isSMTP();

            // SMTP Host.
            $phpmailer->Host = $this->settings['host'];

            // SMTP Port.
            $phpmailer->Port = $this->settings['port'];

            // SMTP Encryption.
            if ( 'none' !== $this->settings['encryption'] ) {
                $phpmailer->SMTPSecure = $this->settings['encryption'];
            } else {
                $phpmailer->SMTPSecure = '';
                $phpmailer->SMTPAutoTLS = false;
            }

            // SMTP Authentication.
            if ( $this->settings['auth'] && ! empty( $this->settings['username'] ) ) {
                $phpmailer->SMTPAuth = true;
                $phpmailer->Username = $this->settings['username'];
                $phpmailer->Password = $this->settings['password'];
            } else {
                $phpmailer->SMTPAuth = false;
            }

            // From Email.
            if ( $this->settings['force_from_email'] && ! empty( $this->settings['from_email'] ) ) {
                $phpmailer->From = $this->settings['from_email'];
                $phpmailer->Sender = $this->settings['from_email'];
            } elseif ( ! empty( $this->settings['from_email'] ) && empty( $phpmailer->From ) ) {
                $phpmailer->From = $this->settings['from_email'];
            }

            // From Name.
            if ( $this->settings['force_from_name'] && ! empty( $this->settings['from_name'] ) ) {
                $phpmailer->FromName = $this->settings['from_name'];
            } elseif ( ! empty( $this->settings['from_name'] ) && empty( $phpmailer->FromName ) ) {
                $phpmailer->FromName = $this->settings['from_name'];
            }

            // Force 'Sender' (Return-Path) to match 'From' email to satisfy strict SMTP providers (Hostinger, etc).
            if ( ! empty( $phpmailer->From ) ) {
                $phpmailer->Sender = $phpmailer->From;
            }

            // Debug mode.
            if ( $this->settings['debug_mode'] ) {
                $phpmailer->SMTPDebug = 2;
                $phpmailer->Debugoutput = function( $str, $level ) {
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                    error_log( "Polar SMTP Mailer Debug [$level]: $str" );
                };
            }

            // Set timeout.
            $phpmailer->Timeout = 30;

            // Set character encoding.
            $phpmailer->CharSet = 'UTF-8';

            /**
             * Fires after PHPMailer is configured.
             *
             * @since 1.0.0
             * @param PHPMailer\PHPMailer\PHPMailer $phpmailer PHPMailer instance.
             * @param array $settings SMTP settings.
             */
            do_action( 'PSM_phpmailer_configured', $phpmailer, $this->settings );

        } catch ( \Exception $e ) {
            // Log error if debug mode is enabled.
            if ( $this->settings['debug_mode'] ) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging for configuration errors.
                error_log( 'Polar SMTP Mailer: PHPMailer configuration error: ' . $e->getMessage() );
            }
        }
    }

    /**
     * Capture mail data before sending.
     *
     * @since 1.0.0
     * @param array $args Mail arguments.
     * @return array Modified mail arguments.
     */
    public function capture_mail_data( $args ) {
        // Store current email data for logging.
        $this->current_email = array(
            'to'          => $args['to'],
            'subject'     => $args['subject'],
            'message'     => $args['message'],
            'headers'     => $args['headers'],
            'attachments' => $args['attachments'],
        );

        // Parse headers.
        $parsed_headers = $this->parse_headers( $args['headers'] );

        // Store CC and BCC.
        $this->current_email['cc']  = isset( $parsed_headers['cc'] ) ? $parsed_headers['cc'] : '';
        $this->current_email['bcc'] = isset( $parsed_headers['bcc'] ) ? $parsed_headers['bcc'] : '';

        return $args;
    }

    /**
     * Parse email headers.
     *
     * @since 1.0.0
     * @param string|array $headers Email headers.
     * @return array Parsed headers.
     */
    private function parse_headers( $headers ) {
        $parsed = array();

        if ( is_string( $headers ) ) {
            $headers = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
        }

        if ( is_array( $headers ) ) {
            foreach ( $headers as $header ) {
                if ( strpos( $header, ':' ) !== false ) {
                    list( $name, $value ) = explode( ':', $header, 2 );
                    $name = strtolower( trim( $name ) );
                    $value = trim( $value );
                    $parsed[ $name ] = $value;
                }
            }
        }

        return $parsed;
    }

    /**
     * Handle mail failure.
     *
     * @since 1.0.0
     * @param WP_Error $error Mail error.
     * @return void
     */
    public function handle_mail_failed( $error ) {
        if ( ! get_option( 'PSM_enable_logging', true ) ) {
            return;
        }

        $error_message = $error->get_error_message();
        $error_data = $error->get_error_data();

        // Log the failed email.
        PSM_DB::insert_log( array(
            'to_email'      => is_array( $this->current_email['to'] ) ? implode( ', ', $this->current_email['to'] ) : $this->current_email['to'],
            'cc_email'      => $this->current_email['cc'],
            'bcc_email'     => $this->current_email['bcc'],
            'subject'       => $this->current_email['subject'],
            'message'       => $this->current_email['message'],
            'headers'       => $this->current_email['headers'],
            'attachments'   => $this->current_email['attachments'],
            'status'        => 'failed',
            'error_message' => $error_message,
            'provider'      => $this->get_provider_name(),
            'mailer_type'   => 'smtp',
            'created_at'    => current_time( 'mysql' ),
        ) );

        // Try backup SMTP if enabled.
        if ( get_option( 'PSM_enable_backup_smtp', false ) ) {
            $this->try_backup_smtp();
        }
    }

    /**
     * Try sending with backup SMTP.
     *
     * @since 1.0.0
     * @return bool True if sent successfully.
     */
    private function try_backup_smtp() {
        $backup_host = get_option( 'PSM_backup_smtp_host', '' );

        if ( empty( $backup_host ) ) {
            return false;
        }

        // Log attempt.
        if ( get_option( 'PSM_enable_logging', true ) ) {
            PSM_DB::insert_log( array(
                'to_email'      => is_array( $this->current_email['to'] ) ? implode( ', ', $this->current_email['to'] ) : $this->current_email['to'],
                'subject'       => $this->current_email['subject'],
                'message'       => 'Backup SMTP Attempted. Primary failed.', // Internal note.
                'status'        => 'backup_attempt',
                'provider'      => 'backup_smtp', // Distinguish provider.
                'created_at'    => current_time( 'mysql' ),
            ) );
        }

        // Store original settings.
        $original_settings = $this->settings;

        // Load backup settings.
        $this->settings = array(
            'host'       => $backup_host,
            'port'       => (int) get_option( 'PSM_backup_smtp_port', 587 ),
            'encryption' => get_option( 'PSM_backup_smtp_encryption', 'tls' ),
            'auth'       => true,
            'username'   => get_option( 'PSM_backup_smtp_username', '' ),
            'password'   => PSM_Encryption::decrypt( get_option( 'PSM_backup_smtp_password', '' ) ),
        );

        // Temporarily disable failure hook to prevent recursion loop if backup fails too.
        remove_action( 'wp_mail_failed', array( $this, 'handle_mail_failed' ) );

        // Try sending again.
        $result = wp_mail(
            $this->current_email['to'],
            $this->current_email['subject'],
            $this->current_email['message'],
            $this->current_email['headers'],
            $this->current_email['attachments']
        );

        // Log result of backup attempt.
        if ( ! $result && get_option( 'PSM_enable_logging', true ) ) {
            // Note: detailed PHPMailer error unavailable here as wp_mail returns false, 
            // but we can log that backup failed.
             PSM_DB::insert_log( array(
                'to_email'      => is_array( $this->current_email['to'] ) ? implode( ', ', $this->current_email['to'] ) : $this->current_email['to'],
                'subject'       => $this->current_email['subject'],
                'status'        => 'failed',
                'error_message' => 'Backup SMTP also failed.',
                'provider'      => 'backup_smtp',
                'created_at'    => current_time( 'mysql' ),
            ) );
        }

        // Restore hooks.
        add_action( 'wp_mail_failed', array( $this, 'handle_mail_failed' ), 10, 1 );

        // Restore original settings.
        $this->settings = $original_settings;

        return $result;
    }

    /**
     * Log successful email.
     *
     * @since 1.0.0
     * @param array $email_data Email data.
     * @return int|false Log ID or false.
     */
    public function log_success( $email_data ) {
        if ( ! get_option( 'PSM_enable_logging', true ) ) {
            return false;
        }

        return PSM_DB::insert_log( array(
            'to_email'    => is_array( $email_data['to'] ) ? implode( ', ', $email_data['to'] ) : $email_data['to'],
            'cc_email'    => isset( $email_data['cc'] ) ? $email_data['cc'] : '',
            'bcc_email'   => isset( $email_data['bcc'] ) ? $email_data['bcc'] : '',
            'subject'     => $email_data['subject'],
            'message'     => $email_data['message'],
            'headers'     => $email_data['headers'],
            'attachments' => $email_data['attachments'],
            'status'      => 'sent',
            'provider'    => $this->get_provider_name(),
            'mailer_type' => 'smtp',
            'sent_at'     => current_time( 'mysql' ),
            'created_at'  => current_time( 'mysql' ),
        ) );
    }

    /**
     * Get current provider name.
     *
     * @since 1.0.0
     * @return string Provider name.
     */
    private function get_provider_name() {
        return PSM_Providers::get_provider_name_from_host( $this->settings['host'] );
    }

    /**
     * Test SMTP connection.
     *
     * @since 1.0.0
     * @param array $settings Optional settings to test. Uses saved settings if not provided.
     * @return array Result with success status and message.
     */
    public function test_connection( $settings = array() ) {
        // Use provided settings or saved settings.
        if ( empty( $settings ) ) {
            $settings = $this->settings;
        }

        // Validate required fields.
        if ( empty( $settings['host'] ) ) {
            return array(
                'success' => false,
                'message' => __( 'SMTP host is required.', 'polar-smtp-mailer' ),
            );
        }

        // Try to create a socket connection.
        $host = $settings['host'];
        $port = (int) $settings['port'];

        // Determine if SSL is required.
        $context = stream_context_create();
        $socket_host = $host;

        if ( 'ssl' === $settings['encryption'] ) {
            $socket_host = 'ssl://' . $host;
        }

        // Attempt connection with timeout.
        $errno = 0;
        $errstr = '';

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
        $connection = @stream_socket_client(
            "$socket_host:$port",
            $errno,
            $errstr,
            10, // 10 second timeout
            STREAM_CLIENT_CONNECT,
            $context
        );

        if ( ! $connection ) {
            return array(
                'success' => false,
                'message' => sprintf(
                    /* translators: 1: Error code, 2: Error message */
                    __( 'Could not connect to SMTP server. Error %1$s: %2$s', 'polar-smtp-mailer' ),
                    $errno,
                    $errstr
                ),
            );
        }

        // Read initial response.
        $response = fgets( $connection, 512 );

        // Check for 220 response (ready).
        if ( 0 !== strpos( $response, '220' ) ) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Required for SMTP socket.
            fclose( $connection );
            return array(
                'success' => false,
                'message' => sprintf(
                    /* translators: %s: Server response */
                    __( 'Unexpected server response: %s', 'polar-smtp-mailer' ),
                    trim( $response )
                ),
            );
        }

        // Send EHLO.
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Required for SMTP socket.
        fwrite( $connection, "EHLO " . wp_parse_url( home_url(), PHP_URL_HOST ) . "\r\n" );
        $response = '';
        $max_lines = 100; // Prevent infinite loop.
        $line_count = 0;
        while ( ( $line = fgets( $connection, 512 ) ) && $line_count < $max_lines ) {
            $response .= $line;
            $line_count++;
            if ( ' ' === substr( $line, 3, 1 ) ) {
                break;
            }
        }

        // Check for STARTTLS if TLS encryption.
        if ( 'tls' === $settings['encryption'] ) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Required for SMTP socket.
            fwrite( $connection, "STARTTLS\r\n" );
            $tls_response = fgets( $connection, 512 );

            if ( 0 !== strpos( $tls_response, '220' ) ) {
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Required for SMTP socket.
                fclose( $connection );
                return array(
                    'success' => false,
                    'message' => __( 'Server does not support STARTTLS.', 'polar-smtp-mailer' ),
                );
            }

            // Enable TLS with modern protocols (TLS 1.2 and 1.3).
            $crypto_method = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
            if ( defined( 'STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT' ) ) {
                $crypto_method |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
            }
            if ( ! stream_socket_enable_crypto( $connection, true, $crypto_method ) ) {
                // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Required for SMTP socket.
                fclose( $connection );
                return array(
                    'success' => false,
                    'message' => __( 'Could not enable TLS encryption.', 'polar-smtp-mailer' ),
                );
            }

            // Send EHLO again after TLS.
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Required for SMTP socket.
            fwrite( $connection, "EHLO " . wp_parse_url( home_url(), PHP_URL_HOST ) . "\r\n" );
            $line_count = 0;
            while ( ( $line = fgets( $connection, 512 ) ) && $line_count < $max_lines ) {
                $line_count++;
                if ( ' ' === substr( $line, 3, 1 ) ) {
                    break;
                }
            }
        }

        // Test authentication if enabled.
        if ( $settings['auth'] && ! empty( $settings['username'] ) ) {
            // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Required for SMTP socket.
            fwrite( $connection, "AUTH LOGIN\r\n" );
            $auth_response = fgets( $connection, 512 );

            if ( 0 === strpos( $auth_response, '334' ) ) {
                // Send username.
                // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode, WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
                fwrite( $connection, base64_encode( $settings['username'] ) . "\r\n" );
                fgets( $connection, 512 );

                // Send password.
                $password = $settings['password'];
                if ( PSM_Encryption::is_encrypted( $password ) ) {
                    $password = PSM_Encryption::decrypt( $password );
                }
                // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode, WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
                fwrite( $connection, base64_encode( $password ) . "\r\n" );
                $auth_result = fgets( $connection, 512 );

                if ( 0 !== strpos( $auth_result, '235' ) ) {
                    // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Required for SMTP socket.
                    fclose( $connection );

                    // Log failed authentication attempt for security monitoring.
                    $this->log_auth_failure( $settings['host'], $settings['username'] );

                    return array(
                        'success' => false,
                        'message' => __( 'SMTP authentication failed. Please check your username and password.', 'polar-smtp-mailer' ),
                    );
                }
            }
        }

        // Send QUIT.
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite -- Required for SMTP socket.
        fwrite( $connection, "QUIT\r\n" );
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- Required for SMTP socket.
        fclose( $connection );

        return array(
            'success' => true,
            'message' => __( 'SMTP connection successful! Your settings are configured correctly.', 'polar-smtp-mailer' ),
        );
    }

    /**
     * Send test email.
     *
     * @since 1.0.0
     * @param string $to Recipient email address.
     * @return array Result with success status and message.
     */
    public function send_test_email( $to ) {
        if ( ! is_email( $to ) ) {
            return array(
                'success' => false,
                'message' => __( 'Invalid email address.', 'polar-smtp-mailer' ),
            );
        }

        $subject = sprintf(
            /* translators: %s: Site name */
            __( 'Test Email from %s', 'polar-smtp-mailer' ),
            get_bloginfo( 'name' )
        );

        $message = sprintf(
            /* translators: 1: Site name, 2: Current date and time */
            __(
                "This is a test email from Polar SMTP Mailer.\n\nIf you received this email, your SMTP settings are configured correctly.\n\nSite: %1\$s\nDate: %2\$s\n\nThank you for using Polar SMTP Mailer!",
                'polar-smtp-mailer'
            ),
            get_bloginfo( 'name' ),
            wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) )
        );

        $from_email = $this->settings['from_email'];
        $from_name  = $this->settings['from_name'];

        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            sprintf( 'From: %s <%s>', $from_name, $from_email ),
        );

        // Send the email.
        $result = wp_mail( $to, $subject, $message, $headers );

        if ( $result ) {
            // Log successful test email.
            $this->log_success( array(
                'to'          => $to,
                'subject'     => $subject,
                'message'     => $message,
                'headers'     => $headers,
                'attachments' => array(),
            ) );

            return array(
                'success' => true,
                'message' => sprintf(
                    /* translators: %s: Recipient email */
                    __( 'Test email sent successfully to %s!', 'polar-smtp-mailer' ),
                    $to
                ),
            );
        }

        return array(
            'success' => false,
            'message' => __( 'Failed to send test email. Please check your SMTP settings and error logs.', 'polar-smtp-mailer' ),
        );
    }

    /**
     * Check if SMTP is enabled.
     *
     * @since 1.0.0
     * @return bool
     */
    public function is_enabled() {
        return $this->enabled;
    }

    /**
     * Get current settings.
     *
     * @since 1.0.0
     * @return array
     */
    public function get_settings() {
        return $this->settings;
    }

    /**
     * Log failed authentication attempt for security monitoring.
     *
     * @since 1.0.0
     * @param string $host     SMTP host.
     * @param string $username SMTP username.
     * @return void
     */
    private function log_auth_failure( $host, $username ) {
        $log_data = array(
            'type'      => 'smtp_auth_failure',
            'host'      => $host,
            'username'  => $username,
            'ip'        => $this->get_client_ip(),
            'user_id'   => get_current_user_id(),
            'timestamp' => current_time( 'mysql' ),
        );

        // Store in transient for admin review (last 50 failures).
        $failures = get_option( 'PSM_auth_failures', array() );
        array_unshift( $failures, $log_data );
        $failures = array_slice( $failures, 0, 50 ); // Keep only last 50.
        
        // Ensure option is not autoloaded to prevent performance issues.
        if ( false === get_option( 'PSM_auth_failures' ) ) {
            add_option( 'PSM_auth_failures', $failures, '', 'no' );
        } else {
            update_option( 'PSM_auth_failures', $failures );
        }

        // Also log to error log if debug mode is enabled.
        if ( $this->settings['debug_mode'] ) {
            // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
            error_log( sprintf(
                'Polar SMTP Mailer: Authentication failure - Host: %s, Username: %s, IP: %s, User ID: %d',
                $host,
                $username,
                $log_data['ip'],
                $log_data['user_id']
            ) );
        }

        /**
         * Fires when SMTP authentication fails.
         *
         * @since 1.0.0
         * @param array $log_data Failed authentication data.
         */
        do_action( 'PSM_auth_failure', $log_data );
    }

    /**
     * Get client IP address.
     *
     * @since 1.0.0
     * @return string Client IP address.
     */
    private function get_client_ip() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP', // Cloudflare.
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        );

        foreach ( $ip_keys as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                $ip = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
                // Handle comma-separated IPs (X-Forwarded-For).
                if ( strpos( $ip, ',' ) !== false ) {
                    $ips = explode( ',', $ip );
                    $ip = trim( $ips[0] );
                }
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }
}
