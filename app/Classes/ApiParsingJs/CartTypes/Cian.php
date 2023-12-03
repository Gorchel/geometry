<?php

namespace App\Classes\ApiParsingJs\CartTypes;

use App\Classes\SalesUp\SalesupHandler;
use App\Properties;

/**
 * Class Torgi
 * @package App\Classes\ApiParsingJs\CartTypes
 *
 * https://torgi.gov.ru/new/api/public/lotcards/22000159970000000044_1
 */
class Cian implements CartInterface
{

    /**
     * @param array $result
     * @return int|mixed
     */
    public function getTotal(array $result)
    {
        if (isset($result['priceTotal']) && !empty($result['priceTotal'])) {
            return $result['priceTotal'];
        }

        if (isset($result['priceTotalPerMonth']) && !empty($result['priceTotalPerMonth'])) {
            return $result['priceTotalPerMonth'];
        }

        if (isset($result['priceTotalPerMonthRur']) && !empty($result['priceTotalPerMonthRur'])) {
            return $result['priceTotalPerMonthRur'];
        }
        if (isset($result['bargainTerms']['priceRur']) && !empty($result['bargainTerms']['priceRur'])) {
            return $result['bargainTerms']['priceRur'];
        }



        return 0;
    }

    /**
     * @param array $result
     * @return string
     */
    public function getStatus(array $result = []): string
    {
        return isset($result['status']) ? $result['status'] : '';
    }

    /**
     * @return array
     */
    public function getSuccessStatuses(): array
    {
            return ['draft', 'published'];
    }

    /**
     * @param array $result
     * @return int|mixed
     */
    public function getAddress(array $result)
    {
        return isset($result['geo']['userInput']) ? $result['geo']['userInput'] : 0;
    }

    /**
     * @param array $result
     * @return int|mixed
     */
    public function getDescription(array $result)
    {
        return isset($result['description']) ? $result['description'] : 0;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return Properties::CUSTOM_SOURCE_CIAN;
    }

