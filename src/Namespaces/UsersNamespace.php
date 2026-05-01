<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Namespaces;

use OrbitConnect\Server\HttpClient;

class UsersNamespace
{
    public function __construct(private readonly HttpClient $http) {}

    /** Register a new end-user for your application */
    public function create(array $input): array
    {
        return $this->http->post('/app-users', $input);
    }

    /** List all app users. Pass true to filter active only. */
    public function list(bool $activeOnly = false): array
    {
        return $this->http->get('/app-users' . ($activeOnly ? '?active=true' : ''));
    }

    /** Get an app user by OrbitConnect ID */
    public function get(string $id): array
    {
        return $this->http->get("/app-users/{$id}");
    }

    /** Get an app user by your own external_id */
    public function getByExternalId(string $externalId): array
    {
        return $this->http->get("/app-users/external/{$externalId}");
    }

    /** Update display_name or metadata */
    public function update(string $id, array $input): array
    {
        return $this->http->patch("/app-users/{$id}", $input);
    }

    /** Soft-deactivate — preserves message/call history */
    public function deactivate(string $id): void
    {
        $this->http->delete("/app-users/{$id}");
    }

    /**
     * Issue a short-lived token for an app_user.
     *
     * @param  int|null $ttl  Token TTL in seconds (default 3600, max 86400)
     */
    public function createToken(string $appUserId, ?int $ttl = null): array
    {
        $body = $ttl !== null ? ['ttl' => $ttl] : [];
        return $this->http->post("/app-users/{$appUserId}/token", $body);
    }
}
