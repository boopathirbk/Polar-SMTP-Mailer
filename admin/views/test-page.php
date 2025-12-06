<?php
/**
 * Test email page template.
 *
 * @package SimpleSmtpMail
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$admin_email = get_option( 'admin_email' );
$smtp_configured = ! empty( get_option( 'ssm_smtp_host', '' ) );
?>

<div class="wrap ssm-wrap">
    <h1 class="ssm-page-title">
        <span class="dashicons dashicons-email-alt"></span>
        <?php esc_html_e( 'Send Test Email', 'simple-smtp-mail' ); ?>
    </h1>

    <?php if ( ! $smtp_configured ) : ?>
        <div class="notice notice-warning">
            <p>
                <?php
                printf(
                    /* translators: %s: Settings page URL */
                    esc_html__( 'SMTP is not configured yet. Please %s first.', 'simple-smtp-mail' ),
                    '<a href="' . esc_url( admin_url( 'admin.php?page=simple-smtp-mail-settings' ) ) . '">' . esc_html__( 'configure your SMTP settings', 'simple-smtp-mail' ) . '</a>'
                );
                ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="ssm-test-email-container">
        <div class="ssm-card">
            <h2><?php esc_html_e( 'Test Email', 'simple-smtp-mail' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Send a test email to verify your SMTP configuration is working correctly.', 'simple-smtp-mail' ); ?></p>

            <form id="ssm-test-email-form">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="ssm_test_email"><?php esc_html_e( 'Send To', 'simple-smtp-mail' ); ?></label></th>
                        <td>
                            <input type="email" name="ssm_test_email" id="ssm_test_email" value="<?php echo esc_attr( $admin_email ); ?>" class="regular-text" required>
                            <p class="description"><?php esc_html_e( 'Enter the email address to send the test email to.', 'simple-smtp-mail' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary button-hero" id="ssm-send-test" <?php disabled( ! $smtp_configured ); ?>>
                        <span class="dashicons dashicons-email-alt"></span>
                        <?php esc_html_e( 'Send Test Email', 'simple-smtp-mail' ); ?>
                    </button>
                </p>
            </form>

            <div id="ssm-test-result" class="ssm-test-result-box" style="display:none;"></div>
        </div>

        <div class="ssm-card">
            <h2><?php esc_html_e( 'Current SMTP Configuration', 'simple-smtp-mail' ); ?></h2>

            <table class="ssm-config-table">
                <tr>
                    <th><?php esc_html_e( 'SMTP Host', 'simple-smtp-mail' ); ?></th>
                    <td><?php echo esc_html( get_option( 'ssm_smtp_host', __( 'Not configured', 'simple-smtp-mail' ) ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Port', 'simple-smtp-mail' ); ?></th>
                    <td><?php echo esc_html( get_option( 'ssm_smtp_port', 587 ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Encryption', 'simple-smtp-mail' ); ?></th>
                    <td><?php echo esc_html( strtoupper( get_option( 'ssm_smtp_encryption', 'tls' ) ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Authentication', 'simple-smtp-mail' ); ?></th>
                    <td><?php echo get_option( 'ssm_smtp_auth', true ) ? esc_html__( 'Yes', 'simple-smtp-mail' ) : esc_html__( 'No', 'simple-smtp-mail' ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'From Email', 'simple-smtp-mail' ); ?></th>
                    <td><?php echo esc_html( get_option( 'ssm_from_email', get_option( 'admin_email' ) ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'From Name', 'simple-smtp-mail' ); ?></th>
                    <td><?php echo esc_html( get_option( 'ssm_from_name', get_bloginfo( 'name' ) ) ); ?></td>
                </tr>
            </table>

            <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=simple-smtp-mail-settings' ) ); ?>" class="button"><?php esc_html_e( 'Edit Settings', 'simple-smtp-mail' ); ?></a></p>
        </div>
    </div>
</div>
