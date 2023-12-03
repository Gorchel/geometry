<?php

namespace App\Classes\ARinvest;

use App\Classes\SalesUp\SalesupHandler;
use Throwable;

/**
 * Class LinkGenerator
 * @package App\Classes\ARinvest;
 */
class LinkGenerator
{
    public $url;

    /**
     * LinkGenerator constructor.
     */
    public function __construct()
    {
        $this->url = env('AR_INVEST');
    }

    /**
     * @param $lat
     * @param $lng
     * @return string
     */
    public function make($lat, $lng)
    {
        return $this->url.'?lat='.$lat.'&lng='.$lng;
    }
}
