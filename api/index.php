<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MSISDNService\MSISDN;

require '../vendor/autoload.php';

$app = new Slim\App();
$container = $app->getContainer();

// Set error handling
$errorHandler = function ($c) {
  return function ($request, $response, $error) use ($c) {
    $r = new stdClass();
    $r->Success = false;
    $r->Message = $error->getMessage();

    return $response->withStatus(500)
      ->withHeader('Content-Type', 'application/json')
      ->write(json_encode($r));
  };
};
$container['errorHandler'] = $errorHandler;
$container['phpErrorHandler'] = $errorHandler;

$app->get('/v1/msisdn/{number}', function (Request $request, Response $response, array $args) {
  $number = $args['number'];

  $parser = MSISDN::getInstance();
  $r = $parser->parse($number);

  return $response->withJson($r);
});

$app->run();
