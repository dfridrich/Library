<?php

namespace Defr;

/**
 * Class NameDays
 * @package Defr
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
abstract class NameDaysAbstract
{
    /**
     * @var array
     */
    private static $data = [];

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    public static function getNameDay(\DateTime $date = null)
    {
        if (!$date) {
            $date = new \DateTime();
        }
        $name = static::$data[$date->format('n')][$date->format('j')];
        if ($name[0] != '!') {
            $return = $name;
        } else {
            $return = substr($name, 1);
        }

        return $return;
    }

    /**
     * @param $name
     *
     * @return \DateTime|null
     */
    public static function getNameDate($name)
    {
        $matches = [];
        for ($i = 1; $i <= 12; $i++) {
            foreach (static::$data[$i] as $day => $value) {
                $check = mb_stripos($value, $name);
                if (false !== $check) {
                    $matches[] = [$i, $day, $value, levenshtein($name, $value)];
                }
            }
        }

        usort(
            $matches,
            function ($a, $b) {
                return $a[3] > $b[3];
            }
        );

        if (count($matches) == 0) {
            return null;
        }

        $date = new \DateTime();
        $date->setDate(date("Y"), $matches[0][0], $matches[0][1]);

        return $date;
    }
}
