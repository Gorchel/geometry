<?php

namespace App\Classes\ARinvest;

use App\Classes\ARinvest\Properties as ARProperty;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CustomHelper;
use App\Properties;
use App\PropertyToAr;
use Exception;

/**
 * Class Properties
 * @package App\Classes\ARinvest;
 */
class PropertySender
{
    /**
     * @param int $id
     * @return bool|mixed|string
     */
    public function send(Properties $property) {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;
        $object = $methods->getObject($property['id']);

        if (empty($object)) {
            return false;
        }

        $typeArr = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_TYPE, []);//Слайдеры
        $price = 0;

        if (!empty($typeArr) && isset($typeArr[0])) {
            if ($typeArr[0] == 'Продажа') {
                $price = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_ADVERT_PRICE);
            } else {
                $price = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_ADVERT_RENT_PRICE);
            }
        }

        if ($price == 0) {
            $property->update_details = 'Empty price';
            $property->save();

            return false;
        }

        try {
            $longitude = CustomHelper::issetField($object['attributes'], 'longitude', 0);
            $latitude = CustomHelper::issetField($object['attributes'], 'latitude', 0);

            if (empty($longitude) || empty($latitude)) {
                throw new Exception('Empty coordinates');
            }

            $arProperty = new ARProperty();
            $arProperty->setParams([
                 'coordinates_width' => $longitude,
                 'coordinates_longitude' => $latitude,
                 'name' => CustomHelper::issetField($object['attributes'], 'name', 'Name not found'),
                 'addres' => CustomHelper::issetField($object['attributes'], 'address', 'Address not found'),
                 'area_ads' => CustomHelper::issetField($object['attributes'], 'total-area', 'Area not found'),
                 'source' => CustomHelper::issetFieldIncludeArray($object['attributes']['customs'], Properties::CUSTOM_SOURCE),
                 'type_ads' => CustomHelper::issetFieldIncludeArray($object['attributes']['customs'], Properties::CUSTOM_TYPE),
                 'object_ads' => CustomHelper::issetFieldIncludeArray($object['attributes']['customs'], Properties::CUSTOM_TYPE_OF_PROPERTY),
                 'floor' => CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_FLOOR),
                 'price_ads' => $price,
                 'category' => CustomHelper::issetFieldIncludeArray($object['attributes']['customs'], Properties::CUSTOM_TYPE_OF_ACTIVITY),
                 'status' => 'active',
                 'link_ads' => CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_PROPERTY_LINK, ''),
            ]);

            $arProperty->setSRID();

            $arResponse = $arProperty->create();

            $property->update_details = json_encode($arResponse);

            if (isset($arResponse['id'])) {
                $propertyToAr = PropertyToAr::getModel($property['id'], $arResponse['id']);
                $propertyToAr->response = json_encode($arResponse);
                $propertyToAr->save();
            }

            $property->save();
        } catch (Exception $exception) {
            $property->update_details = $exception->getMessage();
            $property->save();
        }

        return true;
    }
}
