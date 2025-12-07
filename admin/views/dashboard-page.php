<?php
/**
 * Dashboard page template.
 *
 * @package PolarSmtpMailer
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$stats = PSM_Dashboard::get_stats();
$status = PSM_Dashboard::get_system_status();
$recent_logs = PSM_Dashboard::get_recent_logs( 5 );
?>

<div class="wrap ssm-wrap">
    <h1 class="ssm-page-title">
        <img src="<?php echo esc_url( PSM_PLUGIN_URL . 'assets/images/OpenmojiPolarBear.png' ); ?>" alt="" width="32" height="32" style="vertical-align: middle;">
        <?php esc_html_e( 'Polar SMTP Mailer', 'polar-smtp-mailer' ); ?>
    </h1>

    <div class="ssm-dashboard">
        <!-- Status Banner -->
        <div class="ssm-status-banner <?php echo $status['smtp_configured'] ? 'ssm-status-active' : 'ssm-status-inactive'; ?>">
            <div class="ssm-status-icon">
                <span class="dashicons <?php echo $status['smtp_configured'] ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
            </div>
            <div class="ssm-status-content">
                <h3><?php echo $status['smtp_configured'] ? esc_html__( 'SMTP Configured', 'polar-smtp-mailer' ) : esc_html__( 'SMTP Not Configured', 'polar-smtp-mailer' ); ?></h3>
                <p>
                    <?php if ( $status['smtp_configured'] ) : ?>
                        <?php
                        /* translators: %s: SMTP host/server name */
                        printf( esc_html__( 'Sending via %s', 'polar-smtp-mailer' ), esc_html( $status['smtp_host'] ) ); ?>
                    <?php else : ?>
                        <?php esc_html_e( 'Configure your SMTP settings to start sending emails reliably.', 'polar-smtp-mailer' ); ?>
                    <?php endif; ?>
                </p>
            </div>
            <?php if ( ! $status['smtp_configured'] ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=polar-smtp-mailer-settings' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Configure Now', 'polar-smtp-mailer' ); ?></a>
            <?php endif; ?>
        </div>

        <!-- Stats Cards -->
        <div class="ssm-stats-grid">
            <div class="ssm-stat-card">
                <div class="ssm-stat-icon ssm-icon-today"><span class="dashicons dashicons-calendar"></span></div>
                <div class="ssm-stat-content">
                    <span class="ssm-stat-number"><?php echo esc_html( $stats['today']['total'] ); ?></span>
                    <span class="ssm-stat-label"><?php esc_html_e( 'Today', 'polar-smtp-mailer' ); ?></span>
                </div>
                <div class="ssm-stat-details">
                    <span class="ssm-sent"><?php echo esc_html( $stats['today']['sent'] ); ?> <?php esc_html_e( 'sent', 'polar-smtp-mailer' ); ?></span>
                    <span class="ssm-failed"><?php echo esc_html( $stats['today']['failed'] ); ?> <?php esc_html_e( 'failed', 'polar-smtp-mailer' ); ?></span>
                </div>
            </div>

            <div class="ssm-stat-card">
                <div class="ssm-stat-icon ssm-icon-week"><span class="dashicons dashicons-chart-bar"></span></div>
                <div class="ssm-stat-content">
                    <span class="ssm-stat-number"><?php echo esc_html( $stats['week']['total'] ); ?></span>
                    <span class="ssm-stat-label"><?php esc_html_e( 'This Week', 'polar-smtp-mailer' ); ?></span>
                </div>
                <div class="ssm-stat-details">
                    <span class="ssm-sent"><?php echo esc_html( $stats['week']['sent'] ); ?> <?php esc_html_e( 'sent', 'polar-smtp-mailer' ); ?></span>
                    <span class="ssm-failed"><?php echo esc_html( $stats['week']['failed'] ); ?> <?php esc_html_e( 'failed', 'polar-smtp-mailer' ); ?></span>
                </div>
            </div>

            <div class="ssm-stat-card">
                <div class="ssm-stat-icon ssm-icon-month"><span class="dashicons dashicons-chart-area"></span></div>
                <div class="ssm-stat-content">
                    <span class="ssm-stat-number"><?php echo esc_html( $stats['month']['total'] ); ?></span>
                    <span class="ssm-stat-label"><?php esc_html_e( 'This Month', 'polar-smtp-mailer' ); ?></span>
                </div>
                <div class="ssm-stat-details">
                    <span class="ssm-sent"><?php echo esc_html( $stats['month']['sent'] ); ?> <?php esc_html_e( 'sent', 'polar-smtp-mailer' ); ?></span>
                    <span class="ssm-failed"><?php echo esc_html( $stats['month']['failed'] ); ?> <?php esc_html_e( 'failed', 'polar-smtp-mailer' ); ?></span>
                </div>
            </div>

            <div class="ssm-stat-card">
                <div class="ssm-stat-icon ssm-icon-total"><span class="dashicons dashicons-email"></span></div>
                <div class="ssm-stat-content">
                    <span class="ssm-stat-number"><?php echo esc_html( $stats['all']['total'] ); ?></span>
                    <span class="ssm-stat-label"><?php esc_html_e( 'Total Emails', 'polar-smtp-mailer' ); ?></span>
                </div>
                <div class="ssm-stat-details">
                    <span class="ssm-sent"><?php echo esc_html( $stats['all']['sent'] ); ?> <?php esc_html_e( 'sent', 'polar-smtp-mailer' ); ?></span>
                    <span class="ssm-failed"><?php echo esc_html( $stats['all']['failed'] ); ?> <?php esc_html_e( 'failed', 'polar-smtp-mailer' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Emails -->
        <div class="ssm-dashboard-columns">
            <div class="ssm-dashboard-main">
                <div class="ssm-card">
                    <h2><?php esc_html_e( 'Recent Emails', 'polar-smtp-mailer' ); ?></h2>
                    <?php if ( ! empty( $recent_logs ) ) : ?>
                        <table class="ssm-recent-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'To', 'polar-smtp-mailer' ); ?></th>
                                    <th><?php esc_html_e( 'Subject', 'polar-smtp-mailer' ); ?></th>
                                    <th><?php esc_html_e( 'Status', 'polar-smtp-mailer' ); ?></th>
                                    <th><?php esc_html_e( 'Date', 'polar-smtp-mailer' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $recent_logs as $log ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $log->to_email ); ?></td>
                                        <td><?php echo esc_html( $log->subject ? $log->subject : __( '(no subject)', 'polar-smtp-mailer' ) ); ?></td>
                                        <td><span class="ssm-status ssm-status-<?php echo esc_attr( $log->status ); ?>"><?php echo esc_html( ucfirst( $log->status ) ); ?></span></td>
                                        <td><?php echo esc_html( wp_date( 'M j, g:i a', strtotime( $log->created_at ) ) ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <p class="ssm-view-all"><a href="<?php echo esc_url( admin_url( 'admin.php?page=polar-smtp-mailer-logs' ) ); ?>"><?php esc_html_e( 'View All Logs →', 'polar-smtp-mailer' ); ?></a></p>
                    <?php else : ?>
                        <p class="ssm-no-data"><?php esc_html_e( 'No emails sent yet.', 'polar-smtp-mailer' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ssm-dashboard-sidebar">
                <div class="ssm-card">
                    <h2><?php esc_html_e( 'Quick Actions', 'polar-smtp-mailer' ); ?></h2>
                    <div class="ssm-quick-actions">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=polar-smtp-mailer-test' ) ); ?>" class="ssm-quick-action">
                            <span class="dashicons dashicons-email-alt"></span>
                            <?php esc_html_e( 'Send Test Email', 'polar-smtp-mailer' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=polar-smtp-mailer-settings' ) ); ?>" class="ssm-quick-action">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e( 'SMTP Settings', 'polar-smtp-mailer' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=polar-smtp-mailer-logs' ) ); ?>" class="ssm-quick-action">
                            <span class="dashicons dashicons-list-view"></span>
                            <?php esc_html_e( 'View All Logs', 'polar-smtp-mailer' ); ?>
                        </a>
                    </div>
                </div>

                <div class="ssm-card">
                    <h2><?php esc_html_e( 'System Status', 'polar-smtp-mailer' ); ?></h2>
                    <ul class="ssm-system-status">
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'Logging', 'polar-smtp-mailer' ); ?></span>
                            <span class="ssm-status-value <?php echo $status['logging_enabled'] ? 'ssm-enabled' : 'ssm-disabled'; ?>">
                                <?php echo $status['logging_enabled'] ? esc_html__( 'Enabled', 'polar-smtp-mailer' ) : esc_html__( 'Disabled', 'polar-smtp-mailer' ); ?>
                            </span>
                        </li>
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'Email Queue', 'polar-smtp-mailer' ); ?></span>
                            <span class="ssm-status-value <?php echo $status['queue_enabled'] ? 'ssm-enabled' : 'ssm-disabled'; ?>">
                            <?php
                            /* translators: %d: Number of emails in queue */
                            echo $status['queue_enabled'] ? sprintf( esc_html__( 'Enabled (%d)', 'polar-smtp-mailer' ), esc_html( $status['queue_count'] ) ) : esc_html__( 'Disabled', 'polar-smtp-mailer' ); ?>
                            </span>
                        </li>
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'OpenSSL', 'polar-smtp-mailer' ); ?></span>
                            <span class="ssm-status-value <?php echo $status['openssl'] ? 'ssm-enabled' : 'ssm-disabled'; ?>">
                                <?php echo $status['openssl'] ? esc_html__( 'Available', 'polar-smtp-mailer' ) : esc_html__( 'Not Available', 'polar-smtp-mailer' ); ?>
                            </span>
                        </li>
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'PHP Version', 'polar-smtp-mailer' ); ?></span>
                            <span class="ssm-status-value"><?php echo esc_html( $status['php_version'] ); ?></span>
                        </li>
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'WordPress', 'polar-smtp-mailer' ); ?></span>
                            <span class="ssm-status-value"><?php echo esc_html( $status['wp_version'] ); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
