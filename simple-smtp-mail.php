<?php
/**
 * Plugin Name:       Simple SMTP Mail
 * Plugin URI:        https://github.com/boopathirbk/Simple-SMTP-Mail
 * Description:       A powerful, open-source SMTP mailer plugin with comprehensive email logging, queue management, and modern dashboard. Configure any SMTP provider easily and track all your WordPress emails.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Boopathi R.
 * Author URI:        https://linkedin.com/in/boopathirb
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       simple-smtp-mail
 * Domain Path:       /languages
 *
 * @package SimpleSmtpMail
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Plugin version.
 */
define( 'SSM_VERSION', '1.0.0' );

/**
 * Plugin directory path.
 */
define( 'SSM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL.
 */
define( 'SSM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename.
 */
define( 'SSM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Minimum PHP version.
 */
define( 'SSM_MIN_PHP_VERSION', '7.4' );

/**
 * Minimum WordPress version.
 */
define( 'SSM_MIN_WP_VERSION', '6.0' );

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
final class Simple_SMTP_Mail {

    /**
     * Single instance of the class.
     *
     * @var Simple_SMTP_Mail|null
     */
    private static $instance = null;

    /**
     * Admin instance.
     *
     * @var SSM_Admin|null
     */
    public $admin = null;

    /**
     * Mailer instance.
     *
     * @var SSM_Mailer|null
     */
    public $mailer = null;

    /**
     * Logger instance.
     *
     * @var SSM_Logger|null
     */
    public $logger = null;

    /**
     * Queue instance.
     *
     * @var SSM_Queue|null
     */
    public $queue = null;

    /**
     * Get the single instance of the class.
     *
     * @since 1.0.0
     * @return Simple_SMTP_Mail
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
        if ( version_compare( PHP_VERSION, SSM_MIN_PHP_VERSION, '<' ) ) {
            add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
            return;
        }

        // Check WordPress version.
        global $wp_version;
        if ( version_compare( $wp_version, SSM_MIN_WP_VERSION, '<' ) ) {
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
        require_once SSM_PLUGIN_DIR . 'includes/class-ssm-db.php';
        require_once SSM_PLUGIN_DIR . 'includes/class-ssm-encryption.php';
        require_once SSM_PLUGIN_DIR . 'includes/class-ssm-providers.php';
        require_once SSM_PLUGIN_DIR . 'includes/class-ssm-mailer.php';
        require_once SSM_PLUGIN_DIR . 'includes/class-ssm-logger.php';
        require_once SSM_PLUGIN_DIR . 'includes/class-ssm-queue.php';
        require_once SSM_PLUGIN_DIR . 'includes/class-ssm-ajax.php';
        require_once SSM_PLUGIN_DIR . 'includes/class-ssm-privacy.php';

        // Admin includes.
        if ( is_admin() ) {
            require_once SSM_PLUGIN_DIR . 'admin/class-ssm-admin.php';
            require_once SSM_PLUGIN_DIR . 'admin/class-ssm-settings.php';
            require_once SSM_PLUGIN_DIR . 'admin/class-ssm-logs.php';
            require_once SSM_PLUGIN_DIR . 'admin/class-ssm-dashboard.php';
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

        // Initialize plugin after plugins are loaded.
        add_action( 'plugins_loaded', array( $this, 'init' ) );

        // Load text domain.
        add_action( 'init', array( $this, 'load_textdomain' ) );

        // Add settings link to plugins page.
        add_filter( 'plugin_action_links_' . SSM_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );

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
        // Initialize core components.
        $this->mailer = new SSM_Mailer();
        $this->logger = new SSM_Logger();
        $this->queue  = new SSM_Queue();

        // Initialize admin.
        if ( is_admin() ) {
            $this->admin = new SSM_Admin();
        }

        // Initialize AJAX handlers.
        new SSM_Ajax();

        // Initialize privacy features.
        new SSM_Privacy();
    }

    /**
     * Plugin activation.
     *
     * @since 1.0.0
     * @return void
     */
    public function activate() {
        // Create database tables.
        SSM_DB::create_tables();

        // Set default options.
        $this->set_default_options();

        // Clear any cached data.
        wp_cache_flush();

        // Set activation flag for redirect.
        set_transient( 'ssm_activation_redirect', true, 30 );
    }

    /**
     * Plugin deactivation.
     *
     * @since 1.0.0
     * @return void
     */
    public function deactivate() {
        // Clear scheduled events.
        wp_clear_scheduled_hook( 'ssm_process_email_queue' );
        wp_clear_scheduled_hook( 'ssm_cleanup_logs' );

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
            if ( false === get_option( 'ssm_' . $key ) ) {
                update_option( 'ssm_' . $key, $value );
            }
        }
    }

    /**
     * Load plugin text domain.
     *
     * @since 1.0.0
     * @return void
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'simple-smtp-mail',
            false,
            dirname( SSM_PLUGIN_BASENAME ) . '/languages'
        );
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
            esc_url( admin_url( 'admin.php?page=simple-smtp-mail' ) ),
            esc_html__( 'Settings', 'simple-smtp-mail' )
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
                    esc_html__( 'Simple SMTP Mail requires PHP %1$s or higher. You are running PHP %2$s. Please upgrade your PHP version.', 'simple-smtp-mail' ),
                    esc_html( SSM_MIN_PHP_VERSION ),
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
                    esc_html__( 'Simple SMTP Mail requires WordPress %1$s or higher. You are running WordPress %2$s. Please upgrade your WordPress installation.', 'simple-smtp-mail' ),
                    esc_html( SSM_MIN_WP_VERSION ),
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
        if ( get_option( 'ssm_db_version' ) !== SSM_DB::DB_VERSION || ! SSM_DB::check_tables_exist() ) {
            SSM_DB::create_tables();
        }
    }
}

/**
 * Returns the main instance of Simple_SMTP_Mail.
 *
 * @since 1.0.0
 * @return Simple_SMTP_Mail
 */
function simple_smtp_mail() {
    return Simple_SMTP_Mail::get_instance();
}

// Initialize the plugin.
simple_smtp_mail();
