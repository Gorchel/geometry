<?php

namespace App\Classes\Yandex;

/**
 * Class Geocoder
 * @package App\Classes\SalesUp;
 */
class Geocoder
{
    /**
     * @var array
     */
    public $filters = [];

    /**
     *
     */
    const YA_URL = 'http://geocode-maps.yandex.ru/1.x/';

    /**
     * Geocoder constructor.
     */
    public function __construct()
    {
        $this->filters['format'] = 'json';
        $this->filters['result'] = 1;
        $this->filters['apikey'] = env('YA_KEY');
    }

    /**
     * @param string $geocode
     */
    public function setGeocode(string $geocode)
    {
        $this->filters['geocode'] = $geocode;
    }


    /**
     * @return array|null
     */
    public function request()
    {
        $responseJson = file_get_contents(self::YA_URL.'?' . http_build_query($this->filters, '', '&'));
        $response = json_decode($responseJson);

        if ($response->response->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found > 0)
        {
            return explode(' ', $response->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
        }

        return null;
    }
}
