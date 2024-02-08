<?php

namespace App\Service\ImportGitHubEvents\Storage;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;

class OrmEventStore implements EventStore
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @param array<Event> $events
     */
    public function storeEvents(array $events): void
    {
        foreach ($events as $event) {
            $this->entityManager->persist($event);
        }

        $this->entityManager->flush();
    }
}
