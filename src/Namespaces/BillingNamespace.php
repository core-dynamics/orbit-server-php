<?php

declare(strict_types=1);

namespace OrbitConnect\Server\Namespaces;

use OrbitConnect\Server\HttpClient;

class BillingNamespace
{
    public function __construct(private readonly HttpClient $http) {}

    /** Get the wallet balance for the application */
    public function getWallet(): array
    {
        return $this->http->get('/wallet');
    }

    /** Top up the wallet */
    public function topUp(float $amount): array
    {
        return $this->http->post('/wallet/top-up', ['amount' => $amount]);
    }

    /** List all transactions */
    public function getTransactions(): array
    {
        return $this->http->get('/transactions');
    }

    /** Get usage summary for the current period */
    public function getUsage(): array
    {
        return $this->http->get('/usage');
    }

    /** List all invoices */
    public function getInvoices(): array
    {
        return $this->http->get('/invoices');
    }

    /** Get a single invoice by ID */
    public function getInvoice(string $id): array
    {
        return $this->http->get("/invoices/{$id}");
    }

    /** Generate an invoice for a date range */
    public function generateInvoice(?\DateTimeInterface $from = null, ?\DateTimeInterface $to = null): array
    {
        $body = [];
        if ($from !== null) $body['from'] = $from->format(\DateTimeInterface::ATOM);
        if ($to !== null)   $body['to']   = $to->format(\DateTimeInterface::ATOM);
        return $this->http->post('/invoices/generate', $body);
    }
}
