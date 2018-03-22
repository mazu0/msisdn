<?php

use PHPUnit\Framework\TestCase;

final class MsisdnTest extends TestCase
{
  public function tearDown() {
    Mockery::close();
  }

  public function testClean()
  {
    $msisdn = '+386 40 123 410';
    $cleanValue = '38640123410';

    $mock = Mockery::mock('Msisdn');
    $mock->shouldReceive('clean')
      ->with($msisdn)
      ->andReturn($cleanValue);

    $this->assertEquals(
      $cleanValue,
      $mock->clean($msisdn)
    );
  }

  public function testValidate()
  {
    $msisdn = '38640123410';

    $mock = Mockery::mock('Msisdn');
    $mock->shouldReceive('validate')
      ->with($msisdn)
      ->andReturn(1);

    $this->assertEquals(
      1,
      $mock->validate($msisdn)
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

    $repo = new MnoRepository();
    $parser = new Msisdn($repo);
    $parser->parse($value);
  }

  /**
   * @depends testClean
   * @depends testValidate
   */
  public function testParseValid()
  {
    $msisdn = '38640123410';

    $repo = new MnoRepository();
    $parser = new Msisdn($repo);

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
      $parser->parse($msisdn)
    );
  }
}