<?php

declare(strict_types=1);

namespace App\Tests\InMemory;

use Symfony\Contracts\HttpClient\ResponseInterface;

final class FakeResponse implements ResponseInterface
{
    public function __construct(
        private string $content,
        private int $statusCode,
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(bool $throw = true): array
    {
        return [];
    }

    public function getContent(bool $throw = true): string
    {
        return $this->content;
    }

    public function toArray(bool $throw = true): array
    {
        return [];
    }

    public function cancel(): void
    {
    }

    public function getInfo(string $type = null): mixed
    {
        return [];
    }
}
