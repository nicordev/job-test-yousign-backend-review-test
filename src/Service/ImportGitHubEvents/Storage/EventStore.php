<?php

namespace App\Service\ImportGitHubEvents\Storage;

use App\Entity\Event;

interface EventStore
{
    /**
     * @param array<Event> $events
     */
    public function storeEvents(array $events): void;
}
