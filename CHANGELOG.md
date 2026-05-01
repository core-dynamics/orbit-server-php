# Changelog

## [1.0.0] - 2026-05-01

### Added
- Initial release
- `OrbitServer` client with 9 namespaces: users, conversations, messages, calls, meetings, media, realtime, webhooks, billing
- Native cURL HTTP client — zero dependencies
- Optional HMAC-SHA256 request signing via `signingSecret`
- `OrbitServerException` with `$status` and `$errorCode` properties