    /**
     * @param array $data
     * @param array $result
     * @return array
     */
    public function customData(array &$data, array $result): array
    {
        $data['attributes']['latitude'] = isset($result['geo']['coordinates']['lat']) ? $result['geo']['coordinates']['lat'] : '';
        $data['attributes']['longitude'] = isset($result['geo']['coordinates']['lng']) ? $result['geo']['coordinates']['lng'] : '';
        $data['attributes']['business-usage-type'] = Properties::BUSINESS_USAGE_TYPE_RETAIL; //Вид торгов
        $data['attributes']['object-type'] = Properties::OBJECT_TYPE_RETAIL; //Вид торгов
        $data['attributes']['customs']['custom-61755'] = isset($result['officeType']) ? static::getOfferType($result['officeType']) : ''; //Тип недвижимости
        $data['attributes']['customs']['custom-84066'] = Properties::CUSTOM_SOURCE_CIAN; //Источник
        $dealType = isset($result['dealType']) ? $result['dealType'] : null;

        if (!empty($dealType)) {
            if ($dealType == 'sale') {
                $data['attributes']['customs']['custom-62518'] =  'Продажа'; //Тип обьекта
                $data['attributes']['customs']['custom-87644'] = $this->getTotal($result);
                $data['attributes']['customs']['custom-87647'] = isset($result['totalArea']) ? round($this->getTotal($result) / $result['totalArea']) : 0;;
            } else {
                $data['attributes']['customs']['custom-62518'] =  'Аренда'; //Тип обьекта
                $data['attributes']['customs']['custom-87645'] = isset($result['totalArea']) ? round($this->getTotal($result) / $result['totalArea']) : 0;
                $data['attributes']['customs']['custom-87646'] = $this->getTotal($result);
                $data['attributes']['customs']['custom-69107'] = $this->getTotal($result);
            }
        }

        $data['attributes']['customs']['custom-74193'] = isset($result['status']) ? $this->getStatusName($result['status']) : ''; //Статус
        $data['attributes']['customs']['custom-88239'] = isset($result['status']) ? $this->getStatusName($result['status']) : ''; //Статус
        $data['attributes']['total-area'] = isset($result['totalArea']) ? $result['totalArea'] : ''; //Площадь
        $data['attributes']['customs']['custom-64803'] = isset($result['totalArea']) ? $result['totalArea'] : ''; //Площадь

        $data['attributes']['customs']['custom-88242'] = isset($result['description']) ? $result['description'] : ''; //Описание лота

        //Ownerf
        $owner = isset($result['commercialOwnership']) ? $result['commercialOwnership'] : '';
        $ownerType = 'Собственник';

        if (!empty($owner)) {
            if (empty($owner['isFromOwner'])) {
                $ownerType = 'Представитель';
            }
        }
        $data['attributes']['customs']['custom-73798'] = !empty($owner['isFromOwner']) ? 'Собственник' : 'Представитель'; //Тип контакта

        $data['attributes']['customs']['custom-68794'] = isset($result['floorNumber']) ? $result['floorNumber'] : '';//Этаж

        $undergrounds = isset($result['undergrounds']) ? $result['undergrounds'] : [];

        if (!empty($undergrounds)) {
            $undergroundValue = '';
            foreach ($undergrounds as $underground) {
                $undergroundValue .= $underground['name'].',';
            }

            $data['attributes']['customs']['custom-64792'] = trim($undergroundValue, ',');
        }

        $districts = isset($result['districts']) ? $result['districts'] : [];

        if (!empty($districts)) {
            foreach ($districts as $district) {
                if ($district['type'] == 'raion') {
                    $data['attributes']['customs']['custom-64791'] = $district['name'];
                }
            }
        }

        //Информация по дому
        $building = isset($result['building']) ? $result['building'] : [];

        if (!empty($building)) {
            $buildYear = isset($building['buildYear']) ? $building['buildYear'] : '';
            $passengerLiftsCount = isset($building['passengerLiftsCount']) ? $building['passengerLiftsCount'] : '';
            $parking = isset($building['parking']) ? $building['parking'] : '';

            $buildingDescription = '';

            if (!empty($buildYear)) {
                $buildingDescription .= "Год постройки: ".$buildYear.",";
            }

            if (!empty($passengerLiftsCount)) {
                $buildingDescription .= "Кол-во лифтов: ".$passengerLiftsCount.",";;
            }

//            if (!empty($parking)) {
//                $buildingDescription .= "Паркинг: ".$parking.",";;
//            }

            if (!empty($buildingDescription)) {
                $data['attributes']['description'] .= trim($buildingDescription, ',');
            }
        }

        $phone = isset($result['phones'][0]) ? $result['phones'][0] : [];//Телефон
        $userName = $ownerType.' '.(isset($result['user']['cianUserId']) ? $result['user']['cianUserId'] : '');

        $contactData = [
            'attributes' => [
                'general-phone' => $phone['countryCode'].$phone['number'],
                'first-name' => $userName,
            ]
        ];

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $contact = $methods->contactCreate($contactData);

        if (isset($contact['data']['id'])) {
            $data['relationships']['contact'] = [
                'data' => [
                    'type' => 'contacts',
                    'id' => $contact['data']['id'],
                ]
            ];
        }

        return $data;
    }

    /**
     * @param array $result
     * @return int|mixed
     */
    public function getPhotos(array $result)
    {
        $photos = [];

        if (isset($result['photos'])) {
            foreach($result['photos'] as $photo) {
                $nameArr = explode('/', $photo['fullUrl']);

                $photos[] = [
                    'name' => $nameArr[array_key_last($nameArr)],
                    'url' => $photo['fullUrl']
                ];
            }
        }
        return $photos;
    }

    /**
     * @param string $text
     * @param array $replaced
     * @return string|string[]
     */
    protected function replace(string $text, array $replaced)
    {
        foreach ($replaced as $value) {
            $text = str_replace($value, '', $text);
        }

        return trim($text);
    }


    /**
     * @param string $status
     * @return mixed|string
     */
    public function getStatusName(string $status): string
    {
        $statuses = static::getStatuses();

        if (isset($statuses[$status])) {
            return $statuses[$status];
        }

        return $status;
    }

    /**
     * @return array
     */
    public static function getStatuses(): array {
        return [
            'draft' => 'Отменен',
            'published' => 'Опубликован',
            'deleted' => 'Завершен',
            'deactivated' => 'Завершен',
        ];
    }

    /**
     * @param string $type
     * @return string
     */
    public static function getOfferType(string $type): string
    {
        $types = [
            'freeAppointment' => 'Свободное назначение',
            'building' => 'Здание',
            'floorSpace' => 'Торговая площадь',
        ];

        if (isset($types[$type])) {
            return $types[$type];
        }

        return $type;
    }
}
