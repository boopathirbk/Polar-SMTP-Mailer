<?php
/**
 * Queue class.
 *
 * Handles email queue management for scheduled sending.
 *
 * @package SimpleSmtpMail
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SSM_Queue class.
 */
class SSM_Queue {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks() {
        if ( get_option( 'ssm_enable_queue', false ) ) {
            $this->schedule_queue_processing();
            add_filter( 'pre_wp_mail', array( $this, 'maybe_queue_email' ), 10, 2 );
        }
        add_action( 'ssm_process_email_queue', array( $this, 'process_queue' ) );
        add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );
    }

    /**
     * Schedule queue processing.
     */
    private function schedule_queue_processing() {
        if ( ! wp_next_scheduled( 'ssm_process_email_queue' ) ) {
            wp_schedule_event( time(), 'ssm_queue_interval', 'ssm_process_email_queue' );
        }
    }

    /**
     * Add custom cron schedule.
     */
    public function add_cron_schedule( $schedules ) {
        $interval = (int) get_option( 'ssm_queue_interval', 5 );
        $schedules['ssm_queue_interval'] = array(
            'interval' => $interval * MINUTE_IN_SECONDS,
            /* translators: %d: Number of minutes for queue interval */
            'display'  => sprintf( __( 'Every %d minutes', 'simple-smtp-mail' ), $interval ),
        );
        return $schedules;
    }

    /**
     * Maybe queue email instead of sending immediately.
     */
    public function maybe_queue_email( $result, $atts ) {
        if ( ! get_option( 'ssm_enable_queue', false ) ) {
            return $result;
        }
        if ( ! empty( $atts['subject'] ) && false !== strpos( $atts['subject'], 'Test Email' ) ) {
            return $result;
        }
        if ( apply_filters( 'ssm_bypass_queue', false, $atts ) ) {
            return $result;
        }
        $this->add_to_queue( $atts );
        return true;
    }

    /**
     * Add email to queue.
     */
    public function add_to_queue( $email_data, $priority = 5, $scheduled_at = null ) {
        $to = isset( $email_data['to'] ) ? $email_data['to'] : '';
        if ( is_array( $to ) ) {
            $to = implode( ', ', $to );
        }

        $headers = isset( $email_data['headers'] ) ? $email_data['headers'] : array();

        $data = array(
            'to_email'     => $to,
            'subject'      => isset( $email_data['subject'] ) ? $email_data['subject'] : '',
            'message'      => isset( $email_data['message'] ) ? $email_data['message'] : '',
            'headers'      => $headers,
            'attachments'  => isset( $email_data['attachments'] ) ? $email_data['attachments'] : array(),
            'priority'     => $priority,
            'scheduled_at' => $scheduled_at ? $scheduled_at : current_time( 'mysql' ),
        );

        if ( get_option( 'ssm_enable_logging', true ) ) {
            SSM_DB::insert_log( array(
                'to_email'   => $data['to_email'],
                'subject'    => $data['subject'],
                'message'    => $data['message'],
                'headers'    => $data['headers'],
                'status'     => 'queued',
                'created_at' => current_time( 'mysql' ),
            ) );
        }

        return SSM_DB::insert_queue( $data );
    }

    /**
     * Process queued emails.
     */
    public function process_queue() {
        $batch_size = (int) get_option( 'ssm_queue_batch_size', 10 );
        $queued = SSM_DB::get_queued_emails( $batch_size );
        $processed = 0;

        foreach ( $queued as $item ) {
            // Try to lock the item. If failed, skip it (another process might have taken it).
            if ( ! SSM_DB::lock_queue_item( $item->id ) ) {
                continue;
            }

            $headers = $item->headers;
            if ( is_string( $headers ) ) {
                $decoded = json_decode( $headers, true );
                if ( is_array( $decoded ) ) {
                    $headers = $decoded;
                }
            }

            $attachments = $item->attachments;
            if ( is_string( $attachments ) ) {
                $decoded = json_decode( $attachments, true );
                $attachments = is_array( $decoded ) ? $decoded : array();
            }

            remove_filter( 'pre_wp_mail', array( $this, 'maybe_queue_email' ) );
            $result = wp_mail( $item->to_email, $item->subject, $item->message, $headers, $attachments );
            add_filter( 'pre_wp_mail', array( $this, 'maybe_queue_email' ), 10, 2 );

            if ( $result ) {
                SSM_DB::delete_queue_item( $item->id );
                $processed++;
            } else {
                SSM_DB::increment_queue_attempts( $item->id );
            }
        }

        return $processed;
    }

    /**
     * Get queue count.
     */
    public function get_queue_count() {
        return SSM_DB::get_queue_count();
    }

    /**
     * Clear all items from queue.
     */
    public function clear_queue() {
        global $wpdb;
        $table = SSM_DB::get_queue_table();
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        return $wpdb->query( "TRUNCATE TABLE " . $table );
    }

    /**
     * Manually trigger queue processing.
     */
    public function trigger_processing() {
        $processed = $this->process_queue();
        return array(
            'success'   => true,
            'processed' => $processed,
            /* translators: %d: Number of emails processed */
            'message'   => sprintf( _n( '%d email processed.', '%d emails processed.', $processed, 'simple-smtp-mail' ), $processed ),
        );
    }
}
