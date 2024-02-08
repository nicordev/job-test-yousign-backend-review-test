<?php

namespace App\Tests\Unit\Service\ImportGitHubEvents;

use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\Repo;
use App\Service\ImportGitHubEvents\GHArchivesImportGitHubEvents;
use App\Service\ImportGitHubEvents\Provider\GHArchivesEventsProvider;
use App\Service\ImportGitHubEvents\Storage\EventStore;
use App\Service\ImportGitHubEvents\Transformer\GHArchivesEventsTransformer;
use PHPUnit\Framework\TestCase;

class GHArchivesImportGitHubEventsTest extends TestCase
{
    public function testCanSetQuery(): void
    {
        $fakeProvider = $this->createMock(GHArchivesEventsProvider::class);
        $fakeProvider->expects(self::never())
            ->method('fetch')
        ;
        $fakeTransformer = $this->createMock(GHArchivesEventsTransformer::class);
        $fakeTransformer->expects(self::never())
            ->method('transformToEvents')
        ;
        $fakeRepository = $this->createMock(EventStore::class);
        $fakeRepository->expects(self::never())
            ->method('storeEvents')
        ;
        $importGitHubEvents = new GHArchivesImportGitHubEvents(
            $fakeProvider,
            $fakeTransformer,
            $fakeRepository,
        );
        $importGitHubEvents->setQuery('2015-01-01-15');
    }

    public function testCannotSetQueryWithWrongFormat(): void
    {
        $fakeProvider = $this->createMock(GHArchivesEventsProvider::class);
        $fakeProvider->expects(self::never())
            ->method('fetch')
        ;
        $fakeTransformer = $this->createMock(GHArchivesEventsTransformer::class);
        $fakeTransformer->expects(self::never())
            ->method('transformToEvents')
        ;
        $fakeRepository = $this->createMock(EventStore::class);
        $fakeRepository->expects(self::never())
            ->method('storeEvents')
        ;
        $importGitHubEvents = new GHArchivesImportGitHubEvents(
            $fakeProvider,
            $fakeTransformer,
            $fakeRepository,
        );
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Query must follow format YYYY-MM-DD-h.');
        $importGitHubEvents->setQuery('2015-01-01');
    }

    public function testCanImportGitHubEvents(): void
    {
        $fakeProvider = $this->createMock(GHArchivesEventsProvider::class);
        $fakeProvider->expects(self::once())
            ->method('fetch')
            ->with('2015-01-01-15')
            ->willReturn($this->getArchivesEvents())
        ;
        $fakeTransformer = $this->createMock(GHArchivesEventsTransformer::class);
        $fakeTransformer->expects(self::once())
            ->method('transformToEvents')
            ->with($this->getArchivesEvents())
            ->willReturn($this->getEvents())
        ;
        $fakeRepository = $this->createMock(EventStore::class);
        $fakeRepository->expects(self::once())
            ->method('storeEvents')
            ->with($this->getEvents())
        ;
        $importGitHubEvents = new GHArchivesImportGitHubEvents(
            $fakeProvider,
            $fakeTransformer,
            $fakeRepository,
        );
        $importGitHubEvents->setQuery('2015-01-01-15');
        $importGitHubEvents->execute();
    }

    private function getEvents(): array
    {
        return [
            new Event(
                id: 2489368070,
                type: EventType::COMMIT,
                actor: new Actor(
                    id: 9152315,
                    login: 'davidjhulse',
                    url: 'https://api.github.com/users/davidjhulse',
                    avatarUrl: 'https://avatars.githubusercontent.com/u/9152315?',
                ),
                repo: new Repo(
                    id: 28635890,
                    name: 'davidjhulse/davesbingrewardsbot',
                    url: 'https://api.github.com/repos/davidjhulse/davesbingrewardsbot',
                ),
                payload: ['push_id' => 536740396],
                createAt: new \DateTimeImmutable('2015-01-01T00:00:00Z'),
                comment: null,
            ),
        ];
    }

    private function getArchivesEvents(): array
    {
        return [
            [
                'id' => '2489368070',
                'type' => 'PushEvent',
                'actor' => [
                    'id' => 9152315,
                    'login' => 'davidjhulse',
                    'gravatar_id' => '',
                    'url' => 'https://api.github.com/users/davidjhulse',
                    'avatar_url' => 'https://avatars.githubusercontent.com/u/9152315?',
                ],
                'repo' => [
                    'id' => 28635890,
                    'name' => 'davidjhulse/davesbingrewardsbot',
                    'url' => 'https://api.github.com/repos/davidjhulse/davesbingrewardsbot',
                ],
                'payload' => [
                    'push_id' => 536740396,
                ],
                'public' => true,
                'created_at' => '2015-01-01T00:00:00Z',
            ],
        ];
    }
}
