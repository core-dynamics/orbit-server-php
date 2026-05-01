<?php

declare(strict_types=1);

namespace OrbitConnect\Server;

class HttpClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $secretKey,
        private readonly ?string $signingSecret = null,
    ) {}

    // ── Headers ───────────────────────────────────────────────────────────────

    private function buildHeaders(?string $appUserId, mixed $body): array
    {
        $headers = [
            'Content-Type: application/json',
            'x-api-key: ' . $this->secretKey,
        ];

        if ($appUserId !== null) {
            $headers[] = 'x-app-user-id: ' . $appUserId;
        }

        if ($this->signingSecret !== null) {
            $timestamp = (int) (microtime(true) * 1000);
            $bodyStr   = $body !== null ? json_encode($body, JSON_THROW_ON_ERROR) : '{}';
            $signature = hash_hmac('sha256', $timestamp . '.' . $bodyStr, $this->signingSecret);

            $headers[] = 'x-request-timestamp: ' . $timestamp;
            $headers[] = 'x-request-signature: ' . $signature;
        }

        return $headers;
    }

    // ── Core request ──────────────────────────────────────────────────────────

    public function request(string $method, string $path, mixed $body = null, ?string $appUserId = null): mixed
    {
        $url  = $this->baseUrl . $path;
        $json = $body !== null ? json_encode($body, JSON_THROW_ON_ERROR) : null;

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $this->buildHeaders($appUserId, $body),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        match ($method) {
            'GET'    => null, // default
            'POST'   => curl_setopt_array($ch, [
                            CURLOPT_POST       => true,
                            CURLOPT_POSTFIELDS => $json ?? '{}',
                        ]),
            'PATCH'  => curl_setopt_array($ch, [
                            CURLOPT_CUSTOMREQUEST => 'PATCH',
                            CURLOPT_POSTFIELDS    => $json ?? '{}',
                        ]),
            'DELETE' => curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE'),
            default  => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };

        $raw    = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error  = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            throw new OrbitServerException("cURL error: {$error}", 0);
        }

        $decoded = json_decode((string) $raw, associative: true, flags: JSON_THROW_ON_ERROR);

        if ($status < 200 || $status >= 300) {
            throw new OrbitServerException(
                $decoded['message'] ?? "HTTP {$status}",
                $status,
                $decoded['code'] ?? null, // stored as $errorCode
            );
        }

        return $decoded['data'] ?? null;
    }

    // ── Convenience wrappers ──────────────────────────────────────────────────

    public function get(string $path, ?string $appUserId = null): mixed
    {
        return $this->request('GET', $path, null, $appUserId);
    }

    public function post(string $path, mixed $body = null, ?string $appUserId = null): mixed
    {
        return $this->request('POST', $path, $body, $appUserId);
    }

    public function patch(string $path, mixed $body = null, ?string $appUserId = null): mixed
    {
        return $this->request('PATCH', $path, $body, $appUserId);
    }

    public function delete(string $path, ?string $appUserId = null): mixed
    {
        return $this->request('DELETE', $path, null, $appUserId);
    }
}
