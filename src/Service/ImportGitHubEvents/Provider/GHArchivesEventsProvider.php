<?php

declare(strict_types=1);

namespace App\Service\ImportGitHubEvents\Provider;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GHArchivesEventsProvider
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    /**
     * @return array{}
     */
    public function fetch(string $query): array
    {
        $response = $this->client->request('GET', "https://data.gharchive.org/$query.json.gz");

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("Error from gharchive: {$response->getStatusCode()}");
        }

        $file = gzdecode(data: $response->getContent());

        $lines = array_filter(
            explode(separator: "\n", string: $file),
            static fn (string $line) => strpos(haystack: $line, needle: '{') === 0,
        );

        return array_map(
            static fn (string $line) => json_decode(json: $line, associative: true, flags: JSON_THROW_ON_ERROR),
            $lines,
        );
    }
}
