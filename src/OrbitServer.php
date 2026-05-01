<?php

declare(strict_types=1);

namespace OrbitConnect\Server;

use OrbitConnect\Server\Namespaces\BillingNamespace;
use OrbitConnect\Server\Namespaces\CallsNamespace;
use OrbitConnect\Server\Namespaces\ConversationsNamespace;
use OrbitConnect\Server\Namespaces\MediaNamespace;
use OrbitConnect\Server\Namespaces\MeetingsNamespace;
use OrbitConnect\Server\Namespaces\MessagesNamespace;
use OrbitConnect\Server\Namespaces\RealtimeNamespace;
use OrbitConnect\Server\Namespaces\UsersNamespace;
use OrbitConnect\Server\Namespaces\WebhooksNamespace;

class OrbitServer
{
    private const BASE_URL = 'https://api.orbitconnect.cloud/api/v1';

    public readonly UsersNamespace         $users;
    public readonly ConversationsNamespace $conversations;
    public readonly MessagesNamespace      $messages;
    public readonly CallsNamespace         $calls;
    public readonly MeetingsNamespace      $meetings;
    public readonly MediaNamespace         $media;
    public readonly RealtimeNamespace      $realtime;
    public readonly WebhooksNamespace      $webhooks;
    public readonly BillingNamespace       $billing;

    /**
     * @param  string      $secretKey      Your application's secret API key (x-api-key)
     * @param  string|null $signingSecret  Optional HMAC signing secret — enables request signing
     */
    public function __construct(string $secretKey, ?string $signingSecret = null)
    {
        $http = new HttpClient(self::BASE_URL, $secretKey, $signingSecret);

        $this->users         = new UsersNamespace($http);
        $this->conversations = new ConversationsNamespace($http);
        $this->messages      = new MessagesNamespace($http);
        $this->calls         = new CallsNamespace($http);
        $this->meetings      = new MeetingsNamespace($http);
        $this->media         = new MediaNamespace($http);
        $this->realtime      = new RealtimeNamespace($http);
        $this->webhooks      = new WebhooksNamespace($http);
        $this->billing       = new BillingNamespace($http);
    }

    public static function baseUrl(): string
    {
        return self::BASE_URL;
    }
}
