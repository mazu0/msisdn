<?php

use PHPUnit\Framework\TestCase;

use MSISDNService\MnoEntity;
use MSISDNService\MSISDN;

final class MsisdnTest extends TestCase
{
  public function testClean()
  {
    $msisdn = '+386 40 123 410';
    $cleanValue = '38640123410';

    $service = MSISDN::getInstance();

    $this->assertEquals(
      $cleanValue,
      $service->clean($msisdn)
    );
  }

  public function testValidate()
  {
    $msisdn = '38640123410';

    $service = MSISDN::getInstance();

    $this->assertEquals(
      1,
      $service->validate($msisdn)
    );
  }

  /**
   * @depends testClean
   * @depends testValidate
   */
  public function testParseInvalidArgument()
  {
    $this->expectException(InvalidArgumentException::class);

    $value = '386401wsad23410';

    $service = MSISDN::getInstance();
    $service->parse($value);
  }

  /**
   * @depends testClean
   * @depends testValidate
   */
  public function testParseValid()
  {
    $msisdn = '38640123410';
    $service = MSISDN::getInstance();

    $expectedMno = MnoEntity::fromArray([
      "MobileCountryCode" => "293",
      "MobileNetworkCode" => "40",
      "CountryISO" => "SI",
      "CountryName" => "Slovenia",
      "CountryPrefix" => "386",
      "ProviderName" => "SI.Mobil"
    ]);

    $this->assertEquals(
      $expectedMno,
      $service->parse($msisdn)
    );
  }

  /**
   * @depends testClean
   * @depends testValidate
   */
  /*
  public function testParseWithKeysValid()
  {
    $start = microtime();
    $msisdn = '38640123410';
    $service = MSISDN::getInstance();

    $expectedMno = MnoEntity::fromArray([
      "MobileCountryCode" => "293",
      "MobileNetworkCode" => "40",
      "CountryISO" => "SI",
      "CountryName" => "Slovenia",
      "CountryPrefix" => "386",
      "ProviderName" => "SI.Mobil"
    ]);

    $this->assertEquals(
      $expectedMno,
      $service->parseWithKeys($msisdn)
    );

    $stop = microtime();
    $diff = round(($stop - $start) * 1000, 2);
    echo "With Keys time: $diff ms\n";
  }*/
}