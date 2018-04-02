<?php
/**
 * Application Routes
 */

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use MSISDNService\MSISDNService;

$app->map(['GET', 'POST'], '/v2/{controller}[/{action}/[{param}]]', function (Request $request, Response $response, $controller, $action = 'index', $params = '') use ($app) {
  // resolve controller
  $ctrlArr = explode('-', $controller);
  $ctrlArr = array_map('ucfirst', $ctrlArr);
  $controller = implode('', $ctrlArr);
  $controller =  'Controllers\\' . $controller . 'Controller';

  // get params
  $args = array();
  if (strlen($params) > 0) {
    $params = explode('?', $params);
    $params = $params[0];
    $p = urldecode($params);
    $args = explode('|', $p);
  }

  // invoke via reflection
  $controllerInstance = new $controller($this);
  $reflectionMethod = new ReflectionMethod($controller, $action);
  $r = $reflectionMethod->invokeArgs($controllerInstance, $args);

  return $response->withJson($r);
});

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