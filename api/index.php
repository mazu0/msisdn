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

$app->map(['GET', 'POST'], '/v1/msisdn/[{msisdn}]', function (Request $request, Response $response, array $args) {
  $msisdn = null;
  // resolve parameter
  $reqMethod = $request->getMethod();
  if ($reqMethod === 'POST')
    $msisdn = $request->getParam('msisdn');
  else if ($reqMethod === 'GET')
    $msisdn = $args['msisdn'];

  $service = new MSISDNService();
  $r = $service->parse($msisdn);

  return $response->withJson($r);
});

$app->run();
