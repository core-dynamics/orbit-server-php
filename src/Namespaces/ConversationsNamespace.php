<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Namespaces;

use OrbitConnect\Server\HttpClient;

class ConversationsNamespace
{
    public function __construct(private readonly HttpClient $http) {}

    /** List all conversations for a specific app user */
    public function list(string $appUserId): array
    {
        return $this->http->get('/conversations', $appUserId);
    }

    /**
     * Create a direct or group conversation.
     *
     * Direct:  ['type' => 'direct', 'participant_id' => '...']
     * Group:   ['type' => 'group',  'participant_ids' => [...]]
     */
    public function create(array $input, string $actingUserId): array
    {
        return $this->http->post('/conversations', $input, $actingUserId);
    }

    /** Get a conversation by ID */
    public function get(string $id, string $actingUserId): array
    {
        return $this->http->get("/conversations/{$id}", $actingUserId);
    }

    /** Update a group conversation */
    public function update(string $id, array $data, string $actingUserId): array
    {
        return $this->http->patch("/conversations/{$id}", $data, $actingUserId);
    }

    /** List participants */
    public function listParticipants(string $conversationId, string $actingUserId): array
    {
        return $this->http->get("/conversations/{$conversationId}/users", $actingUserId);
    }

    /** Add a participant */
    public function addParticipant(string $conversationId, string $appUserId, string $actingUserId): array
    {
        return $this->http->post("/conversations/{$conversationId}/users", ['app_user_id' => $appUserId], $actingUserId);
    }

    /** Remove a participant */
    public function removeParticipant(string $conversationId, string $appUserId, string $actingUserId): void
    {
        $this->http->delete("/conversations/{$conversationId}/users/{$appUserId}", $actingUserId);
    }
}
