<?php

use Defr\CnbRates;
use Defr\CnbRates\Rates;

class CnbRatesTest extends PHPUnit_Framework_TestCase
{
    public function testEurRate()
    {
        $cnbRates = new CnbRates();

        /** @var Rates $rates */
        $rates = $cnbRates->getRates();

        $this->assertInstanceOf('\DateTime', $rates->getDate());
        $this->assertEquals('EUR', $rates->getEur()->getCode());
        $this->assertInternalType('float', $rates->getEur()->getRate());
    }
}
