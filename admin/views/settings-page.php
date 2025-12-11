<?php
/**
 * Settings page template.
 *
 * @package PolarSmtpMailer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$psm_settings = PSM_Settings::get_settings();
$psm_providers = PSM_Providers::get_providers();
$psm_encryption_options = PSM_Providers::get_encryption_options();
?>

<div class="wrap ssm-wrap">
    <h1 class="ssm-page-title">
        <span class="dashicons dashicons-admin-settings"></span>
        <?php esc_html_e( 'SMTP Settings', 'polar-smtp-mailer' ); ?>
    </h1>

    <?php settings_errors(); ?>

    <form method="post" action="options.php" id="ssm-settings-form">
        <?php settings_fields( 'PSM_settings' ); ?>

        <div class="ssm-settings-container">
            <!-- SMTP Configuration -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'SMTP Configuration', 'polar-smtp-mailer' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="PSM_smtp_provider"><?php esc_html_e( 'SMTP Provider', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <select name="PSM_smtp_provider" id="PSM_smtp_provider" class="regular-text">
                                <?php foreach ( $psm_providers as $psm_key => $psm_provider ) : ?>
                                    <option value="<?php echo esc_attr( $psm_key ); ?>" <?php selected( $psm_settings['smtp_provider'], $psm_key ); ?>><?php echo esc_html( $psm_provider['name'] ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description" id="ssm-provider-description"></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="PSM_smtp_host"><?php esc_html_e( 'SMTP Host', 'polar-smtp-mailer' ); ?></label></th>
                        <td><input type="text" name="PSM_smtp_host" id="PSM_smtp_host" value="<?php echo esc_attr( $psm_settings['smtp_host'] ); ?>" class="regular-text" placeholder="smtp.example.com"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="PSM_smtp_port"><?php esc_html_e( 'SMTP Port', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <select name="PSM_smtp_port" id="PSM_smtp_port">
                                <option value="25" <?php selected( $psm_settings['smtp_port'], 25 ); ?>>25 (None/Unencrypted)</option>
                                <option value="465" <?php selected( $psm_settings['smtp_port'], 465 ); ?>>465 (SSL)</option>
                                <option value="587" <?php selected( $psm_settings['smtp_port'], 587 ); ?>>587 (TLS - Recommended)</option>
                                <option value="2525" <?php selected( $psm_settings['smtp_port'], 2525 ); ?>>2525 (Alternative)</option>
                            </select>
                            <p class="description"><?php esc_html_e( 'Select the SMTP port. 587 (TLS) is recommended for most providers.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="PSM_smtp_encryption"><?php esc_html_e( 'Encryption', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <select name="PSM_smtp_encryption" id="PSM_smtp_encryption">
                                <?php foreach ( $psm_encryption_options as $psm_key => $psm_label ) : ?>
                                    <option value="<?php echo esc_attr( $psm_key ); ?>" <?php selected( $psm_settings['smtp_encryption'], $psm_key ); ?>><?php echo esc_html( $psm_label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Authentication', 'polar-smtp-mailer' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="PSM_smtp_auth" id="PSM_smtp_auth" value="1" <?php checked( $psm_settings['smtp_auth'] ); ?>> <?php esc_html_e( 'Use SMTP authentication', 'polar-smtp-mailer' ); ?></label>
                        </td>
                    </tr>
                    <tr class="ssm-auth-field">
                        <th scope="row"><label for="PSM_smtp_username"><?php esc_html_e( 'Username', 'polar-smtp-mailer' ); ?></label></th>
                        <td><input type="text" name="PSM_smtp_username" id="PSM_smtp_username" value="<?php echo esc_attr( $psm_settings['smtp_username'] ); ?>" class="regular-text" autocomplete="off" placeholder="user@example.com"></td>
                    </tr>
                    <tr class="ssm-auth-field">
                        <th scope="row"><label for="PSM_smtp_password"><?php esc_html_e( 'Password', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <input type="password" name="PSM_smtp_password" id="PSM_smtp_password" value="<?php echo esc_attr( $psm_settings['smtp_password'] ); ?>" class="regular-text" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Enter your SMTP password', 'polar-smtp-mailer' ); ?>">
                            <button type="button" class="button ssm-toggle-password" aria-label="<?php esc_attr_e( 'Show password', 'polar-smtp-mailer' ); ?>"><span class="dashicons dashicons-visibility"></span></button>
                            <p class="description"><?php esc_html_e( 'Password is encrypted before storage.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="ssm-test-connection">
                    <button type="button" class="button button-secondary" id="ssm-test-connection"><?php esc_html_e( 'Test Connection', 'polar-smtp-mailer' ); ?></button>
                    <span class="ssm-test-result"></span>
                </p>
                <p class="description ssm-test-note">
                    <span class="dashicons dashicons-info"></span>
                    <?php esc_html_e( 'Note: After saving settings, you must re-enter your password to test the connection. Saved passwords are masked for security and cannot be used for testing.', 'polar-smtp-mailer' ); ?>
                </p>
            </div>

            <!-- From Settings -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'From Settings', 'polar-smtp-mailer' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="PSM_from_email"><?php esc_html_e( 'From Email', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <input type="email" name="PSM_from_email" id="PSM_from_email" value="<?php echo esc_attr( $psm_settings['from_email'] ); ?>" class="regular-text" placeholder="you@example.com">
                            <label class="ssm-force-option"><input type="checkbox" name="PSM_force_from_email" value="1" <?php checked( $psm_settings['force_from_email'] ); ?>> <?php esc_html_e( 'Force this email', 'polar-smtp-mailer' ); ?></label>
                            <p class="description"><?php esc_html_e( 'The email address emails will appear to come from.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="PSM_from_name"><?php esc_html_e( 'From Name', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <input type="text" name="PSM_from_name" id="PSM_from_name" value="<?php echo esc_attr( $psm_settings['from_name'] ); ?>" class="regular-text" placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                            <label class="ssm-force-option"><input type="checkbox" name="PSM_force_from_name" value="1" <?php checked( $psm_settings['force_from_name'] ); ?>> <?php esc_html_e( 'Force this name', 'polar-smtp-mailer' ); ?></label>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Email Logging -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Email Logging', 'polar-smtp-mailer' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable Logging', 'polar-smtp-mailer' ); ?></th>
                        <td><label><input type="checkbox" name="PSM_enable_logging" value="1" <?php checked( $psm_settings['enable_logging'] ); ?>> <?php esc_html_e( 'Log all outgoing emails', 'polar-smtp-mailer' ); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="PSM_log_retention_days"><?php esc_html_e( 'Log Retention', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <input type="number" name="PSM_log_retention_days" id="PSM_log_retention_days" value="<?php echo esc_attr( $psm_settings['log_retention_days'] ); ?>" class="small-text" min="0"> <?php esc_html_e( 'days', 'polar-smtp-mailer' ); ?>
                            <p class="description"><?php esc_html_e( 'Set to 0 to keep logs forever.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Email Queue -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Email Queue', 'polar-smtp-mailer' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable Queue', 'polar-smtp-mailer' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="PSM_enable_queue" value="1" <?php checked( $psm_settings['enable_queue'] ); ?>> <?php esc_html_e( 'Queue emails for scheduled sending', 'polar-smtp-mailer' ); ?></label>
                            <p class="description"><?php esc_html_e( 'Useful for bulk emails and rate limiting.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                    <tr class="ssm-queue-field">
                        <th scope="row"><label for="PSM_queue_batch_size"><?php esc_html_e( 'Batch Size', 'polar-smtp-mailer' ); ?></label></th>
                        <td><input type="number" name="PSM_queue_batch_size" id="PSM_queue_batch_size" value="<?php echo esc_attr( $psm_settings['queue_batch_size'] ); ?>" class="small-text" min="1" max="100"> <?php esc_html_e( 'emails per batch', 'polar-smtp-mailer' ); ?></td>
                    </tr>
                    <tr class="ssm-queue-field">
                        <th scope="row"><label for="PSM_queue_interval"><?php esc_html_e( 'Processing Interval', 'polar-smtp-mailer' ); ?></label></th>
                        <td><input type="number" name="PSM_queue_interval" id="PSM_queue_interval" value="<?php echo esc_attr( $psm_settings['queue_interval'] ); ?>" class="small-text" min="1" max="60"> <?php esc_html_e( 'minutes', 'polar-smtp-mailer' ); ?></td>
                    </tr>
                </table>
            </div>

            <!-- Backup SMTP -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Backup SMTP (Fallback)', 'polar-smtp-mailer' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable Backup', 'polar-smtp-mailer' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="PSM_enable_backup_smtp" id="PSM_enable_backup_smtp" value="1" <?php checked( $psm_settings['enable_backup_smtp'] ); ?>> <?php esc_html_e( 'Use backup SMTP when primary fails', 'polar-smtp-mailer' ); ?></label>
                        </td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="PSM_backup_smtp_provider"><?php esc_html_e( 'Backup Provider', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <select name="PSM_backup_smtp_provider" id="PSM_backup_smtp_provider">
                                <?php foreach ( $psm_providers as $psm_key => $psm_provider ) : ?>
                                    <option value="<?php echo esc_attr( $psm_key ); ?>" <?php selected( $psm_settings['backup_smtp_provider'], $psm_key ); ?>><?php echo esc_html( $psm_provider['name'] ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description" id="ssm-backup-provider-description"></p>
                        </td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="PSM_backup_smtp_host"><?php esc_html_e( 'Backup Host', 'polar-smtp-mailer' ); ?></label></th>
                        <td><input type="text" name="PSM_backup_smtp_host" id="PSM_backup_smtp_host" value="<?php echo esc_attr( $psm_settings['backup_smtp_host'] ); ?>" class="regular-text" placeholder="smtp.backup-provider.com"></td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="PSM_backup_smtp_port"><?php esc_html_e( 'Backup Port', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <select name="PSM_backup_smtp_port" id="PSM_backup_smtp_port">
                                <option value="25" <?php selected( $psm_settings['backup_smtp_port'], 25 ); ?>>25 (None/Unencrypted)</option>
                                <option value="465" <?php selected( $psm_settings['backup_smtp_port'], 465 ); ?>>465 (SSL)</option>
                                <option value="587" <?php selected( $psm_settings['backup_smtp_port'], 587 ); ?>>587 (TLS - Recommended)</option>
                                <option value="2525" <?php selected( $psm_settings['backup_smtp_port'], 2525 ); ?>>2525 (Alternative)</option>
                            </select>
                            <p class="description"><?php esc_html_e( 'Select the SMTP port for backup server.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="PSM_backup_smtp_encryption"><?php esc_html_e( 'Encryption', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <select name="PSM_backup_smtp_encryption" id="PSM_backup_smtp_encryption">
                                <?php foreach ( $psm_encryption_options as $psm_key => $psm_label ) : ?>
                                    <option value="<?php echo esc_attr( $psm_key ); ?>" <?php selected( $psm_settings['backup_smtp_encryption'], $psm_key ); ?>><?php echo esc_html( $psm_label ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="PSM_backup_smtp_username"><?php esc_html_e( 'Username', 'polar-smtp-mailer' ); ?></label></th>
                        <td><input type="text" name="PSM_backup_smtp_username" id="PSM_backup_smtp_username" value="<?php echo esc_attr( $psm_settings['backup_smtp_username'] ); ?>" class="regular-text" placeholder="user@example.com"></td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="PSM_backup_smtp_password"><?php esc_html_e( 'Password', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <input type="password" name="PSM_backup_smtp_password" id="PSM_backup_smtp_password" value="<?php echo esc_attr( $psm_settings['backup_smtp_password'] ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'Enter your SMTP password', 'polar-smtp-mailer' ); ?>">
                            <button type="button" class="button ssm-toggle-password" aria-label="<?php esc_attr_e( 'Show password', 'polar-smtp-mailer' ); ?>"><span class="dashicons dashicons-visibility"></span></button>
                            <p class="description"><?php esc_html_e( 'Password is encrypted before storage.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="PSM_backup_from_email"><?php esc_html_e( 'Prioritize Backup From Email', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <input type="email" name="PSM_backup_from_email" id="PSM_backup_from_email" value="<?php echo esc_attr( get_option( 'PSM_backup_from_email' ) ); ?>" class="regular-text" placeholder="backup@example.com">
                            <p class="description"><?php esc_html_e( 'Optional. Leave empty to use primary "From Email".', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                    <tr class="ssm-backup-field">
                        <th scope="row"><label for="PSM_backup_from_name"><?php esc_html_e( 'Prioritize Backup From Name', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <input type="text" name="PSM_backup_from_name" id="PSM_backup_from_name" value="<?php echo esc_attr( get_option( 'PSM_backup_from_name' ) ); ?>" class="regular-text" placeholder="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
                            <p class="description"><?php esc_html_e( 'Optional. Leave empty to use primary "From Name".', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                </table>
                <p class="ssm-test-connection">
                    <button type="button" class="button button-secondary" id="ssm-test-backup-connection"><?php esc_html_e( 'Test Backup Connection', 'polar-smtp-mailer' ); ?></button>
                    <span class="ssm-backup-test-result"></span>
                </p>
                <p class="description ssm-test-note">
                     <span class="dashicons dashicons-info"></span>
                     <?php esc_html_e( 'Note: Similar to primary settings, please re-enter passwords, save settings and then click Test Backup Connection.', 'polar-smtp-mailer' ); ?>
                 </p>
            </div>

            <!-- Debug -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Debug Settings', 'polar-smtp-mailer' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Debug Mode', 'polar-smtp-mailer' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="PSM_debug_mode" value="1" <?php checked( $psm_settings['debug_mode'] ); ?>> <?php esc_html_e( 'Enable debug logging', 'polar-smtp-mailer' ); ?></label>
                            <p class="description"><?php esc_html_e( 'Logs detailed SMTP communications to error log. Not recommended for production.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Privacy & GDPR -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Privacy & GDPR', 'polar-smtp-mailer' ); ?></h2>
                <p class="description"><?php esc_html_e( 'These settings help you comply with GDPR and other privacy regulations.', 'polar-smtp-mailer' ); ?></p>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Email Content', 'polar-smtp-mailer' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="PSM_privacy_exclude_content" value="1" <?php checked( get_option( 'PSM_privacy_exclude_content', false ) ); ?>> <?php esc_html_e( 'Do not log email body content', 'polar-smtp-mailer' ); ?></label>
                            <p class="description"><?php esc_html_e( 'When enabled, only email metadata (recipient, subject, date) will be logged. Email body content will not be stored.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Data Erasure', 'polar-smtp-mailer' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="PSM_privacy_anonymize" value="1" <?php checked( get_option( 'PSM_privacy_anonymize', false ) ); ?>> <?php esc_html_e( 'Anonymize instead of delete', 'polar-smtp-mailer' ); ?></label>
                            <p class="description"><?php esc_html_e( 'When processing erasure requests via WordPress Privacy Tools, anonymize personal data instead of deleting records completely.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                </table>

                <div class="ssm-privacy-info">
                    <h4><?php esc_html_e( 'Privacy Compliance Features', 'polar-smtp-mailer' ); ?></h4>
                    <ul>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'WordPress Privacy Tools integration (export & erasure)', 'polar-smtp-mailer' ); ?></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Automatic log retention with scheduled cleanup', 'polar-smtp-mailer' ); ?></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'Privacy policy suggested content', 'polar-smtp-mailer' ); ?></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e( 'SMTP credentials encrypted at rest', 'polar-smtp-mailer' ); ?></li>
                    </ul>
                    <p><a href="<?php echo esc_url( admin_url( 'options-privacy.php' ) ); ?>" class="button"><?php esc_html_e( 'View Privacy Policy Guide', 'polar-smtp-mailer' ); ?></a></p>
                </div>
            </div>

            <!-- Uninstall Settings -->
            <div class="ssm-card">
                <h2><?php esc_html_e( 'Uninstall Settings', 'polar-smtp-mailer' ); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Delete All Data', 'polar-smtp-mailer' ); ?></th>
                        <td>
                            <label><input type="checkbox" name="PSM_delete_data_on_uninstall" value="1" <?php checked( $psm_settings['delete_data_on_uninstall'] ); ?>> <?php esc_html_e( 'Delete all plugin settings and logs on uninstall', 'polar-smtp-mailer' ); ?></label>
                            <p class="description"><?php esc_html_e( 'If enabled, all configuration and log data will be permanently removed when you delete the plugin.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <?php submit_button( __( 'Save Settings', 'polar-smtp-mailer' ) ); ?>
    </form>
</div>
