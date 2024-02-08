<?php

namespace App\Service\ImportGitHubEvents\Storage;

use Doctrine\ORM\EntityManager;

class OrmEventStore implements EventStore
{
    public function __construct(private EntityManager $entityManager)
    {
    }

    public function storeEvents(array $events): void
    {
        foreach ($events as $event) {
            $this->entityManager->persist($event);
        }

        $this->entityManager->flush();
    }
}
