<?php

namespace App\Classes\Zenrows;

/**
 * Class ZenrowsRequest
 * @package App\Classes\Zenrows
 */
class ZenrowsRequest
{
    /**
     * @var mixed
     */
    private $apikey;

    /**
     * @var string
     */
    private $settings = '';

    /**
     * ZenrowsRequest constructor.
     */
    public function __construct()
    {
        $this->apikey = env('ZENROWS_APIKEY');
    }

    /**
     *
     */
    public function setPremiumProxy()
    {
        $this->settings .= 'premium_proxy=true&proxy_country=ru';
    }

    /**
     * @param string $url
     */
    public function get(string $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROXY, 'http://'.$this->apikey.':'.$this->settings.'@proxy.zenrows.com:8001');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
