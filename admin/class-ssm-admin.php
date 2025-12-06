<?php
/**
 * Admin class.
 *
 * @package SimpleSmtpMail
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * SSM_Admin class.
 */
class SSM_Admin {

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
        add_menu_page(
            __( 'Simple SMTP Mail', 'simple-smtp-mail' ),
            __( 'SMTP Mail', 'simple-smtp-mail' ),
            'manage_options',
            'simple-smtp-mail',
            array( $this, 'render_dashboard' ),
            'dashicons-email-alt',
            80
        );

        add_submenu_page(
            'simple-smtp-mail',
            __( 'Dashboard', 'simple-smtp-mail' ),
            __( 'Dashboard', 'simple-smtp-mail' ),
            'manage_options',
            'simple-smtp-mail',
            array( $this, 'render_dashboard' )
        );

        add_submenu_page(
            'simple-smtp-mail',
            __( 'Settings', 'simple-smtp-mail' ),
            __( 'Settings', 'simple-smtp-mail' ),
            'manage_options',
            'simple-smtp-mail-settings',
            array( $this, 'render_settings' )
        );

        add_submenu_page(
            'simple-smtp-mail',
            __( 'Email Logs', 'simple-smtp-mail' ),
            __( 'Email Logs', 'simple-smtp-mail' ),
            'manage_options',
            'simple-smtp-mail-logs',
            array( $this, 'render_logs' )
        );

        add_submenu_page(
            'simple-smtp-mail',
            __( 'Test Email', 'simple-smtp-mail' ),
            __( 'Test Email', 'simple-smtp-mail' ),
            'manage_options',
            'simple-smtp-mail-test',
            array( $this, 'render_test' )
        );

        add_submenu_page(
            'simple-smtp-mail',
            __( 'About', 'simple-smtp-mail' ),
            __( 'About', 'simple-smtp-mail' ),
            'manage_options',
            'simple-smtp-mail-about',
            array( $this, 'render_about' )
        );
    }

    /**
     * Enqueue admin assets.
     */
    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'simple-smtp-mail' ) === false ) {
            return;
        }

        wp_enqueue_style(
            'ssm-admin',
            SSM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SSM_VERSION
        );

        wp_enqueue_script(
            'ssm-admin',
            SSM_PLUGIN_URL . 'assets/js/admin.js',
            array( 'jquery' ),
            SSM_VERSION,
            true
        );

        wp_localize_script( 'ssm-admin', 'ssm_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'ssm_nonce' ),
            'strings'  => array(
                'confirm_delete'  => __( 'Are you sure you want to delete this?', 'simple-smtp-mail' ),
                'testing'         => __( 'Testing...', 'simple-smtp-mail' ),
                'sending'         => __( 'Sending...', 'simple-smtp-mail' ),
                'success'         => __( 'Success!', 'simple-smtp-mail' ),
                'error'           => __( 'Error', 'simple-smtp-mail' ),
            ),
        ) );
    }

    /**
     * Redirect after activation.
     */
    public function maybe_redirect() {
        if ( get_transient( 'ssm_activation_redirect' ) ) {
            delete_transient( 'ssm_activation_redirect' );
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Safe read-only check for multi-activation.
            if ( ! isset( $_GET['activate-multi'] ) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=simple-smtp-mail' ) );
                exit;
            }
        }
    }

    /**
     * Render dashboard page.
     */
    public function render_dashboard() {
        require_once SSM_PLUGIN_DIR . 'admin/views/dashboard-page.php';
    }

    /**
     * Render settings page.
     */
    public function render_settings() {
        require_once SSM_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    /**
     * Render logs page.
     */
    public function render_logs() {
        require_once SSM_PLUGIN_DIR . 'admin/views/logs-page.php';
    }

    /**
     * Render test email page.
     */
    public function render_test() {
        require_once SSM_PLUGIN_DIR . 'admin/views/test-page.php';
    }

    /**
     * Render about page.
     */
    public function render_about() {
        require_once SSM_PLUGIN_DIR . 'admin/views/about-page.php';
    }
}
