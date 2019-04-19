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
    'timeout'  => 10.0
    ]
);


########################################################################
section('Concurrent');
########################################################################

$promises = [
    'image' => $client->getAsync('/image'),
    'png'   => $client->getAsync('/image/png'),
    'jpeg'  => $client->getAsync('/image/jpeg'),
    'webp'  => $client->getAsync('/image/webp'),
];

// Wait on all of the requests to complete. 
// Throws a ConnectException, if any of the requests fail
GuzzleHttp\Promise\unwrap($promises);

// Wait for the requests to complete, even if some of them fail
$results = \GuzzleHttp\Promise\settle($promises)->wait();

// You can access each result using the key provided 
// to the unwrap function.
echo '/image : '      . $results['image']['value']->getStatusCode() . PHP_EOL;
echo '/image/png : '  . $results['png']  ['value']->getStatusCode() . PHP_EOL;
echo '/image/jpeg : ' . $results['jpeg'] ['value']->getStatusCode() . PHP_EOL;
echo '/image/webp : ' . $results['webp'] ['value']->getStatusCode() . PHP_EOL;


########################################################################
section('Concurrent with error and unwrap');
########################################################################

$promises = [
    'image' => $client->getAsync('/image'),
    'png'   => $client->getAsync('/image/png'),
    'jpeg'  => $client->getAsync('/image/jpeg'),
    'webp'  => $client->getAsync('/image/webp'),
    'error' => $client->getAsync('/status/500'),
];

try {
    // Wait on all of the requests to complete. 
    // Throws a ConnectException, if any of the requests fail
    GuzzleHttp\Promise\unwrap($promises);
}catch (GuzzleHttp\Exception\RequestException $e) {
    if ($e->hasResponse()) {
        echo Psr7\str($e->getResponse());
    }
}
// Wait for the requests to complete, 
// even if some of them fail
$results = \GuzzleHttp\Promise\settle($promises)->wait();



########################################################################
section('Concurrent with errors without unwrap');
########################################################################

$promises = [
    'image' => $client->getAsync('/image'),
    'png'   => $client->getAsync('/image/png'),
    'jpeg'  => $client->getAsync('/image/jpeg'),
    'webp'  => $client->getAsync('/image/webp'),
    'error' => $client->getAsync('/status/500'),
];

// Wait on all of the requests to complete. 
// Throws a ConnectException, if any of the requests fail
// GuzzleHttp\Promise\unwrap($promises);

// Wait for the requests to complete, even if some of them fail
$results = \GuzzleHttp\Promise\settle($promises)->wait();

// You can access each result using the key provided to 
// the unwrap function.
echo '/image : '      . $results['image']['value']->getStatusCode() . PHP_EOL;
echo '/image/png : '  . $results['png']  ['value']->getStatusCode() . PHP_EOL;
echo '/image/jpeg : ' . $results['jpeg'] ['value']->getStatusCode() . PHP_EOL;
echo '/image/webp : ' . $results['webp'] ['value']->getStatusCode() . PHP_EOL;
echo '/status/500 : ' . $results['error']['state'] . PHP_EOL;



########################################################################
section('Concurrent + chain Promises');
########################################################################

$promises = [
    'delay8' => $client->getAsync('/delay/8')->then(
        function () {
            echo 'Finish to get /delay/8' . PHP_EOL;
        }
    ),
    'delay6' => $client->getAsync('/delay/6')->then(function () { echo 'Finish to get /delay/6' . PHP_EOL; }),
    'delay4' => $client->getAsync('/delay/4')->then(function () { echo 'Finish to get /delay/4' . PHP_EOL; }),
    'delay2' => $client->getAsync('/delay/2')->then(function () { echo 'Finish to get /delay/2' . PHP_EOL; }),
    'delay9' => $client->getAsync('/delay/9')->then(function () { echo 'Finish to get /delay/9' . PHP_EOL; }),
    'delay7' => $client->getAsync('/delay/7')->then(function () { echo 'Finish to get /delay/7' . PHP_EOL; }),
    'delay5' => $client->getAsync('/delay/5')->then(function () { echo 'Finish to get /delay/5' . PHP_EOL; }),
    'delay3' => $client->getAsync('/delay/3')->then(function () { echo 'Finish to get /delay/3' . PHP_EOL; }),
    'delay1' => $client->getAsync('/delay/1')->then(function () { echo 'Finish to get /delay/1' . PHP_EOL; }),
];

// Wait on all of the requests to complete. Throws a ConnectException
// if any of the requests fail
$results = \GuzzleHttp\Promise\unwrap($promises);

// Wait for the requests to complete, even if some of them fail
$results = \GuzzleHttp\Promise\settle($promises)->wait();
