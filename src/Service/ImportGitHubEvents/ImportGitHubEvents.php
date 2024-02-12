<?php

declare(strict_types=1);

namespace App\Service\ImportGitHubEvents;

interface ImportGitHubEvents
{
    public function setQuery(string $query): void;

    public function execute(): void;
}
