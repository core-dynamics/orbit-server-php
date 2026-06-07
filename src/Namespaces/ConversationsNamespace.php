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
     * Direct:  ['type' => 'direct', 'participant_id' => '...', 'context' => [...]]
     * Group:   ['type' => 'group',  'participant_ids' => [...], 'context' => [...]]
     *
     * The 'context' key is optional. When provided, it attaches business context
     * (booking, service, purchase, support) to the conversation.
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

    /**
     * Update conversation context (partial merge).
     *
     * Only the provided fields are merged into the existing context.
     *
     * @param string $id           Conversation ID
     * @param array  $context      Partial context fields to merge
     * @param string $actingUserId The app_user performing the update
     * @return array               Updated conversation
     */
    public function updateContext(string $id, array $context, string $actingUserId): array
    {
        return $this->http->patch("/conversations/{$id}/context", ['context' => $context], $actingUserId);
    }

    /**
     * Find conversations by reference ID.
     *
     * Looks up conversations whose context.referenceId matches the given value.
     *
     * @param string $referenceId  The reference ID to search for
     * @param string $actingUserId The app_user performing the lookup
     * @return array               List of matching conversations
     */
    public function findByReference(string $referenceId, string $actingUserId): array
    {
        return $this->http->get('/conversations/by-reference?reference_id=' . urlencode($referenceId), $actingUserId);
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
