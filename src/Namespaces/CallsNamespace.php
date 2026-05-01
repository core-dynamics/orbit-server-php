<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Namespaces;

use OrbitConnect\Server\HttpClient;

class CallsNamespace
{
    public function __construct(private readonly HttpClient $http) {}

    /** Initiate a call from one app_user to another */
    public function initiate(array $input, string $actingUserId): array
    {
        return $this->http->post('/calls/initiate', $input, $actingUserId);
    }

    /** Accept an incoming call */
    public function accept(string $callId, string $actingUserId): array
    {
        return $this->http->post("/calls/{$callId}/accept", [], $actingUserId);
    }

    /** Reject an incoming call */
    public function reject(string $callId, string $actingUserId): array
    {
        return $this->http->post("/calls/{$callId}/reject", [], $actingUserId);
    }

    /** End an active call */
    public function end(string $callId, string $actingUserId): array
    {
        return $this->http->post("/calls/{$callId}/end", [], $actingUserId);
    }

    /** Get a call session by ID */
    public function get(string $callId, string $actingUserId): array
    {
        return $this->http->get("/calls/{$callId}", $actingUserId);
    }

    /** List all calls for an app_user */
    public function list(string $actingUserId): array
    {
        return $this->http->get('/calls', $actingUserId);
    }

    /** Submit quality metrics */
    public function submitMetrics(string $callId, array $input, string $actingUserId): array
    {
        return $this->http->post("/calls/{$callId}/metrics", $input, $actingUserId);
    }

    /** Get all metrics for a call */
    public function getMetrics(string $callId, string $actingUserId): array
    {
        return $this->http->get("/calls/{$callId}/metrics", $actingUserId);
    }

    /** Toggle recording for a call */
    public function toggleRecord(string $callId, string $actingUserId): array
    {
        return $this->http->post("/calls/{$callId}/recordings", [], $actingUserId);
    }

    /** List recordings for a call */
    public function listRecordings(string $callId, string $actingUserId): array
    {
        return $this->http->get("/calls/{$callId}/recordings", $actingUserId);
    }

    /** Get a recording by ID */
    public function getRecording(string $recordingId, string $actingUserId): array
    {
        return $this->http->get("/recordings/{$recordingId}", $actingUserId);
    }
}
