<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MSISDNService\MnoRepository;
use MSISDNService\MSISDNService;

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

// Load mobile number operators repository
MnoRepository::getInstance()->loadFile('../data/operators.json');

$app->get('/v1/msisdn/{number}', function (Request $request, Response $response, array $args) {
  $number = $args['number'];

  $service = new MSISDNService();
  $r = $service->parse($number);

  return $response->withJson($r);
});

$app->run();
