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

    <div class="ssm-about-container">
        <!-- Plugin Info Card -->
        <div class="ssm-card ssm-about-card">
            <div class="ssm-card-header">
                <h2><span class="dashicons dashicons-email-alt"></span> <?php esc_html_e( 'Simple SMTP Mail', 'simple-smtp-mail' ); ?></h2>
            </div>
            <div class="ssm-card-body">
                <p class="ssm-version">
                    <strong><?php esc_html_e( 'Version:', 'simple-smtp-mail' ); ?></strong> <?php echo esc_html( SSM_VERSION ); ?>
                </p>
                <p class="ssm-description">
                    <?php esc_html_e( 'A powerful, open-source WordPress SMTP plugin with comprehensive email logging, queue management, backup SMTP failover, and a modern admin dashboard.', 'simple-smtp-mail' ); ?>
                </p>
                
                <h3><?php esc_html_e( 'Key Features', 'simple-smtp-mail' ); ?></h3>
                <ul class="ssm-feature-list">
                    <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( '15+ Pre-configured SMTP Providers', 'simple-smtp-mail' ); ?></li>
                    <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Email Logging with Search & Export', 'simple-smtp-mail' ); ?></li>
                    <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Email Queue with Background Processing', 'simple-smtp-mail' ); ?></li>
                    <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Backup SMTP Failover', 'simple-smtp-mail' ); ?></li>
                    <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'AES-256 Password Encryption', 'simple-smtp-mail' ); ?></li>
                    <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'GDPR Compliant Privacy Features', 'simple-smtp-mail' ); ?></li>
                    <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Modern Dashboard with Statistics', 'simple-smtp-mail' ); ?></li>
                </ul>
            </div>
        </div>

        <!-- Author Card -->
        <div class="ssm-card ssm-author-card">
            <div class="ssm-card-header">
                <h2><span class="dashicons dashicons-admin-users"></span> <?php esc_html_e( 'Developer', 'simple-smtp-mail' ); ?></h2>
            </div>
            <div class="ssm-card-body">
                <div class="ssm-author-info">
                    <div class="ssm-author-avatar">
                        <img src="https://avatars.githubusercontent.com/u/20055518?v=4" alt="Boopathi R." width="80" height="80">
                    </div>
                    <div class="ssm-author-details">
                        <h3>Boopathi R.</h3>
                        <p><?php esc_html_e( 'Full Stack Developer', 'simple-smtp-mail' ); ?></p>
                    </div>
                </div>
                
                <div class="ssm-author-links">
                    <a href="https://github.com/boopathirbk" target="_blank" rel="noopener noreferrer" class="ssm-link-button">
                        <span class="dashicons dashicons-github"></span> GitHub
                    </a>
                    <a href="https://linkedin.com/in/boopathirb" target="_blank" rel="noopener noreferrer" class="ssm-link-button">
                        <span class="dashicons dashicons-linkedin"></span> LinkedIn
                    </a>
                </div>
            </div>
        </div>

        <!-- Support Card -->
        <div class="ssm-card ssm-support-card">
            <div class="ssm-card-header">
                <h2><span class="dashicons dashicons-sos"></span> <?php esc_html_e( 'Support', 'simple-smtp-mail' ); ?></h2>
            </div>
            <div class="ssm-card-body">
                <p><?php esc_html_e( 'Need help? Have questions or suggestions? Feel free to reach out!', 'simple-smtp-mail' ); ?></p>
                
                <div class="ssm-support-options">
                    <div class="ssm-support-item">
                        <span class="dashicons dashicons-email"></span>
                        <div>
                            <strong><?php esc_html_e( 'Email Support', 'simple-smtp-mail' ); ?></strong>
                            <a href="mailto:genius@duck.com">genius@duck.com</a>
                        </div>
                    </div>
                    <div class="ssm-support-item">
                        <span class="dashicons dashicons-admin-comments"></span>
                        <div>
                            <strong><?php esc_html_e( 'GitHub Issues', 'simple-smtp-mail' ); ?></strong>
                            <a href="https://github.com/boopathirbk/Simple-SMTP-Mail/issues" target="_blank" rel="noopener noreferrer">
                                <?php esc_html_e( 'Report a Bug', 'simple-smtp-mail' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donate Card -->
        <div class="ssm-card ssm-donate-card">
            <div class="ssm-card-header">
                <h2><span class="dashicons dashicons-heart"></span> <?php esc_html_e( 'Support This Project', 'simple-smtp-mail' ); ?></h2>
            </div>
            <div class="ssm-card-body">
                <p><?php esc_html_e( 'If you find this plugin useful, consider buying me a coffee! Your support helps keep this project alive and free for everyone.', 'simple-smtp-mail' ); ?></p>
                
                <a href="https://paypal.me/boopathirbk" target="_blank" rel="noopener noreferrer" class="ssm-donate-button">
                    <span class="dashicons dashicons-money-alt"></span>
                    <?php esc_html_e( 'Donate via PayPal', 'simple-smtp-mail' ); ?>
                </a>
                
                <p class="ssm-donate-note">
                    <span class="dashicons dashicons-star-filled"></span>
                    <?php esc_html_e( 'Also, please leave a 5-star review if you enjoy the plugin!', 'simple-smtp-mail' ); ?>
                </p>
            </div>
        </div>

        <!-- License Card -->
        <div class="ssm-card ssm-license-card">
            <div class="ssm-card-header">
                <h2><span class="dashicons dashicons-media-text"></span> <?php esc_html_e( 'License', 'simple-smtp-mail' ); ?></h2>
            </div>
            <div class="ssm-card-body">
                <p>
                    <?php esc_html_e( 'Simple SMTP Mail is open-source software licensed under the', 'simple-smtp-mail' ); ?>
                    <a href="https://www.gnu.org/licenses/gpl-2.0.html" target="_blank" rel="noopener noreferrer">GPL v2 or later</a>.
                </p>
                <p class="ssm-license-note">
                    <?php esc_html_e( 'You are free to use, modify, and distribute this plugin.', 'simple-smtp-mail' ); ?>
                </p>
            </div>
        </div>
    </div>
</div>
