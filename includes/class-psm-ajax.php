<?php
/**
 * AJAX handler class.
 *
 * @package PolarSmtpMailer
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Ajax class.
 */
class PSM_Ajax {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'wp_ajax_PSM_test_connection', array( $this, 'test_connection' ) );
        add_action( 'wp_ajax_PSM_send_test_email', array( $this, 'send_test_email' ) );
        add_action( 'wp_ajax_PSM_view_log', array( $this, 'view_log' ) );
        add_action( 'wp_ajax_PSM_delete_log', array( $this, 'delete_log' ) );
        add_action( 'wp_ajax_PSM_resend_email', array( $this, 'resend_email' ) );
        add_action( 'wp_ajax_PSM_export_logs', array( $this, 'export_logs' ) );
        add_action( 'wp_ajax_PSM_get_stats', array( $this, 'get_stats' ) );
        add_action( 'wp_ajax_PSM_process_queue', array( $this, 'process_queue' ) );
        add_action( 'wp_ajax_PSM_get_provider', array( $this, 'get_provider' ) );
    }

    /**
     * Verify nonce and capability.
     *
     * @since 1.0.0
     * @return void
     */
    private function verify_request() {
        if ( ! check_ajax_referer( 'PSM_nonce', 'nonce', false ) ) {
            wp_send_json_error( array( 'message' => __( 'Security check failed.', 'polar-smtp-mailer' ) ) );
            exit; // Ensure execution stops.
        }
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'polar-smtp-mailer' ) ) );
            exit; // Ensure execution stops.
        }
    }

    /**
     * Test SMTP connection.
     */
    public function test_connection() {
        $this->verify_request();

        // Nonce verified in verify_request() above.
        // phpcs:disable WordPress.Security.NonceVerification.Missing
        $settings = array(
            'host'       => isset( $_POST['host'] ) ? sanitize_text_field( wp_unslash( $_POST['host'] ) ) : '',
            'port'       => isset( $_POST['port'] ) ? absint( $_POST['port'] ) : 587,
            'encryption' => isset( $_POST['encryption'] ) ? sanitize_text_field( wp_unslash( $_POST['encryption'] ) ) : 'tls',
            'auth'       => isset( $_POST['auth'] ) && 'true' === $_POST['auth'],
            'username'   => isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '',
            'password'   => isset( $_POST['password'] ) ? sanitize_text_field( wp_unslash( $_POST['password'] ) ) : '',
        );
        // phpcs:enable

        $mailer = new PSM_Mailer();
        $result = $mailer->test_connection( $settings );

        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * Send test email with rate limiting.
     *
     * @since 1.0.0
     */
    public function send_test_email() {
        $this->verify_request();

        // Rate limiting: max 5 test emails per 10 minutes.
        $user_id = get_current_user_id();
        $rate_limit_key = 'PSM_test_email_count_' . $user_id;
        $rate_limit_time_key = 'PSM_test_email_time_' . $user_id;
        $max_attempts = 5;
        $time_window = 600; // 10 minutes in seconds.

        $attempt_count = (int) get_transient( $rate_limit_key );
        $first_attempt_time = get_transient( $rate_limit_time_key );

        if ( $attempt_count >= $max_attempts && $first_attempt_time ) {
            $time_remaining = $time_window - ( time() - $first_attempt_time );
            if ( $time_remaining > 0 ) {
                wp_send_json_error( array(
                    'message' => sprintf(
                        /* translators: %d: Minutes remaining */
                        __( 'Rate limit exceeded. Please wait %d minutes before sending another test email.', 'polar-smtp-mailer' ),
                        ceil( $time_remaining / 60 )
                    ),
                ) );
                exit;
            }
            // Reset if time window has passed.
            delete_transient( $rate_limit_key );
            delete_transient( $rate_limit_time_key );
            $attempt_count = 0;
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request().
        $to = isset( $_POST['to'] ) ? sanitize_email( wp_unslash( $_POST['to'] ) ) : '';

        if ( ! is_email( $to ) ) {
            wp_send_json_error( array( 'message' => __( 'Invalid email address.', 'polar-smtp-mailer' ) ) );
            exit;
        }

        // Update rate limit counters.
        if ( 0 === $attempt_count ) {
            set_transient( $rate_limit_time_key, time(), $time_window );
        }
        set_transient( $rate_limit_key, $attempt_count + 1, $time_window );

        $mailer = new PSM_Mailer();
        $result = $mailer->send_test_email( $to );

        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * View email log.
     */
    public function view_log() {
        $this->verify_request();

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request().
        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid log ID.', 'polar-smtp-mailer' ) ) );
        }

        $log = PSM_DB::get_log( $id );

        if ( ! $log ) {
            wp_send_json_error( array( 'message' => __( 'Log not found.', 'polar-smtp-mailer' ) ) );
        }

        wp_send_json_success( array(
            'log' => array(
                'id'         => $log->id,
                'to_email'   => esc_html( $log->to_email ),
                'subject'    => esc_html( $log->subject ),
                'message'    => wp_kses_post( $log->message ),
                'headers'    => esc_html( $log->headers ),
                'status'     => esc_html( $log->status ),
                'error'      => esc_html( $log->error_message ),
                'provider'   => esc_html( $log->provider ),
                'sent_at'    => $log->sent_at,
                'created_at' => $log->created_at,
            ),
        ) );
    }

    /**
     * Delete email log.
     */
    public function delete_log() {
        $this->verify_request();

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request().
        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid log ID.', 'polar-smtp-mailer' ) ) );
        }

        $result = PSM_DB::delete_log( $id );

        if ( $result ) {
            wp_send_json_success( array( 'message' => __( 'Log deleted successfully.', 'polar-smtp-mailer' ) ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Failed to delete log.', 'polar-smtp-mailer' ) ) );
        }
    }

    /**
     * Resend email.
     */
    public function resend_email() {
        $this->verify_request();

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request().
        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

        if ( ! $id ) {
            wp_send_json_error( array( 'message' => __( 'Invalid log ID.', 'polar-smtp-mailer' ) ) );
        }

        $logger = new PSM_Logger();
        $result = $logger->resend_email( $id );

        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }

    /**
     * Export logs.
     */
    public function export_logs() {
        $this->verify_request();

        // phpcs:disable WordPress.Security.NonceVerification.Missing
        $format = isset( $_POST['format'] ) ? sanitize_text_field( wp_unslash( $_POST['format'] ) ) : 'csv';
        $status = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';
        // phpcs:enable

        $logger = new PSM_Logger();
        $args = array( 'status' => $status );

        if ( 'json' === $format ) {
            $content = $logger->export_json( $args );
            $filename = 'email-logs-' . gmdate( 'Y-m-d' ) . '.json';
            $mime = 'application/json';
        } else {
            $content = $logger->export_csv( $args );
            $filename = 'email-logs-' . gmdate( 'Y-m-d' ) . '.csv';
            $mime = 'text/csv';
        }

        wp_send_json_success( array(
            'content'  => $content,
            'filename' => $filename,
            'mime'     => $mime,
        ) );
    }

    /**
     * Get email statistics.
     */
    public function get_stats() {
        $this->verify_request();

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request().
        $period = isset( $_POST['period'] ) ? sanitize_text_field( wp_unslash( $_POST['period'] ) ) : 'all';

        $stats = PSM_DB::get_stats( $period );
        $daily = PSM_DB::get_daily_stats( 30 );

        wp_send_json_success( array(
            'stats' => $stats,
            'daily' => $daily,
        ) );
    }

    /**
     * Process email queue.
     */
    public function process_queue() {
        $this->verify_request();

        $queue = new PSM_Queue();
        $result = $queue->trigger_processing();

        wp_send_json_success( $result );
    }

    /**
     * Get provider settings.
     */
    public function get_provider() {
        $this->verify_request();

        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce verified in verify_request().
        $provider_key = isset( $_POST['provider'] ) ? sanitize_text_field( wp_unslash( $_POST['provider'] ) ) : '';
        $provider = PSM_Providers::get_provider( $provider_key );

        if ( $provider ) {
            wp_send_json_success( array( 'provider' => $provider ) );
        } else {
            wp_send_json_error( array( 'message' => __( 'Provider not found.', 'polar-smtp-mailer' ) ) );
        }
    }
}
