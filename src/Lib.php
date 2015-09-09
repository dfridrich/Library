<?php

namespace Defr;

use Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator;

/**
 * Zakladni funkce pro praci s aplikaci.
 */
class Lib
{
    /**
     * Instance teto tridy.
     *
     * @var null
     */
    protected static $_instance = null;

    /**
     * Promenna pro uchovavani dat.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Vrati instanci Lib.
     *
     * @return Lib
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Vrati IP adresu.
     *
     * @return string
     */
    public static function getIp()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }

    /**
     * Vrati hostname.
     *
     * @return string
     */
    public static function getHost()
    {
        return self::getIp() ? gethostbyaddr(self::getIp()) : null;
    }

    /**
     * @param bool $string
     *
     * @return array|null|string
     */
    public static function getBrowser($string = true)
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $u_agent = $_SERVER['HTTP_USER_AGENT'];
            $bname = 'Unknown';
            $platform = 'Unknown';
            $version = '';

            //First get the platform?
            if (preg_match('/linux/i', $u_agent)) {
                $platform = 'linux';
            } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
                $platform = 'mac';
            } elseif (preg_match('/windows|win32/i', $u_agent)) {
                $platform = 'windows';
            }

            $bname = 'N/A';
            $ub = 'N/A';

            // Next get the name of the useragent yes seperately and for good reason
            if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
                $bname = 'Internet Explorer';
                $ub = 'MSIE';
            } elseif (preg_match('/Firefox/i', $u_agent)) {
                $bname = 'Mozilla Firefox';
                $ub = 'Firefox';
            } elseif (preg_match('/Chrome/i', $u_agent)) {
                $bname = 'Google Chrome';
                $ub = 'Chrome';
            } elseif (preg_match('/Safari/i', $u_agent)) {
                $bname = 'Apple Safari';
                $ub = 'Safari';
            } elseif (preg_match('/Opera/i', $u_agent)) {
                $bname = 'Opera';
                $ub = 'Opera';
            } elseif (preg_match('/Netscape/i', $u_agent)) {
                $bname = 'Netscape';
                $ub = 'Netscape';
            }

            // finally get the correct version number
            $known = array('Version', $ub, 'other');
            $pattern = '#(?<browser>'.implode('|', $known).')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
            if (!preg_match_all($pattern, $u_agent, $matches)) {
                // we have no matching number just continue
            }

            // see how many we have
            $i = count($matches['browser']);
            if ($i != 1) {
                //we will have two since we are not using 'other' argument yet
                //see if version is before or after the name
                if (strripos($u_agent, 'Version') < strripos($u_agent, $ub)) {
                    $version = @$matches['version'][0];
                } else {
                    $version = @$matches['version'][1];
                }
            } else {
                $version = @$matches['version'][0];
            }

            // check if we have a number
            if ($version == null || $version == '') {
                $version = '?';
            }

            if ($string) {
                return $bname.' '.$version.' ('.ucfirst($platform).')';
            } else {
                return array(
                    'userAgent' => $u_agent,
                    'name' => $bname,
                    'version' => $version,
                    'platform' => $platform,
                    'pattern' => $pattern,
                );
            }
        } else {
            if ($string) {
                return;
            } else {
                return array(
                    'userAgent' => null,
                    'name' => null,
                    'version' => null,
                    'platform' => null,
                    'pattern' => null,
                );
            }
        }
    }

    /**
     * @param int  $length
     * @param bool $safeChars
     *
     * @return string
     */
    public static function generateShortLink($length = 5, $safeChars = false)
    {
        /*$pool = '23456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';
        $str = substr(str_shuffle(str_repeat($pool, ceil($length/strlen($pool)))), 0, $length);
        return $str;*/
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        if ($safeChars) {
            $keys = array_diff($keys, array('0', '1', 'i', 'l', 'o'));
        }

        for ($i = 0; $i < $length; ++$i) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    /**
     * @param int  $lenght
     * @param bool $safeChars
     *
     * @return string
     */
    public static function generateRandomString($lenght = 40, $safeChars = false)
    {
        return self::generateShortLink($lenght, $safeChars);
    }

    /**
     * @param $string
     * @param int  $length
     * @param int  $start
     * @param bool $ellipsis
     *
     * @return string
     */
    public static function substring($string, $length = 50, $start = 0, $ellipsis = true)
    {
        $newString = trim(mb_substr($string, $start, $length, 'UTF-8'));
        if (strlen($newString) < strlen($string) && $ellipsis) {
            return $newString.'...';
        } //&hellip;
        else {
            return $newString;
        }
    }

    /**
     * @param $link
     * @param $param
     * @param $value
     *
     * @return string
     */
    public static function addUrlParam($link, $param, $value)
    {
        // TODO opravit
        $url = parse_url($link, PHP_URL_PATH);
        if (!isset($url[$param])) {
            if (strpos($link, '?')) {
                $link .= '&'.$param.'='.$value;
            } else {
                $link .= '?'.$param.'='.$value;
            }
        }

        return $link;
    }

    /**
     * @param string $hash
     *
     * @return string
     */
    public static function getToken($hash = 'md5')
    {
        return hash(
            $hash,
            (array_key_exists('REMOTE_ADDR', $_SERVER) ? $_SERVER['REMOTE_ADDR'] : null)
            .uniqid(mt_rand())
        );
    }

    /**
     * Vraci zeleny nebo cerveny label ano/ne.
     *
     * @param $bool
     *
     * @return string
     */
    public static function getBoolLabel($bool)
    {
        return $bool
            ? '<span class="label label-green">Ano</span>'
            : '<span class="label label-red">Ne</span>';
    }

    /**
     * Vrati seo friendly slug.
     *
     * @param $text
     *
     * @return mixed|string
     */
    public static function slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('#[^\\pL\d]+#u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = strtolower($text);

        // remove unwanted characters
        $text = preg_replace('#[^-\w]+#', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Vrati zpravu do prikazove radky.
     *
     * @param $string mixed
     */
    public static function cliMessage($string)
    {
        $preString = '  | ';
        if (is_array($string)) {
            echo $preString.implode("\n==> ", $string)."\n";
        } else {
            echo $preString.$string."\n";
        }
    }

    /**
     * Vrati idealni slozku pro ulozeni.
     *
     * @param int $id
     *
     * @return float
     */
    public static function getSaveFolder($id = 0)
    {
        //return date('y/m/W/N') . md5(date());
        return floor($id / 1000);
    }

    /**
     * Odstrani diakritiku ze zadaneho
     * retezce.
     *
     * @param string $string
     *
     * @return string
     */
    public static function stripDiacritics($string)
    {
        $string = str_replace(
            array(
                'ě',
                'š',
                'č',
                'ř',
                'ž',
                'ý',
                'á',
                'í',
                'é',
                'ú',
                'ů',
                'ó',
                'ť',
                'ď',
                'ľ',
                'ň',
                'ŕ',
                'â',
                'ă',
                'ä',
                'ĺ',
                'ć',
                'ç',
                'ę',
                'ë',
                'î',
                'ń',
                'ô',
                'ő',
                'ö',
                'ů',
                'ű',
                'ü',
            ),
            array(
                'e',
                's',
                'c',
                'r',
                'z',
                'y',
                'a',
                'i',
                'e',
                'u',
                'u',
                'o',
                't',
                'd',
                'l',
                'n',
                'a',
                'a',
                'a',
                'a',
                'a',
                'a',
                'c',
                'e',
                'e',
                'i',
                'n',
                'o',
                'o',
                'o',
                'u',
                'u',
                'u',
            ),
            $string
        );
        $string = str_replace(
            array(
                'Ě',
                'Š',
                'Č',
                'Ř',
                'Ž',
                'Ý',
                'Á',
                'Í',
                'É',
                'Ú',
                'Ů',
                'Ó',
                'Ť',
                'Ď',
                'Ľ',
                'Ň',
                'Ä',
                'Ć',
                'Ë',
                'Ö',
                'Ü',
            ),
            array(
                'E',
                'S',
                'C',
                'R',
                'Z',
                'Y',
                'A',
                'I',
                'E',
                'U',
                'U',
                'O',
                'T',
                'D',
                'L',
                'N',
                'A',
                'C',
                'E',
                'O',
                'U',
            ),
            $string
        );

        return $string;
    }

    /**
     * Upravi zadany retezec do podoby,
     * ktera muze byt bezpecne pouzita
     * jako id nebo predevsim v url
     * jako rewrite string.
     *
     * Muze obsahovat znaky:
     * 0-9, a-z, A-Z, -
     *
     * @param string $string
     * @param bool   $capitalize
     *
     * @return string
     */
    public static function idize($string, $capitalize = false)
    {
        $string = strtolower(self::stripDiacritics($string));

        $string = preg_replace('/[^0-9a-zA-Z-]/i', '-', $string);
        $string = preg_replace('/(-+)/i', '-', $string);
        $string = trim($string, '-');

        return $capitalize ? strtoupper($string) : $string;
    }

    /**
     * Rozparsuje jmeno vcetne titulu pred a za jmenem.
     *
     * @param $name
     *
     * @return array
     */
    public static function parseName($name)
    {
        $name = urldecode($name);
        $names = explode(' ', str_replace(',', '', $name));

        $pretitle = $first_name = $last_name = $posttitle = '';

        foreach ($names as $name) {
            if ((substr($name, -1) == '.' || $name == 'et') && empty($first_name)) {
                $pretitle .= $name.' ';
                continue;
            }
            if (empty($first_name)) {
                $first_name = $name;
                continue;
            }
            if (substr($name, -1) == '.' && !empty($last_name)) {
                $posttitle .= $name.' ';
                continue;
            }
            if (!empty($first_name)) {
                $last_name .= $name.' ';
                continue;
            }
        }

        return array(
            trim($pretitle),
            ucwords(trim(strtolower($first_name))),
            ucwords(trim(strtolower($last_name))),
            trim($posttitle),
        );
    }

    /**
     * Vrati jmeno ve tvaru J. Novak.
     *
     * @param $name
     *
     * @return string
     */
    public static function getShortName($name)
    {
        $name = self::parseName($name);

        return substr($name[1], 0, 1).'. '.$name[2];
    }

    /**
     * Counts image DPI.
     *
     * @param $filename
     *
     * @return array
     */
    public static function getImageDpi($filename)
    {
        $a = fopen($filename, 'r');
        $string = fread($a, 20);
        fclose($a);
        $data = bin2hex(substr($string, 14, 4));
        $x = substr($data, 0, 4);
        $y = substr($data, 0, 4);

        return array(hexdec($x), hexdec($y));
    }

    /**
     * Identify image without Imagick.
     *
     * @param $filename
     *
     * @return array
     */
    public static function identifyImage($filename)
    {
        $dpi = self::getImageDpi($filename);
        $data = array();
        $data['dpi_x'] = $dpi[0];
        $data['dpi_y'] = $dpi[1];

        // Sometimes it returns 10752, which is (WHY?!) 72 dpi
        if ($data['dpi_x'] == 10752) {
            $data['dpi_x'] = 72;
        }
        if ($data['dpi_y'] == 10752) {
            $data['dpi_y'] = 72;
        }

        $info = getimagesize($filename);
        $data['image_w'] = $info[0];
        $data['image_h'] = $info[1];
        $data['bits'] = $info['bits'];
        $data['channels'] = '';
        if ($info['channels'] == 3) {
            $data['channels'] = 'RGB';
        }
        if ($info['channels'] == 4) {
            $data['channels'] = 'CMYK';
        }

        return $data;
    }

    /**
     * @param $size
     * @param string $unit
     *
     * @return string
     */
    public static function humanFileSize($size, $unit = 'MB')
    {
        if ((!$unit && $size >= 1 << 30) || $unit == 'GB') {
            return number_format($size / (1 << 30), 2).' GB';
        }
        if ((!$unit && $size >= 1 << 20) || $unit == 'MB') {
            return number_format($size / (1 << 20), 2).' MB';
        }
        if ((!$unit && $size >= 1 << 10) || $unit == 'KB') {
            return number_format($size / (1 << 10), 2).' KB';
        }

        return number_format($size).' bytes';
    }

    /**
     * @param $origDpi
     * @param $origW
     * @param $printW
     *
     * @return float
     */
    public static function countDpi($origDpi, $origW, $printW)
    {
        if ($origDpi == 0 or $printW == 0 or $origW == 0) {
            return 0;
        }

        return round($origDpi / ($printW / (($origW / $origDpi) * 2.54)));
    }

    /**
     * @param $rgb1
     * @param $rgb2
     *
     * @return number
     */
    public static function colorDiff($rgb1, $rgb2)
    {
        $rgb1 = str_replace('#', '', $rgb1);
        $rgb2 = str_replace('#', '', $rgb2);

        // do the math on each tuple
        // could use bitwise operates more efeceintly but just do strings for now.
        $red1 = hexdec(substr($rgb1, 0, 2));
        $green1 = hexdec(substr($rgb1, 2, 2));
        $blue1 = hexdec(substr($rgb1, 4, 2));

        $red2 = hexdec(substr($rgb2, 0, 2));
        $green2 = hexdec(substr($rgb2, 2, 2));
        $blue2 = hexdec(substr($rgb2, 4, 2));

        //return abs($red1 - $red2) + abs($green1 - $green2) + abs($blue2 - $blue2) ;
        //die($red2 . ' ' .$red1);
        return sqrt(pow($red2 - $red1, 2) + pow($green2 - $green1, 2) + pow($blue2 - $blue1, 2));
    }

    /**
     * @param $array
     * @param $key
     */
    public static function aasort(&$array, $key)
    {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = $ret;
    }

    /**
     * @param $string
     *
     * @return array
     */
    public static function normalizeKeywords($string)
    {
        $out = array();
        // Jen pismena, cisla
        $string = preg_replace('/[^0-9a-zA-Z-]/i', ' ', self::stripDiacritics($string));
        // Vice mezer dame pryc
        $string = preg_replace("/\s+/", ' ', $string);
        $string = explode(' ', $string);
        foreach ($string as $word) {
            if (is_numeric($word)) {
                $out[] = $word;
            } else {
                $len = strlen($word);
                if ($len >= 7) {
                    $word = substr($word, 0, -3);
                } elseif ($len > 3) {
                    $word = substr($word, 0, -1);
                }
                $out[] = $word;
            }
        }

        return $out;
    }

    /**
     * @param int $vat
     *
     * @return string
     */
    public static function getVatCoefficient($vat = 21)
    {
        return number_format($vat / (100 + $vat), 4);
    }

    /**
     * @param $price
     *
     * @return float
     */
    public static function roundPrice($price)
    {
        return (float) number_format($price, 2, '.', '');
    }

    /**
     * @param $number
     * @param int $decimals
     *
     * @return float
     */
    public static function round($number, $decimals = 2)
    {
        return (float) number_format($number, $decimals, '.', '');
    }

    /**
     * @param $price
     * @param $vat
     *
     * @return float
     */
    public static function getPriceWithoutVat($price, $vat)
    {
        return self::roundPrice($price / (1 + $vat / 100));
    }

    /**
     * @param $price
     * @param $vat
     *
     * @return float
     */
    public static function getPriceWithVat($price, $vat)
    {
        return self::roundPrice($price * (1 + $vat / 100));
    }

    /**
     * @param $price
     * @param $vat
     *
     * @return float
     */
    public static function getVatFromPrice($price, $vat)
    {
        return self::roundPrice($price * self::getVatCoefficient($vat));
    }

    /**
     * @param $string
     * @param $encoding
     *
     * @return string
     */
    public static function mbUcFirst($string, $encoding = 'UTF-8')
    {
        $strlen = mb_strlen($string, $encoding);
        $firstChar = mb_substr($string, 0, 1, $encoding);
        $then = mb_substr($string, 1, $strlen - 1, $encoding);

        return mb_strtoupper($firstChar, $encoding).$then;
    }

    /**
     * @param $price
     * @param int $decPoint
     *
     * @return string
     */
    public static function price($price, $decPoint = 2)
    {
        if (!is_numeric($price)) {
            $price = 0;
        }

        return number_format($price, $decPoint, ',', ' ').' Kč';
    }

    /**
     * @param $origDpi
     * @param $minDpi
     * @param $origW
     *
     * @return float
     */
    public static function countMaxPrintSize($origDpi, $minDpi, $origW)
    {
        // TODO
        return self::round(($origW / $minDpi) * 2.54, 1);
    }

    /**
     * @param $count
     * @param $words
     *
     * @return string
     */
    public static function declinationFromArray($count, $words)
    {
        if ($count == 0 or $count >= 5) {
            return $count.' '.$words[0];
        } elseif ($count == 1) {
            return $count.' '.$words[1];
        } elseif ($count >= 2 && $count <= 4) {
            return $count.' '.$words[2];
        }
    }

    /**
     * @param $string
     * @param $salt
     *
     * @return string
     */
    public static function getTokenForString($string, $salt)
    {
        return hash('crc32', $string.$salt);
    }

    /**
     * @param $account
     * @param $amount
     * @param $variableSymbol
     * @param string $message
     * @param int    $size
     *
     * @return string
     */
    public static function qrPaymentImage($account, $amount, $variableSymbol, $message = 'QR Platba', $size = 200)
    {
        $account = explode('/', $account);
        $url = 'http://api.paylibo.com/paylibo/generator/czech/image?'
            .'accountNumber='.str_replace(array(' ', '-'), '', $account[0])
            .'&bankCode='.$account[1]
            .'&amount='.number_format($amount, 2, '.', '')
            .'&message='.urlencode($message)
            .'&size='.$size
            .'&currency=CZK'
            .'&vs='.$variableSymbol;

        return $url;
    }

    /**
     * Get the directory size.
     *
     * @param $directory
     *
     * @return int
     */
    public static function dirSize($directory)
    {
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, \FilesystemIterator::KEY_AS_PATHNAME)) as $file) {
            $size += $file->getSize();
        }

        return $size;
    }

    /**
     * @return string
     */
    public static function getMaxUploadSize()
    {
        return array(
            str_replace('M', ' MB', ini_get('upload_max_filesize')),
            str_replace('M', ' MB', ini_get('post_max_size')),
        );
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public static function toggleBoolean($value)
    {
        return (bool) $value ? false : true;
    }

    /**
     * @param array $new
     * @param array $previous
     *
     * @return array
     */
    public static function getChanges(array $new, array $previous)
    {
        $changes = array();
        $changedKeys = array_keys(array_diff_assoc($new, $previous));
        foreach ($changedKeys as $key) {
            $changes[$key] = isset($previous[$key]) ? [$previous[$key], $new[$key]] : $new[$key];
        }

        return $changes;
    }

    /**
     * @param $hex
     * @param $percent
     *
     * @return string
     */
    public static function colourBrightness($hex, $percent)
    {
        // Work out if hash given
        $hash = '';
        if (stristr($hex, '#')) {
            $hex = str_replace('#', '', $hex);
            $hash = '#';
        }
        /// HEX TO RGB
        $rgb = array(hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
        //// CALCULATE
        for ($i = 0; $i < 3; ++$i) {
            // See if brighter or darker
            if ($percent > 0) {
                // Lighter
                $rgb[$i] = round($rgb[$i] * $percent) + round(255 * (1 - $percent));
            } else {
                // Darker
                $positivePercent = $percent - ($percent * 2);
                $rgb[$i] = round($rgb[$i] * $positivePercent) + round(0 * (1 - $positivePercent));
            }
            // In case rounding up causes us to go to 256
            if ($rgb[$i] > 255) {
                $rgb[$i] = 255;
            }
        }
        //// RBG to Hex
        $hex = '';
        for ($i = 0; $i < 3; ++$i) {
            // Convert the decimal digit to hex
            $hexDigit = dechex($rgb[$i]);
            // Add a leading zero if necessary
            if (strlen($hexDigit) == 1) {
                $hexDigit = '0'.$hexDigit;
            }
            // Append to the hex string
            $hex .= $hexDigit;
        }

        return $hash.$hex;
    }

    /**
     * @param $string
     *
     * @return float
     */
    public static function toNumber($string)
    {
        return floatval(str_replace(',', '.', str_replace('.', '', $string)));
    }

    /**
     * @param $string
     *
     * @return mixed
     */
    public static function toInteger($string)
    {
        return intval(preg_replace('/(?<=\d)\s+(?=\d)/', '', trim($string)));
    }

    public static function dateToText(\DateTime $date)
    {
        // TODO pretty date
    }

    /**
     * @param $email
     *
     * @return mixed
     */
    public static function isEmail($email)
    {
        return !filter_var($email, FILTER_VALIDATE_EMAIL) ? false : true;
    }
}
