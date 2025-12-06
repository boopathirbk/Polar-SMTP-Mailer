<?php
/**
 * Database handler class.
 *
 * Handles all database operations including table creation,
 * migrations, and CRUD operations for email logs and queue.
 *
 * @package SimpleSmtpMail
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SSM_DB class.
 *
 * @since 1.0.0
 */
class SSM_DB {

    /**
     * Current database version.
     *
     * @var string
     */
    const DB_VERSION = '1.0.0';

    /**
     * Email logs table name.
     *
     * @var string
     */
    private static $logs_table;

    /**
     * Email queue table name.
     *
     * @var string
     */
    private static $queue_table;

    /**
     * Initialize table names.
     *
     * @since 1.0.0
     * @return void
     */
    private static function init_table_names() {
        global $wpdb;
        self::$logs_table  = $wpdb->prefix . 'ssm_email_logs';
        self::$queue_table = $wpdb->prefix . 'ssm_email_queue';
    }

    /**
     * Create database tables.
     *
     * @since 1.0.0
     * @return void
     */
    public static function create_tables() {
        global $wpdb;

        self::init_table_names();

        $charset_collate = $wpdb->get_charset_collate();

        // Email logs table.
        $logs_sql = "CREATE TABLE " . self::$logs_table . " (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            to_email VARCHAR(255) NOT NULL,
            cc_email TEXT,
            bcc_email TEXT,
            subject VARCHAR(255),
            message LONGTEXT,
            headers TEXT,
            attachments TEXT,
            status VARCHAR(20) DEFAULT 'queued',
            error_message TEXT,
            provider VARCHAR(50),
            mailer_type VARCHAR(20) DEFAULT 'smtp',
            retries TINYINT DEFAULT 0,
            sent_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_status (status),
            KEY idx_sent_at (sent_at),
            KEY idx_to_email (to_email(100)),
            KEY idx_created_at (created_at)
        ) $charset_collate;";

        // Email queue table.
        $queue_sql = "CREATE TABLE " . self::$queue_table . " (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            to_email VARCHAR(255) NOT NULL,
            cc_email TEXT,
            bcc_email TEXT,
            subject VARCHAR(255),
            message LONGTEXT,
            headers TEXT,
            attachments TEXT,
            priority TINYINT DEFAULT 5,
            attempts TINYINT DEFAULT 0,
            max_attempts TINYINT DEFAULT 3,
            scheduled_at DATETIME,
            locked_at DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_scheduled (scheduled_at),
            KEY idx_priority (priority),
            KEY idx_locked (locked_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        dbDelta( $logs_sql );
        dbDelta( $queue_sql );

        update_option( 'ssm_db_version', self::DB_VERSION );
    }

    /**
     * Get logs table name.
     *
     * @since 1.0.0
     * @return string
     */
    public static function get_logs_table() {
        self::init_table_names();
        return self::$logs_table;
    }

    /**
     * Get queue table name.
     *
     * @since 1.0.0
     * @return string
     */
    public static function get_queue_table() {
        self::init_table_names();
        return self::$queue_table;
    }

