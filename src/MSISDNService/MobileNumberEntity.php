<?php namespace MSISDNService;

class MobileNumberEntity
{
  public $CountryISO;
  public $CountryPrefix;
  public $MobileNetworkCode;
  public $ProviderName;
  public $SubscriberNumber;

  public static function fromArray(array $arr): self
  {
    $entity = new MobileNumberEntity();
    foreach ($arr as $key => $val)
    {
      if (property_exists($entity, $key))
        $entity->{$key} = $val;
    }

    return $entity;
  }
}