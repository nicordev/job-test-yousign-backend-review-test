<?php

namespace App\Service\ImportGitHubEvents\Storage;

use App\Entity\Event;

interface EventStore
{
    /**
     * @param array<Events> $events
     */
    public function storeEvents(array $events): void;
}
