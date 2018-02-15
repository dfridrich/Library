<?php

/*
 * This file is part of Library package.
 *
 * (c) Dennis Fridrich <fridrich.dennis@gmail.com>
 *
 * For the full copyright and license information,
 * please view the contract or license.
 */

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
        $this->assertSame('EUR', $rates->getEur()->getCode());
        $this->assertInternalType('float', $rates->getEur()->getRate());
    }
}
