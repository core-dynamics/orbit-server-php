# orbit-server-php

[![Latest Version on Packagist](https://img.shields.io/packagist/v/orbitconnect/orbit-server-php.svg)](https://packagist.org/packages/orbitconnect/orbit-server-php)
[![PHP Version](https://img.shields.io/packagist/php-v/orbitconnect/orbit-server-php.svg)](https://packagist.org/packages/orbitconnect/orbit-server-php)
[![License](https://img.shields.io/packagist/l/orbitconnect/orbit-server-php.svg)](LICENSE)

Official PHP server SDK for [OrbitConnect](https://orbitconnect.cloud) — chat, calls, meetings, media, webhooks, and billing. Zero dependencies, native cURL only.

**Requires PHP 8.1+**

---

## Installation

```bash
composer require orbitconnect/orbit-server-php
```

---

## Initialization

```php
use OrbitConnect\Server\OrbitServer;

$orbit = new OrbitServer(secretKey: 'sk_live_...');
```

### With request signing (recommended for production)

```php
$orbit = new OrbitServer(
    secretKey:     'sk_live_...',
    signingSecret: 'whsec_...',
);
```

When a `signingSecret` is provided, every request is signed with HMAC-SHA256 via `x-request-signature` and `x-request-timestamp` headers. The secret must match `SIGNING_SECRET` on your OrbitConnect backend.

---

## Namespaces

| Namespace       | Access via              | Covers                                      |
|-----------------|-------------------------|---------------------------------------------|
| Users           | `$orbit->users`         | App user CRUD, token issuance               |
| Conversations   | `$orbit->conversations` | Direct & group conversations, participants  |
| Messages        | `$orbit->messages`      | Send, edit, react, pin, search, sync        |
| Calls           | `$orbit->calls`         | Initiate, accept, reject, metrics, recordings |
| Meetings        | `$orbit->meetings`      | Schedule, start, end, participants, recordings |
| Media           | `$orbit->media`         | Upload, stream, access control, recordings  |
| Realtime        | `$orbit->realtime`      | Sessions, transitions, events, context      |
| Webhooks        | `$orbit->webhooks`      | Endpoints, subscriptions, deliveries, events |
| Billing         | `$orbit->billing`       | Wallet, transactions, usage, invoices       |

---

## Usage

### Users

```php
// Create an app user
$user = $orbit->users->create([
    'external_id'  => 'user_123',
    'display_name' => 'Jane Doe',
    'metadata'     => ['plan' => 'pro'],
]);

// Issue a short-lived token for your frontend (pass to client SDK)
$token = $orbit->users->createToken($user['id'], ttl: 3600);

// Fetch by your own ID
$user = $orbit->users->getByExternalId('user_123');

// Deactivate (soft-delete, preserves history)
$orbit->users->deactivate($user['id']);
```

### Conversations & Messages

```php
// Start a direct conversation
$conv = $orbit->conversations->create(
    ['type' => 'direct', 'participant_id' => $otherUserId],
    $actingUserId
);

//context conversations
$orbit->conversations->create([
    'type' => 'direct',
    'participant_id' => $customerId,
    'context' => [
        'type' => 'booking',
        'referenceId' => 'booking_888',
        'title' => 'Hair Appointment',
        'status' => 'confirmed',
        'actor' => ['id' => $providerId, 'type' => 'provider', 'name' => 'Glow Salon'],
    ],
], $providerId);

// Update context
$orbit->conversations->updateContext($convId, ['status' => 'completed'], $providerId);

// Find by reference
$convs = $orbit->conversations->findByReference('booking_888', $customerId);


// Send a message
$msg = $orbit->messages->send([
    'conversation_id' => $conv['id'],
    'content'         => 'Hey there!',
    'type'            => 'text',
], $actingUserId);

// Reply
$orbit->messages->reply([
    'conversation_id'     => $conv['id'],
    'content'             => 'Hello back!',
    'reply_to_message_id' => $msg['id'],
], $actingUserId);

// Paginate messages
$messages = $orbit->messages->list($conv['id'], [
    'limit'         => 50,
    'afterSequence' => 100,
], $actingUserId);

// Search
$results = $orbit->messages->search($conv['id'], 'hello', $actingUserId);
```

### Calls

```php
$call = $orbit->calls->initiate([
    'callee_id' => $calleeId,
    'type'      => 'video',
], $callerId);

$orbit->calls->accept($call['id'], $calleeId);
$orbit->calls->end($call['id'], $callerId);
```

### Meetings

```php
$meeting = $orbit->meetings->create([
    'title'        => 'Team standup',
    'scheduled_at' => '2026-05-02T09:00:00Z',
], $hostId);

$orbit->meetings->addParticipant($meeting['id'], $guestId, $hostId);
$orbit->meetings->start($meeting['id'], $hostId);

// Generate a join token for a guest
$token = $orbit->meetings->generateToken($meeting['id'], [
    'app_user_id' => $guestId,
    'role'        => 'participant',
], $hostId);

$orbit->meetings->end($meeting['id'], $hostId);
```

### Media

```php
// Get a pre-signed upload URL
$session = $orbit->media->generateUploadUrl('video/mp4', $userId);
// PUT your file to $session['upload_path']

// Get a stream URL once processing is done
$stream = $orbit->media->getStreamUrl($mediaId, $userId);

// Grant another user download access
$orbit->media->grantAccess($mediaId, [
    'app_user_id' => $otherUserId,
    'access_type' => 'download',
], $userId);
```

### Webhooks

```php
$wh = $orbit->webhooks->create('https://yourapp.com/webhooks/orbit');

$orbit->webhooks->subscribe($wh['id'], 'message.sent');
$orbit->webhooks->subscribe($wh['id'], 'call.ended');

// Publish a custom event
$orbit->webhooks->publish('user.upgraded', ['user_id' => $userId, 'plan' => 'pro']);

// Rotate the signing secret
$orbit->webhooks->rotateSecret($wh['id']);
```

### Billing

```php
$wallet = $orbit->billing->getWallet();
echo $wallet['balance'];

$orbit->billing->topUp(50.00);

$invoice = $orbit->billing->generateInvoice(
    new DateTime('2026-04-01'),
    new DateTime('2026-04-30'),
);
```

---

## Error handling

All API errors throw `OrbitConnect\Server\OrbitServerException`:

```php
use OrbitConnect\Server\OrbitServerException;

try {
    $meeting = $orbit->meetings->get('mtg_unknown', $userId);
} catch (OrbitServerException $e) {
    echo $e->status;     // HTTP status code, e.g. 404
    echo $e->errorCode;  // API error code string, e.g. "resource_not_found"
    echo $e->getMessage();
}
```

---

## Requirements

- PHP 8.1+
- `ext-curl`
- `ext-json`

---

## License

MIT
