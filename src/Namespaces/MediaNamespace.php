<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Namespaces;

use OrbitConnect\Server\HttpClient;

class MediaNamespace
{
    public function __construct(private readonly HttpClient $http) {}

    // ── Upload ────────────────────────────────────────────────────────────────

    /** Generate a pre-signed upload URL for direct upload */
    public function generateUploadUrl(string $mimeType, string $actingUserId): array
    {
        return $this->http->post('/media/upload-url', ['mime_type' => $mimeType], $actingUserId);
    }

    /** Initialise a multipart upload session */
    public function initMultipart(string $mimeType, string $filename, string $actingUserId): array
    {
        return $this->http->post('/media/multipart/init', ['mime_type' => $mimeType, 'filename' => $filename], $actingUserId);
    }

    /** Complete a multipart upload and register the media file */
    public function completeMultipart(string $sessionId, string $mimeType, int $size, string $actingUserId): array
    {
        return $this->http->post('/media/multipart/complete', [
            'session_id' => $sessionId,
            'mime_type'  => $mimeType,
            'size'       => $size,
        ], $actingUserId);
    }

    // ── Media CRUD ────────────────────────────────────────────────────────────

    /** Get a media file by ID */
    public function get(string $mediaId, string $actingUserId): array
    {
        return $this->http->get("/media/{$mediaId}", $actingUserId);
    }

    /** Get a download URL for a media file */
    public function getDownloadUrl(string $mediaId, string $actingUserId): array
    {
        return $this->http->get("/media/{$mediaId}/download-url", $actingUserId);
    }

    /** Get a stream URL for a video or audio file */
    public function getStreamUrl(string $mediaId, string $actingUserId): array
    {
        return $this->http->get("/media/{$mediaId}/stream", $actingUserId);
    }

    /** Get extracted metadata (duration, dimensions, codec) */
    public function getMetadata(string $mediaId, string $actingUserId): array
    {
        return $this->http->get("/media/{$mediaId}/metadata", $actingUserId);
    }

    /** Get processing job status */
    public function getStatus(string $mediaId, string $actingUserId): array
    {
        return $this->http->get("/media/{$mediaId}/status", $actingUserId);
    }

    /** Trigger reprocessing (transcode, thumbnail, metadata) */
    public function process(string $mediaId, string $actingUserId): array
    {
        return $this->http->post("/media/{$mediaId}/process", [], $actingUserId);
    }

    /** Delete a media file */
    public function delete(string $mediaId, string $actingUserId): void
    {
        $this->http->delete("/media/{$mediaId}", $actingUserId);
    }

    /** Search media files */
    public function search(string $query, string $actingUserId): array
    {
        return $this->http->get('/media/search?q=' . urlencode($query), $actingUserId);
    }

    // ── Access control ────────────────────────────────────────────────────────

    /** Grant access to a media file for another app_user */
    public function grantAccess(string $mediaId, array $input, string $actingUserId): array
    {
        return $this->http->post("/media/{$mediaId}/access", $input, $actingUserId);
    }

    /** List all access grants for a media file */
    public function listAccess(string $mediaId, string $actingUserId): array
    {
        return $this->http->get("/media/{$mediaId}/access", $actingUserId);
    }

    // ── Recordings ────────────────────────────────────────────────────────────

    /** Start a recording session */
    public function startRecording(array $input, string $actingUserId): array
    {
        return $this->http->post('/recordings/start', $input, $actingUserId);
    }

    /** Stop a recording and queue processing */
    public function stopRecording(string $recordingId, string $actingUserId): array
    {
        return $this->http->post("/recordings/{$recordingId}/stop", [], $actingUserId);
    }

    /** Get a recording by ID */
    public function getRecording(string $recordingId, string $actingUserId): array
    {
        return $this->http->get("/recordings/{$recordingId}", $actingUserId);
    }

    /** List all recordings for the acting user */
    public function listRecordings(string $actingUserId): array
    {
        return $this->http->get('/recordings', $actingUserId);
    }
}
