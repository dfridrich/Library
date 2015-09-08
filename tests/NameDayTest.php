<?php

use Defr\NameDays;

class NameDayTest extends PHPUnit_Framework_TestCase
{
    public function testNameDay()
    {
        $date = new \DateTime('2015-09-11T00:00:00+0000');
        $this->assertEquals('Dennis/Denisa', NameDays::getNameDay($date));
    }
}