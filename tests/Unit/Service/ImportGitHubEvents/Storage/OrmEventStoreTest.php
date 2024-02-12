<?php

namespace App\Tests\Unit\Service\ImportGitHubEvents\Storage;

use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\Repo;
use App\Service\ImportGitHubEvents\Storage\OrmEventStore;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class OrmEventStoreTest extends TestCase
{
    public function testCanStoreEvents(): void
    {
        $repo = new Repo(
            id: 1,
            name: 'dummy name',
            url: 'dummy-url.com',
        );
        $actor = new Actor(
            id: 12,
            login: 'dummy-login',
            url: 'dummy-url.com',
            avatarUrl: 'dummy-avatar-url.com',
        );
        $event = new Event(
            id: 123,
            type: EventType::COMMIT,
            actor: $actor,
            repo: $repo,
            payload: [],
            createAt: new \DateTimeImmutable('2024-01-01 12:13:05'),
            comment: null,
        );
        $fakeEntityManager = $this->createMock(EntityManager::class);
        $fakeEntityManager->expects(self::once())
            ->method('persist')
            ->with($event)
        ;
        $fakeEntityManager->expects(self::once())
            ->method('flush')
        ;
        $store = new OrmEventStore(
            $fakeEntityManager
        );

        $store->storeEvents([$event]);
    }
}
