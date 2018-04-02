<?php

use MSISDNService\MnoRepository;

require_once __DIR__ . '/../../vendor/autoload.php';

/*
 * Create the application
*/
$config = require_once __DIR__ . '/../app/config/config.php';
$app = new Slim\App($config);
$container = $app->getContainer();

// add strategy so the routes placeholder values are passed as separate arguments
$container['foundHandler'] = function() {
  return new \Slim\Handlers\Strategies\RequestResponseArgs();
};

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

// register service
$container['MsisdnService'] = function($c) {
  return new MSISDNService\MSISDNService();
};

/**
 * Load data here for now so it is available through the request
 */
MnoRepository::getInstance()->loadFile('../../data/operators.json');

return $app;