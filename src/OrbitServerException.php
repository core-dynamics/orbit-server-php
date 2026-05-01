<?php

declare(strict_types=1);

namespace OrbitConnect\Server;

use RuntimeException;

class OrbitServerException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status,
        public readonly ?string $errorCode = null,
    ) {
        parent::__construct($message);
    }
}
