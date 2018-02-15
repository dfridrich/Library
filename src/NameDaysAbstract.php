<?php

/*
 * This file is part of Library package.
 *
 * (c) Dennis Fridrich <fridrich.dennis@gmail.com>
 *
 * For the full copyright and license information,
 * please view the contract or license.
 */

namespace Defr;

/**
 * Class NameDays.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
abstract class NameDaysAbstract
{
    /**
     * @var array
     */
    public static $data = [];

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
        if ('!' !== $name[0]) {
            $return = $name;
        } else {
            $return = mb_substr($name, 1);
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
        for ($i = 1; $i <= 12; ++$i) {
            foreach (static::$data[$i] as $day => $values) {
                $values = explode(', ', $values);
                foreach ($values as $value) {
                    $check = mb_stripos($value, $name);
                    if (false !== $check) {
                        $matches[] = [$i, $day, $value, levenshtein($name, $value)];
                    }
                }
            }
        }

        usort(
            $matches,
            function ($a, $b) {
                return $a[3] > $b[3];
            }
        );

        if (0 === count($matches)) {
            return null;
        }

        $date = new \DateTime();
        $date->setDate(date('Y'), $matches[0][0], $matches[0][1]);

        return $date;
    }
}
