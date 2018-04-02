<?php namespace Controllers;

use InvalidArgumentException;

class MsisdnController
{
  private $service = null;

  public function __construct($container = null)
  {
    $this->service = $container->get('MsisdnService');
  }

  public function parse($msisdn = null)
  {
    if ($msisdn === null)
      throw new InvalidArgumentException('MSISDN number is missing');

    return $this->service->parse($msisdn);
  }
}