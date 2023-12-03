<?php

namespace App\Helpers;

use Exception;

/**
 * Class CurlHelper
 * @package common\helpers
 */
class CurlHelper
{
    /**
     * Constants for saving curl request information
     */
    const CURL_GETINFO = 'curl_get_info';

    /**
     * Cached last request data
     * @var null|array
     */
    private static $_curlInfo = [];

    /**
     * Return last request curl_get_info data
     * @return array
     */
    public static function getLastRequestFullInfo()
    {
        return static::$_curlInfo;
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public static function request($url, $post = null, $headers = null, $userAgent = '', $followLocation = false,
        $customMethod = null, $logErrors = false, ?string $customRequest = null)
    {
        $_post = [];

        if (is_array($post)) {
            foreach ($post as $name => $value) {
                $_post[] = $name . '=' . urlencode($value);
            }
        }

        $ch = static::curlInit($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (!empty($customMethod)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $customMethod);
        } else {
            curl_setopt($ch, CURLOPT_POST, (int)!empty($post));
        }
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followLocation);

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);

        if (is_array($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $_post));
        } elseif (is_string($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        if (isset($customRequest)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $customRequest);
        }

        if (is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($userAgent != '') {
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        }

        $result = curl_exec($ch);
        $curlErrorCode = curl_errno($ch);
        $curlError = curl_error($ch);

        $result = json_decode($result, true);

        if (json_last_error() === false) {
            throw new Exception('Curl helper json error');
        }

        return $result;
    }

    /**
     * Receive headers from requested url
     * @param string $url
     * @param string $post
     * @param string $headers
     * @param string $userAgent
     * @param bool $followLocation
     * @return null|array
     */
    public static function requestHeaders(
        string $url,
        string $post = '',
        string $headers = '',
        string $userAgent = '',
        bool $followLocation = false
    ): ?array
    {
        $_post = [];

        $ch = static::curlInit($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_POST, (int)!empty($post));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followLocation);

        if (is_array($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, join('&', $_post));
        } else if (is_string($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        if (is_array($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ($userAgent != '') {
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        }

        $result = curl_exec($ch);
        $curlErrorCode = curl_errno($ch);
        $curlError = curl_error($ch);

        return $headers;
    }

    /**
     * Curl init
     * @param null|string $url
     * @param array $params
     * @return resource
     */
    public static function curlInit($url = null, $params = [])
    {
        static::$_curlInfo = [];

        $ch = curl_init($url);

        if (!empty($proxyConfig['ip'])) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, $proxyConfig['ip'] . ':' . $proxyConfig['port']);
        }

        return $ch;
    }
}