    /**
     * Insert email log.
     *
     * @since 1.0.0
     * @param array $data Log data.
     * @return int|false The log ID on success, false on failure.
     */
    public static function insert_log( $data ) {
        global $wpdb;

        self::init_table_names();

        $defaults = array(
            'to_email'      => '',
            'cc_email'      => '',
            'bcc_email'     => '',
            'subject'       => '',
            'message'       => '',
            'headers'       => '',
            'attachments'   => '',
            'status'        => 'queued',
            'error_message' => '',
            'provider'      => '',
            'mailer_type'   => 'smtp',
            'retries'       => 0,
            'sent_at'       => null,
            'created_at'    => current_time( 'mysql' ),
        );

        $data = wp_parse_args( $data, $defaults );

        // Serialize arrays.
        if ( is_array( $data['headers'] ) ) {
            $data['headers'] = wp_json_encode( $data['headers'] );
        }
        if ( is_array( $data['attachments'] ) ) {
            $data['attachments'] = wp_json_encode( $data['attachments'] );
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $result = $wpdb->insert(
            self::$logs_table,
            $data,
            array(
                '%s', // to_email
                '%s', // cc_email
                '%s', // bcc_email
                '%s', // subject
                '%s', // message
                '%s', // headers
                '%s', // attachments
                '%s', // status
                '%s', // error_message
                '%s', // provider
                '%s', // mailer_type
                '%d', // retries
                '%s', // sent_at
                '%s', // created_at
            )
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Update email log.
     *
     * @since 1.0.0
     * @param int   $id   Log ID.
     * @param array $data Log data to update.
     * @return bool True on success, false on failure.
     */
    public static function update_log( $id, $data ) {
        global $wpdb;

        self::init_table_names();

        // Serialize arrays.
        if ( isset( $data['headers'] ) && is_array( $data['headers'] ) ) {
            $data['headers'] = wp_json_encode( $data['headers'] );
        }
        if ( isset( $data['attachments'] ) && is_array( $data['attachments'] ) ) {
            $data['attachments'] = wp_json_encode( $data['attachments'] );
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->update(
            self::$logs_table,
            $data,
            array( 'id' => $id ),
            null,
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Get email log by ID.
     *
     * @since 1.0.0
     * @param int $id Log ID.
     * @return object|null Log object or null if not found.
     */
    public static function get_log( $id ) {
        global $wpdb;

        self::init_table_names();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM %i WHERE id = %d",
                self::$logs_table,
                $id
            )
        );

        return $result;
    }

    /**
     * Get email logs with pagination and filters.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return array Array of log objects.
     */
    public static function get_logs( $args = array() ) {
        global $wpdb;

        self::init_table_names();

        $defaults = array(
            'per_page'   => 20,
            'page'       => 1,
            'status'     => '',
            'search'     => '',
            'date_from'  => '',
            'date_to'    => '',
            'orderby'    => 'created_at',
            'order'      => 'DESC',
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( '1=1' );
        $values = array();

        // Status filter.
        if ( ! empty( $args['status'] ) ) {
            $where[] = 'status = %s';
            $values[] = $args['status'];
        }

        // Search filter.
        if ( ! empty( $args['search'] ) ) {
            $search = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where[] = '(to_email LIKE %s OR subject LIKE %s)';
            $values[] = $search;
            $values[] = $search;
        }

        // Date filters.
        if ( ! empty( $args['date_from'] ) ) {
            $where[] = 'created_at >= %s';
            $values[] = $args['date_from'] . ' 00:00:00';
        }

        if ( ! empty( $args['date_to'] ) ) {
            $where[] = 'created_at <= %s';
            $values[] = $args['date_to'] . ' 23:59:59';
        }

        $where_clause = implode( ' AND ', $where );

        // Sanitize orderby and order.
        $allowed_orderby = array( 'id', 'to_email', 'subject', 'status', 'created_at', 'sent_at' );
        $orderby = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'created_at';
        $order = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';

        $offset = ( $args['page'] - 1 ) * $args['per_page'];

        // Build query.
        $query = "SELECT * FROM %i WHERE $where_clause ORDER BY $orderby $order LIMIT %d OFFSET %d";

        // Prepare query with values.
        $prepared_values = array_merge( array( self::$logs_table ), $values, array( $args['per_page'], $offset ) );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
        $results = $wpdb->get_results(
            $wpdb->prepare( $query, $prepared_values )
        );

        return $results ? $results : array();
    }

    /**
     * Get total logs count with filters.
     *
     * @since 1.0.0
     * @param array $args Query arguments.
     * @return int Total count.
     */
    public static function get_logs_count( $args = array() ) {
        global $wpdb;

        self::init_table_names();

        $defaults = array(
            'status'    => '',
            'search'    => '',
            'date_from' => '',
            'date_to'   => '',
        );

        $args = wp_parse_args( $args, $defaults );

        $where = array( '1=1' );
        $values = array();

        // Status filter.
        if ( ! empty( $args['status'] ) ) {
            $where[] = 'status = %s';
            $values[] = $args['status'];
        }

        // Search filter.
        if ( ! empty( $args['search'] ) ) {
            $search = '%' . $wpdb->esc_like( $args['search'] ) . '%';
            $where[] = '(to_email LIKE %s OR subject LIKE %s)';
            $values[] = $search;
            $values[] = $search;
        }

        // Date filters.
        if ( ! empty( $args['date_from'] ) ) {
            $where[] = 'created_at >= %s';
            $values[] = $args['date_from'] . ' 00:00:00';
        }

        if ( ! empty( $args['date_to'] ) ) {
            $where[] = 'created_at <= %s';
            $values[] = $args['date_to'] . ' 23:59:59';
        }

        $where_clause = implode( ' AND ', $where );

        $query = "SELECT COUNT(*) FROM %i WHERE $where_clause";
        $prepared_values = array_merge( array( self::$logs_table ), $values );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
        $count = $wpdb->get_var(
            $wpdb->prepare( $query, $prepared_values )
        );

        return (int) $count;
    }

    /**
     * Delete email log.
     *
     * @since 1.0.0
     * @param int $id Log ID.
     * @return bool True on success, false on failure.
     */
    public static function delete_log( $id ) {
        global $wpdb;

        self::init_table_names();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->delete(
            self::$logs_table,
            array( 'id' => $id ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Bulk delete email logs.
     *
     * @since 1.0.0
     * @param array $ids Array of log IDs.
     * @return int Number of rows deleted.
     */
    public static function bulk_delete_logs( $ids ) {
        global $wpdb;

        self::init_table_names();

        if ( empty( $ids ) ) {
            return 0;
        }

        $ids = array_map( 'absint', $ids );
        $placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM %i WHERE id IN ($placeholders)",
                array_merge( array( self::$logs_table ), $ids )
            )
        );

        return $result ? $result : 0;
    }

    /**
     * Clean up old logs based on retention days.
     *
     * @since 1.0.0
     * @param int $days Number of days to retain logs.
     * @return int Number of rows deleted.
     */
    public static function cleanup_old_logs( $days = 30 ) {
        global $wpdb;

        self::init_table_names();

        $date = gmdate( 'Y-m-d H:i:s', strtotime( "-{$days} days" ) );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM %i WHERE created_at < %s",
                self::$logs_table,
                $date
            )
        );

        return $result ? $result : 0;
    }

    /**
     * Get email statistics.
     *
     * @since 1.0.0
     * @param string $period Period for stats (today, week, month, all).
     * @return array Statistics array.
     */
    public static function get_stats( $period = 'all' ) {
        global $wpdb;

        self::init_table_names();

        $where = '1=1';

        switch ( $period ) {
            case 'today':
                $where = 'DATE(created_at) = CURDATE()';
                break;
            case 'week':
                $where = 'created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
                break;
            case 'month':
                $where = 'created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)';
                break;
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $stats = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = 'queued' THEN 1 ELSE 0 END) as queued
                FROM %i 
                WHERE $where",
                self::$logs_table
            ),
            ARRAY_A
        );

        return array(
            'total'  => (int) ( $stats['total'] ?? 0 ),
            'sent'   => (int) ( $stats['sent'] ?? 0 ),
            'failed' => (int) ( $stats['failed'] ?? 0 ),
            'queued' => (int) ( $stats['queued'] ?? 0 ),
        );
    }

    /**
     * Get daily email stats for chart.
     *
     * @since 1.0.0
     * @param int $days Number of days.
     * @return array Daily stats.
     */
    public static function get_daily_stats( $days = 30 ) {
        global $wpdb;

        self::init_table_names();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
                FROM %i 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL %d DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC",
                self::$logs_table,
                $days
            ),
            ARRAY_A
        );

        return $results ? $results : array();
    }

