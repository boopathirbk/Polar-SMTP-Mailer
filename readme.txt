=== Simple SMTP Mail ===
Contributors: boopathir
Tags: smtp, email, mail, email log, wp mail, gmail smtp, outlook smtp
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful, open-source SMTP mailer plugin with comprehensive email logging, queue management, backup SMTP failover, and a modern admin dashboard.

== Description ==

Simple SMTP Mail helps you configure any SMTP provider to send WordPress emails reliably. It includes comprehensive email logging, a modern dashboard, and advanced features like email queuing and backup SMTP.

**Now with Enhanced UI/UX and Accessibility (WCAG AA) support!**

= Key Features =

* **Easy SMTP Configuration** - Configure your SMTP settings in minutes with pre-configured provider templates
* **15+ Provider Presets** - Gmail, Outlook, SendGrid, Mailgun, Amazon SES, Brevo, Postmark, and more
* **Secure Password Storage** - Passwords are encrypted using AES-256 encryption
* **Email Logging** - Track all sent, failed, and queued emails with detailed logs
* **Modern Dashboard** - Beautiful stats, charts, and quick actions
* **User Experience & Accessibility** - WCAG AA Compliant, screen reader friendly, and mobile-optimized responsive tables
* **Email Queue** - Schedule bulk emails with rate limiting and background processing
* **Backup SMTP** - Automatic fallback when primary SMTP fails (Failover)
* **Privacy & GDPR** - Integrated personal data exporter, eraser, and anonymization options
* **Export Logs** - Download logs as CSV or JSON format
* **Debug Mode** - Detailed logging for troubleshooting
* **Multisite Ready** - Works with WordPress Multisite

= Pre-configured SMTP Providers =

* Gmail / Google Workspace
* Outlook / Microsoft 365
* Yahoo Mail
* Zoho Mail
* SendGrid
* Mailgun
* Amazon SES
* Brevo (Sendinblue)
* Postmark
* SparkPost
* SMTP.com
* Elastic Email
* Mailjet
* Pepipost (Netcore)
* Custom SMTP

= Requirements =

* WordPress 6.0 or higher
* PHP 7.4 or higher
* OpenSSL extension

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/simple-smtp-mail/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to SMTP Mail → Settings to configure your SMTP server
4. Send a test email to verify your configuration

== Frequently Asked Questions ==

= How do I configure Gmail SMTP? =

1. Go to SMTP Mail → Settings
2. Select "Gmail / Google Workspace" from the provider dropdown
3. Enter your Gmail address as the username
4. Create an App Password in your Google Account settings
5. Use the App Password as your SMTP password

= Why are my emails going to spam? =

Make sure your From Email address matches your SMTP username or is from a domain you own. Also ensure SPF, DKIM, and DMARC records are properly configured for your domain.

= How do I enable email logging? =

Email logging is enabled by default. Go to SMTP Mail → Email Logs to view all sent and failed emails.

= Can I use this with WooCommerce? =

Yes! Simple SMTP Mail works with all WordPress emails including WooCommerce order notifications.

= How do I export my email logs? =

Go to SMTP Mail → Email Logs and click the "Export CSV" or "Export JSON" button.

== Screenshots ==

1. Dashboard with email statistics
2. SMTP Settings page
3. Email Logs with search and filters
4. Send Test Email page
5. Email preview modal

== Changelog ==

= 1.0.0 =
* Initial release
* Enhanced UI/UX with improved accessibility (WCAG AA)
* Mobile-optimized responsive tables
* Memory optimization for large log exports
* SMTP configuration with 15+ provider presets
* Support for TLS, SSL, and no encryption
* Email logging with search, filter, and export
* Email resend functionality
* Email queue with background processing
* Priority-based queue processing
* Backup SMTP failover
* GDPR-compliant privacy features (exporter & eraser)
* Modern admin dashboard with statistics
* Rate limiting and security logging
* Debug mode for troubleshooting
* Full internationalization support

== Upgrade Notice ==

= 1.0.0 =
Initial release of Simple SMTP Mail with robust Security, Accessibility, and Queue Management features.
