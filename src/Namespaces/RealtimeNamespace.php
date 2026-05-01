<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Namespaces;

use OrbitConnect\Server\HttpClient;

class RealtimeNamespace
{
    public function __construct(private readonly HttpClient $http) {}

    // ── Sessions ──────────────────────────────────────────────────────────────

    /** Create a new realtime session for an app_user */
    public function createSession(array $input, string $actingUserId): array
    {
        return $this->http->post('/sessions', $input, $actingUserId);
    }

    /** List all sessions for an app_user */
    public function listSessions(string $actingUserId): array
    {
        return $this->http->get('/sessions', $actingUserId);
    }

    /** Get a session by ID */
    public function getSession(string $sessionId, string $actingUserId): array
    {
        return $this->http->get("/sessions/{$sessionId}", $actingUserId);
    }

    /** Update session (e.g. context_id) */
    public function updateSession(string $sessionId, array $data, string $actingUserId): array
    {
        return $this->http->patch("/sessions/{$sessionId}", $data, $actingUserId);
    }

    /** End a session */
    public function endSession(string $sessionId, string $actingUserId): array
    {
        return $this->http->post("/sessions/{$sessionId}/end", [], $actingUserId);
    }

    // ── Transitions ───────────────────────────────────────────────────────────

    /** Trigger a state transition */
    public function transition(string $sessionId, string $transitionType, ?string $contextId, string $actingUserId): array
    {
        return $this->http->post("/sessions/{$sessionId}/transition", [
            'transition_type' => $transitionType,
            'context_id'      => $contextId,
        ], $actingUserId);
    }

    /** List all transitions for a session */
    public function listTransitions(string $sessionId, string $actingUserId): array
    {
        return $this->http->get("/sessions/{$sessionId}/transitions", $actingUserId);
    }

    // ── Events ────────────────────────────────────────────────────────────────

    /** Emit a custom event on a session */
    public function emitEvent(string $sessionId, array $input, string $actingUserId): array
    {
        return $this->http->post("/sessions/{$sessionId}/events", $input, $actingUserId);
    }

    /** List all events for a session */
    public function listEvents(string $sessionId, string $actingUserId): array
    {
        return $this->http->get("/sessions/{$sessionId}/events", $actingUserId);
    }

    /** Get a single event by ID */
    public function getEvent(string $sessionId, string $eventId, string $actingUserId): array
    {
        return $this->http->get("/sessions/{$sessionId}/events/{$eventId}", $actingUserId);
    }

    // ── Context ───────────────────────────────────────────────────────────────

    /** Get the context mapping for a session */
    public function getContext(string $sessionId, string $actingUserId): array
    {
        return $this->http->get("/sessions/{$sessionId}/context", $actingUserId);
    }

    /** Update the context mapping */
    public function updateContext(string $sessionId, array $input, string $actingUserId): array
    {
        return $this->http->patch("/sessions/{$sessionId}/context", $input, $actingUserId);
    }
}