    /**
     * Insert email to queue.
     *
     * @since 1.0.0
     * @param array $data Email data.
     * @return int|false Queue ID on success, false on failure.
     */
    public static function insert_queue( $data ) {
        global $wpdb;

        self::init_table_names();

        $defaults = array(
            'to_email'     => '',
            'cc_email'     => '',
            'bcc_email'    => '',
            'subject'      => '',
            'message'      => '',
            'headers'      => '',
            'attachments'  => '',
            'priority'     => 5,
            'attempts'     => 0,
            'max_attempts' => 3,
            'scheduled_at' => current_time( 'mysql' ),
            'locked_at'    => null,
            'created_at'   => current_time( 'mysql' ),
        );

        $data = wp_parse_args( $data, $defaults );

        // Serialize arrays.
        if ( is_array( $data['headers'] ) ) {
            $data['headers'] = wp_json_encode( $data['headers'] );
        }
        if ( is_array( $data['attachments'] ) ) {
            $data['attachments'] = wp_json_encode( $data['attachments'] );
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $result = $wpdb->insert(
            self::$queue_table,
            $data,
            array(
                '%s', // to_email
                '%s', // cc_email
                '%s', // bcc_email
                '%s', // subject
                '%s', // message
                '%s', // headers
                '%s', // attachments
                '%d', // priority
                '%d', // attempts
                '%d', // max_attempts
                '%s', // scheduled_at
                '%s', // locked_at
                '%s', // created_at
            )
        );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Get queued emails for processing.
     *
     * @since 1.0.0
     * @param int $limit Number of emails to fetch.
     * @return array Array of queue items.
     */
    public static function get_queued_emails( $limit = 10 ) {
        global $wpdb;

        self::init_table_names();

        $now = current_time( 'mysql' );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . self::$queue_table . " 
                WHERE scheduled_at <= %s 
                AND (locked_at IS NULL OR locked_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE))
                AND attempts < max_attempts
                ORDER BY priority ASC, scheduled_at ASC 
                LIMIT %d",
                $now,
                $limit
            )
        );

