<?php

namespace Defr;

use Defr\CnbRates\Rate;
use Defr\CnbRates\Rates;

/**
 * Class OpenWeather.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 */
class CnbRates
{
    const API_URL = 'http://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt?date=%s';

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param null $cacheDir
     */
    public function __construct($cacheDir = null)
    {
        if ($cacheDir === null) {
            $cacheDir = sys_get_temp_dir();
        }
        $this->cacheDir = $cacheDir.'/defr';
    }

    /**
     * @param \DateTime $date
     *
     * @return Rates
     */
    public function getRates(\DateTime $date = null)
    {
        if ($date === null) {
            $date = new \DateTime();
        }

        /*
         * Pokud je pozadan kurz pro dnesni den a jeste nebylo 14:15 zverejnim vcerejsi kurz
         */
        if ($date->format('Hi') < 1415) {
            $date->modify('-1 day');
        }

        $cachedFileName = $date->format('Ymd').'.php';

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir);
        }

        $cachedFile = $this->cacheDir.'/cnb_'.$cachedFileName;

        if (!is_file($cachedFile)) {
            $url = sprintf(self::API_URL, urlencode($date->format('d.m.Y')));
            $file = explode("\n", file_get_contents($url));

            $rates = [];

            foreach ($file as $index => $line) {
                // Datum a hlavicku souboru preskocime
                if ($index < 2) {
                    continue;
                }
                // Pokud je radek prazdny
                if (empty($line)) {
                    continue;
                }
                $rate = explode('|', $line);

                $rates[$rate[3]] = new Rate($date,
                    $rate[3],
                    $rate[0],
                    $rate[1],
                    Lib::toNumber($rate[2]),
                    Lib::toNumber($rate[4]));
            }
            file_put_contents($cachedFile, serialize($rates));
        } else {
            $rates = unserialize(file_get_contents($cachedFile));
        }

        // Prevedeni do objektu Rates pro lepsi pristup v sablonach
        return new Rates($date, $rates);
    }
}
