<?php

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use Predis\Client as PredisClient;

use Rocket\{Response, Validator, Api};

require __DIR__ . '/../vendor/autoload.php';
$config = include_once __DIR__ . '/../config/parameters.php';

Sentry\init(['dsn' => $config['sentry_key']]);

header("Content-Type: application/json");

$response = new Response();

try {
    $validator = new Validator($_GET);

    $validator->check();

    $api = new Api(new GuzzleClient(), new PredisClient());
    $api->fetchData();

    echo $response->data($api->getData(), $validator);
} catch (Exception $exception) {
    echo $response->error($exception->getMessage());
} catch (GuzzleException $exception) {
    echo $response->error();
}
