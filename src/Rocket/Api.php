<?php

declare(strict_types=1);

namespace Rocket;

use GuzzleHttp\Client as GuzzleClient;
use Predis\Client as PredisClient;

class Api
{
    const DATA_ENDPOINT = 'https://ll.thespacedevs.com/2.0.0/launch/upcoming?limit=5';

    /**
     * @var GuzzleClient
     */
    private GuzzleClient $guzzleClient;

    /**
     * @var PredisClient
     */
    private PredisClient $predisClient;

    /**
     * @var array
     */
    private array $data;

    /**
     * @param GuzzleClient $guzzleClient
     * @param PredisClient $predisClient
     */
    public function __construct(GuzzleClient $guzzleClient, PredisClient $predisClient)
    {
        $this->guzzleClient = $guzzleClient;
        $this->predisClient = $predisClient;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function fetchData(): void
    {
        $redisKey = 'lametric:rocket-launches';

        $launchesFile = $this->predisClient->get($redisKey);
        $ttl          = $this->predisClient->ttl($redisKey);

        if (!$launchesFile || $ttl < 0) {
            $this->data = $this->callApi();

            // save to redis
            $this->predisClient->set($redisKey, json_encode($this->data));
            $this->predisClient->expire($redisKey, 60 * 10);
        } else {
            $this->data = json_decode($launchesFile, true);
        }
    }

    /**
     * @return array
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function callApi(): array
    {
        $resource = $this->guzzleClient->request('GET', self::DATA_ENDPOINT);
        $data     = json_decode((string)$resource->getBody(), true);

        $launches = [];

        foreach ($data['results'] as $launch) {
            $launches[] = [
                'date' => $launch['net'],
                'name' => $launch['name'],
            ];
        }

        return $launches;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
