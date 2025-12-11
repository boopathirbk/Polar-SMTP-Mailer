# <img src="assets/images/OpenmojiPolarBear.png" width="40" alt="Polar Bear Icon"> Polar SMTP Mailer

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![Tested up to](https://img.shields.io/badge/Tested%20up%20to-6.9-brightgreen.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Donate](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://paypal.me/boopathirbk)

### 🚀 *The lightweight, feature-packed SMTP plugin WordPress deserves.*

> **Stop losing emails. Start delivering with confidence.**  
> A 100% open-source SMTP mailer with email logging, queue management, backup failover, and a beautiful dashboard — no premium upsells, no feature restrictions.

![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen.svg)

---

## 🏆 Why Choose Polar SMTP Mailer?

| Feature | Polar SMTP Mailer | WP Mail SMTP | Post SMTP | FluentSMTP | Easy WP SMTP |
|---------|:----------------:|:------------:|:---------:|:----------:|:------------:|
| **100% Free & Open Source** | ✅ | ❌ Pro | ❌ Pro | ✅ | ❌ Pro |
| **Email Logging** | ✅ | ❌ Pro | ✅ | ✅ | ✅ |
| **Backup SMTP Failover** | ✅ | ❌ Pro | ✅ | ❌ | ❌ |
| **Email Queue** | ✅ | ❌ Pro | ✅ | ❌ | ❌ |
| **15+ Provider Presets** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Password Encryption** | ✅ AES-256 | ✅ | ✅ | ✅ | ✅ |
| **GDPR Privacy Tools** | ✅ | ❌ Pro | ❌ | ❌ | ❌ |
| **Modern Dashboard** | ✅ | ❌ Pro | ✅ | ✅ | ❌ |
| **Self-Healing Database** | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Clean Uninstall Option** | ✅ | ❌ | ❌ | ❌ | ❌ |
| **No Upsells/Ads** | ✅ | ❌ | ❌ | ✅ | ❌ |

---

## ✨ Features at a Glance

| Feature | Description |
|---------|-------------|
| 📧 **15+ SMTP Providers** | Gmail, Outlook, Yahoo, SendGrid, Mailgun, Amazon SES, Hostinger & more |
| 📊 **Email Logging** | Complete history with search, filter, export (CSV/JSON) |
| 🔄 **Email Queue** | Background processing with priority & retry logic |
| 🔒 **Backup SMTP** | Automatic failover when primary fails |
| 🛡️ **Enterprise Security** | AES-256 encryption, rate limiting, CSRF protection |
| 🔐 **GDPR Compliant** | Privacy exporter, eraser, and anonymization |
| 🎨 **Modern Dashboard** | Statistics, charts, and quick actions |
| ♿ **Accessible** | WCAG AA compliant, screen reader friendly |

---

## 📧 SMTP Configuration

### Supported Providers

| Provider | Host | Port (TLS) | Port (SSL) |
|----------|------|------------|------------|
| Gmail / Google Workspace | smtp.gmail.com | 587 | 465 |
| Outlook / Microsoft 365 | smtp.office365.com | 587 | - |
| Yahoo Mail | smtp.mail.yahoo.com | 587 | 465 |
| Zoho Mail | smtp.zoho.com | 587 | 465 |
| Hostinger | smtp.hostinger.com | 465 (SSL) | 465 |
| SendGrid | smtp.sendgrid.net | 587 | 465 |
| Mailgun | smtp.mailgun.org | 587 | 465 |
| Amazon SES | email-smtp.[region].amazonaws.com | 587 | 465 |
| Brevo (Sendinblue) | smtp-relay.brevo.com | 587 | 465 |
| Postmark | smtp.postmarkapp.com | 587 | 465 |
| SparkPost | smtp.sparkpostmail.com | 587 | - |
| SMTP.com | send.smtp.com | 587 | 465 |
| Elastic Email | smtp.elasticemail.com | 2525 | - |
| Mailjet | in-v3.mailjet.com | 587 | 465 |
| Pepipost (Netcore) | smtp.pepipost.com | 587 | 465 |

### Encryption Options

| Type | Port | Recommendation |
|------|------|----------------|
| **TLS** | 587 | ✅ Recommended (STARTTLS) |
| **SSL** | 465 | Good alternative |
| **None** | 25 | ⚠️ Not recommended |

---

## 📊 Email Logging

- **Complete History** - Track all sent, failed, and queued emails
- **View Details** - Full email content, headers, CC/BCC, attachments
- **Search & Filter** - Find by recipient, subject, status, or date range
- **One-Click Actions** - View, Resend, Delete individual logs
- **Bulk Operations** - Delete multiple logs at once
- **Export** - Download as CSV or JSON
- **Auto Cleanup** - Configurable retention (default: 30 days)

---

## 🔄 Email Queue System

| Feature | Description |
|---------|-------------|
| Background Processing | Via WP-Cron, no server impact |
| Batch Size | Configurable (default: 10) |
| Processing Interval | Configurable (default: 5 min) |
| Priority Support | 1-10 scale for urgent emails |
| Retry Logic | Up to 3 attempts on failure |
| Lock Mechanism | Prevents duplicate processing |
| Test Bypass | Test emails skip queue |

---

## 🛡️ Security

| Feature | Implementation |
|---------|----------------|
| Password Encryption | AES-256-CBC with WordPress AUTH_KEY |
| CSRF Protection | Nonces on all forms and AJAX |
| Authorization | `manage_options` capability required |
| Rate Limiting | 5 test emails per 10 minutes |
| Input Sanitization | All inputs sanitized |
| Output Escaping | All output escaped (XSS prevention) |
| SQL Injection | Prepared statements everywhere |

---

## 🔐 Privacy & GDPR

- ✅ WordPress Privacy Exporter integration
- ✅ WordPress Privacy Eraser integration
- ✅ Email anonymization option
- ✅ Content exclusion from logs
- ✅ Auto-suggested privacy policy text
- ✅ Configurable data retention

---

## 🎨 Modern Dashboard

- 📈 Email statistics (Today, Week, Month, All-time)
- 📊 Visual charts with daily trends
- ⚡ Quick actions (Test email, View logs, Settings)
- 🔌 Connection health indicator
- 📬 Real-time queue counter
- 📱 Fully responsive design

---

## 📥 Installation

### From GitHub
```bash
# Download and extract to wp-content/plugins/
git clone https://github.com/boopathirbk/polar-smtp-mailer.git
```

### From WordPress
1. **Plugins → Add New → Upload Plugin**
2. Upload the ZIP file
3. Activate and configure at **Polar SMTP → Settings**

---

## ⚙️ Quick Setup

1. Navigate to **Polar SMTP → Settings**
2. Select your SMTP provider from dropdown
3. Enter your credentials (username/password)
4. Click **Test Connection** to verify
5. Send a test email to confirm delivery
6. **Save Settings**

---

## 🔧 Requirements

| Requirement | Version |
|-------------|---------|
| WordPress | 6.0+ |
| PHP | 7.4+ |
| MySQL | 5.6+ / MariaDB 10.0+ |
| OpenSSL | Required for encryption |

---

## 🧑‍💻 Developer Hooks

### Add Custom Provider
```php
add_filter( 'psm_smtp_providers', function( $providers ) {
    $providers['my_provider'] = array(
        'name'       => 'My Custom Provider',
        'host'       => 'smtp.example.com',
        'port'       => 587,
        'encryption' => 'tls',
        'auth'       => true,
    );
    return $providers;
});
```

### Bypass Queue for Urgent Emails
```php
add_filter( 'psm_bypass_queue', function( $bypass, $atts ) {
    if ( strpos( $atts['subject'], 'Urgent' ) !== false ) {
        return true; // Send immediately
    }
    return $bypass;
}, 10, 2 );
```

---

## 📸 Screenshots

| Dashboard | Settings |
|-----------|----------|
| ![Dashboard](screenshots/dashboard-page.png) | ![Settings](screenshots/settings-page.png) |

| Email Logs | Test Email |
|------------|------------|
| ![Logs](screenshots/email-logs-page.png) | ![Test](screenshots/send-test-email-page.png) |

---

## 📝 Changelog

### 1.0.4 (2025-12-11)
- **Compliance:** Fixed global variable naming conventions in admin views.
- **Compliance:** Fixed hook naming conventions.
- **Fix:** Backup SMTP connection test.
- **Fix:** Uninstall cleanup.

### 1.0.3 (2025-12-10)
- Removed debug logging for compliance.

### 1.0.2 (2025-12-10)
- Fixed HTML email display in log viewer.
- Fixed empty recipient validation in queue.
- Improved PHP 8.2 compatibility.

### 1.0.1 (2025-12-10)
- Security fixes (Encryption, SQL prep).
- Fixed race condition in queue processing.
- TLS 1.2/1.3 support.

### 1.0.0 (2025-12-06)
**Initial Release:**
- SMTP configuration with 15+ provider presets
- Email logging with View, Resend, Delete actions
- Email queue with background processing
- Backup SMTP failover
- GDPR-compliant privacy features
- Modern admin dashboard with statistics
- AES-256-CBC password encryption
- Rate limiting and security features
- Hostinger SMTP sender fix
- Self-healing database
- Clean uninstall option
- Full i18n support

---

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/Amazing`)
3. Commit changes (`git commit -m 'Add Amazing Feature'`)
4. Push to branch (`git push origin feature/Amazing`)
5. Open a Pull Request

---

## 📄 License

GPL v2 or later - see [LICENSE](LICENSE) for details.

---

## 👨‍💻 Author

**Boopathi R.**
- LinkedIn: [@boopathirb](https://linkedin.com/in/boopathirb)
- GitHub: [@boopathirbk](https://github.com/boopathirbk)

---

## ☕ Support This Project

If you find this plugin useful, consider buying me a coffee!

[![Donate with PayPal](https://img.shields.io/badge/Donate-PayPal-blue.svg?style=for-the-badge&logo=paypal)](https://paypal.me/boopathirbk)

---

Made with ❤️ for the WordPress community
