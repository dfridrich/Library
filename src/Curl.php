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
 * Class Curl.
 *
 * @author Dennis Fridrich <fridrich.dennis@gmail.com>
 *
 * @see https://github.com/php-curl-class/php-curl-class
 */
class Curl
{
    const USER_AGENT = 'PHP';

    private $_cookies = [];
    private $_headers = [];
    private $_options = [];

    private $_multi_parent = false;
    private $_multi_child = false;
    private $_before_send = null;
    private $_success = null;
    private $_error = null;
    private $_complete = null;

    public $curl;

    /**
     * @var self[]
     */
    public $curls;

    public $error = false;
    public $error_code = 0;
    public $error_message = null;

    public $curl_error = false;
    public $curl_error_code = 0;
    public $curl_error_message = null;

    public $http_error = false;
    public $http_status_code = 0;
    public $http_error_message = null;

    public $request_headers = null;
    public $response_headers = null;
    public $response = null;

    public function __construct()
    {
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }

        $this->curl = curl_init();
        $this->setUserAgent(self::USER_AGENT);
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        $this->setOpt(CURLOPT_HEADER, true);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @param $url_mixed
     * @param array $data
     *
     * @throws \ErrorException
     *
     * @return int|mixed|null
     */
    public function get($url_mixed, $data = [])
    {
        if (is_array($url_mixed)) {
            $curl_multi = curl_multi_init();
            $this->_multi_parent = true;

            $this->curls = [];

            foreach ($url_mixed as $url) {
                $curl = new self();
                $curl->_multi_child = true;
                $curl->setOpt(CURLOPT_URL, $this->_buildURL($url, $data), $curl->curl);
                $curl->setOpt(CURLOPT_HTTPGET, true);
                $this->_call($this->_before_send, $curl);
                $this->curls[] = $curl;

                $curlm_error_code = curl_multi_add_handle($curl_multi, $curl->curl);
                if (!(CURLM_OK === $curlm_error_code)) {
                    throw new \ErrorException(
                        'cURL multi add handle error: '.
                        curl_multi_strerror($curlm_error_code)
                    );
                }
            }

            foreach ($this->curls as $ch) {
                foreach ($this->_options as $key => $value) {
                    $ch->setOpt($key, $value);
                }
            }

            do {
                $status = curl_multi_exec($curl_multi, $active);
            } while (CURLM_CALL_MULTI_PERFORM === $status || $active);

            foreach ($this->curls as $ch) {
                $this->exec($ch);
            }
        } else {
            $this->setopt(CURLOPT_URL, $this->_buildURL($url_mixed, $data));
            $this->setopt(CURLOPT_HTTPGET, true);

            return $this->exec();
        }

        return null;
    }

    /**
     * @param $url
     * @param array $data
     *
     * @return int|mixed
     */
    public function post($url, $data = [])
    {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, $this->_postfields($data));

        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     *
     * @return int|mixed
     */
    public function put($url, $data = [])
    {
        $this->setOpt(CURLOPT_URL, $url);
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PUT');
        $this->setOpt(CURLOPT_POSTFIELDS, http_build_query($data));

        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     *
     * @return int|mixed
     */
    public function patch($url, $data = [])
    {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'PATCH');
        $this->setOpt(CURLOPT_POSTFIELDS, $data);

        return $this->exec();
    }

    /**
     * @param $url
     * @param array $data
     *
     * @return int|mixed
     */
    public function delete($url, $data = [])
    {
        $this->setOpt(CURLOPT_URL, $this->_buildURL($url, $data));
        $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->exec();
    }

    /**
     * @param $username
     * @param $password
     */
    public function setBasicAuthentication($username, $password)
    {
        $this->setOpt(CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $this->setOpt(CURLOPT_USERPWD, $username.':'.$password);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $key.': '.$value;
        $this->setOpt(CURLOPT_HTTPHEADER, array_values($this->_headers));
    }

    /**
     * @param $user_agent
     */
    public function setUserAgent($user_agent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $user_agent);
    }

