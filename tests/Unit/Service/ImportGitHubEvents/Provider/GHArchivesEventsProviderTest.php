<?php

namespace App\Tests\Unit\Service\ImportGitHubEvents\Provider;

use App\Service\ImportGitHubEvents\Provider\GHArchivesEventsProvider;
use App\Tests\InMemory\FakeResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GHArchivesEventsProviderTest extends TestCase
{
    public function testCanImportGHArchivesEvents(): void
    {
        $client = $this->createMock(HttpClientInterface::class);
        $client->expects(self::once())
            ->method('request')
            ->with('GET', 'https://data.gharchive.org/2015-01-01-15.json.gz')
            ->willReturn(
                new FakeResponse(
                    content: \file_get_contents(__DIR__.'/../../../../InMemory/data/2015-01-01-15.json.gz'),
                    statusCode: 200,
                )
            )
        ;
        $provider = new GHArchivesEventsProvider(
            $client
        );
        $events = $provider->fetch('2015-01-01-15');

        self::assertNotEmpty($events);
        self::assertIsArray($events[0]);
        self::assertArrayHasKey('id', $events[0]);
        self::assertSame('2489651045', $events[0]['id']);
    }
}
