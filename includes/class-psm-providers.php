<?php
/**
 * SMTP Providers class.
 *
 * Pre-configured settings for popular SMTP providers.
 *
 * @package PolarSmtpMailer
 * @since 1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * PSM_Providers class.
 *
 * @since 1.0.0
 */
class PSM_Providers {

    /**
     * Get all available providers.
     *
     * @since 1.0.0
     * @return array Array of provider configurations.
     */
    public static function get_providers() {
        $providers = array(
            'custom'      => array(
                'name'        => __( 'Other SMTP', 'polar-smtp-mailer' ),
                'description' => __( 'Configure your own SMTP server settings.', 'polar-smtp-mailer' ),
                'host'        => '',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-email-alt',
            ),
            'gmail'       => array(
                'name'        => __( 'Gmail / Google Workspace', 'polar-smtp-mailer' ),
                'description' => __( 'Use Gmail or Google Workspace SMTP. Requires App Password.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.gmail.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-google',
                'help_url'    => 'https://support.google.com/mail/answer/185833',
                'help_text'   => __( 'You need to generate an App Password in your Google Account settings.', 'polar-smtp-mailer' ),
            ),
            'outlook'     => array(
                'name'        => __( 'Outlook / Microsoft 365', 'polar-smtp-mailer' ),
                'description' => __( 'Use Outlook.com or Microsoft 365 SMTP.', 'polar-smtp-mailer' ),
                'host'        => 'smtp-mail.outlook.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-email',
                'help_url'    => 'https://support.microsoft.com/en-us/office/pop-imap-and-smtp-settings',
            ),
            'office365'   => array(
                'name'        => __( 'Office 365', 'polar-smtp-mailer' ),
                'description' => __( 'Use Office 365 SMTP relay.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.office365.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-email',
            ),
            'yahoo'       => array(
                'name'        => __( 'Yahoo Mail', 'polar-smtp-mailer' ),
                'description' => __( 'Use Yahoo Mail SMTP. Requires App Password.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.mail.yahoo.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-email-alt2',
            ),
            'zoho'        => array(
                'name'        => __( 'Zoho Mail', 'polar-smtp-mailer' ),
                'description' => __( 'Use Zoho Mail SMTP.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.zoho.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-email',
            ),
            'sendgrid'    => array(
                'name'        => __( 'SendGrid', 'polar-smtp-mailer' ),
                'description' => __( 'Use SendGrid SMTP relay service.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.sendgrid.net',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
                'username_placeholder' => 'apikey',
                'help_text'   => __( 'Use "apikey" as username and your SendGrid API key as password.', 'polar-smtp-mailer' ),
                'help_url'    => 'https://docs.sendgrid.com/for-developers/sending-email/integrating-with-the-smtp-api',
            ),
            'mailgun'     => array(
                'name'        => __( 'Mailgun', 'polar-smtp-mailer' ),
                'description' => __( 'Use Mailgun SMTP relay service.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.mailgun.org',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
                'help_text'   => __( 'Use your Mailgun SMTP credentials from the Mailgun dashboard.', 'polar-smtp-mailer' ),
                'help_url'    => 'https://documentation.mailgun.com/en/latest/quickstart-sending.html#send-via-smtp',
            ),
            'amazon_ses'  => array(
                'name'        => __( 'Amazon SES', 'polar-smtp-mailer' ),
                'description' => __( 'Use Amazon Simple Email Service SMTP.', 'polar-smtp-mailer' ),
                'host'        => 'email-smtp.us-east-1.amazonaws.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
                'help_text'   => __( 'Update the host to match your AWS region. Use SMTP credentials from the SES console.', 'polar-smtp-mailer' ),
                'help_url'    => 'https://docs.aws.amazon.com/ses/latest/dg/smtp-credentials.html',
            ),
            'brevo'       => array(
                'name'        => __( 'Brevo (Sendinblue)', 'polar-smtp-mailer' ),
                'description' => __( 'Use Brevo (formerly Sendinblue) SMTP relay.', 'polar-smtp-mailer' ),
                'host'        => 'smtp-relay.brevo.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
                'help_url'    => 'https://help.brevo.com/hc/en-us/articles/360007666839-Configure-your-SMTP-relay-settings',
            ),
            'postmark'    => array(
                'name'        => __( 'Postmark', 'polar-smtp-mailer' ),
                'description' => __( 'Use Postmark SMTP delivery.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.postmarkapp.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
                'help_text'   => __( 'Use your Postmark Server API Token as both username and password.', 'polar-smtp-mailer' ),
                'help_url'    => 'https://postmarkapp.com/developer/user-guide/smtp-headers-and-api',
            ),
            'sparkpost'   => array(
                'name'        => __( 'SparkPost', 'polar-smtp-mailer' ),
                'description' => __( 'Use SparkPost SMTP relay.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.sparkpostmail.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
                'username_placeholder' => 'SMTP_Injection',
                'help_text'   => __( 'Use "SMTP_Injection" as username and your SparkPost API key as password.', 'polar-smtp-mailer' ),
                'help_url'    => 'https://developers.sparkpost.com/api/smtp/',
            ),
            'smtp_com'    => array(
                'name'        => __( 'SMTP.com', 'polar-smtp-mailer' ),
                'description' => __( 'Use SMTP.com relay service.', 'polar-smtp-mailer' ),
                'host'        => 'send.smtp.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
                'help_url'    => 'https://www.smtp.com/resources/getting-started/smtp-relay/',
            ),
            'elasticemail' => array(
                'name'        => __( 'Elastic Email', 'polar-smtp-mailer' ),
                'description' => __( 'Use Elastic Email SMTP.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.elasticemail.com',
                'port'        => 2525,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
                'help_url'    => 'https://elasticemail.com/developers/guides/send-email-via-smtp',
            ),
            'mailjet'     => array(
                'name'        => __( 'Mailjet', 'polar-smtp-mailer' ),
                'description' => __( 'Use Mailjet SMTP relay.', 'polar-smtp-mailer' ),
                'host'        => 'in-v3.mailjet.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
                'help_text'   => __( 'Use your API Key as username and Secret Key as password.', 'polar-smtp-mailer' ),
                'help_url'    => 'https://dev.mailjet.com/email/guides/getting-started/',
            ),
            'pepipost'    => array(
                'name'        => __( 'Pepipost (Netcore)', 'polar-smtp-mailer' ),
                'description' => __( 'Use Pepipost SMTP relay.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.pepipost.com',
                'port'        => 587,
                'encryption'  => 'tls',
                'auth'        => true,
                'icon'        => 'dashicons-cloud',
            ),
            'hostinger'   => array(
                'name'        => __( 'Hostinger', 'polar-smtp-mailer' ),
                'description' => __( 'Use Hostinger SMTP mail service.', 'polar-smtp-mailer' ),
                'host'        => 'smtp.hostinger.com',
                'port'        => 465,
                'encryption'  => 'ssl',
                'auth'        => true,
                'icon'        => 'dashicons-email-alt',
                'help_url'    => 'https://support.hostinger.com/en/articles/1583465-how-to-find-email-configuration-settings',
            ),
        );

        /**
         * Filter the available SMTP providers.
         *
         * @since 1.0.0
         * @param array $providers Array of provider configurations.
         */
        return apply_filters( 'PSM_smtp_providers', $providers );
    }

    /**
     * Get a specific provider configuration.
     *
     * @since 1.0.0
     * @param string $provider_key Provider key.
     * @return array|null Provider configuration or null if not found.
     */
    public static function get_provider( $provider_key ) {
        $providers = self::get_providers();
        return isset( $providers[ $provider_key ] ) ? $providers[ $provider_key ] : null;
    }

    /**
     * Get provider select options.
     *
     * @since 1.0.0
     * @return array Provider options for select field.
     */
    public static function get_provider_options() {
        $providers = self::get_providers();
        $options   = array();

        foreach ( $providers as $key => $provider ) {
            $options[ $key ] = $provider['name'];
        }

        return $options;
    }

    /**
     * Get encryption options.
     *
     * @since 1.0.0
     * @return array Encryption options.
     */
    public static function get_encryption_options() {
        return array(
            'none' => __( 'None', 'polar-smtp-mailer' ),
            'ssl'  => __( 'SSL', 'polar-smtp-mailer' ),
            'tls'  => __( 'TLS', 'polar-smtp-mailer' ),
        );
    }

    /**
     * Get common ports for each encryption type.
     *
     * @since 1.0.0
     * @return array Port mappings.
     */
    public static function get_encryption_ports() {
        return array(
            'none' => 25,
            'ssl'  => 465,
            'tls'  => 587,
        );
    }

    /**
     * Get provider name from SMTP host.
     *
     * @since 1.0.0
     * @param string $host SMTP host.
     * @return string Provider name.
     */
    public static function get_provider_name_from_host( $host = '' ) {
        if ( empty( $host ) ) {
            $host = get_option( 'PSM_smtp_host', '' );
        }

        if ( empty( $host ) ) {
            return 'PHP Mail';
        }

        $provider_map = array(
            'smtp.gmail.com'         => 'Gmail',
            'smtp-mail.outlook.com'  => 'Outlook',
            'smtp.office365.com'     => 'Office 365',
            'smtp.sendgrid.net'      => 'SendGrid',
            'smtp.mailgun.org'       => 'Mailgun',
            'email-smtp'             => 'Amazon SES',
            'smtp-relay.brevo.com'   => 'Brevo',
            'smtp.postmarkapp.com'   => 'Postmark',
            'smtp.sparkpostmail.com' => 'SparkPost',
            'send.smtp.com'          => 'SMTP.com',
            'smtp.zoho.com'          => 'Zoho',
            'smtp.elasticemail.com'  => 'Elastic Email',
            'in-v3.mailjet.com'      => 'Mailjet',
            'smtp.pepipost.com'      => 'Pepipost',
            'smtp.mail.yahoo.com'    => 'Yahoo',
            'smtp.hostinger.com'     => 'Hostinger',
        );

        foreach ( $provider_map as $domain => $name ) {
            if ( false !== strpos( $host, $domain ) ) {
                return $name;
            }
        }

        return 'Custom SMTP';
    }
}
