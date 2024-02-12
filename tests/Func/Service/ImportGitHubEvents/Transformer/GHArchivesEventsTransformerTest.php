<?php

namespace App\Tests\Func\Service\ImportGitHubEvents\Transformer;

use App\Entity\Event;
use App\Service\ImportGitHubEvents\Dto\GHArchivesActorInput;
use App\Service\ImportGitHubEvents\Dto\GHArchivesEventInput;
use App\Service\ImportGitHubEvents\Dto\GHArchivesRepoInput;
use App\Service\ImportGitHubEvents\Transformer\GHArchivesEventsTransformer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GHArchivesEventsTransformerTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    public function setUp(): void
    {
        $this->validator = $this->getContainer()->get('validator');
    }

    public function testCanTransformValidGHArchivesEvents(): void
    {
        $transformer = new GHArchivesEventsTransformer($this->validator);
        $eventInput = $this->getValidGHArchivesEventInput();
        $results = $transformer->transformToEvents([
            $eventInput
        ]);

        self::assertNotEmpty($results);
        self::assertInstanceOf(Event::class, $results[0]);
        self::assertSame(789, $results[0]->id());
    }

    public function testCannotTransformInvalidGHArchivesEvents(): void
    {
        $transformer = new GHArchivesEventsTransformer($this->validator);
        $eventInput = $this->getInvalidGHArchivesEventInput();
        $results = $transformer->transformToEvents([
            $eventInput
        ]);

        self::assertEmpty($results);
    }

    private function getValidGHArchivesEventInput(): GHArchivesEventInput
    {
        $actorInput = new GHArchivesActorInput();
        $actorInput->id = 123;
        $actorInput->login = 'petroav';
        $actorInput->url = 'https://api.github.com/users/petroav';
        $actorInput->avatar_url = "https://avatars.githubusercontent.com/u/665991?";

        $repoInput = new GHArchivesRepoInput();
        $repoInput->id = 456;
        $repoInput->name = 'petroav/6.828';
        $repoInput->url = 'https://api.github.com/repos/petroav/6.828';

        $eventInput = new GHArchivesEventInput();
        $eventInput->id = '789';
        $eventInput->created_at = '2015-01-01T15:00:00Z';
        $eventInput->type = 'PushEvent';
        $eventInput->payload = ["ref" => "master"];
        $eventInput->actor = $actorInput;
        $eventInput->repo = $repoInput;

        return $eventInput;
    }

    private function getInvalidGHArchivesEventInput(): GHArchivesEventInput
    {
        $actorInput = new GHArchivesActorInput();
        $actorInput->id = -1;
        $actorInput->login = '';
        $actorInput->url = 'ola';
        $actorInput->avatar_url = "zog";

        $repoInput = new GHArchivesRepoInput();
        $repoInput->id = 456;
        $repoInput->name = 'petroav/6.828';
        $repoInput->url = 'https://api.github.com/repos/petroav/6.828';

        $eventInput = new GHArchivesEventInput();
        $eventInput->id = '789';
        $eventInput->created_at = '2015-01-01T15:00:00Z';
        $eventInput->type = 'PushEvent';
        $eventInput->payload = ["ref" => "master"];
        $eventInput->actor = $actorInput;
        $eventInput->repo = $repoInput;

        return $eventInput;
    }
}
