<?php

declare(strict_types=1);

namespace App\Service\ImportGitHubEvents\Transformer;

use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\Repo;

final class GHArchivesEventsTransformer
{
    public function transformToEvents(array $archivesEvents): array
    {
        $events = [];

        foreach ($archivesEvents as $event) {
            $type = match ($event['type']) {
                'PullRequestEvent' => EventType::PULL_REQUEST,
                'PullRequestReviewCommentEvent' => EventType::COMMENT,
                'CommitCommentEvent' => EventType::COMMENT,
                'IssueCommentEvent' => EventType::COMMENT,
                'PushEvent' => EventType::COMMIT,
                default => null,
            };

            if (null === $type) {
                continue;
            }

            $events[] = new Event(
                id: (int) $event['id'],
                type: $type,
                actor: new Actor(
                    id: $event['actor']['id'],
                    login: $event['actor']['login'],
                    url: $event['actor']['url'],
                    avatarUrl: $event['actor']['avatar_url'],
                ),
                repo: new Repo(
                    id: $event['repo']['id'],
                    name: $event['repo']['name'],
                    url: $event['repo']['url'],
                ),
                payload: $event['payload'],
                createAt: new \DateTimeImmutable($event['created_at']),
                comment: null,
            );
        }

        return $events;
    }
}
