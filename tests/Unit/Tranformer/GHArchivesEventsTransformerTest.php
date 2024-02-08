<?php

namespace App\Tests\Unit\Transformer;

use App\Entity\Event;
use App\Service\ImportGitHubEvents\Transformer\GHArchivesEventsTransformer;
use PHPUnit\Framework\TestCase;

class GHArchivesEventsProviderTest extends TestCase
{
    public function testCanTransformGHArchivesEvents(): void
    {
        $transformer = new GHArchivesEventsTransformer();
        $results = $transformer->transformToEvents([
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
                    'size' => 1,
                    'distinct_size' => 1,
                    'ref' => 'refs/heads/master',
                    'head' => 'a9b22a6d80c1e0bb49c1cf75a3c075b642c28f81',
                    'before' => '86ffa724b4d70fce46e760f8cc080f5ec3d7d85f',
                    'commits' => [
                        [
                            'sha' => 'a9b22a6d80c1e0bb49c1cf75a3c075b642c28f81',
                            'author' => [
                                'email' => 'da8d7d1118ca5befd4d0d3e4f449c76ba6f1ee7e@live.com',
                                'name' => 'davidjhulse',
                            ],
                            'message' => 'Fixed issue with multiple account support',
                            'distinct' => true,
                            'url' => 'https://api.github.com/repos/davidjhulse/davesbingrewardsbot/commits/a9b22a6d80c1e0bb49c1cf75a3c075b642c28f81',
                        ],
                    ],
                ],
                'public' => true,
                'created_at' => '2015-01-01T00:00:00Z',
            ],
        ]);

        self::assertNotEmpty($results);
        self::assertInstanceOf(Event::class, $results[0]);
        self::assertSame(2489368070, $results[0]->id());
    }
}
