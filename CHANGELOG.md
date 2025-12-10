# Changelog

All notable changes to the Polar SMTP Mailer plugin will be documented in this file.

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
