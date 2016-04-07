<?php

namespace Defr\CnbRates;

/**
 * Class Rate
 * @package Defr\CnbRates
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class Rate
{
    /**
     * @var \DateTime
     */
    public $date;

    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var float
     */
    public $rate;

    /**
     * @param \DateTime $date
     * @param $code
     * @param $country
     * @param $currency
     * @param $amount
     * @param $rate
     */
    public function __construct(\DateTime $date, $code, $country, $currency, $amount, $rate)
    {
        $this->date = $date;
        $this->code = $code;
        $this->country = $country;
        $this->currency = $currency;
        $this->amount = (float) $amount;
        $this->rate = (float) $rate;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return strval($this->rate);
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }
}
