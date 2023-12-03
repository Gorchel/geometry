<?php

namespace App\Classes\Companies;

use App\Company;

/**
 * Class FilterOrders
 * @package App\Classes\Companies;
 */
class CompaniesList
{
    /**
     * @return array
     */
    public function getList(): array
    {
        $names = [];

        Company::select('attributes')
            ->chunk(1000, function($companies) use (&$names) {
                foreach ($companies as $company) {
                    $attributesJson = json_decode($company['attributes'], true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        break;
                    }

                    $names[] = $attributesJson['name'];
                }
            });

        $names = array_unique($names);

        $assocNames = [];

        foreach ($names as $name) {
            $assocNames[] = ['value' => $name];
        }

        return $assocNames;
    }
}
