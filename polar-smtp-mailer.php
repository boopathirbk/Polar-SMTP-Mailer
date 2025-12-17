<?php
/**
 * Plugin Name:       Polar SMTP Mailer
 * Plugin URI:        https://github.com/boopathirbk/polar-smtp-mailer
 * Description:       A powerful, open-source SMTP mailer plugin with comprehensive email logging, queue management, and modern dashboard. Configure any SMTP provider easily and track all your WordPress emails.
 * Version:           1.0.5
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Boopathi R.
 * Author URI:        https://linkedin.com/in/boopathirb
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       polar-smtp-mailer
 * Domain Path:       /languages
 *
 * @package PolarSmtpMailer
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin version.
 */
define( 'PSM_VERSION', '1.0.5' );

/**
 * Plugin directory path.
 */
define( 'PSM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'PSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'PSM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Minimum PHP version.
 */
define( 'PSM_MIN_PHP_VERSION', '7.4' );

/**
 * Minimum WordPress version.
 */
define( 'PSM_MIN_WP_VERSION', '6.0' );

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
final class Polar_SMTP_Mailer {

    /**
     * Single instance of the class.
     *
     * @var Polar_SMTP_Mailer|null
     */
    private static $instance = null;

    /**
     * Admin instance.
     *
     * @var PSM_Admin|null
     */
    public $admin = null;

    /**
     * Mailer instance.
     *
     * @var PSM_Mailer|null
     */
    public $mailer = null;

    /**
     * Logger instance.
     *
     * @var PSM_Logger|null
     */
    public $logger = null;

    /**
     * Queue instance.
     *
     * @var PSM_Queue|null
     */
    public $queue = null;

    /**
     * Get the single instance of the class.
     *
     * @since 1.0.0
     * @return Polar_SMTP_Mailer
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    private function __construct() {
        $this->check_requirements();
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Check plugin requirements.
     *
     * @since 1.0.0
     * @return void
     */
    private function check_requirements() {
        // Check PHP version.
        if ( version_compare( PHP_VERSION, PSM_MIN_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
            return;
        }

        // Check WordPress version.
        global $wp_version;
        if ( version_compare( $wp_version, PSM_MIN_WP_VERSION, '<' ) ) {
            add_action( 'admin_notices', array( $this, 'wp_version_notice' ) );
            return;
        }
    }

    /**
     * Load plugin dependencies.
     *
     * @since 1.0.0
     * @return void
     */
    private function load_dependencies() {
        // Core includes.
        require_once PSM_PLUGIN_DIR . 'includes/class-psm-db.php';
        require_once PSM_PLUGIN_DIR . 'includes/class-psm-encryption.php';
        require_once PSM_PLUGIN_DIR . 'includes/class-psm-providers.php';
        require_once PSM_PLUGIN_DIR . 'includes/class-psm-mailer.php';
        require_once PSM_PLUGIN_DIR . 'includes/class-psm-logger.php';
        require_once PSM_PLUGIN_DIR . 'includes/class-psm-queue.php';
        require_once PSM_PLUGIN_DIR . 'includes/class-psm-ajax.php';
        require_once PSM_PLUGIN_DIR . 'includes/class-psm-privacy.php';
        require_once PSM_PLUGIN_DIR . 'includes/class-psm-debug-logger.php';

        // Admin includes.
        if ( is_admin() ) {
            require_once PSM_PLUGIN_DIR . 'admin/class-psm-admin.php';
            require_once PSM_PLUGIN_DIR . 'admin/class-psm-settings.php';
            require_once PSM_PLUGIN_DIR . 'admin/class-psm-logs.php';
            require_once PSM_PLUGIN_DIR . 'admin/class-psm-dashboard.php';
        }
    }

    /**
     * Initialize hooks.
     *
     * @since 1.0.0
     * @return void
     */
    private function init_hooks() {
        // Activation and deactivation hooks.
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // Initialize mailer IMMEDIATELY to capture all wp_mail calls.
        // This must run before any other plugin can call wp_mail.
        $this->mailer = new PSM_Mailer();
        $this->logger = new PSM_Logger();

        // Initialize other components after plugins are loaded.
        add_action( 'plugins_loaded', array( $this, 'init' ) );

        // Add settings link to plugins page.
        add_filter( 'plugin_action_links_' . PSM_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );

        // DB Migration check.
        add_action( 'admin_init', array( $this, 'check_db_version' ) );
    }

    /**
     * Initialize plugin components.
     *
     * @since 1.0.0
     * @return void
     */
    public function init() {
        // Initialize queue (can wait for plugins_loaded).
        $this->queue = new PSM_Queue();

        // Initialize admin.
        if ( is_admin() ) {
            $this->admin = new PSM_Admin();
        }

        // Initialize AJAX handlers.
        new PSM_Ajax();

        // Initialize privacy features.
        new PSM_Privacy();
    }

    /**
     * Plugin activation.
     *
     * @since 1.0.0
     * @return void
     */
    public function activate() {
        // Create database tables.
        PSM_DB::create_tables();

        // Set default options.
        $this->set_default_options();

        // Clear any cached data.
        wp_cache_flush();

        // Set activation flag for redirect.
        set_transient( 'PSM_activation_redirect', true, 30 );
    }

    /**
     * Plugin deactivation.
     *
     * @since 1.0.0
     * @return void
     */
    public function deactivate() {
        // Clear scheduled events.
        wp_clear_scheduled_hook( 'PSM_process_email_queue' );
        wp_clear_scheduled_hook( 'PSM_cleanup_logs' );

        // Clear any cached data.
        wp_cache_flush();
    }

    /**
     * Set default plugin options.
     *
     * @since 1.0.0
     * @return void
     */
    private function set_default_options() {
        $defaults = array(
            'smtp_host'           => '',
            'smtp_port'           => 587,
            'smtp_encryption'     => 'tls',
            'smtp_auth'           => true,
            'smtp_username'       => '',
            'smtp_password'       => '',
            'from_email'          => get_option( 'admin_email' ),
            'from_name'           => get_bloginfo( 'name' ),
            'force_from_email'    => false,
            'force_from_name'     => false,
            'enable_logging'      => true,
            'log_retention_days'  => 30,
            'enable_queue'        => false,
            'queue_batch_size'    => 10,
            'queue_interval'      => 5,
            'enable_backup_smtp'  => false,
            'backup_smtp_provider' => 'custom',
            'backup_smtp_host'    => '',
            'backup_smtp_port'    => 587,
            'backup_smtp_encryption' => 'tls',
            'backup_smtp_username'   => '',
            'backup_smtp_password'   => '',
            'debug_mode'          => false,
            'delete_data_on_uninstall' => false,
        );

        foreach ( $defaults as $key => $value ) {
            if ( false === get_option( 'PSM_' . $key ) ) {
                update_option( 'PSM_' . $key, $value );
            }
        }
    }



    /**
     * Add settings link to plugins page.
     *
     * @since 1.0.0
     * @param array $links Plugin action links.
     * @return array Modified plugin action links.
     */
    public function add_settings_link( $links ) {
        $settings_link = sprintf(
            '<a href="%s">%s</a>',
            esc_url( admin_url( 'admin.php?page=polar-smtp-mailer' ) ),
            esc_html__( 'Settings', 'polar-smtp-mailer' )
        );
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * PHP version notice.
     *
     * @since 1.0.0
     * @return void
     */
    public function php_version_notice() {
        ?>
        <div class="notice notice-error">
            <p>
                <?php
                printf(
                    /* translators: 1: Required PHP version, 2: Current PHP version */
                    esc_html__( 'Polar SMTP Mailer requires PHP %1$s or higher. You are running PHP %2$s. Please upgrade your PHP version.', 'polar-smtp-mailer' ),
                    esc_html( PSM_MIN_PHP_VERSION ),
                    esc_html( PHP_VERSION )
                );
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * WordPress version notice.
     *
     * @since 1.0.0
     * @return void
     */
    public function wp_version_notice() {
        global $wp_version;
        ?>
        <div class="notice notice-error">
            <p>
                <?php
                printf(
                    /* translators: 1: Required WordPress version, 2: Current WordPress version */
                    esc_html__( 'Polar SMTP Mailer requires WordPress %1$s or higher. You are running WordPress %2$s. Please upgrade your WordPress installation.', 'polar-smtp-mailer' ),
                    esc_html( PSM_MIN_WP_VERSION ),
                    esc_html( $wp_version )
                );
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Prevent cloning.
     *
     * @since 1.0.0
     * @return void
     */
    private function __clone() {}

    /**
     * Prevent unserializing.
     *
     * @since 1.0.0
     * @return void
     */
    public function __wakeup() {
        throw new Exception( 'Cannot unserialize singleton' );
    }

    /**
     * Check DB version and update if necessary.
     *
     * @since 1.0.0
     * @return void
     */
    public function check_db_version() {
        if ( get_option( 'PSM_db_version' ) !== PSM_DB::DB_VERSION || ! PSM_DB::check_tables_exist() ) {
            PSM_DB::create_tables();
        }
    }
}

/**
 * Returns the main instance of Polar_SMTP_Mailer.
 *
 * @since 1.0.0
 * @return Polar_SMTP_Mailer
 */
function polar_smtp_mailer() {
    return Polar_SMTP_Mailer::get_instance();
}

// Initialize the plugin.
polar_smtp_mailer();
