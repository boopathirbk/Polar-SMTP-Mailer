<?php
/**
 * Privacy class.
 *
 * Handles GDPR and privacy compliance features including
 * WordPress privacy tools integration.
 *
 * @package SimpleSmtpMail
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SSM_Privacy class.
 *
 * @since 1.0.0
 */
class SSM_Privacy {

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
        // Register data exporter.
        add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_exporter' ) );

        // Register data eraser.
        add_filter( 'wp_privacy_personal_data_erasers', array( $this, 'register_eraser' ) );

        // Add privacy policy content.
        add_action( 'admin_init', array( $this, 'add_privacy_policy_content' ) );
    }

    /**
     * Register personal data exporter.
     *
     * @since 1.0.0
     * @param array $exporters Registered exporters.
     * @return array Modified exporters.
     */
    public function register_exporter( $exporters ) {
        $exporters['simple-smtp-mail'] = array(
            'exporter_friendly_name' => __( 'Simple SMTP Mail Email Logs', 'simple-smtp-mail' ),
            'callback'               => array( $this, 'export_personal_data' ),
        );
        return $exporters;
    }

    /**
     * Register personal data eraser.
     *
     * @since 1.0.0
     * @param array $erasers Registered erasers.
     * @return array Modified erasers.
     */
    public function register_eraser( $erasers ) {
        $erasers['simple-smtp-mail'] = array(
            'eraser_friendly_name' => __( 'Simple SMTP Mail Email Logs', 'simple-smtp-mail' ),
            'callback'             => array( $this, 'erase_personal_data' ),
        );
        return $erasers;
    }

    /**
     * Export personal data for a user.
     *
     * @since 1.0.0
     * @param string $email_address User email address.
     * @param int    $page          Page number.
     * @return array Export data.
     */
    public function export_personal_data( $email_address, $page = 1 ) {
        $per_page = 100;
        $data_to_export = array();

        // Get logs where this email was the recipient.
        $logs = SSM_DB::get_logs( array(
            'per_page' => $per_page,
            'page'     => $page,
            'search'   => $email_address,
        ) );

        foreach ( $logs as $log ) {
            // Only include if email matches exactly.
            if ( false === strpos( $log->to_email, $email_address ) &&
                 false === strpos( $log->cc_email, $email_address ) &&
                 false === strpos( $log->bcc_email, $email_address ) ) {
                continue;
            }

            $data = array(
                array(
                    'name'  => __( 'Email ID', 'simple-smtp-mail' ),
                    'value' => $log->id,
                ),
                array(
                    'name'  => __( 'Recipient', 'simple-smtp-mail' ),
                    'value' => $log->to_email,
                ),
                array(
                    'name'  => __( 'Subject', 'simple-smtp-mail' ),
                    'value' => $log->subject,
                ),
                array(
                    'name'  => __( 'Status', 'simple-smtp-mail' ),
                    'value' => $log->status,
                ),
                array(
                    'name'  => __( 'Date Sent', 'simple-smtp-mail' ),
                    'value' => $log->created_at,
                ),
            );

            // Only include message content if configured.
            if ( ! get_option( 'ssm_privacy_exclude_content', false ) ) {
                $data[] = array(
                    'name'  => __( 'Email Content', 'simple-smtp-mail' ),
                    'value' => wp_strip_all_tags( $log->message ),
                );
            }

            $data_to_export[] = array(
                'group_id'          => 'email-logs',
                'group_label'       => __( 'Email Logs', 'simple-smtp-mail' ),
                'group_description' => __( 'Logs of emails sent to this address.', 'simple-smtp-mail' ),
                'item_id'           => 'email-' . $log->id,
                'data'              => $data,
            );
        }

        $done = count( $logs ) < $per_page;

        return array(
            'data' => $data_to_export,
            'done' => $done,
        );
    }

    /**
     * Erase personal data for a user.
     *
     * @since 1.0.0
     * @param string $email_address User email address.
     * @param int    $page          Page number.
     * @return array Erase result.
     */
    public function erase_personal_data( $email_address, $page = 1 ) {
        global $wpdb;

        $per_page = 100;
        $items_removed = 0;
        $items_retained = 0;
        $messages = array();

        $table = SSM_DB::get_logs_table();

        // Get logs where this email was the recipient.
        $logs = SSM_DB::get_logs( array(
            'per_page' => $per_page,
            'page'     => $page,
            'search'   => $email_address,
        ) );

        foreach ( $logs as $log ) {
            // Check if email matches.
            if ( false === strpos( $log->to_email, $email_address ) &&
                 false === strpos( $log->cc_email, $email_address ) &&
                 false === strpos( $log->bcc_email, $email_address ) ) {
                continue;
            }

            // Check privacy setting - anonymize or delete.
            if ( get_option( 'ssm_privacy_anonymize', false ) ) {
                // Anonymize instead of delete.
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->update(
                    $table,
                    array(
                        'to_email'  => '[deleted]@anonymized.local',
                        'cc_email'  => '',
                        'bcc_email' => '',
                        'message'   => __( '[Content removed for privacy]', 'simple-smtp-mail' ),
                        'headers'   => '',
                    ),
                    array( 'id' => $log->id ),
                    array( '%s', '%s', '%s', '%s', '%s' ),
                    array( '%d' )
                );
                $items_removed++;
            } else {
                // Delete the log.
                SSM_DB::delete_log( $log->id );
                $items_removed++;
            }
        }

        $done = count( $logs ) < $per_page;

        return array(
            'items_removed'  => $items_removed,
            'items_retained' => $items_retained,
            'messages'       => $messages,
            'done'           => $done,
        );
    }

    /**
     * Add privacy policy suggested content.
     *
     * @since 1.0.0
     * @return void
     */
    public function add_privacy_policy_content() {
        if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
            return;
        }

        $content = sprintf(
            '<h2>%s</h2>' .
            '<p>%s</p>' .
            '<h3>%s</h3>' .
            '<p>%s</p>' .
            '<ul>' .
            '<li>%s</li>' .
            '<li>%s</li>' .
            '<li>%s</li>' .
            '<li>%s</li>' .
            '</ul>' .
            '<h3>%s</h3>' .
            '<p>%s</p>' .
            '<h3>%s</h3>' .
            '<p>%s</p>',
            __( 'Email Logging', 'simple-smtp-mail' ),
            __( 'This website uses Simple SMTP Mail to send and log transactional emails.', 'simple-smtp-mail' ),
            __( 'What data we collect', 'simple-smtp-mail' ),
            __( 'When emails are sent from this website, we may log the following information:', 'simple-smtp-mail' ),
            __( 'Recipient email address', 'simple-smtp-mail' ),
            __( 'Email subject line', 'simple-smtp-mail' ),
            __( 'Date and time of sending', 'simple-smtp-mail' ),
            __( 'Delivery status (sent, failed, queued)', 'simple-smtp-mail' ),
            __( 'Why we collect this data', 'simple-smtp-mail' ),
            __( 'Email logs help us troubleshoot delivery issues and ensure important notifications reach our users. This is in our legitimate interest to provide reliable email communications.', 'simple-smtp-mail' ),
            __( 'Data retention', 'simple-smtp-mail' ),
            sprintf(
                /* translators: %d: Number of days */
                __( 'Email logs are automatically deleted after %d days. You can request deletion of your data at any time through WordPress\'s privacy tools.', 'simple-smtp-mail' ),
                (int) get_option( 'ssm_log_retention_days', 30 )
            )
        );

        wp_add_privacy_policy_content(
            'Simple SMTP Mail',
            wp_kses_post( $content )
        );
    }

    /**
     * Get privacy-related settings.
     *
     * @since 1.0.0
     * @return array Privacy settings.
     */
    public static function get_privacy_settings() {
        return array(
            'exclude_content' => get_option( 'ssm_privacy_exclude_content', false ),
            'anonymize'       => get_option( 'ssm_privacy_anonymize', false ),
            'retention_days'  => get_option( 'ssm_log_retention_days', 30 ),
        );
    }
}
