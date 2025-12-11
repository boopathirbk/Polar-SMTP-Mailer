<?php
/**
 * Logger class.
 *
 * Handles email logging and log management.
 *
 * @package PolarSmtpMailer
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Logger class.
 *
 * @since 1.0.0
 */
#[AllowDynamicProperties]
class PSM_Logger {

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     *
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        // Hook into successful mail sending.
        add_action( 'wp_mail_succeeded', array( $this, 'log_mail_success' ), 10, 1 );

        // Schedule log cleanup.
        if ( ! wp_next_scheduled( 'PSM_cleanup_logs' ) ) {
            wp_schedule_event( time(), 'daily', 'PSM_cleanup_logs' );
        }
        add_action( 'PSM_cleanup_logs', array( $this, 'cleanup_old_logs' ) );
    }

    /**
     * Log successful email.
     *
     * @since 1.0.0
     * @param array $mail_data Email data from WordPress.
     * @return void
     */
    public function log_mail_success( $mail_data ) {
        if ( ! get_option( 'PSM_enable_logging', true ) ) {
            return;
        }

        // Parse recipients.
        $to = isset( $mail_data['to'] ) ? $mail_data['to'] : array();
        if ( is_array( $to ) ) {
            $to = implode( ', ', $to );
        }

        // Parse headers for CC, BCC, and From.
        $headers = isset( $mail_data['headers'] ) ? $mail_data['headers'] : array();
        $parsed = $this->parse_headers( $headers );

        // Extract from_email - check parsed headers first, then fall back to plugin settings.
        $from_email = '';
        if ( isset( $parsed['from'] ) ) {
            // Extract email from "Name <email@example.com>" format.
            if ( preg_match( '/<([^>]+)>/', $parsed['from'], $matches ) ) {
                $from_email = $matches[1];
            } else {
                $from_email = $parsed['from'];
            }
        }
        if ( empty( $from_email ) ) {
            $from_email = get_option( 'PSM_from_email', get_option( 'admin_email' ) );
        }

        // Check privacy setting - exclude message content if enabled.
        $message = isset( $mail_data['message'] ) ? $mail_data['message'] : '';
        if ( get_option( 'PSM_privacy_exclude_content', false ) ) {
            $message = __( '[Content not logged for privacy]', 'polar-smtp-mailer' );
        }

        // Insert log.
        PSM_DB::insert_log( array(
            'to_email'    => $to,
            'from_email'  => $from_email,
            'cc_email'    => isset( $parsed['cc'] ) ? $parsed['cc'] : '',
            'bcc_email'   => isset( $parsed['bcc'] ) ? $parsed['bcc'] : '',
            'subject'     => isset( $mail_data['subject'] ) ? $mail_data['subject'] : '',
            'message'     => $message,
            'headers'     => $headers,
            'attachments' => $this->sanitize_attachments( isset( $mail_data['attachments'] ) ? $mail_data['attachments'] : array() ),
            'status'      => 'sent',
            'provider'    => $this->get_current_provider(),
            'mailer_type' => 'smtp',
            'sent_at'     => current_time( 'mysql' ),
            'created_at'  => current_time( 'mysql' ),
        ) );
    }

    /**
     * Sanitize attachments for logging.
     *
     * Prevents logging of large file contents if passed as string.
     *
     * @since 1.0.1
     * @param array|string $attachments Attachments.
     * @return array|string Sanitized attachments.
     */
    private function sanitize_attachments( $attachments ) {
        if ( empty( $attachments ) ) {
            return array();
        }

        if ( is_string( $attachments ) ) {
            // Check if it's a file path or potentially content.
            if ( strlen( $attachments ) > 255 && ! file_exists( $attachments ) ) {
                return __( '[Large attachment content truncated]', 'polar-smtp-mailer' );
            }
            return array( $attachments );
        }

        if ( is_array( $attachments ) ) {
            $sanitized = array();
            foreach ( $attachments as $attachment ) {
                if ( is_string( $attachment ) ) {
                    if ( strlen( $attachment ) > 255 && ! file_exists( $attachment ) ) {
                        $sanitized[] = __( '[Large attachment content truncated]', 'polar-smtp-mailer' );
                    } else {
                        $sanitized[] = $attachment;
                    }
                } elseif ( is_array( $attachment ) && isset( $attachment[0] ) ) {
                    // PHPMailer complex format [path, name, encoding, type]
                    $sanitized[] = $attachment[0]; // Log just the path.
                }
            }
            return $sanitized;
        }

        return $attachments;
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
                if ( is_string( $header ) && strpos( $header, ':' ) !== false ) {
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
     * Get current SMTP provider.
     *
     * @since 1.0.0
     * @return string Provider name.
     */
    private function get_current_provider() {
        return PSM_Providers::get_provider_name_from_host();
    }

    /**
     * Cleanup old logs based on retention settings.
     *
     * @since 1.0.0
     * @return int Number of logs deleted.
     */
    public function cleanup_old_logs() {
        $retention_days = (int) get_option( 'PSM_log_retention_days', 30 );

        if ( $retention_days <= 0 ) {
            return 0; // Keep logs forever.
        }

        return PSM_DB::cleanup_old_logs( $retention_days );
    }

    /**
     * Get log by ID.
     *
     * @since 1.0.0
     * @param int $id Log ID.
     * @return object|null Log object or null.
     */
    public function get_log( $id ) {
        return PSM_DB::get_log( $id );
    }

    /**
     * Get logs with filters.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return array Array of logs.
     */
    public function get_logs( $args = array() ) {
        return PSM_DB::get_logs( $args );
    }

    /**
     * Get total log count.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return int Total count.
     */
    public function get_logs_count( $args = array() ) {
        return PSM_DB::get_logs_count( $args );
    }

    /**
     * Delete log.
     *
     * @since 1.0.0
     * @param int $id Log ID.
     * @return bool True on success.
     */
    public function delete_log( $id ) {
        return PSM_DB::delete_log( $id );
    }

    /**
     * Bulk delete logs.
     *
     * @since 1.0.0
     * @param array $ids Array of log IDs.
     * @return int Number of deleted logs.
     */
    public function bulk_delete_logs( $ids ) {
        return PSM_DB::bulk_delete_logs( $ids );
    }

    /**
     * Resend email from log.
     *
     * @since 1.0.0
     * @param int $id Log ID.
     * @return array Result with success status and message.
     */
    public function resend_email( $id ) {
        $log = $this->get_log( $id );

        if ( ! $log ) {
            return array(
                'success' => false,
                'message' => __( 'Email log not found.', 'polar-smtp-mailer' ),
            );
        }

        // Parse headers back to array.
        $headers = $log->headers;
        if ( is_string( $headers ) ) {
            $decoded = json_decode( $headers, true );
            if ( is_array( $decoded ) ) {
                $headers = $decoded;
            }
        }

        // Parse attachments.
        $attachments = $log->attachments;
        if ( is_string( $attachments ) ) {
            $decoded = json_decode( $attachments, true );
            if ( is_array( $decoded ) ) {
                $attachments = $decoded;
            } else {
                $attachments = array();
            }
        }

        // Send the email.
        $result = wp_mail(
            $log->to_email,
            $log->subject,
            $log->message,
            $headers,
            $attachments
        );

        if ( $result ) {
            return array(
                'success' => true,
                'message' => sprintf(
                    /* translators: %s: Recipient email */
                    __( 'Email resent successfully to %s!', 'polar-smtp-mailer' ),
                    $log->to_email
                ),
            );
        }

        return array(
            'success' => false,
            'message' => __( 'Failed to resend email. Please check your SMTP settings.', 'polar-smtp-mailer' ),
        );
    }

    /**
     * Export logs to CSV.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return string CSV content.
     */
    public function export_csv( $args = array() ) {
        $csv_content = '';
        
        // Header row.
        $csv_content .= $this->array_to_csv_line( array(
            __( 'ID', 'polar-smtp-mailer' ),
            __( 'To', 'polar-smtp-mailer' ),
            __( 'CC', 'polar-smtp-mailer' ),
            __( 'BCC', 'polar-smtp-mailer' ),
            __( 'Subject', 'polar-smtp-mailer' ),
            __( 'Status', 'polar-smtp-mailer' ),
            __( 'Provider', 'polar-smtp-mailer' ),
            __( 'Error', 'polar-smtp-mailer' ),
            __( 'Sent At', 'polar-smtp-mailer' ),
            __( 'Created At', 'polar-smtp-mailer' ),
        ) );

        // Process in chunks to save memory.
        $chunk_size = 500;
        $page = 1;
        $args['per_page'] = $chunk_size;

        do {
            $args['page'] = $page;
            $logs = $this->get_logs( $args );
            
            if ( empty( $logs ) ) {
                break;
            }

            foreach ( $logs as $log ) {
                $csv_content .= $this->array_to_csv_line( array(
                    $log->id,
                    $log->to_email,
                    $log->cc_email,
                    $log->bcc_email,
                    $log->subject,
                    $log->status,
                    $log->provider,
                    $log->error_message,
                    $log->sent_at,
                    $log->created_at,
                ) );
            }

            // Clean up memory.
            unset( $logs );
            $page++;
            
            // Safety limit (e.g. 20,000 records max for now).
            if ( $page > 40 ) {
                break;
            }

        } while ( true );

        return $csv_content;
    }

    /**
     * Convert array to CSV line.
     *
     * @since 1.0.0
     * @param array $fields Array of fields.
     * @return string CSV formatted line.
     */
    private function array_to_csv_line( $fields ) {
        $csv_line = array();
        foreach ( $fields as $field ) {
            // Escape double quotes and wrap in quotes.
            $field = str_replace( '"', '""', (string) $field );
            $csv_line[] = '"' . $field . '"';
        }
        return implode( ',', $csv_line ) . "\n";
    }

    /**
     * Export logs to JSON.
     *
     * Uses chunked processing to prevent memory issues with large datasets.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return string JSON content.
     */
    public function export_json( $args = array() ) {
        $export = array();

        // Process in chunks to save memory.
        $chunk_size = 500;
        $page = 1;
        $args['per_page'] = $chunk_size;

        do {
            $args['page'] = $page;
            $logs = $this->get_logs( $args );

            if ( empty( $logs ) ) {
                break;
            }

            foreach ( $logs as $log ) {
                $export[] = array(
                    'id'            => $log->id,
                    'to_email'      => $log->to_email,
                    'cc_email'      => $log->cc_email,
                    'bcc_email'     => $log->bcc_email,
                    'subject'       => $log->subject,
                    'message'       => $log->message,
                    'status'        => $log->status,
                    'provider'      => $log->provider,
                    'error_message' => $log->error_message,
                    'sent_at'       => $log->sent_at,
                    'created_at'    => $log->created_at,
                );
            }

            // Clean up memory.
            unset( $logs );
            $page++;

            // Safety limit (e.g. 20,000 records max).
            if ( $page > 40 ) {
                break;
            }

        } while ( true );

        return wp_json_encode( $export, JSON_PRETTY_PRINT );
    }

    /**
     * Get email statistics.
     *
     * @since 1.0.0
     * @param string $period Period (today, week, month, all).
     * @return array Statistics.
     */
    public function get_stats( $period = 'all' ) {
        return PSM_DB::get_stats( $period );
    }

    /**
     * Get daily statistics for charts.
     *
     * @since 1.0.0
     * @param int $days Number of days.
     * @return array Daily statistics.
     */
    public function get_daily_stats( $days = 30 ) {
        return PSM_DB::get_daily_stats( $days );
    }
}
