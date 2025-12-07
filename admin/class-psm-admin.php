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
        // Polar bear SVG icon (base64 encoded for WordPress admin menu).
        $icon_svg = 'data:image/svg+xml;base64,' . base64_encode( '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 72 72"><path fill="#a0a5aa" d="M50.698 53.942H43.89s-.368-3.912 2.578-4.226s2.072-4.568 2.072-4.568h7.327v8.794z"/><path fill="#a0a5aa" d="M55.867 50.631s-6.603.44-5.015 4.225H60.2s2.457 0 1.97-5.839c0 0 1.71-15.86-.497-16.688l2.852 1.809A.967.967 0 0 0 66 33.29v-.773c-.463-1.756-9.415-9.374-16.873-9.374a15.6 15.6 0 0 0-4.885.772s-.044.011-.11.034c-.718.182-4.564 1.193-5.48 1.284c-1.017.09-17.603-.598-17.603-.598c-2.347-.038-2.789-.802-4.365-.357c-4.084 1.155-11.413 5.4-10.4 6.914c1.078 1.612 2.072 4.638 10.554 1.31h.044c.299 0 2.166.205 6.774 4.702q.002.014.011.023c3.017 2.942 12.037 14.86 12.863 15.516s5.851 2.112 5.16-3.01a38 38 0 0 1-2.895-.41l-.116-3.328c5.067 1.376 11.937-1.05 11.937-1.05"/><path fill="#a0a5aa" d="M23.242 37.157c.132.534 3.272 12.045-.011 12.506s-3.79 1.158-3.702 3.475h8.752l4.165-3.839s-3.74-9.79-9.204-12.142"/></svg>' );

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
