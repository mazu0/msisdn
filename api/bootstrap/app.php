<?php

use MSISDNService\MnoRepository;

require_once __DIR__ . '/../../vendor/autoload.php';

/*
 * Create the application
*/
$config = require_once __DIR__ . '/../app/config/config.php';
$app = new Slim\App($config);
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

/**
 * Load data here for now so it is available through the request
 */
MnoRepository::getInstance()->loadFile('../../data/operators.json');

return $app;