<?php

namespace App\Classes\ARinvest;

use App\Helpers\CurlHelper;

/**
 * Class Auth
 * @package App\Classes\ARinvest;
 */
class Auth
{
    /**
     * @var mixed
     */
    public $url;

    /**
     * LinkGenerator constructor.
     */
    public function __construct()
    {
        $this->url = env('AR_INVEST_API');
    }

    /**
     *
     */
    public function run()
    {
        $url = trim($this->url, '/').'/api/auth/token/login';

        $response = CurlHelper::request($url, [
            'email' => env('AR_INVEST_USERNAME'),
            'password' => env('AR_INVEST_PASSWORD'),
        ], [
            'Content-type' => 'application/json',
        ]);

        if (!isset($response['auth_token'])) {
            return null;
        }

        return $response['auth_token'];
    }
}
