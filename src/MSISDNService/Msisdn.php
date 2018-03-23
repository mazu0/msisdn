<?php namespace MSISDNService;

use PHPUnit\Framework\Exception;
use Singleton\Singleton;
use InvalidArgumentException;

class MSISDN extends Singleton
{
  private static $minKeyLength = 2;
  private static $maxKeyLength = 7;

  /**
   * @var MnoRepository
   */
  protected $mnoRepo;

  final protected function __construct()
  {
    $this->mnoRepo = MnoRepository::getInstance();
  }

  /**
   * Removes unnecessary characters (plus prefix and empty spaces)
   *
   * @param $msisdn
   * @return mixed|string
   */
  public function clean(string $msisdn): string
  {
    // remove + prefix
    $msisdn = ltrim($msisdn, '+');

    // remove whitespaces
    $msisdn = preg_replace('/\s+/', '', $msisdn);

    return $msisdn;
  }

  /**
   * Validates if input contains a range of digits
   * Shortest: 7 (ie. Niue 683XXXX)
   * Longest: 15 (maximum allowed by the standard ie. French Guiana 594694XXXXXXXXX)
   *
   * @param $msisdn
   * @return bool
   */
  public function validate(string $msisdn): bool
  {
    return preg_match('/^[1-9]{1}[0-9]{6,14}$/', $msisdn) ? true : false;
  }

  /**
   * MSISDN information (source http://www.msisdn.org/)
   *
   * Format definition defined:
   * In GSM standard 1800, this number is built up as
   * MSISDN = CC + NDC + SN
   * CC = Country Code
   * NDC = National Destination Code
   * SN = Subscriber Number
   *
   * In the GSM standard PCS 1900, format has next value
   * MSISDN = CC + NPA + SN
   * CC = Country Code
   * NPA = Number Planning Area
   * SN = Subscriber Number
   *
   * @param $msisdn
   * @return MobileNumberEntity|null
   */
  public function parse(string $msisdn): MobileNumberEntity
  {
    // clean value
    $msisdn = $this->clean($msisdn);

    // validate syntax
    $valid = $this->validate($msisdn);

    if (!$valid)
      throw new InvalidArgumentException('MSISDN number is invalid: ' . $msisdn);

    $mnoData = null;
    // best match by finding longest identifier in the lookup
    $opKey = null;
    for ($i = static::$maxKeyLength; $i >= static::$minKeyLength; --$i)
    {
      $opKey = substr($msisdn, 0, $i);
      $mnoData = $this->mnoRepo->get($opKey);
      if ($mnoData !== null)
        break;
    }

    $mobileNumberData = null;
    if ($mnoData !== null) {
      // set mobile number operator data
      $mobileNumberData = MobileNumberEntity::fromArray($mnoData);
      // resolve subscriber number
      $mobileNumberData->SubscriberNumber = substr($msisdn, strlen($opKey));
    }
    else
      throw new InvalidArgumentException('MSISDN number is invalid: ' . $msisdn);

    return $mobileNumberData;
  }
}