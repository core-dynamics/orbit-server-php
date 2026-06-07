# Changelog

## [Unreleased]

### Added — Conversation Context System

- `ConversationsNamespace::updateContext(string $id, array $context, string $actingUserId)` — partial merge update of conversation context
- `ConversationsNamespace::findByReference(string $referenceId, string $actingUserId)` — lookup conversations by `context.referenceId`
- `ConversationsNamespace::create()` now accepts optional `context` key in the input array for attaching business context (booking, service, purchase, support) to conversations

---

## [1.0.0] - 2026-05-01

### Added
- Initial release
- `OrbitServer` client with 9 namespaces: users, conversations, messages, calls, meetings, media, realtime, webhooks, billing
- Native cURL HTTP client — zero dependencies
- Optional HMAC-SHA256 request signing via `signingSecret`
- `OrbitServerException` with `$status` and `$errorCode` properties
