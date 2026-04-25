# Changelog

## [1.1.0]

### Added
- `UserClient::getUserUuidByAuth0Id(string $auth0Id): ?string` — Resolves a DiarioHilario UUID from an Auth0 subject ID (`sub`). Result is cached for 1 hour; errors are cached for 60 seconds to avoid hammering the service on failures.

---

## [1.0.0] — 2026-04-25

### Added
- `UserClient` — HTTP client wrapping the dh-core API with Symfony Cache support.
- `UserClient::getUserName(string $uuid): ?string` — Returns the display name for a given DiarioHilario UUID. Result is cached for 1 hour.
