<?php
/**
 * Queue class.
 *
 * Handles email queue management for scheduled sending.
 *
 * @package PolarSmtpMailer
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Queue class.
 */
#[AllowDynamicProperties]
class PSM_Queue {

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
        if ( get_option( 'PSM_enable_queue', false ) ) {
            $this->schedule_queue_processing();
            add_filter( 'pre_wp_mail', array( $this, 'maybe_queue_email' ), 10, 2 );
        }
        add_action( 'PSM_process_email_queue', array( $this, 'process_queue' ) );
        add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ) );
    }

    /**
     * Schedule queue processing.
     */
    private function schedule_queue_processing() {
        if ( ! wp_next_scheduled( 'PSM_process_email_queue' ) ) {
            wp_schedule_event( time(), 'PSM_queue_interval', 'PSM_process_email_queue' );
        }
    }

    /**
     * Add custom cron schedule.
     */
    public function add_cron_schedule( $schedules ) {
        $interval = (int) get_option( 'PSM_queue_interval', 5 );
        $schedules['PSM_queue_interval'] = array(
            'interval' => $interval * MINUTE_IN_SECONDS,
            /* translators: %d: Number of minutes for queue interval */
            'display'  => sprintf( __( 'Every %d minutes', 'polar-smtp-mailer' ), $interval ),
        );
        return $schedules;
    }

    /**
     * Maybe queue email instead of sending immediately.
     */
    public function maybe_queue_email( $result, $atts ) {
        if ( ! get_option( 'PSM_enable_queue', false ) ) {
            return $result;
        }
        if ( ! empty( $atts['subject'] ) && false !== strpos( $atts['subject'], 'Test Email' ) ) {
            return $result;
        }
        /**
         * Filters whether to bypass the queue and send the email immediately.
         *
         * @since 1.0.0
         * @param bool  $bypass Whether to bypass the queue. Default false.
         * @param array $atts   Email attributes (to, subject, message, headers, attachments).
         */
        // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- PSM is the established plugin prefix.
        if ( apply_filters( 'psm_bypass_queue', false, $atts ) ) {
            return $result;
        }
        $this->add_to_queue( $atts );
        return true;
    }

    /**
     * Add email to queue.
     *
     * Note: We don't create a log entry here to avoid duplicates.
     * The log entry is created when the email is actually sent via wp_mail_succeeded hook.
     *
     * @since 1.0.0
     * @param array  $email_data   Email data.
     * @param int    $priority     Priority (1-10, lower = higher priority).
     * @param string $scheduled_at Scheduled time (MySQL datetime format).
     * @return int|false Queue ID on success, false on failure.
     */
    public function add_to_queue( $email_data, $priority = 5, $scheduled_at = null ) {
        $to = isset( $email_data['to'] ) ? $email_data['to'] : '';
        if ( is_array( $to ) ) {
            $to = implode( ', ', $to );
        }

        if ( empty( $to ) ) {
            return false;
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

        return PSM_DB::insert_queue( $data );
    }

    /**
     * Process queued emails.
     */
    public function process_queue() {
        $batch_size = (int) get_option( 'PSM_queue_batch_size', 10 );
        $queued = PSM_DB::get_queued_emails( $batch_size );
        $processed = 0;

        foreach ( $queued as $item ) {
            // Try to lock the item. If failed, skip it (another process might have taken it).
            if ( ! PSM_DB::lock_queue_item( $item->id ) ) {
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
                PSM_DB::delete_queue_item( $item->id );
                $processed++;
            } else {
                PSM_DB::increment_queue_attempts( $item->id );
            }
        }

        return $processed;
    }

    /**
     * Get queue count.
     */
    public function get_queue_count() {
        return PSM_DB::get_queue_count();
    }

    /**
     * Clear all items from queue.
     *
     * @since 1.0.0
     * @return int|false Number of rows affected or false on failure/permission denied.
     */
    public function clear_queue() {
        // Capability check to prevent unauthorized clearing.
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }

        global $wpdb;
        $table = PSM_DB::get_queue_table();
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
            'message'   => sprintf( _n( '%d email processed.', '%d emails processed.', $processed, 'polar-smtp-mailer' ), $processed ),
        );
    }
}
