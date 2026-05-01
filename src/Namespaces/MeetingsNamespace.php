<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Namespaces;

use OrbitConnect\Server\HttpClient;

class MeetingsNamespace
{
    public function __construct(private readonly HttpClient $http) {}

    // ── Core lifecycle ────────────────────────────────────────────────────────

    /** Create or schedule a meeting */
    public function create(array $input, string $actingUserId): array
    {
        return $this->http->post('/meetings', $input, $actingUserId);
    }

    /** List meetings for an app_user */
    public function list(string $actingUserId): array
    {
        return $this->http->get('/meetings', $actingUserId);
    }

    /** Get a meeting by ID */
    public function get(string $meetingId, string $actingUserId): array
    {
        return $this->http->get("/meetings/{$meetingId}", $actingUserId);
    }

    /** Update meeting title/description */
    public function update(string $meetingId, array $data, string $actingUserId): array
    {
        return $this->http->patch("/meetings/{$meetingId}", $data, $actingUserId);
    }

    /** Start a scheduled meeting (host only) */
    public function start(string $meetingId, string $actingUserId): array
    {
        return $this->http->post("/meetings/{$meetingId}/start", [], $actingUserId);
    }

    /** End an active meeting */
    public function end(string $meetingId, string $actingUserId): array
    {
        return $this->http->post("/meetings/{$meetingId}/end", [], $actingUserId);
    }

    /** Join a meeting (optionally with a token) */
    public function join(string $meetingId, string $actingUserId, ?string $token = null): array
    {
        $body = $token !== null ? ['token' => $token] : [];
        return $this->http->post("/meetings/{$meetingId}/join", $body, $actingUserId);
    }

    /** Leave a meeting */
    public function leave(string $meetingId, string $actingUserId): void
    {
        $this->http->post("/meetings/{$meetingId}/leave", [], $actingUserId);
    }

    // ── Participant management ────────────────────────────────────────────────

    /** Add a participant */
    public function addParticipant(string $meetingId, string $appUserId, string $actingUserId): array
    {
        return $this->http->post("/meetings/{$meetingId}/users", ['app_user_id' => $appUserId], $actingUserId);
    }

    /** Remove a participant from the list */
    public function removeParticipant(string $meetingId, string $appUserId, string $actingUserId): void
    {
        $this->http->delete("/meetings/{$meetingId}/users/{$appUserId}", $actingUserId);
    }

    /** Kick a participant (remove + event log) */
    public function kickParticipant(string $meetingId, string $appUserId, string $actingUserId): void
    {
        $this->http->post("/meetings/{$meetingId}/remove", ['app_user_id' => $appUserId], $actingUserId);
    }

    /** Update a participant's role */
    public function updateRole(string $meetingId, string $appUserId, string $role, string $actingUserId): void
    {
        $this->http->patch("/meetings/{$meetingId}/users/{$appUserId}/role", ['role' => $role], $actingUserId);
    }

    /** Mute a participant */
    public function mute(string $meetingId, string $appUserId, string $actingUserId): void
    {
        $this->http->post("/meetings/{$meetingId}/mute", ['app_user_id' => $appUserId], $actingUserId);
    }

    /** Unmute a participant */
    public function unmute(string $meetingId, string $appUserId, string $actingUserId): void
    {
        $this->http->post("/meetings/{$meetingId}/unmute", ['app_user_id' => $appUserId], $actingUserId);
    }

    // ── Screen share ──────────────────────────────────────────────────────────

    /** Signal screen share start */
    public function startScreenShare(string $meetingId, string $actingUserId): void
    {
        $this->http->post("/meetings/{$meetingId}/screen-share/start", [], $actingUserId);
    }

    /** Signal screen share stop */
    public function stopScreenShare(string $meetingId, string $actingUserId): void
    {
        $this->http->post("/meetings/{$meetingId}/screen-share/stop", [], $actingUserId);
    }

    // ── Token ─────────────────────────────────────────────────────────────────

    /** Generate a join token for a specific user or an open link */
    public function generateToken(string $meetingId, array $input, string $actingUserId): array
    {
        return $this->http->post("/meetings/{$meetingId}/token", $input, $actingUserId);
    }

    // ── Recording ─────────────────────────────────────────────────────────────

    /** Start recording */
    public function startRecording(string $meetingId, string $actingUserId): array
    {
        return $this->http->post("/meetings/{$meetingId}/record/start", [], $actingUserId);
    }

    /** Stop recording */
    public function stopRecording(string $meetingId, string $actingUserId): mixed
    {
        return $this->http->post("/meetings/{$meetingId}/record/stop", [], $actingUserId);
    }

    /** List recordings */
    public function listRecordings(string $meetingId, string $actingUserId): array
    {
        return $this->http->get("/meetings/{$meetingId}/record", $actingUserId);
    }

    /** Get a specific recording by ID */
    public function getRecording(string $meetingId, string $recordingId, string $actingUserId): array
    {
        return $this->http->get("/meetings/{$meetingId}/record/{$recordingId}", $actingUserId);
    }

    // ── Metrics ───────────────────────────────────────────────────────────────

    /** Submit quality metrics */
    public function submitMetrics(string $meetingId, array $input, string $actingUserId): array
    {
        return $this->http->post("/meetings/{$meetingId}/metrics", $input, $actingUserId);
    }

    /** Get all metrics for a meeting */
    public function getMetrics(string $meetingId, string $actingUserId): array
    {
        return $this->http->get("/meetings/{$meetingId}/metrics", $actingUserId);
    }
}