    /**
     * @param $referrer
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);
    }

    /**
     * @param $key
     * @param $value
     */
    public function setCookie($key, $value)
    {
        $this->_cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, http_build_query($this->_cookies, '', '; '));
    }

    /**
     * @param $option
     * @param $value
     * @param null $_ch
     *
     * @return bool
     */
    public function setOpt($option, $value, $_ch = null)
    {
        $ch = null === $_ch ? $this->curl : $_ch;
        $this->_options[$option] = $value;

        return curl_setopt($ch, $option, $value);
    }

    /**
     * @param bool $on
     */
    public function verbose($on = true)
    {
        $this->setOpt(CURLOPT_VERBOSE, $on);
    }

    public function close()
    {
        if ($this->_multi_parent) {
            foreach ($this->curls as $curl) {
                curl_close($curl->curl);
            }
        }

        curl_close($this->curl);
    }

    /**
     * @param $function
     */
    public function beforeSend($function)
    {
        $this->_before_send = $function;
    }

    /**
     * @param $callback
     */
    public function success($callback)
    {
        $this->_success = $callback;
    }

    /**
     * @param $callback
     */
    public function error($callback)
    {
        $this->_error = $callback;
    }

    /**
     * @param $callback
     */
    public function complete($callback)
    {
        $this->_complete = $callback;
    }

    /**
     * @param $url
     * @param array $data
     *
     * @return string
     */
    private function _buildURL($url, $data = [])
    {
        return $url.(empty($data) ? '' : '?'.http_build_query($data));
    }

    /**
     * @param $data
     *
     * @return array|string
     */
    private function _postfields($data)
    {
        if (is_array($data)) {
            if ($this->is_array_multidim($data)) {
                $data = $this->http_build_multi_query($data);
            } else {
                // Fix "Notice: Array to string conversion" when $value in
                // curl_setopt($ch, CURLOPT_POSTFIELDS, $value) is an array
                // that contains an empty array.
                foreach ($data as $key => $value) {
                    if (is_array($value) && empty($value)) {
                        $data[$key] = '';
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param null $_ch
     *
     * @return int|mixed
     */
    protected function exec($_ch = null)
    {
        $ch = null === $_ch ? $this : $_ch;

        if ($ch->_multi_child) {
            $ch->response = curl_multi_getcontent($ch->curl);
        } else {
            $ch->response = curl_exec($ch->curl);
        }

        $ch->curl_error_code = curl_errno($ch->curl);
        $ch->curl_error_message = curl_error($ch->curl);
        $ch->curl_error = !(0 === $ch->curl_error_code);
        $ch->http_status_code = curl_getinfo($ch->curl, CURLINFO_HTTP_CODE);
        $ch->http_error = in_array(floor($ch->http_status_code / 100), [4, 5], true);
        $ch->error = $ch->curl_error || $ch->http_error;
        $ch->error_code = $ch->error ? ($ch->curl_error ? $ch->curl_error_code : $ch->http_status_code) : 0;

        $ch->request_headers = preg_split(
            '/\r\n/',
            curl_getinfo($ch->curl, CURLINFO_HEADER_OUT),
            null,
            PREG_SPLIT_NO_EMPTY
        );
        $ch->response_headers = '';
        if (!(false === mb_strpos($ch->response, "\r\n\r\n"))) {
            list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            if ('HTTP/1.1 100 Continue' === $response_header) {
                list($response_header, $ch->response) = explode("\r\n\r\n", $ch->response, 2);
            }
            $ch->response_headers = preg_split('/\r\n/', $response_header, null, PREG_SPLIT_NO_EMPTY);
        }

        $ch->http_error_message = $ch->error ? (isset($ch->response_headers['0']) ? $ch->response_headers['0'] : '') : '';
        $ch->error_message = $ch->curl_error ? $ch->curl_error_message : $ch->http_error_message;

        if (!$ch->error) {
            $ch->_call($this->_success, $ch);
        } else {
            $ch->_call($this->_error, $ch);
        }

        $ch->_call($this->_complete, $ch);

        return $ch->error_code;
    }

    /**
     * @param $function
     */
    private function _call($function)
    {
        if (is_callable($function)) {
            $args = func_get_args();
            array_shift($args);
            call_user_func_array($function, $args);
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param $array
     *
     * @return bool
     */
    protected function is_array_assoc($array)
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }

    /**
     * @param $array
     *
     * @return bool
     */
    protected function is_array_multidim($array)
    {
        if (!is_array($array)) {
            return false;
        }

        return !(count($array) === count($array, COUNT_RECURSIVE));
    }

    /**
     * @param $data
     * @param null $key
     *
     * @return string
     */
    protected function http_build_multi_query($data, $key = null)
    {
        $query = [];

        if (empty($data)) {
            return $key.'=';
        }

        $is_array_assoc = $this->is_array_assoc($data);

        foreach ($data as $k => $value) {
            if (is_string($value) || is_numeric($value)) {
                $brackets = $is_array_assoc ? '['.$k.']' : '[]';
                $query[] = urlencode(null === $key ? $k : $key.$brackets).'='.rawurlencode($value);
            } else {
                if (is_array($value)) {
                    $nested = null === $key ? $k : $key.'['.$k.']';
                    $query[] = $this->http_build_multi_query($value, $nested);
                }
            }
        }

        return implode('&', $query);
    }
}
