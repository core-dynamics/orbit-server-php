<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Tests;

use OrbitConnect\Server\OrbitServer;
use OrbitConnect\Server\OrbitServerException;
use OrbitConnect\Server\Namespaces\UsersNamespace;
use OrbitConnect\Server\Namespaces\ConversationsNamespace;
use OrbitConnect\Server\Namespaces\MessagesNamespace;
use OrbitConnect\Server\Namespaces\CallsNamespace;
use OrbitConnect\Server\Namespaces\MeetingsNamespace;
use OrbitConnect\Server\Namespaces\MediaNamespace;
use OrbitConnect\Server\Namespaces\RealtimeNamespace;
use OrbitConnect\Server\Namespaces\WebhooksNamespace;
use OrbitConnect\Server\Namespaces\BillingNamespace;
use PHPUnit\Framework\TestCase;

class OrbitServerTest extends TestCase
{
    private OrbitServer $orbit;

    protected function setUp(): void
    {
        $this->orbit = new OrbitServer('sk_test_dummy');
    }

    public function testNamespacesAreInstantiated(): void
    {
        $this->assertInstanceOf(UsersNamespace::class,         $this->orbit->users);
        $this->assertInstanceOf(ConversationsNamespace::class, $this->orbit->conversations);
        $this->assertInstanceOf(MessagesNamespace::class,      $this->orbit->messages);
        $this->assertInstanceOf(CallsNamespace::class,         $this->orbit->calls);
        $this->assertInstanceOf(MeetingsNamespace::class,      $this->orbit->meetings);
        $this->assertInstanceOf(MediaNamespace::class,         $this->orbit->media);
        $this->assertInstanceOf(RealtimeNamespace::class,      $this->orbit->realtime);
        $this->assertInstanceOf(WebhooksNamespace::class,      $this->orbit->webhooks);
        $this->assertInstanceOf(BillingNamespace::class,       $this->orbit->billing);
    }

    public function testBaseUrl(): void
    {
        $this->assertSame('https://api.orbitconnect.cloud/api/v1', OrbitServer::baseUrl());
    }

    public function testExceptionExposesStatusAndErrorCode(): void
    {
        $e = new OrbitServerException('Not found', 404, 'resource_not_found');

        $this->assertSame(404,                  $e->status);
        $this->assertSame('resource_not_found', $e->errorCode);
        $this->assertSame('Not found',          $e->getMessage());
    }

    public function testExceptionErrorCodeIsNullable(): void
    {
        $e = new OrbitServerException('Server error', 500);
        $this->assertNull($e->errorCode);
    }
}
