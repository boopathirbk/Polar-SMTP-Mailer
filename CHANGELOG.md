# Changelog

All notable changes to the Polar SMTP Mailer plugin will be documented in this file.

## [1.0.4] - 2025-12-11

### Fixed

- **Global Variable Compliance** - Fixed all "NonPrefixedVariableFound" Plugin Check errors in admin views (`dashboard`, `settings`, `logs`, `test`) by renaming variables with `psm_` prefix.
- **Hook Naming Compliance** - Renamed all custom hooks (`PSM_smtp_providers` -> `psm_smtp_providers`, etc.) to lowercase with `psm_` prefix for WordPress coding standards compliance.
- **Backup SMTP Test** - Fixed JavaScript context issue and missing `preventDefault` that caused the "Test Backup Connection" button to be unresponsive.
- **Backup Test Result Styling** - Added proper success/error color styling for backup connection test results.
- **Uninstall Cleanup** - Improved uninstall process to properly delete new backup SMTP options.
- **Duplicate logs** - Fixed issue where test emails created two log entries.
- **Global wp_mail capture** - Mailer now hooks immediately during plugin load to capture ALL emails.

### Added

- **From Email Tracking** - Email logs now capture and display the sender email address.
- **Enhanced Email Modal** - Redesigned email details modal with modern grid layout showing To, From, Subject, Status, Provider, and Date.
- **Improved Modal Responsiveness** - Modal now adapts properly to mobile screens.
- **Debug Logs admin page** - New "Debug Logs" menu appears when debug mode is enabled. View SMTP communication logs directly in the plugin.

---

## [1.0.3] - 2025-12-10

### Fixed

- **Removed debug logging** - Removed all `error_log()` calls to pass WordPress.org Plugin Check requirements.

---

## [1.0.2] - 2025-12-10

### Fixed

- **Fixed HTML email display in log viewer** - HTML emails now render correctly in the log viewer modal instead of showing raw source code.

- **Fixed empty recipient validation** - Added validation to prevent queueing emails with empty recipients.

- **Fixed decryption failure handling** - Plugin now gracefully handles decryption failures (e.g., after AUTH_KEY changes) by clearing the password and logging a warning, instead of failing silently.

- **Fixed duplicate PHPDoc block** - Removed accidentally duplicated documentation comment.

### Improved

- **Added PHP 8.2 compatibility** - Added `#[AllowDynamicProperties]` attribute to core classes to prevent deprecation notices.

- **Added exception handling for PHPMailer** - PHPMailer configuration is now wrapped in try-catch to prevent fatal errors.

- **Added attachment sanitization** - Large attachment data is now truncated in logs to prevent database bloat.

- **Optimized auth failure logging** - Authentication failure logs now use `autoload=no` to prevent loading on every page request.

- **Improved hook documentation** - Added PHPDoc blocks for `PSM_bypass_queue` filter.

---

## [1.0.1] - 2025-12-10

### Security

- **CRITICAL: Fixed static IV vulnerability in encryption** - The encryption system now uses a random IV (Initialization Vector) per encryption instead of a deterministic static IV. This significantly improves password encryption security. Backward compatibility is maintained for existing encrypted passwords using the legacy format (`enc:` prefix), while new encryptions use the secure format (`enc2:` prefix).

- **Fixed potential SQL injection in table existence check** - The `check_tables_exist()` method now uses `$wpdb->prepare()` properly instead of direct string concatenation.

- **Added capability check to `clear_queue()` method** - Prevents unauthorized queue clearing by requiring `manage_options` capability.

### Fixed

- **Fixed race condition in queue processing** - Implemented proper row-level locking using database transactions with `SELECT ... FOR UPDATE` to prevent multiple processes from grabbing the same queued email.

- **Fixed TLS version compatibility** - Updated SMTP connection test to use TLS 1.2/1.3 instead of the deprecated TLS 1.0. This fixes connection issues with modern mail servers that reject older TLS versions.

- **Fixed potential infinite loops in SMTP response reading** - Added iteration limits (max 100 lines) to prevent hanging when SMTP servers send unexpected response formats.

- **Fixed missing exit statements in AJAX handlers** - Added explicit `exit` calls after `wp_send_json_error()` in `view_log`, `delete_log`, and `resend_email` handlers for consistency and safety.

- **Fixed duplicate log entries when using email queue** - Removed premature log insertion when adding emails to queue. Logs are now only created when emails are actually sent, preventing duplicate entries.

- **Fixed privacy erase not clearing attachments** - The GDPR data erasure now also clears the `attachments` field, which may contain file paths with personally identifiable information.

- **Fixed password placeholder edge case** - Added a secondary token check (`__PSM_UNCHANGED__`) to prevent the edge case where a user's actual password matches the display placeholder.

- **Fixed JSON export memory issue** - Changed JSON export to use chunked processing (500 records per chunk) similar to CSV export, preventing memory exhaustion with large datasets.

### Improved

- **Enhanced encryption format** - New `enc2:` encryption format stores the random IV alongside the ciphertext, enabling proper AES-CBC security while maintaining backward compatibility with existing `enc:` format passwords.

- **Better code documentation** - Added PHPDoc blocks with `@since`, `@param`, and `@return` tags to improved methods.

---

## [1.0.0] - 2025-12-06

### Added

- Initial release
- SMTP configuration with multiple provider presets
- Email logging with search and filtering
- Email queue system for scheduled sending
- Backup SMTP failover support
- Test email functionality with rate limiting
- GDPR/Privacy compliance tools
- Secure password encryption using AES-256-CBC
- Dashboard with email statistics
- CSV and JSON log export
