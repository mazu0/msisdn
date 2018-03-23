<?php namespace MSISDNService;

class MobileNumber
{
  /**
   * Two-letter country code defined by ISO 3166-1 alpha-2.
   * Examples:
   * - JP: Japan
   * - MQ: Martinique
   * - UK:	United Kingdom
   *
   * @var string
   */
  public $CountryISO;

  /**
   * International calling code
   *
   * @var
   */
  public $CountryPrefix;

  /**
   * Mobile service provider network code.
   *
   * @var string
   */
  public $MobileNetworkCode;

  /**
   * Mobile service provider name.
   *
   * @var string
   */
  public $ProviderName;

  /**
   * Mobile subscriber number.
   *
   * @var string
   */
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