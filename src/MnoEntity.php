<?php

class MnoEntity
{
  public $MobileNetworkCode;
  public $CountryISO;
  public $CountryPrefix;
  public $ProviderName;

  public static function fromArray($arr)
  {
    $entity = new MnoEntity();
    foreach ($arr as $key => $val)
    {
      if (property_exists($entity, $key))
      {
        $entity->{$key} = $val;
      }
    }

    return $entity;
  }
}