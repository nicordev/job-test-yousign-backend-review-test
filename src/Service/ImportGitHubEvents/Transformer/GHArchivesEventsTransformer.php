<?php

declare(strict_types=1);

namespace App\Service\ImportGitHubEvents\Transformer;

use App\Entity\Repo;
use App\Entity\Actor;
use App\Entity\Event;
use App\Entity\EventType;
use App\Service\ImportGitHubEvents\Dto\GHArchivesRepoInput;
use App\Service\ImportGitHubEvents\Dto\GHArchivesActorInput;
use App\Service\ImportGitHubEvents\Dto\GHArchivesEventInput;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GHArchivesEventsTransformer
{
    private array $repos = [];
    private array $actors = [];

    public function __construct(private ValidatorInterface $validator)
    {
    }

    /**
     * @param array<GHArchivesEventInput> $archivesEvents
     */
    public function transformToEvents(array $archivesEvents): array
    {
        $events = [];

        foreach ($archivesEvents as $event) {
            $type = $this->getEventType($event);

            if (null === $type || !$this->isEventValid($event)) {
                continue;
            }

            $events[] = new Event(
                id: (int) $event->id,
                type: $type,
                actor: $this->getActor($event->actor),
                repo: $this->getRepo($event->repo),
                payload: $event->payload,
                createAt: new \DateTimeImmutable($event->createdAt),
                comment: null,
            );
        }

        return $events;
    }

    private function isEventValid(GHArchivesEventInput $event): bool
    {
        $eventErrors = $this->validator->validate($event);

        if ($eventErrors->count() > 0) {
            return false;
        }

        $actorErrors = $this->validator->validate($event->actor);

        if ($actorErrors->count() > 0) {
            return false;
        }

        $repoErrors = $this->validator->validate($event->repo);

        if ($repoErrors->count() > 0) {
            return false;
        }

        return true;
    }

    private function getEventType(GHArchivesEventInput $event): ?string
    {
        return match ($event->type) {
            'PullRequestEvent' => EventType::PULL_REQUEST,
            'PullRequestReviewCommentEvent' => EventType::COMMENT,
            'CommitCommentEvent' => EventType::COMMENT,
            'IssueCommentEvent' => EventType::COMMENT,
            'PushEvent' => EventType::COMMIT,
            default => null,
        };
    }

    private function getRepo(GHArchivesRepoInput $repo): Repo
    {
        $repoId = $repo->id;

        if (\array_key_exists($repoId, $this->repos)) {
            return $this->repos[$repoId];
        }

        $repo = new Repo(
            id: $repoId,
            name: $repo->name,
            url: $repo->url,
        );
        $this->repos[$repoId] = $repo;

        return $repo;
    }

    private function getActor(GHArchivesActorInput $actor): Actor
    {
        $actorId = $actor->id;

        if (\array_key_exists($actorId, $this->actors)) {
            return $this->actors[$actorId];
        }

        $actor = new Actor(
            id: $actorId,
            login: $actor->login,
            url: $actor->url,
            avatarUrl: $actor->avatarUrl,
        );
        $this->actors[$actorId] = $actor;

        return $actor;
    }
}
