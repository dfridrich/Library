<?php

/*
 * This file is part of Library package.
 *
 * (c) Dennis Fridrich <fridrich.dennis@gmail.com>
 *
 * For the full copyright and license information,
 * please view the contract or license.
 */

use Defr\NameDays;
use Defr\NameDaysCz;
use Defr\NameDaysSk;
use PHPUnit\Framework\TestCase;

class NameDayTest extends TestCase
{
    public function testCzNameDay()
    {
        $date = new \DateTime('2015-09-11T00:00:00+0000');
        $this->assertSame('Dennis/Denisa', NameDays::getNameDay($date));
        $this->assertSame('Dennis/Denisa', NameDaysCz::getNameDay($date));
    }

    public function testSkNameDay()
    {
        $date = new \DateTime('2015-03-05T00:00:00+0000');
        $this->assertSame('Fridrich', NameDaysSk::getNameDay($date));
    }
}
