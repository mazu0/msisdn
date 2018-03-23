<?php namespace MSISDNService;

use InvalidArgumentException;

class MSISDNService
{
  /**
   * Shortest posible key (International calling code + Mobile network code)
   * - International calling code length: 1
   * - Mobile network code length: 1
   *
   * @var int
   */
  private static $minKeyLength = 2;

  /**
   * Longest posible key (International calling code + Mobile network code)
   *
   * @var int
   */
  private static $maxKeyLength = 7;

  /**
   * Data provider
   *
   * @var MnoRepository
   */
  protected $mnoRepo;

  final public function __construct()
  {
    $this->mnoRepo = MnoRepository::getInstance();
  }

  /**
   * Removes unnecessary characters which are still valid (plus prefix and empty spaces)
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
   * @return MobileNumber
   *
   * @throws InvalidArgumentException if the msisdn input is invalid
   */
  public function parse(string $msisdn = null): MobileNumber
  {
    if ($msisdn === null)
      throw new InvalidArgumentException('MSISDN number is missing');

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

    $mobileNumber = null;
    if ($mnoData !== null) {
      // set mobile number operator data
      $mobileNumber = MobileNumber::fromArray($mnoData);
      // resolve subscriber number
      $mobileNumber->SubscriberNumber = substr($msisdn, strlen($opKey));
    }
    else
      throw new InvalidArgumentException('MSISDN number is invalid: ' . $msisdn);

    return $mobileNumber;
  }
}