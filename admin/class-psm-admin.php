<?php
/**
 * Admin class.
 *
 * @package PolarSmtpMailer
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Admin class.
 */
class PSM_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'admin_init', array( $this, 'maybe_redirect' ) );
    }

    /**
     * Add admin menu.
     */
    public function add_admin_menu() {
        // Polar bear SVG icon (pre-encoded base64 for WordPress admin menu).
        // phpcs:ignore Generic.Files.LineLength.TooLong
        $icon_svg = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgdmlld0JveD0iMCAwIDcyIDcyIj48cGF0aCBmaWxsPSIjYTBhNWFhIiBkPSJNNTAuNjk4IDUzLjk0Mkg0My44OXMtLjM2OC0zLjkxMiAyLjU3OC00LjIyNnMyLjA3Mi00LjU2OCAyLjA3Mi00LjU2OGg3LjMyN3Y4Ljc5NHoiLz48cGF0aCBmaWxsPSIjYTBhNWFhIiBkPSJNNTUuODY3IDUwLjYzMXMtNi42MDMuNDQtNS4wMTUgNC4yMjVINjAuMnMyLjQ1NyAwIDEuOTctNS44MzljMCAwIDEuNzEtMTUuODYtLjQ5Ny0xNi42ODhsMi44NTIgMS44MDlBLjk2Ny45NjcgMCAwIDAgNjYgMzMuMjl2LS43NzNjLS40NjMtMS43NTYtOS40MTUtOS4zNzQtMTYuODczLTkuMzc0YTE1LjYgMTUuNiAwIDAgMC00Ljg4NS43NzJzLS4wNDQuMDExLS4xMS4wMzRjLS43MTguMTgyLTQuNTY0IDEuMTkzLTUuNDggMS4yODRjLTEuMDE3LjA5LTE3LjYwMy0uNTk4LTE3LjYwMy0uNTk4Yy0yLjM0Ny0uMDM4LTIuNzg5LS44MDItNC4zNjUtLjM1N2MtNC4wODQgMS4xNTUtMTEuNDEzIDUuNC0xMC40IDYuOTE0YzEuMDc4IDEuNjEyIDIuMDcyIDQuNjM4IDEwLjU1NCAxLjMxaC4wNDRjLjI5OSAwIDIuMTY2LjIwNSA2Ljc3NCA0LjcwMnEuMDAyLjAxNC4wMTEuMDIzYzMuMDE3IDIuOTQyIDEyLjAzNyAxNC44NiAxMi44NjMgMTUuNTE2czUuODUxIDIuMTEyIDUuMTYtMy4wMWEzOCAzOCAwIDAgMS0yLjg5NS0uNDFsLS4xMTYtMy4zMjhjNS4wNjcgMS4zNzYgMTEuOTM3LTEuMDUgMTEuOTM3LTEuMDUiLz48cGF0aCBmaWxsPSIjYTBhNWFhIiBkPSJNMjMuMjQyIDM3LjE1N2MuMTMyLjUzNCAzLjI3MiAxMi4wNDUtLjAxMSAxMi41MDZzLTMuNzkgMS4xNTgtMy43MDIgMy40NzVoOC43NTJsNC4xNjUtMy44MzlzLTMuNzQtOS43OS05LjIwNC0xMi4xNDIiLz48L3N2Zz4=';

        add_menu_page(
            __( 'Polar SMTP Mailer', 'polar-smtp-mailer' ),
            __( 'Polar SMTP', 'polar-smtp-mailer' ),
            'manage_options',
            'polar-smtp-mailer',
            array( $this, 'render_dashboard' ),
            $icon_svg,
            80
        );

        add_submenu_page(
            'polar-smtp-mailer',
            __( 'Dashboard', 'polar-smtp-mailer' ),
            __( 'Dashboard', 'polar-smtp-mailer' ),
            'manage_options',
            'polar-smtp-mailer',
            array( $this, 'render_dashboard' )
        );

        add_submenu_page(
            'polar-smtp-mailer',
            __( 'Settings', 'polar-smtp-mailer' ),
            __( 'Settings', 'polar-smtp-mailer' ),
            'manage_options',
            'polar-smtp-mailer-settings',
            array( $this, 'render_settings' )
        );

        add_submenu_page(
            'polar-smtp-mailer',
            __( 'Email Logs', 'polar-smtp-mailer' ),
            __( 'Email Logs', 'polar-smtp-mailer' ),
            'manage_options',
            'polar-smtp-mailer-logs',
            array( $this, 'render_logs' )
        );

        add_submenu_page(
            'polar-smtp-mailer',
            __( 'Test Email', 'polar-smtp-mailer' ),
            __( 'Test Email', 'polar-smtp-mailer' ),
            'manage_options',
            'polar-smtp-mailer-test',
            array( $this, 'render_test' )
        );

        add_submenu_page(
            'polar-smtp-mailer',
            __( 'About', 'polar-smtp-mailer' ),
            __( 'About', 'polar-smtp-mailer' ),
            'manage_options',
            'polar-smtp-mailer-about',
            array( $this, 'render_about' )
        );
    }

    /**
     * Enqueue admin assets.
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'polar-smtp-mailer' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'ssm-admin',
            PSM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            PSM_VERSION
        );

        wp_enqueue_script(
            'ssm-admin',
            PSM_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            PSM_VERSION,
            true
        );

        wp_localize_script( 'ssm-admin', 'PSM_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'PSM_nonce' ),
            'strings'  => array(
                'confirm_delete'  => __( 'Are you sure you want to delete this?', 'polar-smtp-mailer' ),
                'testing'         => __( 'Testing...', 'polar-smtp-mailer' ),
                'sending'         => __( 'Sending...', 'polar-smtp-mailer' ),
                'success'         => __( 'Success!', 'polar-smtp-mailer' ),
                'error'           => __( 'Error', 'polar-smtp-mailer' ),
            ),
        ) );
    }

    /**
     * Redirect after activation.
     */
    public function maybe_redirect() {
        if ( get_transient( 'PSM_activation_redirect' ) ) {
            delete_transient( 'PSM_activation_redirect' );
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe read-only check for multi-activation.
            if ( ! isset( $_GET['activate-multi'] ) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=polar-smtp-mailer' ) );
                exit;
            }
        }
    }

    /**
     * Render dashboard page.
     */
    public function render_dashboard() {
        require_once PSM_PLUGIN_DIR . 'admin/views/dashboard-page.php';
    }

    /**
     * Render settings page.
     */
    public function render_settings() {
        require_once PSM_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    /**
     * Render logs page.
     */
    public function render_logs() {
        require_once PSM_PLUGIN_DIR . 'admin/views/logs-page.php';
    }

    /**
     * Render test email page.
     */
    public function render_test() {
        require_once PSM_PLUGIN_DIR . 'admin/views/test-page.php';
    }

    /**
     * Render about page.
     */
    public function render_about() {
        require_once PSM_PLUGIN_DIR . 'admin/views/about-page.php';
    }
}
