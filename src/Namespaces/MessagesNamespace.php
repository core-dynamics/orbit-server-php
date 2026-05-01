<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Namespaces;

use OrbitConnect\Server\HttpClient;

class MessagesNamespace
{
    public function __construct(private readonly HttpClient $http) {}

    /** Send a message to a conversation */
    public function send(array $input, string $actingUserId): array
    {
        $conversationId = $input['conversation_id'];
        $body = [
            'content'         => $input['content'] ?? null,
            'type'            => $input['type'] ?? 'system',
            'metadata'        => $input['metadata'] ?? null,
            'idempotency_key' => $input['idempotency_key'] ?? null,
        ];
        return $this->http->post("/conversations/{$conversationId}/messages", array_filter($body, fn($v) => $v !== null), $actingUserId);
    }

    /** Fetch messages in a conversation */
    public function list(string $conversationId, array $opts = [], ?string $actingUserId = null): array
    {
        $params = [];
        if (isset($opts['afterSequence'])) $params['after_sequence'] = $opts['afterSequence'];
        if (isset($opts['limit']))         $params['limit']          = $opts['limit'];
        if (isset($opts['offset']))        $params['offset']         = $opts['offset'];

        $qs = $params ? '?' . http_build_query($params) : '';
        return $this->http->get("/conversations/{$conversationId}/messages{$qs}", $actingUserId);
    }

    /** Get a single message */
    public function get(string $id, string $actingUserId): array
    {
        return $this->http->get("/messages/{$id}", $actingUserId);
    }

    /** Edit a message (sender only) */
    public function edit(string $id, string $content, string $actingUserId): array
    {
        return $this->http->patch("/messages/{$id}", ['content' => $content], $actingUserId);
    }

    /** Delete a message (soft-delete for the requester) */
    public function delete(string $id, string $actingUserId): void
    {
        $this->http->delete("/messages/{$id}", $actingUserId);
    }

    /** Recall a message — wipes content for all participants */
    public function recall(string $id, string $actingUserId): array
    {
        return $this->http->patch("/messages/{$id}/recall", [], $actingUserId);
    }

    /** Acknowledge delivery */
    public function ack(string $id, string $actingUserId): void
    {
        $this->http->post("/messages/{$id}/ack", [], $actingUserId);
    }

    /** Mark a message as read */
    public function markRead(string $id, string $actingUserId): void
    {
        $this->http->post("/messages/{$id}/read", [], $actingUserId);
    }

    /** Toggle a reaction on a message */
    public function react(string $id, string $reactionType, string $actingUserId): array
    {
        return $this->http->post("/messages/{$id}/reactions", ['reaction_type' => $reactionType], $actingUserId);
    }

    /** Reply to a message */
    public function reply(array $input, string $actingUserId): array
    {
        $conversationId = $input['conversation_id'];
        $body = [
            'content'             => $input['content'] ?? null,
            'type'                => $input['type'] ?? 'text',
            'metadata'            => $input['metadata'] ?? null,
            'reply_to_message_id' => $input['reply_to_message_id'],
        ];
        return $this->http->post("/conversations/{$conversationId}/messages/reply", array_filter($body, fn($v) => $v !== null), $actingUserId);
    }

    /** Forward a message to one or more conversations */
    public function forward(string $id, array $targetConversationIds, string $actingUserId): array
    {
        return $this->http->post("/messages/{$id}/forward", ['target_conversation_ids' => $targetConversationIds], $actingUserId);
    }

    /** Pin a message */
    public function pin(string $id, string $actingUserId): array
    {
        return $this->http->post("/messages/{$id}/pin", [], $actingUserId);
    }

    /** Unpin a message */
    public function unpin(string $id, string $actingUserId): void
    {
        $this->http->delete("/messages/{$id}/pin", $actingUserId);
    }

    /** Get pinned messages in a conversation */
    public function getPinned(string $conversationId, string $actingUserId): array
    {
        return $this->http->get("/conversations/{$conversationId}/messages/pinned", $actingUserId);
    }

    /** Get per-recipient delivery info */
    public function getInfo(string $id, string $actingUserId): mixed
    {
        return $this->http->get("/messages/{$id}/info", $actingUserId);
    }

    /** Search messages in a conversation */
    public function search(string $conversationId, string $query, string $actingUserId, int $limit = 20): array
    {
        $qs = http_build_query(['conversation_id' => $conversationId, 'q' => $query, 'limit' => $limit]);
        return $this->http->get("/search/messages?{$qs}", $actingUserId);
    }

    /** Search messages across all conversations the user belongs to */
    public function searchGlobal(string $query, string $actingUserId, int $limit = 20): array
    {
        $qs = http_build_query(['q' => $query, 'limit' => $limit]);
        return $this->http->get("/search/messages/global?{$qs}", $actingUserId);
    }

    /** Sync messages updated after a timestamp */
    public function sync(string $conversationId, \DateTimeInterface $since, string $actingUserId, int $limit = 100): array
    {
        $qs = http_build_query([
            'conversation_id' => $conversationId,
            'since'           => $since->format(\DateTimeInterface::ATOM),
            'limit'           => $limit,
        ]);
        return $this->http->get("/sync/messages?{$qs}", $actingUserId);
    }
}
