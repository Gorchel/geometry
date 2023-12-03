<?php

namespace App\Helpers;

use App\Classes\Dadata\DadataComponent;
use App\Classes\MapExport\Resident;
use App\Properties;

/**
 * Class PopulationHelper
 * @package App\Helpers
 */
class PopulationHelper
{
    /**
     * @param array $data
     * @param string $address
     */
    public static function getDadata(array &$data, string $address): array
    {
        $component = new DadataComponent();
        $result = $component->getAddress($address);

        if (!isset($data['attributes'])) {
            $data['attributes'] = [];
        }

        if (isset($result['geo_lon'])) {
            $data['attributes']['longitude'] = $result['geo_lon'];
        }

        if (isset($result['geo_lat'])) {
            $data['attributes']['latitude'] = $result['geo_lat'];
        }

        return [
            'lon' => $result['geo_lon'] ?? null,
            'lat' => $result['geo_lat'] ?? null,
        ];
    }

    /**
     * @param array $data
     * @param string $lon
     * @param string $lat
     * @return array
     */
    public static function getResidents(array &$data, string $lon, string $lat): int
    {
        if (empty($lon) || empty($lat)) {
            return 0;
        }

        $residentsClass = new Resident();
        $residents = $residentsClass->get($lon, $lat);

        if (!isset($data['attributes'])) {
            $data['attributes'] = [];
        }

        if (!empty($residents)) {
            $data['attributes']['customs'][Properties::CUSTOM_POPULATION_RESIDENTS] = $residents;
        }

        return $residents;
    }
}
