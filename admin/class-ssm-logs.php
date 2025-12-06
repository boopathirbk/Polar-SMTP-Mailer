<?php
/**
 * Logs list table class.
 *
 * @package SimpleSmtpMail
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * SSM_Logs_Table class.
 */
class SSM_Logs_Table extends WP_List_Table {

    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct( array(
            'singular' => 'email-log',
            'plural'   => 'email-logs',
            'ajax'     => false,
        ) );
    }

    /**
     * Get table columns.
     */
    public function get_columns() {
        return array(
            'cb'         => '<input type="checkbox" />',
            'to_email'   => __( 'To', 'simple-smtp-mail' ),
            'subject'    => __( 'Subject', 'simple-smtp-mail' ),
            'status'     => __( 'Status', 'simple-smtp-mail' ),
            'provider'   => __( 'Provider', 'simple-smtp-mail' ),
            'created_at' => __( 'Date', 'simple-smtp-mail' ),
            'actions'    => __( 'Actions', 'simple-smtp-mail' ),
        );
    }

    /**
     * Get sortable columns.
     */
    public function get_sortable_columns() {
        return array(
            'to_email'   => array( 'to_email', false ),
            'subject'    => array( 'subject', false ),
            'status'     => array( 'status', false ),
            'created_at' => array( 'created_at', true ),
        );
    }

    /**
     * Prepare items.
     */
    public function prepare_items() {
        $per_page = 20;
        $current_page = $this->get_pagenum();

        $args = array(
            'per_page' => $per_page,
            'page'     => $current_page,
            'status'   => isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '',
            'search'   => isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '',
            'orderby'  => isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'created_at',
            'order'    => isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'DESC',
        );

        $this->items = SSM_DB::get_logs( $args );
        $total_items = SSM_DB::get_logs_count( $args );

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil( $total_items / $per_page ),
        ) );
    }

    /**
     * Checkbox column.
     */
    public function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="log_ids[]" value="%d" />', $item->id );
    }

    /**
     * To email column.
     */
    public function column_to_email( $item ) {
        return esc_html( $item->to_email );
    }

    /**
     * Subject column.
     */
    public function column_subject( $item ) {
        return esc_html( $item->subject ? $item->subject : __( '(no subject)', 'simple-smtp-mail' ) );
    }

    /**
     * Status column.
     */
    public function column_status( $item ) {
        $classes = array(
            'sent'   => 'ssm-status-sent',
            'failed' => 'ssm-status-failed',
            'queued' => 'ssm-status-queued',
        );
        $class = isset( $classes[ $item->status ] ) ? $classes[ $item->status ] : '';
        return sprintf( '<span class="ssm-status %s">%s</span>', esc_attr( $class ), esc_html( ucfirst( $item->status ) ) );
    }

    /**
     * Provider column.
     */
    public function column_provider( $item ) {
        return esc_html( $item->provider ? $item->provider : '-' );
    }

    /**
     * Date column.
     */
    public function column_created_at( $item ) {
        return esc_html( wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $item->created_at ) ) );
    }

    /**
     * Actions column.
     */
    public function column_actions( $item ) {
        $actions = sprintf(
            '<button type="button" class="button button-small ssm-view-log" data-id="%d">%s</button> ',
            $item->id,
            __( 'View', 'simple-smtp-mail' )
        );
        $actions .= sprintf(
            '<button type="button" class="button button-small ssm-resend-email" data-id="%d">%s</button> ',
            $item->id,
            __( 'Resend', 'simple-smtp-mail' )
        );
        $actions .= sprintf(
            '<button type="button" class="button button-small button-link-delete ssm-delete-log" data-id="%d">%s</button>',
            $item->id,
            __( 'Delete', 'simple-smtp-mail' )
        );
        return $actions;
    }

    /**
     * Get bulk actions.
     */
    public function get_bulk_actions() {
        return array(
            'delete' => __( 'Delete', 'simple-smtp-mail' ),
        );
    }

    /**
     * Process bulk actions.
     *
     * @since 1.0.0
     * @return void
     */
    public function process_bulk_action() {
        if ( 'delete' === $this->current_action() && ! empty( $_POST['log_ids'] ) ) {
            check_admin_referer( 'bulk-' . $this->_args['plural'] );
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $ids = array_map( 'absint', wp_unslash( $_POST['log_ids'] ) );
            SSM_DB::bulk_delete_logs( $ids );
        }
    }

    /**
     * No items message.
     */
    public function no_items() {
        esc_html_e( 'No email logs found.', 'simple-smtp-mail' );
    }

    /**
     * Extra table nav for filters.
     */
    protected function extra_tablenav( $which ) {
        if ( 'top' !== $which ) {
            return;
        }
        ?>
        <div class="alignleft actions">
            <select name="status">
                <option value=""><?php esc_html_e( 'All Statuses', 'simple-smtp-mail' ); ?></option>
                <option value="sent" <?php selected( isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '', 'sent' ); ?>><?php esc_html_e( 'Sent', 'simple-smtp-mail' ); ?></option>
                <option value="failed" <?php selected( isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '', 'failed' ); ?>><?php esc_html_e( 'Failed', 'simple-smtp-mail' ); ?></option>
                <option value="queued" <?php selected( isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : '', 'queued' ); ?>><?php esc_html_e( 'Queued', 'simple-smtp-mail' ); ?></option>
            </select>
            <?php submit_button( __( 'Filter', 'simple-smtp-mail' ), '', 'filter_action', false ); ?>
        </div>
        <?php
    }
}

/**
 * SSM_Logs class.
 */
class SSM_Logs {

    /**
     * Get logs table instance.
     */
    public static function get_table() {
        return new SSM_Logs_Table();
    }
}
