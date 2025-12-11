<?php
/**
 * Test email page template.
 *
 * @package PolarSmtpMailer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- View template variables.
$psm_admin_email = get_option( 'admin_email' );
$psm_smtp_configured = ! empty( get_option( 'PSM_smtp_host', '' ) );
?>

<div class="wrap ssm-wrap">
    <h1 class="ssm-page-title">
        <span class="dashicons dashicons-email-alt"></span>
        <?php esc_html_e( 'Send Test Email', 'polar-smtp-mailer' ); ?>
    </h1>

    <?php if ( ! $psm_smtp_configured ) : ?>
        <div class="notice notice-warning">
            <p>
                <?php
                printf(
                    /* translators: %s: Settings page URL */
                    esc_html__( 'SMTP is not configured yet. Please %s first.', 'polar-smtp-mailer' ),
                    '<a href="' . esc_url( admin_url( 'admin.php?page=polar-smtp-mailer-settings' ) ) . '">' . esc_html__( 'configure your SMTP settings', 'polar-smtp-mailer' ) . '</a>'
                );
                ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="ssm-test-email-container">
        <div class="ssm-card">
            <h2><?php esc_html_e( 'Test Email', 'polar-smtp-mailer' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Send a test email to verify your SMTP configuration is working correctly.', 'polar-smtp-mailer' ); ?></p>

            <form id="ssm-test-email-form">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="PSM_test_email"><?php esc_html_e( 'Send To', 'polar-smtp-mailer' ); ?></label></th>
                        <td>
                            <input type="email" name="PSM_test_email" id="PSM_test_email" value="<?php echo esc_attr( $psm_admin_email ); ?>" class="regular-text" placeholder="you@example.com" required>
                            <p class="description"><?php esc_html_e( 'Enter the email address to send the test email to.', 'polar-smtp-mailer' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary" id="ssm-send-test" <?php disabled( ! $psm_smtp_configured ); ?>>
                        <span class="dashicons dashicons-email-alt"></span>
                        <?php esc_html_e( 'Send Test Email', 'polar-smtp-mailer' ); ?>
                    </button>
                </p>
            </form>

            <div id="ssm-test-result" class="ssm-test-result-box" style="display:none;"></div>
        </div>

        <div class="ssm-card">
            <h2><?php esc_html_e( 'Current SMTP Configuration', 'polar-smtp-mailer' ); ?></h2>

            <table class="ssm-config-table">
                <tr>
                    <th><?php esc_html_e( 'SMTP Host', 'polar-smtp-mailer' ); ?></th>
                    <td><?php echo esc_html( get_option( 'PSM_smtp_host', __( 'Not configured', 'polar-smtp-mailer' ) ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Port', 'polar-smtp-mailer' ); ?></th>
                    <td><?php echo esc_html( get_option( 'PSM_smtp_port', 587 ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Encryption', 'polar-smtp-mailer' ); ?></th>
                    <td><?php echo esc_html( strtoupper( get_option( 'PSM_smtp_encryption', 'tls' ) ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Authentication', 'polar-smtp-mailer' ); ?></th>
                    <td><?php echo get_option( 'PSM_smtp_auth', true ) ? esc_html__( 'Yes', 'polar-smtp-mailer' ) : esc_html__( 'No', 'polar-smtp-mailer' ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'From Email', 'polar-smtp-mailer' ); ?></th>
                    <td><?php echo esc_html( get_option( 'PSM_from_email', get_option( 'admin_email' ) ) ); ?></td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'From Name', 'polar-smtp-mailer' ); ?></th>
                    <td><?php echo esc_html( get_option( 'PSM_from_name', get_bloginfo( 'name' ) ) ); ?></td>
                </tr>
            </table>

            <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=polar-smtp-mailer-settings' ) ); ?>" class="button"><?php esc_html_e( 'Edit Settings', 'polar-smtp-mailer' ); ?></a></p>
        </div>
    </div>
</div>
