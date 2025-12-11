=== Polar SMTP Mailer ===
Contributors: boopathir
Tags: smtp, email, wp mail, email log, gmail
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.0.4
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Stop losing emails. Start delivering with confidence. The lightweight, feature-packed SMTP plugin WordPress deserves — 100% free, no upsells.

== Description ==

**Polar SMTP Mailer** is a powerful, 100% open-source SMTP mailer with everything you need to ensure reliable email delivery — no premium upsells, no feature restrictions.

= 🚀 Why Choose Polar SMTP Mailer? =

Unlike other SMTP plugins that lock essential features behind paywalls, Polar SMTP Mailer gives you **everything for free**:

* ✅ **Email Logging** - Track all sent, failed, and queued emails
* ✅ **Backup SMTP Failover** - Automatic switch when primary fails
* ✅ **Email Queue** - Background processing with priority & retry
* ✅ **GDPR Privacy Tools** - Built-in exporter, eraser, anonymization
* ✅ **Modern Dashboard** - Beautiful stats and quick actions
* ✅ **Self-Healing Database** - Automatic table creation & repair
* ✅ **Clean Uninstall** - Optionally remove all data on deletion
* ✅ **No Ads or Upsells** - Pure, clean experience

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

* AES-256-CBC password encryption with random IV
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

1. Upload the plugin files to `/wp-content/plugins/polar-smtp-mailer/`
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

Yes! Polar SMTP Mailer works with all WordPress emails including WooCommerce order notifications.

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

= 1.0.4 (2025-12-11) =
* **Compliance:** Fixed global variable naming conventions in admin views (Plugin Check).
* **Compliance:** Fixed hook naming conventions to use proper `psm_` prefix (Plugin Check).
* **UI:** Enhanced email log modal with grid layout and sender details.
* **UI:** Added From Email tracking to email logs.
* **UI:** Improved modal responsiveness for mobile devices.
* **UI:** Fixed backup test connection result color styling.
* **Fix:** Resolved JavaScript context issue in backup SMTP connection test.
* **Fix:** Improved uninstall cleanup to remove backup options.
* **Fix:** Various minor naming convention fixes across the codebase.
* Added Debug Logs admin page (visible when debug mode enabled)
* Fixed duplicate email logs when sending test emails
* Fixed global wp_mail capture for early emails

= 1.0.3 (2025-12-10) =
* Removed debug logging to pass Plugin Check requirements

= 1.0.2 (2025-12-10) =
* Fixed HTML email display in log viewer
* Fixed empty recipient validation in queue
* Fixed decryption failure handling after AUTH_KEY changes
* Improved PHP 8.2 compatibility
* Improved exception handling for PHPMailer
* Optimized auth failure logging

= 1.0.1 (2025-12-10) =
* **Security:** Fixed encryption to use random IV per encryption (AES-256-CBC best practice)
* **Security:** Fixed SQL preparation in table existence check
* **Security:** Added capability check to queue clear function
* **Fixed:** Race condition in queue processing with proper database locking
* **Fixed:** TLS compatibility - now uses TLS 1.2/1.3 instead of deprecated TLS 1.0
* **Fixed:** Potential infinite loops in SMTP response reading
* **Fixed:** Missing exit statements in AJAX handlers
* **Fixed:** Duplicate log entries when using email queue
* **Fixed:** Privacy erase now clears attachments field
* **Fixed:** Password placeholder edge case
* **Fixed:** JSON export memory issue with large datasets
* **Improved:** Backward compatible encryption (existing passwords continue to work)
* **Improved:** Better code documentation

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

= 1.0.2 =
Stability release with PHP 8.2 support, improved error handling, and bug fixes. Recommended for all users.

= 1.0.1 =
Security and bug fix release. Includes improved encryption, TLS 1.2/1.3 support, and 14 bug fixes. Recommended for all users.

= 1.0.0 =
Initial release with robust security, accessibility, and queue management features.

