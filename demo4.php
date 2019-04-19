<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'common.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;

$client = new Client(
    [
    // Base URI is used with relative requests
    'base_uri' => 'http://localhost:8086',
    // You can set any number of default request options.
    'timeout'  => 10.0,
    ]
);

########################################################################
section('Pool');
########################################################################

$requests = function ($total) {    
    for ($i = 0; $i < $total; $i++) {
        $uri = 'delay/' . ($i % 10);
        echo "Create Request  $i  with uri $uri" . PHP_EOL;
        yield new Request('GET', $uri);
    }
};

$pool = new Pool($client, $requests(10), [
    'concurrency' => 5,
    'fulfilled' => function ($response, $index) {
        echo "Fulfilled Request $index for url " . json_decode($response->getBody())->url . PHP_EOL;
    },
    'rejected' => function ($reason, $index) {
        // this is delivered each failed request
    },
]);

// Initiate the transfers and create a promise
$promise = $pool->promise();

// Force the pool of requests to complete.
$promise->wait();
