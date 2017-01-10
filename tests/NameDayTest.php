<?php

use Defr\NameDays;
use Defr\NameDaysCz;
use Defr\NameDaysSk;

class NameDayTest extends PHPUnit_Framework_TestCase
{
    public function testCzNameDay()
    {
        $date = new \DateTime('2015-09-11T00:00:00+0000');
        $this->assertEquals('Dennis/Denisa', NameDays::getNameDay($date));
        $this->assertEquals('Dennis/Denisa', NameDaysCz::getNameDay($date));
    }
    public function testSkNameDay()
    {
        $date = new \DateTime('2015-03-05T00:00:00+0000');
        $this->assertEquals('Fridrich', NameDaysSk::getNameDay($date));
    }
}
