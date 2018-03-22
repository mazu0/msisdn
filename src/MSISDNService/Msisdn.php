<?php namespace MSISDNService;

use Singleton\Singleton;
use InvalidArgumentException;

class MSISDN extends Singleton
{
  private static $minKeyLength = 3;
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
  public function clean($msisdn)
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
   * @return int
   */
  public function validate($msisdn)
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
   * @return MnoEntity|null
   */
  public function parse($msisdn)
  {
    // clean value
    $msisdn = $this->clean($msisdn);

    // validate syntax
    $valid = $this->validate($msisdn);

    if (!$valid)
      throw new InvalidArgumentException('MSISDN number is invalid: ' . $msisdn);

    $operators = $this->mnoRepo->getAll();
    $mnoKeys = array_keys($operators);

    $mnoData = null;
    // match operator
    if (preg_match('/^(' . implode('|', $mnoKeys) . ').*$/', $msisdn, $match)) {
      $opData = $operators[$match[1]];
      $mnoData = MnoEntity::fromArray($opData);
    } else {
      throw new InvalidArgumentException('MSISDN number is invalid: ' . $msisdn);
    }

    return $mnoData;
  }

  public function parseWithKeys($msisdn)
  {
    // clean value
    $msisdn = $this->clean($msisdn);

    // validate syntax
    $valid = $this->validate($msisdn);

    if (!$valid)
      throw new InvalidArgumentException('MSISDN number is invalid: ' . $msisdn);

    // keys for matching
    $prefixKeys = [];
    for ($i = static::$minKeyLength; $i <= static::$maxKeyLength; $i++)
    {
      $prefixKeys[$i] = substr($msisdn, 0, $i);
    }

    $operators = $this->mnoRepo->getAll();
    $mnoKeys = array_keys($operators);

    $mnoData = null;
    // match operator
    for ($i = static::$maxKeyLength; $i >= static::$minKeyLength; --$i)
    {
      $key = $prefixKeys[$i];
      if (isset($operators[$key])) {
        $mnoData = $operators[$key];
        break;
      }
    }

    if ($mnoData === null)
      throw new InvalidArgumentException('MSISDN number is invalid: ' . $msisdn);

    return $mnoData;
  }
}