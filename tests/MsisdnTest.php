<?php

use PHPUnit\Framework\TestCase;

use MSISDNService\MnoEntity;
use MSISDNService\MnoRepository;
use MSISDNService\MSISDN;

final class MsisdnTest extends TestCase
{
  private $service = null;

  public function tearDown() {
    Mockery::close();
  }

  private function getService()
  {
    if ($this->service == null) {
      $this->service = new MSISDN(new MnoRepository());
    }

    return $this->service;
  }

  public function testClean()
  {
    // cold start
    $service = $this->getService();

    $start = microtime(true);
    $msisdn = '+386 40 123 410';
    $cleanValue = '38640123410';

    $service = $this->getService();

    $this->assertEquals(
      $cleanValue,
      $service->clean($msisdn)
    );

    $stop = microtime(true);
    $diff = round(($stop - $start)*1000,2);
    echo "Clean t_diff=$diff ms\n";
  }

  public function testValidate()
  {
    $msisdn = '38640123410';

    $service = $this->getService();

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

    $service = $this->getService();
    $service->parse($value);
  }

  /**
   * @depends testClean
   * @depends testValidate
   */
  public function testParseValid()
  {
    $start = microtime();
    $msisdn = '38640123410';
    $service = $this->getService();

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

    $stop = microtime();
    $diff = round(($stop - $start) * 1000, 2);
    //echo "t_diff=$diff ms\n";
    echo "Regex time: $diff ms\n";
  }

  /**
   * @depends testClean
   * @depends testValidate
   */
  public function testParseWithKeysValid()
  {
    $start = microtime();
    $msisdn = '38640123410';
    $service = $this->getService();

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
  }
}