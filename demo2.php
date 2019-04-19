<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'common.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Promise\Promise;

$client = new Client(
    [
    // Base URI is used with relative requests
    'base_uri' => 'http://localhost:8086',
    // You can set any number of default request options.
    'timeout'  => 2.0,
    'headers' => ['MyToken'=> 'Bearer mytoken'],
    ]
);

########################################################################
section('Async + Promise');
########################################################################
$promise = $client->getAsync('/get');

$promise->then(
    function ($response) {
        echo Psr7\str($response);
    },
    function ($e) {
        echo Psr7\str($e->getResponse());
    }
);

echo PHP_EOL . "...... Call not send .... " . PHP_EOL . PHP_EOL;

$promise->wait();



########################################################################
section('Error with Async + Promise');
########################################################################
$promise = $client->getAsync('/status/500', 
        ['http_errors' => false]
    );

$promise->then(
    function ($response) {
        echo Psr7\str($response);
    },
    function ($e) {
        echo Psr7\str($e->getResponse());
    }
);

$promise->wait();



########################################################################
section('Async + Chain Promise');
########################################################################
$statusCode = 0;

$promise = $client->getAsync('/get');
$promise->then(
    function ($response) {
        return $response->getStatusCode();
    }
)->then(
    function (int $val) {
        echo "The status code is ";
        sleep(2);
        return $val;
    }
)->then(
    function (int $val) {
        echo $val;
        return $val;
    }
)->then(
    function (int $val) use (&$statusCode) {
        echo PHP_EOL;
        $statusCode = $val;
    }
);

$promise->wait();

echo PHP_EOL . "Final result : " . $statusCode . PHP_EOL;
