<?php
/**
 * Dashboard page template.
 *
 * @package SimpleSmtpMail
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$stats = SSM_Dashboard::get_stats();
$status = SSM_Dashboard::get_system_status();
$recent_logs = SSM_Dashboard::get_recent_logs( 5 );
?>

<div class="wrap ssm-wrap">
    <h1 class="ssm-page-title">
        <span class="dashicons dashicons-email-alt"></span>
        <?php esc_html_e( 'Simple SMTP Mail', 'simple-smtp-mail' ); ?>
    </h1>

    <div class="ssm-dashboard">
        <!-- Status Banner -->
        <div class="ssm-status-banner <?php echo $status['smtp_configured'] ? 'ssm-status-active' : 'ssm-status-inactive'; ?>">
            <div class="ssm-status-icon">
                <span class="dashicons <?php echo $status['smtp_configured'] ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
            </div>
            <div class="ssm-status-content">
                <h3><?php echo $status['smtp_configured'] ? esc_html__( 'SMTP Configured', 'simple-smtp-mail' ) : esc_html__( 'SMTP Not Configured', 'simple-smtp-mail' ); ?></h3>
                <p>
                    <?php if ( $status['smtp_configured'] ) : ?>
                        <?php printf( esc_html__( 'Sending via %s', 'simple-smtp-mail' ), esc_html( $status['smtp_host'] ) ); ?>
                    <?php else : ?>
                        <?php esc_html_e( 'Configure your SMTP settings to start sending emails reliably.', 'simple-smtp-mail' ); ?>
                    <?php endif; ?>
                </p>
            </div>
            <?php if ( ! $status['smtp_configured'] ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=simple-smtp-mail-settings' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Configure Now', 'simple-smtp-mail' ); ?></a>
            <?php endif; ?>
        </div>

        <!-- Stats Cards -->
        <div class="ssm-stats-grid">
            <div class="ssm-stat-card">
                <div class="ssm-stat-icon ssm-icon-today"><span class="dashicons dashicons-calendar"></span></div>
                <div class="ssm-stat-content">
                    <span class="ssm-stat-number"><?php echo esc_html( $stats['today']['total'] ); ?></span>
                    <span class="ssm-stat-label"><?php esc_html_e( 'Today', 'simple-smtp-mail' ); ?></span>
                </div>
                <div class="ssm-stat-details">
                    <span class="ssm-sent"><?php echo esc_html( $stats['today']['sent'] ); ?> <?php esc_html_e( 'sent', 'simple-smtp-mail' ); ?></span>
                    <span class="ssm-failed"><?php echo esc_html( $stats['today']['failed'] ); ?> <?php esc_html_e( 'failed', 'simple-smtp-mail' ); ?></span>
                </div>
            </div>

            <div class="ssm-stat-card">
                <div class="ssm-stat-icon ssm-icon-week"><span class="dashicons dashicons-chart-bar"></span></div>
                <div class="ssm-stat-content">
                    <span class="ssm-stat-number"><?php echo esc_html( $stats['week']['total'] ); ?></span>
                    <span class="ssm-stat-label"><?php esc_html_e( 'This Week', 'simple-smtp-mail' ); ?></span>
                </div>
                <div class="ssm-stat-details">
                    <span class="ssm-sent"><?php echo esc_html( $stats['week']['sent'] ); ?> <?php esc_html_e( 'sent', 'simple-smtp-mail' ); ?></span>
                    <span class="ssm-failed"><?php echo esc_html( $stats['week']['failed'] ); ?> <?php esc_html_e( 'failed', 'simple-smtp-mail' ); ?></span>
                </div>
            </div>

            <div class="ssm-stat-card">
                <div class="ssm-stat-icon ssm-icon-month"><span class="dashicons dashicons-chart-area"></span></div>
                <div class="ssm-stat-content">
                    <span class="ssm-stat-number"><?php echo esc_html( $stats['month']['total'] ); ?></span>
                    <span class="ssm-stat-label"><?php esc_html_e( 'This Month', 'simple-smtp-mail' ); ?></span>
                </div>
                <div class="ssm-stat-details">
                    <span class="ssm-sent"><?php echo esc_html( $stats['month']['sent'] ); ?> <?php esc_html_e( 'sent', 'simple-smtp-mail' ); ?></span>
                    <span class="ssm-failed"><?php echo esc_html( $stats['month']['failed'] ); ?> <?php esc_html_e( 'failed', 'simple-smtp-mail' ); ?></span>
                </div>
            </div>

            <div class="ssm-stat-card">
                <div class="ssm-stat-icon ssm-icon-total"><span class="dashicons dashicons-email"></span></div>
                <div class="ssm-stat-content">
                    <span class="ssm-stat-number"><?php echo esc_html( $stats['all']['total'] ); ?></span>
                    <span class="ssm-stat-label"><?php esc_html_e( 'Total Emails', 'simple-smtp-mail' ); ?></span>
                </div>
                <div class="ssm-stat-details">
                    <span class="ssm-sent"><?php echo esc_html( $stats['all']['sent'] ); ?> <?php esc_html_e( 'sent', 'simple-smtp-mail' ); ?></span>
                    <span class="ssm-failed"><?php echo esc_html( $stats['all']['failed'] ); ?> <?php esc_html_e( 'failed', 'simple-smtp-mail' ); ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Emails -->
        <div class="ssm-dashboard-columns">
            <div class="ssm-dashboard-main">
                <div class="ssm-card">
                    <h2><?php esc_html_e( 'Recent Emails', 'simple-smtp-mail' ); ?></h2>
                    <?php if ( ! empty( $recent_logs ) ) : ?>
                        <table class="ssm-recent-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e( 'To', 'simple-smtp-mail' ); ?></th>
                                    <th><?php esc_html_e( 'Subject', 'simple-smtp-mail' ); ?></th>
                                    <th><?php esc_html_e( 'Status', 'simple-smtp-mail' ); ?></th>
                                    <th><?php esc_html_e( 'Date', 'simple-smtp-mail' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $recent_logs as $log ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $log->to_email ); ?></td>
                                        <td><?php echo esc_html( $log->subject ? $log->subject : __( '(no subject)', 'simple-smtp-mail' ) ); ?></td>
                                        <td><span class="ssm-status ssm-status-<?php echo esc_attr( $log->status ); ?>"><?php echo esc_html( ucfirst( $log->status ) ); ?></span></td>
                                        <td><?php echo esc_html( wp_date( 'M j, g:i a', strtotime( $log->created_at ) ) ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <p class="ssm-view-all"><a href="<?php echo esc_url( admin_url( 'admin.php?page=simple-smtp-mail-logs' ) ); ?>"><?php esc_html_e( 'View All Logs →', 'simple-smtp-mail' ); ?></a></p>
                    <?php else : ?>
                        <p class="ssm-no-data"><?php esc_html_e( 'No emails sent yet.', 'simple-smtp-mail' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ssm-dashboard-sidebar">
                <div class="ssm-card">
                    <h2><?php esc_html_e( 'Quick Actions', 'simple-smtp-mail' ); ?></h2>
                    <div class="ssm-quick-actions">
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=simple-smtp-mail-test' ) ); ?>" class="ssm-quick-action">
                            <span class="dashicons dashicons-email-alt"></span>
                            <?php esc_html_e( 'Send Test Email', 'simple-smtp-mail' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=simple-smtp-mail-settings' ) ); ?>" class="ssm-quick-action">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e( 'SMTP Settings', 'simple-smtp-mail' ); ?>
                        </a>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=simple-smtp-mail-logs' ) ); ?>" class="ssm-quick-action">
                            <span class="dashicons dashicons-list-view"></span>
                            <?php esc_html_e( 'View All Logs', 'simple-smtp-mail' ); ?>
                        </a>
                    </div>
                </div>

                <div class="ssm-card">
                    <h2><?php esc_html_e( 'System Status', 'simple-smtp-mail' ); ?></h2>
                    <ul class="ssm-system-status">
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'Logging', 'simple-smtp-mail' ); ?></span>
                            <span class="ssm-status-value <?php echo $status['logging_enabled'] ? 'ssm-enabled' : 'ssm-disabled'; ?>">
                                <?php echo $status['logging_enabled'] ? esc_html__( 'Enabled', 'simple-smtp-mail' ) : esc_html__( 'Disabled', 'simple-smtp-mail' ); ?>
                            </span>
                        </li>
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'Email Queue', 'simple-smtp-mail' ); ?></span>
                            <span class="ssm-status-value <?php echo $status['queue_enabled'] ? 'ssm-enabled' : 'ssm-disabled'; ?>">
                                <?php echo $status['queue_enabled'] ? sprintf( esc_html__( 'Enabled (%d)', 'simple-smtp-mail' ), $status['queue_count'] ) : esc_html__( 'Disabled', 'simple-smtp-mail' ); ?>
                            </span>
                        </li>
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'OpenSSL', 'simple-smtp-mail' ); ?></span>
                            <span class="ssm-status-value <?php echo $status['openssl'] ? 'ssm-enabled' : 'ssm-disabled'; ?>">
                                <?php echo $status['openssl'] ? esc_html__( 'Available', 'simple-smtp-mail' ) : esc_html__( 'Not Available', 'simple-smtp-mail' ); ?>
                            </span>
                        </li>
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'PHP Version', 'simple-smtp-mail' ); ?></span>
                            <span class="ssm-status-value"><?php echo esc_html( $status['php_version'] ); ?></span>
                        </li>
                        <li>
                            <span class="ssm-status-label"><?php esc_html_e( 'WordPress', 'simple-smtp-mail' ); ?></span>
                            <span class="ssm-status-value"><?php echo esc_html( $status['wp_version'] ); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
