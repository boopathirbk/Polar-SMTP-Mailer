<?php
/**
 * About page template.
 *
 * @package SimpleSmtpMail
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="wrap ssm-wrap">
    <h1 class="ssm-page-title">
        <span class="dashicons dashicons-info"></span>
        <?php esc_html_e( 'About Simple SMTP Mail', 'simple-smtp-mail' ); ?>
    </h1>

    <div class="ssm-about-grid">
        <!-- Plugin Info Card -->
        <div class="ssm-card">
            <h2><span class="dashicons dashicons-email-alt"></span> <?php esc_html_e( 'Simple SMTP Mail', 'simple-smtp-mail' ); ?></h2>
            <p><strong><?php esc_html_e( 'Version:', 'simple-smtp-mail' ); ?></strong> <?php echo esc_html( SSM_VERSION ); ?></p>
            <p><?php esc_html_e( 'A powerful, open-source WordPress SMTP plugin with comprehensive email logging, queue management, backup SMTP failover, and a modern admin dashboard.', 'simple-smtp-mail' ); ?></p>
            
            <h3><?php esc_html_e( 'Key Features', 'simple-smtp-mail' ); ?></h3>
            <ul>
                <li>✅ <?php esc_html_e( '15+ Pre-configured SMTP Providers', 'simple-smtp-mail' ); ?></li>
                <li>✅ <?php esc_html_e( 'Email Logging with Search & Export', 'simple-smtp-mail' ); ?></li>
                <li>✅ <?php esc_html_e( 'Email Queue with Background Processing', 'simple-smtp-mail' ); ?></li>
                <li>✅ <?php esc_html_e( 'Backup SMTP Failover', 'simple-smtp-mail' ); ?></li>
                <li>✅ <?php esc_html_e( 'AES-256 Password Encryption', 'simple-smtp-mail' ); ?></li>
                <li>✅ <?php esc_html_e( 'GDPR Compliant Privacy Features', 'simple-smtp-mail' ); ?></li>
                <li>✅ <?php esc_html_e( 'Modern Dashboard with Statistics', 'simple-smtp-mail' ); ?></li>
            </ul>
        </div>

        <!-- Developer & Links Card -->
        <div class="ssm-card">
            <h2><span class="dashicons dashicons-admin-users"></span> <?php esc_html_e( 'Developer', 'simple-smtp-mail' ); ?></h2>
            <p><strong>Boopathi R.</strong></p>
            
            <p>
                <a href="https://github.com/boopathirbk/Simple-SMTP-Mail" target="_blank" rel="noopener noreferrer" class="button button-secondary">
                    <span class="dashicons dashicons-external" style="vertical-align: middle; margin-top: -2px;"></span> <?php esc_html_e( 'GitHub Repository', 'simple-smtp-mail' ); ?>
                </a>
                <a href="https://linkedin.com/in/boopathirb" target="_blank" rel="noopener noreferrer" class="button button-secondary">
                    <span class="dashicons dashicons-businessperson" style="vertical-align: middle; margin-top: -2px;"></span> <?php esc_html_e( 'LinkedIn', 'simple-smtp-mail' ); ?>
                </a>
            </p>
        </div>

        <!-- Support Card -->
        <div class="ssm-card">
            <h2><span class="dashicons dashicons-sos"></span> <?php esc_html_e( 'Support', 'simple-smtp-mail' ); ?></h2>
            <p><?php esc_html_e( 'Need help? Have questions or suggestions? Feel free to reach out!', 'simple-smtp-mail' ); ?></p>
            
            <table class="widefat striped" style="margin-top: 15px;">
                <tr>
                    <td><span class="dashicons dashicons-email" style="color: #2271b1;"></span> <strong><?php esc_html_e( 'Email Support', 'simple-smtp-mail' ); ?></strong></td>
                    <td><a href="mailto:genius@duck.com">genius@duck.com</a></td>
                </tr>
                <tr>
                    <td><span class="dashicons dashicons-admin-comments" style="color: #2271b1;"></span> <strong><?php esc_html_e( 'GitHub Issues', 'simple-smtp-mail' ); ?></strong></td>
                    <td><a href="https://github.com/boopathirbk/Simple-SMTP-Mail/issues" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Report a Bug', 'simple-smtp-mail' ); ?></a></td>
                </tr>
            </table>
        </div>

        <!-- Donate Card -->
        <div class="ssm-card">
            <h2><span class="dashicons dashicons-heart"></span> <?php esc_html_e( 'Support This Project', 'simple-smtp-mail' ); ?></h2>
            <p><?php esc_html_e( 'If you find this plugin useful, consider buying me a coffee! Your support helps keep this project alive and free for everyone.', 'simple-smtp-mail' ); ?></p>
            
            <p style="margin: 20px 0;">
                <a href="https://paypal.me/boopathirbk" target="_blank" rel="noopener noreferrer" class="button button-primary button-hero">
                    <span class="dashicons dashicons-money-alt" style="vertical-align: middle; margin-top: -2px;"></span> <?php esc_html_e( 'Donate via PayPal', 'simple-smtp-mail' ); ?>
                </a>
            </p>
            
            <p>
                <span class="dashicons dashicons-star-filled" style="color: #f0b849;"></span>
                <?php esc_html_e( 'Also, please leave a 5-star review if you enjoy the plugin!', 'simple-smtp-mail' ); ?>
            </p>
        </div>

        <!-- License Card -->
        <div class="ssm-card">
            <h2><span class="dashicons dashicons-media-text"></span> <?php esc_html_e( 'License', 'simple-smtp-mail' ); ?></h2>
            <p>
                <?php esc_html_e( 'Simple SMTP Mail is open-source software licensed under the', 'simple-smtp-mail' ); ?>
                <a href="https://www.gnu.org/licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">GPL v2 or later</a>.
            </p>
            <p style="color: #646970;">
                <?php esc_html_e( 'You are free to use, modify, and distribute this plugin.', 'simple-smtp-mail' ); ?>
            </p>
        </div>
    </div>
</div>

<style>
.ssm-about-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.ssm-about-grid .ssm-card {
    background: #fff;
    border: 1px solid #c3c4c7;
    border-radius: 4px;
    padding: 20px;
}
.ssm-about-grid .ssm-card h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #c3c4c7;
}
.ssm-about-grid .ssm-card h2 .dashicons {
    color: #2271b1;
    margin-right: 5px;
}
.ssm-about-grid .ssm-card ul {
    margin-left: 0;
    padding-left: 0;
    list-style: none;
}
.ssm-about-grid .ssm-card ul li {
    padding: 5px 0;
}
.ssm-about-grid .button {
    margin-right: 10px;
    margin-bottom: 10px;
}
</style>
