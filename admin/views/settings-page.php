<?php
/**
 * Settings page template.
 *
 * @package SimpleSmtpMail
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$settings = SSM_Settings::get_settings();
$providers = SSM_Providers::get_providers();
$encryption_options = SSM_Providers::get_encryption_options();
?>

<div class="wrap ssm-wrap">
    <h1 class="ssm-page-title">
        <span class="dashicons dashicons-admin-settings"></span>
        <?php esc_html_e( 'SMTP Settings', 'simple-smtp-mail' ); ?>
    </h1>

    <?php settings_errors(); ?>

    <form method="post" action="options.php" id="ssm-settings-form">
        <?php settings_fields( 'ssm_settings' ); ?>

        <div class="ssm-settings-container">
            <!-- SMTP Configuration -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'SMTP Configuration', 'simple-smtp-mail' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="ssm_smtp_provider"><?php esc_html_e( 'SMTP Provider', 'simple-smtp-mail' ); ?></label></th>
                        <td>
                            <select name="ssm_smtp_provider" id="ssm_smtp_provider" class="regular-text">
                                <?php foreach ( $providers as $key => $provider ) : ?>
                                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['smtp_provider'], $key ); ?>><?php echo esc_html( $provider['name'] ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description" id="ssm-provider-description"></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ssm_smtp_host"><?php esc_html_e( 'SMTP Host', 'simple-smtp-mail' ); ?></label></th>
                        <td><input type="text" name="ssm_smtp_host" id="ssm_smtp_host" value="<?php echo esc_attr( $settings['smtp_host'] ); ?>" class="regular-text" placeholder="smtp.example.com"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ssm_smtp_port"><?php esc_html_e( 'SMTP Port', 'simple-smtp-mail' ); ?></label></th>
                        <td>
                            <input type="number" name="ssm_smtp_port" id="ssm_smtp_port" value="<?php echo esc_attr( $settings['smtp_port'] ); ?>" class="small-text" min="1" max="65535" list="ssm_port_options" placeholder="587">
                            <datalist id="ssm_port_options">
                                <option value="25" label="None">
                                <option value="465" label="SSL">
                                <option value="587" label="TLS">
                                <option value="2525" label="Alt">
                            </datalist>
                            <p class="description"><?php esc_html_e( 'Select a common port or enter a custom one.', 'simple-smtp-mail' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ssm_smtp_encryption"><?php esc_html_e( 'Encryption', 'simple-smtp-mail' ); ?></label></th>
                        <td>
                            <select name="ssm_smtp_encryption" id="ssm_smtp_encryption">
                                <?php foreach ( $encryption_options as $key => $label ) : ?>
                                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['smtp_encryption'], $key ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Authentication', 'simple-smtp-mail' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="ssm_smtp_auth" id="ssm_smtp_auth" value="1" <?php checked( $settings['smtp_auth'] ); ?>> <?php esc_html_e( 'Use SMTP authentication', 'simple-smtp-mail' ); ?></label>
                        </td>
                    </tr>
                    <tr class="ssm-auth-field">
                        <th scope="row"><label for="ssm_smtp_username"><?php esc_html_e( 'Username', 'simple-smtp-mail' ); ?></label></th>
                        <td><input type="text" name="ssm_smtp_username" id="ssm_smtp_username" value="<?php echo esc_attr( $settings['smtp_username'] ); ?>" class="regular-text" autocomplete="off" placeholder="user@example.com"></td>
                    </tr>
                    <tr class="ssm-auth-field">
                        <th scope="row"><label for="ssm_smtp_password"><?php esc_html_e( 'Password', 'simple-smtp-mail' ); ?></label></th>
                        <td>
                            <input type="password" name="ssm_smtp_password" id="ssm_smtp_password" value="<?php echo esc_attr( $settings['smtp_password'] ); ?>" class="regular-text" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Enter your SMTP password', 'simple-smtp-mail' ); ?>">
                            <button type="button" class="button ssm-toggle-password" aria-label="<?php esc_attr_e( 'Show password', 'simple-smtp-mail' ); ?>"><span class="dashicons dashicons-visibility"></span></button>
                            <p class="description"><?php esc_html_e( 'Password is encrypted before storage.', 'simple-smtp-mail' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="ssm-test-connection">
                    <button type="button" class="button button-secondary" id="ssm-test-connection"><?php esc_html_e( 'Test Connection', 'simple-smtp-mail' ); ?></button>
                    <span class="ssm-test-result"></span>
                </p>
            </div>

            <!-- From Settings -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'From Settings', 'simple-smtp-mail' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="ssm_from_email"><?php esc_html_e( 'From Email', 'simple-smtp-mail' ); ?></label></th>
                        <td>
                            <input type="email" name="ssm_from_email" id="ssm_from_email" value="<?php echo esc_attr( $settings['from_email'] ); ?>" class="regular-text" placeholder="you@example.com">
                            <label class="ssm-force-option"><input type="checkbox" name="ssm_force_from_email" value="1" <?php checked( $settings['force_from_email'] ); ?>> <?php esc_html_e( 'Force this email', 'simple-smtp-mail' ); ?></label>
                            <p class="description"><?php esc_html_e( 'The email address emails will appear to come from.', 'simple-smtp-mail' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ssm_from_name"><?php esc_html_e( 'From Name', 'simple-smtp-mail' ); ?></label></th>
                        <td>
                            <input type="text" name="ssm_from_name" id="ssm_from_name" value="<?php echo esc_attr( $settings['from_name'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                            <label class="ssm-force-option"><input type="checkbox" name="ssm_force_from_name" value="1" <?php checked( $settings['force_from_name'] ); ?>> <?php esc_html_e( 'Force this name', 'simple-smtp-mail' ); ?></label>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Email Logging -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Email Logging', 'simple-smtp-mail' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable Logging', 'simple-smtp-mail' ); ?></th>
                        <td><label><input type="checkbox" name="ssm_enable_logging" value="1" <?php checked( $settings['enable_logging'] ); ?>> <?php esc_html_e( 'Log all outgoing emails', 'simple-smtp-mail' ); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ssm_log_retention_days"><?php esc_html_e( 'Log Retention', 'simple-smtp-mail' ); ?></label></th>
                        <td>
                            <input type="number" name="ssm_log_retention_days" id="ssm_log_retention_days" value="<?php echo esc_attr( $settings['log_retention_days'] ); ?>" class="small-text" min="0"> <?php esc_html_e( 'days', 'simple-smtp-mail' ); ?>
                            <p class="description"><?php esc_html_e( 'Set to 0 to keep logs forever.', 'simple-smtp-mail' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Email Queue -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Email Queue', 'simple-smtp-mail' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable Queue', 'simple-smtp-mail' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="ssm_enable_queue" value="1" <?php checked( $settings['enable_queue'] ); ?>> <?php esc_html_e( 'Queue emails for scheduled sending', 'simple-smtp-mail' ); ?></label>
                            <p class="description"><?php esc_html_e( 'Useful for bulk emails and rate limiting.', 'simple-smtp-mail' ); ?></p>
                        </td>
                    </tr>
                    <tr class="ssm-queue-field">
                        <th scope="row"><label for="ssm_queue_batch_size"><?php esc_html_e( 'Batch Size', 'simple-smtp-mail' ); ?></label></th>
                        <td><input type="number" name="ssm_queue_batch_size" id="ssm_queue_batch_size" value="<?php echo esc_attr( $settings['queue_batch_size'] ); ?>" class="small-text" min="1" max="100"> <?php esc_html_e( 'emails per batch', 'simple-smtp-mail' ); ?></td>
                    </tr>
                    <tr class="ssm-queue-field">
                        <th scope="row"><label for="ssm_queue_interval"><?php esc_html_e( 'Processing Interval', 'simple-smtp-mail' ); ?></label></th>
                        <td><input type="number" name="ssm_queue_interval" id="ssm_queue_interval" value="<?php echo esc_attr( $settings['queue_interval'] ); ?>" class="small-text" min="1" max="60"> <?php esc_html_e( 'minutes', 'simple-smtp-mail' ); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Backup SMTP -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Backup SMTP (Fallback)', 'simple-smtp-mail' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable Backup', 'simple-smtp-mail' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="ssm_enable_backup_smtp" id="ssm_enable_backup_smtp" value="1" <?php checked( $settings['enable_backup_smtp'] ); ?>> <?php esc_html_e( 'Use backup SMTP when primary fails', 'simple-smtp-mail' ); ?></label>
                        </td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="ssm_backup_smtp_host"><?php esc_html_e( 'Backup Host', 'simple-smtp-mail' ); ?></label></th>
                        <td><input type="text" name="ssm_backup_smtp_host" id="ssm_backup_smtp_host" value="<?php echo esc_attr( $settings['backup_smtp_host'] ); ?>" class="regular-text" placeholder="smtp.backup-provider.com"></td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="ssm_backup_smtp_port"><?php esc_html_e( 'Backup Port', 'simple-smtp-mail' ); ?></label></th>
                        <td><input type="number" name="ssm_backup_smtp_port" id="ssm_backup_smtp_port" value="<?php echo esc_attr( $settings['backup_smtp_port'] ); ?>" class="small-text" placeholder="587"></td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="ssm_backup_smtp_encryption"><?php esc_html_e( 'Encryption', 'simple-smtp-mail' ); ?></label></th>
                        <td>
                            <select name="ssm_backup_smtp_encryption" id="ssm_backup_smtp_encryption">
                                <?php foreach ( $encryption_options as $key => $label ) : ?>
                                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $settings['backup_smtp_encryption'], $key ); ?>><?php echo esc_html( $label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="ssm_backup_smtp_username"><?php esc_html_e( 'Username', 'simple-smtp-mail' ); ?></label></th>
                        <td><input type="text" name="ssm_backup_smtp_username" id="ssm_backup_smtp_username" value="<?php echo esc_attr( $settings['backup_smtp_username'] ); ?>" class="regular-text" placeholder="your_username"></td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="ssm_backup_smtp_password"><?php esc_html_e( 'Password', 'simple-smtp-mail' ); ?></label></th>
                        <td><input type="password" name="ssm_backup_smtp_password" id="ssm_backup_smtp_password" value="<?php echo esc_attr( $settings['backup_smtp_password'] ); ?>" class="regular-text" placeholder="••••••••"></td>
                    </tr>
                </table>
            </div>

            <!-- Debug -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Debug Settings', 'simple-smtp-mail' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Debug Mode', 'simple-smtp-mail' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="ssm_debug_mode" value="1" <?php checked( $settings['debug_mode'] ); ?>> <?php esc_html_e( 'Enable debug logging', 'simple-smtp-mail' ); ?></label>
                            <p class="description"><?php esc_html_e( 'Logs detailed SMTP communications to error log. Not recommended for production.', 'simple-smtp-mail' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Privacy & GDPR -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Privacy & GDPR', 'simple-smtp-mail' ); ?></h2>
                <p class="description"><?php esc_html_e( 'These settings help you comply with GDPR and other privacy regulations.', 'simple-smtp-mail' ); ?></p>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Email Content', 'simple-smtp-mail' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="ssm_privacy_exclude_content" value="1" <?php checked( get_option( 'ssm_privacy_exclude_content', false ) ); ?>> <?php esc_html_e( 'Do not log email body content', 'simple-smtp-mail' ); ?></label>
                            <p class="description"><?php esc_html_e( 'When enabled, only email metadata (recipient, subject, date) will be logged. Email body content will not be stored.', 'simple-smtp-mail' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Data Erasure', 'simple-smtp-mail' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="ssm_privacy_anonymize" value="1" <?php checked( get_option( 'ssm_privacy_anonymize', false ) ); ?>> <?php esc_html_e( 'Anonymize instead of delete', 'simple-smtp-mail' ); ?></label>
                            <p class="description"><?php esc_html_e( 'When processing erasure requests via WordPress Privacy Tools, anonymize personal data instead of deleting records completely.', 'simple-smtp-mail' ); ?></p>
                        </td>
                    </tr>
                </table>

                <div class="ssm-privacy-info">
                    <h4><?php esc_html_e( 'Privacy Compliance Features', 'simple-smtp-mail' ); ?></h4>
                    <ul>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'WordPress Privacy Tools integration (export & erasure)', 'simple-smtp-mail' ); ?></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Automatic log retention with scheduled cleanup', 'simple-smtp-mail' ); ?></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Privacy policy suggested content', 'simple-smtp-mail' ); ?></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'SMTP credentials encrypted at rest', 'simple-smtp-mail' ); ?></li>
                    </ul>
                    <p><a href="<?php echo esc_url( admin_url( 'options-privacy.php' ) ); ?>" class="button"><?php esc_html_e( 'View Privacy Policy Guide', 'simple-smtp-mail' ); ?></a></p>
                </div>
            </div>
        </div>

        <?php submit_button( __( 'Save Settings', 'simple-smtp-mail' ) ); ?>
    </form>
</div>
