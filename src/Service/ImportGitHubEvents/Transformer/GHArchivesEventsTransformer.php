<?php

declare(strict_types=1);

namespace App\Service\ImportGitHubEvents\Transformer;

use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\EventType;
use App\Entity\Repo;

class GHArchivesEventsTransformer
{
    private array $repos = [];
    private array $actors = [];

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
                actor: $this->getActor($event['actor']),
                repo: $this->getRepo($event['repo']),
                payload: $event['payload'],
                createAt: new \DateTimeImmutable($event['created_at']),
                comment: null,
            );
        }

        return $events;
    }

    private function getRepo(array $repo): Repo
    {
        $repoId = $repo['id'];

        if (\array_key_exists($repoId, $this->repos)) {
            return $this->repos[$repoId];
        }

        $repo = new Repo(
            id: $repo['id'],
            name: $repo['name'],
            url: $repo['url'],
        );
        $this->repos[$repoId] = $repo;

        return $repo;
    }

    private function getActor(array $actor): Actor
    {
        $actorId = $actor['id'];

        if (\array_key_exists($actorId, $this->actors)) {
            return $this->actors[$actorId];
        }

        $actor = new Actor(
            id: $actor['id'],
            login: $actor['login'],
            url: $actor['url'],
            avatarUrl: $actor['avatar_url'],
        );
        $this->actors[$actorId] = $actor;

        return $actor;
    }
}
