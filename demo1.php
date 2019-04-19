<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'common.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;

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
section('Common HTTP GET call');
########################################################################
$response = $client->get('/get');
echo Psr7\str($response);



########################################################################
section('Override Request options');
########################################################################
$response = $client->get('/get', 
        ['headers' => [
            'MyToken'=> 'Bearer myNewTokenWithAdminRole',
            'User-Agent' => 'Jerome MARCHAND'
        ]]
    );
echo Psr7\str($response);



########################################################################
section('Follow redirections (With debug traces)');
########################################################################
$client->get('/redirect/1', 
        ['debug' => true]
    );



########################################################################
section('Do not follow redirections (With debug traces)');
########################################################################
$client->get('/redirect/1', 
        ['allow_redirects'=> false,'debug' => true]
    );



########################################################################
section('Throw Exception on errors');
########################################################################
try {
    // Default value
    $response = $client->get('/status/500');
} catch (GuzzleHttp\Exception\RequestException $e) {
    if ($e->hasResponse()) {
        echo Psr7\str($e->getResponse());
    }
}



########################################################################
section('But possible to disable throwing exceptions');
########################################################################
$response = $client->get('/status/500', 
        ['http_errors' => false]
    );
echo Psr7\str($response);



########################################################################
section('Response body is a steam');
########################################################################
$response = $client->get('/json');

$body = $response->getBody();
var_dump($body);



########################################################################
section('get Response body as string');
########################################################################
var_dump($body->getContents());



########################################################################
section('use json_decode on body Steam to get an Object/array');
########################################################################
var_dump(\json_decode($body));



########################################################################
section('Send JSON content');
########################################################################
$response = $client->post(
    '/post',
    [
        'json' => ['foo' => 'bar'],
    ]
);
echo Psr7\str($response);
