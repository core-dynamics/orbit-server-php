<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Namespaces;

use OrbitConnect\Server\HttpClient;

class WebhooksNamespace
{
    public function __construct(private readonly HttpClient $http) {}

    // ── Webhook CRUD ──────────────────────────────────────────────────────────

    /** Create a webhook endpoint */
    public function create(string $url): array
    {
        return $this->http->post('/webhooks', ['url' => $url]);
    }

    /** List all webhooks for the organization */
    public function list(): array
    {
        return $this->http->get('/webhooks');
    }

    /** Get a webhook by ID */
    public function get(string $webhookId): array
    {
        return $this->http->get("/webhooks/{$webhookId}");
    }

    /** Update a webhook (url, is_active) */
    public function update(string $webhookId, array $data): array
    {
        return $this->http->patch("/webhooks/{$webhookId}", $data);
    }

    /** Delete a webhook */
    public function delete(string $webhookId): void
    {
        $this->http->delete("/webhooks/{$webhookId}");
    }

    /** Rotate the signing secret */
    public function rotateSecret(string $webhookId): array
    {
        return $this->http->post("/webhooks/{$webhookId}/rotate-secret", []);
    }

    // ── Subscriptions ─────────────────────────────────────────────────────────

    /** Subscribe to an event type */
    public function subscribe(string $webhookId, string $eventType): array
    {
        return $this->http->post("/webhooks/{$webhookId}/subscriptions", ['event_type' => $eventType]);
    }

    /** List subscriptions for a webhook */
    public function listSubscriptions(string $webhookId): array
    {
        return $this->http->get("/webhooks/{$webhookId}/subscriptions");
    }

    /** Remove a subscription */
    public function unsubscribe(string $webhookId, string $subscriptionId): void
    {
        $this->http->delete("/webhooks/{$webhookId}/subscriptions/{$subscriptionId}");
    }

    // ── Deliveries ────────────────────────────────────────────────────────────

    /** List deliveries for a webhook */
    public function listDeliveries(string $webhookId): array
    {
        return $this->http->get("/webhooks/{$webhookId}/deliveries");
    }

    /** Get a delivery by ID */
    public function getDelivery(string $deliveryId): array
    {
        return $this->http->get("/deliveries/{$deliveryId}");
    }

    /** Get all attempts for a delivery */
    public function getAttempts(string $deliveryId): array
    {
        return $this->http->get("/deliveries/{$deliveryId}/attempts");
    }

    // ── Testing & Events ──────────────────────────────────────────────────────

    /** Send a test event to a webhook endpoint */
    public function test(string $webhookId): array
    {
        return $this->http->post("/webhooks/{$webhookId}/test", []);
    }

    /** Publish a platform event (fans out to all subscribed webhooks) */
    public function publish(string $type, array $payload): array
    {
        return $this->http->post('/events/publish', ['type' => $type, 'payload' => $payload]);
    }

    /** List all platform events for the organization */
    public function listEvents(): array
    {
        return $this->http->get('/events');
    }

    /** Replay a past event */
    public function replay(string $eventId): array
    {
        return $this->http->post("/events/{$eventId}/replay", []);
    }
}
