<?php

declare(strict_types=1);

namespace Rocket;

use GuzzleHttp\Client as GuzzleClient;
use Predis\Client as PredisClient;

class Api
{
    const DATA_ENDPOINT = 'https://launchlibrary.net/1.4/launch/next/5';

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     * @var PredisClient
     */
    private $predisClient;

    /**
     * @var array
     */
    private $data;

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
    public function fetchData()
    {
        $redisKey = 'lametric:rocket-launches';

        $launchesFile = $this->predisClient->get($redisKey);
        $ttl          = $this->predisClient->ttl($redisKey);

        if (!$launchesFile || $ttl < 0) {
            $this->data = $this->callApi();

            // save to redis
            $this->predisClient->set($redisKey, json_encode($this->data));
            $this->predisClient->expireat($redisKey, strtotime("+10 minutes"));
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

        foreach ($data['launches'] as $launch) {
            $launches[] = [
                'date' => $launch['isostart'],
                'name' => $launch['rocket']['name'],
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
