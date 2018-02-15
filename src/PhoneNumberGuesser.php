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
 * Class PhoneNumberGuesser.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class PhoneNumberGuesser
{
    const TYPE_MOBILE = 'mobile';
    const TYPE_LANDLINE = 'landline';
    const TYPE_SPECIAL = 'special';
    const TYPE_OTHER = 'other';
    const TYPE_FREE = 'free';
    const TYPE_INTERNATIONAL = 'international';

    const DOMESTIC_PREFIX = '00420';
    const INTERNATIONAL_PREFIX = '00';
    const DOMESTIC_LENGTH = 9;
    const SPECIAL_PREFIX = '*,#';
    const REGEXP_MOBILE = '/^(6|7){1}[0-9]{8}$/i';
    const REGEXP_LANDLINE = '/^(2|3|4|5){1}[0-9]{8}$/i';
    const REGEXP_FREE = '/^(800){1}[0-9]{6}$/i';
    const REGEXP_SPECIAL = '/^(8|9){1}[0-9]{8}$/i';

    /**
     * @param $number
     *
     * @return string
     */
    public static function phoneType($number)
    {
        $number = self::reformatPhone($number);

        // Cisla s tuzemskou predvolbou
        if (self::DOMESTIC_PREFIX === mb_substr($number, 0, mb_strlen(self::DOMESTIC_PREFIX))) {
            return self::handleDomestic($number);
        }

        // Cisla odpovidajici tuzemske delce
        if (self::DOMESTIC_LENGTH === mb_strlen($number)) {
            return self::handleDomestic($number);
        }

        // Cisla s predvolbou
        if (in_array(mb_substr($number, 0, 1), explode(',', self::SPECIAL_PREFIX), true)) {
            return self::handleSpecial($number);
        }

        // Mezinarodni
        if (self::INTERNATIONAL_PREFIX === mb_substr($number, 0, mb_strlen(self::INTERNATIONAL_PREFIX))) {
            return self::TYPE_INTERNATIONAL;
        }

        // Pokud se nic nechytlo
        return self::handleOther($number);
    }

    /**
     * @param $number
     *
     * @return mixed
     */
    public static function reformatPhone($number)
    {
        // Odstranime znak + a pracujeme jen s cislem (pokud se nejedna o specialni cislo)
        $number = str_replace('+', '00', $number);
        // Odstranime mezery
        $number = str_replace([' ', '-'], '', $number);

        return $number;
    }

    /**
     * @param $number
     *
     * @return string
     */
    private static function handleDomestic($number)
    {
        // Zbavime predvolby zeme
        $number = str_replace(self::DOMESTIC_PREFIX, '', $number);
        // Mobily
        if (preg_match(self::REGEXP_MOBILE, $number)) {
            return self::TYPE_MOBILE;
        }
        // Pevne linky
        if (preg_match(self::REGEXP_LANDLINE, $number)) {
            return self::TYPE_LANDLINE;
        }
        // Bezplatne linky
        if (preg_match(self::REGEXP_FREE, $number)) {
            return self::TYPE_FREE;
        }
        // Linky se specialnim tarifem
        if (preg_match(self::REGEXP_SPECIAL, $number)) {
            return self::TYPE_SPECIAL;
        }

        // Ostatni nerozpoznane linky
        return self::TYPE_OTHER;
    }

    /**
     * @param $number
     *
     * @return string
     */
    private static function handleSpecial($number)
    {
        return self::TYPE_SPECIAL;
    }

    /**
     * @param $number
     *
     * @return string
     */
    private static function handleOther($number)
    {
        return self::TYPE_OTHER;
    }
}
