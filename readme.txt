=== Simple SMTP Mail ===
Contributors: boopathir
Tags: smtp, email, mail, email log, wp mail
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful, open-source SMTP mailer plugin with comprehensive email logging, queue management, and modern dashboard.

== Description ==

Simple SMTP Mail helps you configure any SMTP provider to send WordPress emails reliably. It includes comprehensive email logging, a modern dashboard, and advanced features like email queuing and backup SMTP.

= Key Features =

* **Easy SMTP Configuration** - Configure your SMTP settings in minutes with pre-configured provider templates
* **15+ Provider Presets** - Gmail, Outlook, SendGrid, Mailgun, Amazon SES, Brevo, Postmark, and more
* **Secure Password Storage** - Passwords are encrypted using AES-256 encryption
* **Email Logging** - Track all outgoing emails with detailed logs
* **Modern Dashboard** - Beautiful stats, charts, and quick actions
* **Test Email** - Verify your configuration with a single click
* **Connection Testing** - Test SMTP connectivity before saving
* **Email Queue** - Schedule bulk emails with rate limiting
* **Backup SMTP** - Automatic fallback when primary SMTP fails
* **Export Logs** - Download logs as CSV or JSON
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
* SMTP configuration with 15+ provider presets
* Email logging with search and filters
* Modern dashboard with statistics
* Test email functionality
* Connection testing
* Email queue system
* Backup SMTP support
* CSV/JSON export
* Debug mode

== Upgrade Notice ==

= 1.0.0 =
Initial release of Simple SMTP Mail.
