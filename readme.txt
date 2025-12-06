=== Simple SMTP Mail ===
Contributors: boopathir
Tags: smtp, email, mail, email log, wp mail, gmail smtp, outlook smtp, sendgrid, mailgun
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful, open-source SMTP mailer plugin with comprehensive email logging, queue management, backup SMTP failover, and a modern admin dashboard.

== Description ==

Simple SMTP Mail helps you configure any SMTP provider to send WordPress emails reliably. It includes comprehensive email logging, a modern dashboard, and advanced features like email queuing and backup SMTP.

= ✨ Key Features =

* **Easy SMTP Configuration** - Pre-configured templates for 15+ providers
* **15+ Provider Presets** - Gmail, Outlook, Hostinger, SendGrid, Mailgun, Amazon SES, and more
* **Secure Password Storage** - AES-256-CBC encryption with WordPress salts
* **Email Logging** - Track all sent, failed, and queued emails with detailed logs
* **Email Queue** - Background processing with WP-Cron
* **Backup SMTP** - Automatic failover when primary SMTP fails
* **Modern Dashboard** - Statistics, charts, and quick actions
* **Privacy & GDPR** - Integrated personal data exporter, eraser, and anonymization
* **Export Logs** - Download logs as CSV or JSON format
* **Debug Mode** - Detailed logging for troubleshooting
* **Multisite Ready** - Works with WordPress Multisite
* **Clean Uninstall** - Optionally delete all settings and logs on removal
* **Self-Healing Database** - Automatic table creation and repair
* **WCAG AA Compliant** - Screen reader friendly, mobile-optimized

= 📧 Supported SMTP Providers =

* Gmail / Google Workspace
* Outlook / Microsoft 365
* Yahoo Mail
* Zoho Mail
* Hostinger
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
* Custom SMTP (any provider)

= 🔒 Security Features =

* AES-256-CBC password encryption
* CSRF protection with nonces
* Rate limiting (5 test emails per 10 minutes)
* Capability checks (admin only)
* Prepared SQL statements
* Input sanitization & output escaping

= 🔐 Privacy & GDPR =

* WordPress Privacy Exporter integration
* WordPress Privacy Eraser integration
* Email anonymization option
* Content exclusion from logs
* Configurable data retention

= Requirements =

* WordPress 6.0 or higher
* PHP 7.4 or higher
* OpenSSL extension (for encryption)

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/simple-smtp-mail/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to **SMTP Mail → Settings** to configure your SMTP server
4. Click **Test Connection** to verify settings
5. Send a test email to confirm delivery

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

= What if my primary SMTP fails? =

Enable Backup SMTP in Settings. When primary SMTP fails, emails automatically route through your backup server.

== Screenshots ==

1. Dashboard with email statistics and charts
2. SMTP Settings page with provider selection
3. Email Logs with search, filter, and bulk actions
4. Send Test Email page
5. Email preview modal

== Changelog ==

= 1.0.0 (2025-12-06) =
* Initial release
* SMTP configuration with 15+ provider presets
* Email logging with View, Resend, Delete actions
* Bulk delete support for email logs
* Email queue with background processing
* Priority-based queue processing
* Backup SMTP failover
* GDPR-compliant privacy features (exporter & eraser)
* Modern admin dashboard with statistics
* AES-256-CBC password encryption
* Rate limiting and security features
* Hostinger SMTP sender fix
* Self-healing database
* Clean uninstall option
* Full internationalization support
* WCAG AA accessibility compliance

== Upgrade Notice ==

= 1.0.0 =
Initial release with robust security, accessibility, and queue management features.
