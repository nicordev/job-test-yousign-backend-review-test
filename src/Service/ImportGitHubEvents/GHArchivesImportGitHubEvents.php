<?php

declare(strict_types=1);

namespace App\Service\ImportGitHubEvents;

use LogicException;

final class GHArchivesImportGitHubEvents implements ImportGitHubEvents
{
    private string $query;

    public function setQuery(string $query): void
    {
        if (!\preg_match(pattern: '#\d{4}-\d{2}-\d{2}-(\d{1,2}|\{\d{1,2}\.\.\d{1,2}\})#', subject: $query)) {
            throw new LogicException('Query must follow format YYYY-MM-DD-h.');
        }
        $this->query = $query;
    }

    public function execute(): void
    {
    }
}
