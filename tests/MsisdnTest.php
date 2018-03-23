<?php

use PHPUnit\Framework\TestCase;

use MSISDNService\MobileNumberEntity;
use MSISDNService\MSISDN;

/**
 * @coversDefaultClass MSISDNService\MSISDN
 */
final class MsisdnTest extends TestCase
{
  /*
   * @covers ::testClean
   */
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

  /*
   * @covers ::validate
   */
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
   * Test throwing InvalidArgumentException when msisdn is malformed
   *
   * @covers ::parse
   *
   * @depends testClean
   * @depends testValidate
   *
   * @expectedException InvalidArgumentException
   * @expectedExceptionMessage MSISDN number is invalid: 386401wsad23410
   *
   */
  public function testParseInvalidArgument()
  {
    $this->expectException(InvalidArgumentException::class);

    $value = '386401wsad23410';

    $service = MSISDN::getInstance();
    $service->parse($value);
  }

  /**
   * Test parsing a simple msisdn containing only digits
   *
   * @covers Parse
   *
   * @depends testClean
   * @depends testValidate
   */
  public function testParseSimple()
  {
    $msisdn = '38640123410';
    $service = MSISDN::getInstance();

    $expectedMno = MobileNumberEntity::fromArray([
      "CountryISO" => "SI",
      "CountryPrefix" => "386",
      "MobileNetworkCode" => "40",
      "ProviderName" => "SI.Mobil",
      "SubscriberNumber" => "123410"
    ]);

    $this->assertEquals(
      $expectedMno,
      $service->parse($msisdn)
    );
  }

  /**
   * Test parsing a msisdn containing a '+' prefix white spaces
   *
   * @covers Parse
   *
   * @depends testClean
   * @depends testValidate
   */
  public function testParse()
  {
    $msisdn = '+44 7700 900663';
    $service = MSISDN::getInstance();

    $expectedMno = MobileNumberEntity::fromArray([
      "CountryISO" => "GB",
      "CountryPrefix" => "44",
      "MobileNetworkCode" => "77",
      "ProviderName" => "BT Group",
      "SubscriberNumber" => "00900663"
    ]);

    $this->assertEquals(
      $expectedMno,
      $service->parse($msisdn)
    );
  }
}