        return $results ? $results : array();
    }

    /**
     * Lock queue item for processing.
     *
     * @since 1.0.0
     * @param int $id Queue ID.
     * @return bool True on success.
     */
    public static function lock_queue_item( $id ) {
        global $wpdb;

        self::init_table_names();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->query(
            $wpdb->prepare(
                "UPDATE " . self::$queue_table . " SET locked_at = %s WHERE id = %d AND (locked_at IS NULL OR locked_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE))",
                current_time( 'mysql' ),
                $id
            )
        );

        return $result > 0;
    }

    /**
     * Delete queue item.
     *
     * @since 1.0.0
     * @param int $id Queue ID.
     * @return bool True on success.
     */
    public static function delete_queue_item( $id ) {
        global $wpdb;

        self::init_table_names();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->delete(
            self::$queue_table,
            array( 'id' => $id ),
            array( '%d' )
        );

        return false !== $result;
    }

    /**
     * Increment queue item attempts.
     *
     * @since 1.0.0
     * @param int $id Queue ID.
     * @return bool True on success.
     */
    public static function increment_queue_attempts( $id ) {
        global $wpdb;

        self::init_table_names();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->query(
            $wpdb->prepare(
                "UPDATE " . self::$queue_table . " SET attempts = attempts + 1, locked_at = NULL WHERE id = %d",
                $id
            )
        );

        return false !== $result;
    }

    /**
     * Get queue count.
     *
     * @since 1.0.0
     * @return int Queue count.
     */
    public static function get_queue_count() {
        global $wpdb;

        self::init_table_names();

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM " . self::$queue_table
            )
        );

        return (int) $count;
    }
}
