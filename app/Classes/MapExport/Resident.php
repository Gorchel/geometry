<?php

namespace App\Classes\MapExport;

/**
 * Class ApiClient
 * @package App\MapExport\Google
 */
class Resident
{
    public const DEFAULT_RADIUS = 300;

    public $url;

    /**
     * Resident constructor.
     */
    public function __construct()
    {
        $this->url = 'https://map.export-base.ru/api/resident';
    }

    /**
     * @param string $lon долгота
     * @param string $lat широта
     * @param int $radius
     * @return false|string
     */
    public function get(string $lon, string $lat, int $radius = self::DEFAULT_RADIUS)
    {
        $lon = str_replace(',', '.', $lon);
        $lat = str_replace(',', '.', $lat);

        $path = $this->url.'?radius='.$radius.'&lat='.$lat.'&lon='.$lon;

        $jsonResult = file_get_contents($path);

        $result = json_decode($jsonResult, true);
  
        if (json_last_error() === false) {
            return 0;
        }

        if (empty($result)) {
            return 0;
        }

        $resident = 0;

        foreach($result as $row) {
            $resident += $row['number_of_residents'];
        }

        return $resident;
    }
}
