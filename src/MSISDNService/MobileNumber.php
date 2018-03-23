<?php namespace MSISDNService;

class MobileNumber
{
  public $CountryISO;
  public $CountryPrefix;
  public $MobileNetworkCode;
  public $ProviderName;
  public $SubscriberNumber;

  public static function fromArray(array $arr): self
  {
    $entity = new self();
    foreach ($arr as $key => $val)
    {
      if (property_exists($entity, $key))
        $entity->{$key} = $val;
    }

    return $entity;
  }
}