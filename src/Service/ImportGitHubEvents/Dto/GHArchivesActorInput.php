<?php

namespace App\Service\ImportGitHubEvents\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class GHArchivesActorInput
{
    /**
     * @Assert\Positive
     */
    public int $id;

    /**
     * @Assert\NotBlank
     */
    public string $login;

    /**
     * @Assert\NotBlank
     * @Assert\Url
     */
    public string $url;

    /**
     * @Assert\NotBlank
     * @Assert\Url
     */
    public string $avatar_url;
}
