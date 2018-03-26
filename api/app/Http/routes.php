<?php
/**
 * Application Routes
 */

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MSISDNService\MSISDNService;

// ToDo - write general handler for controller (when added)
/*
$app->addRoute(['GET', 'POST'], '/v2/{controller}[/{action}[/param]]', function (Request $request, Response $response, array $args) {
});*/

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