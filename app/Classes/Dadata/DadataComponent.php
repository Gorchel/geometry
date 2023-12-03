<?php

namespace App\Classes\Dadata;

use Dadata\DadataClient;
use Exception;

/**
 * Class DadataComponent
 * @package App\Classes\Dadata
 */
class DadataComponent
{
    /**
     * @param string $address
     * @return array
     */
    public function getAddress(string $address)
    {
        try {
            $dadata = new DadataClient(env('DADATA_TOKEN'), env('DADATA_SECRET'));
            return $dadata->clean("address", $address);
        } catch (Exception $c) {
            return [];
        }
    }
}
