<?php

namespace App\Classes\Filters;

use App\Helpers\CustomHelper;

/**
 * Class FilterOrders
 * @package App\Classes\Filters;
 */
class FilterOrders
{
    /**
     * @var array
     */
    public $customFields = [
        1 => [//сдам
            'type_of_property' => 'custom-67826',
            'address' => 'custom-67827',
//            'city' => [
//                'street' => [
//                    1 => ['custom' => 'custom-67921', 'type' => 'str'],//msk
//                    2 => ['custom' => 'custom-67916', 'type' => 'str'],//spb
//                ],
//                'district' => [
//                    1 => ['custom' => 'custom-67942', 'type' => 'array'],//msk
//                    2 => ['custom' => 'custom-67941', 'type' => 'array'],//spb
//                ],
//                'metro' => [
//                    1 => 'custom-67940',//msk
//                    2 => 'custom-67939',//spb
//                ],
//            ],
            'address_program' => 'custom-67911',
            'client_type' => 'custom-67822',
            'type_of_activity' => 'custom-67947',
            'footage' => 'custom-67828',
            'budget_volume' => 'custom-67829',
            'budget_footage' => 'custom-67829',//????
            'ranges' => [
                'footage' => [
                    'value' => 'custom-67828',
                ], //По площади (кв/м)
                'budget_volume' => [
                    'value' => 'custom-67829',
                ],//По бюджету, руб.мес.  в диапазоне от до
                'payback_period' => 'custom-67892',//Предполагаемый срок окупаемости
            ],
            'city_field' => 'custom-74051'
        ],
        2 => [//продам
            'type_of_property' => 'custom-67849',
            'address' => 'custom-67850',
//            'city' => [
//                'district' => [
//                    1 => ['custom' => 'custom-67945', 'type' => 'array'],//msk
//                    2 => ['custom' => 'custom-67943', 'type' => 'array'],//spb
//                ],
//                'metro' => [
//                    1 => 'custom-67946',//msk
//                    2 => 'custom-67944',//spb
//                ],
//            ],
//            'address_program' => 'custom-67911',
            'client_type' => 'custom-67822',
            'footage' => 'custom-67851',
            'budget_volume' => 'custom-67853',
            'payback_period' => 'custom-67853',
            'is_landlord' => 'custom-67855',
            'ranges' => [
                'footage' => [
                    'value' => 'custom-67851',
                ], //По площади (кв/м)
                'budget_volume' => [
                    'value' => 'custom-67853',
                ],//По бюджету, руб.мес.  в диапазоне от до
                'budget_footage' => [
                    'value' => 'custom-67854',
                ],//По бюджету за 1 кв/м в мес
                'payback_period' => 'custom-67892',//Предполагаемый срок окупаемости
            ],
            'city_field' => 'custom-74050'
        ],
        3 => [//куплю
            'type_of_property' => 'custom-67879',
            'address' => 'custom-67827',
            'ranges' => [
                'footage' => [
                    'from' => 'custom-67882',
                    'to' => 'custom-67883'
                ], //По площади (кв/м)
                'budget_volume' => [
                    'from' => 'custom-67880',
                    'to' => 'custom-67881'
                ],// По бюджету, руб.мес.  в диапазоне от до
                'payback_period' => 'custom-67892',//окупаемость
//                'payback_period' => [
//                    'from' => 'custom-67892',
//                    'to' => 'custom-76198'
//                ],//окупаемость
                'rent'  => [
                    'from' => 'custom-76199',
                    'to' => 'custom-76200'
                ],//аренда
                'income'  => [
                    'from' => 'custom-76201',
                    'to' => 'custom-76202'
                ],//аренда
            ],
            'city' => [
                'district' => [
                    1 => ['custom' => 'custom-67945', 'type' => 'array'],//msk
                    2 => ['custom' => 'custom-67943', 'type' => 'array'],//spb
                ],
                'metro' => [
                    1 => ['custom' => 'custom-67946', 'type' => 'array'],//msk
                    2 => ['custom' => 'custom-67944', 'type' => 'array'],//spb
                ],
            ],
            'city_field' => 'custom-74049'
        ],
        4 => [//сниму
            'type_of_property' => 'custom-67902',
            'ranges' => [
                'footage' => [
                    'from' => 'custom-67904',
                    'to' => 'custom-67905'
                ], //По площади (кв/м)
                'budget_volume' => [
                    'from' => 'custom-67906',
                    'to' => 'custom-67907'
                ],// По бюджету, руб.мес.  в диапазоне от до
                'budget_footage' => [
                    'from' => 'custom-67908',
                    'to' => 'custom-67909'
                ],//По бюджету за 1 кв/м в мес
            ],
            'city' => [
                'district' => [
                    1 => ['custom' => 'custom-67942', 'type' => 'array'],//msk
                    2 => ['custom' => 'custom-67941', 'type' => 'array'],//spb
                ],
                'metro' => [
                    1 => ['custom' => 'custom-67940', 'type' => 'array'],//msk
                    2 => ['custom' => 'custom-67939', 'type' => 'array'],//spb
                ],
                'street' => [
                    1 => ['custom' => 'custom-67921', 'type' => 'array'],//msk
                    2 => ['custom' => 'custom-67916', 'type' => 'str'],//spb
                ],
                'street_house' => [
                    1 => ['custom' => 'custom-67921', 'type' => 'array'],//msk
                    2 => ['custom' => 'custom-67916', 'type' => 'str'],//spb
                ],
            ],
            'type_of_activity' => 'custom-69022',
            'near_metro' => 'custom-67913',
            'city_field' => 'custom-74603',
            'address_program_send' => 'custom-82445'
        ],
    ];

    /**
     * @var array
     */
    protected $customPropertyFields = [
        3 => [//Куплю
            'budget_volume' => 'custom-61759',
            'budget_footage' => 'custom-61758',
            'actual_payback' => 'custom-61718',
            'payback_mpo' => 'custom-78661',
            'payback_yield' => 'custom-72702',
            'payback_period' => 'custom-72698',
            'actual_yield' => 'custom-61720',
            'type_of_property' => 'custom-61755',
            'type_of_activity' => 'custom-61774',
            'metro' => 'custom-65155',
            'metro_on_foot' => 'custom-64802',
            'district' => 'custom-65154',
//            'address' => 'custom-65154',
            'is_landlord' => 'custom-61757',
            'near_metro' => 'custom-64802',
            'select_vip' => 'custom-75719',
        ],
        4 => [//сниму
            'budget_volume' => 'custom-61759',
            'budget_footage' => 'custom-61758',
            'actual_payback' => 'custom-61718',
            'payback_mpo' => 'custom-78661',
            'payback_yield' => 'custom-72702',
            'payback_period' => 'custom-72698',
            'actual_yield' => 'custom-61720',
            'type_of_property' => 'custom-61755',
            'type_of_activity' => 'custom-61774',
            'metro_on_foot' => 'custom-64802',
            'metro' => 'custom-65155',
            'district' => 'custom-65154',
//            'address' => 'custom-65154',
            'is_landlord' => 'custom-61757',
            'near_metro' => 'custom-64802',
            'select_vip' => 'custom-75719',
        ]
    ];

    public static $propertyStatuses = [
        10041 => 'Активный',
        10043 => 'Архивный',
        10044 => 'Горящий',
        10304 => 'В разработке',
        10315 => 'Сдан (сдали мы)',
    ];

    /**
     * @param $order
     * @param $objData
     * @param int $typeOfObjectAddress
     * @return bool
     */
    public function filter($order, $objData, $typeOfObjectAddress = 1, $cityTypeId = 2)
    {
        $attributes = json_decode($order['attributes'], true);
        $customs = json_decode($order['customs'], true);
        $mainChecker = 0;

        $customFields = $this->customFields[$objData['object_type']];//Массив с ключами

        if ($cityTypeId == 2) {
            $searchCity = 'Санкт-Петербург';
        } else {
            $searchCity = 'Москва';
        }

        $cityByCustoms = $this->getValue('city_field', $customs, $customFields);

        if (is_array($cityByCustoms)) {
            $cityByCustoms = array_diff($cityByCustoms, ['']);

            if (!isset($cityByCustoms[0]) || empty($cityByCustoms[0])) {
                return false;
            }

            $cityByCustoms = $cityByCustoms[0];
        }

        if (strpos($cityByCustoms, $searchCity) === false) {
            return false;
        }

        //Тип недвижимости / вид деятельности
        foreach (['type_of_property','type_of_activity'] as $key) {
            if (!empty($objData[$key])) {
                $ordersValues = $this->getValue($key, $customs, $customFields);

                if (is_array($ordersValues)) {
                    $ordersValues = array_diff($this->getValue($key, $customs, $customFields), ['']);

                    if (!empty($ordersValues)) {
                        $mainChecker = 1;

                        $data = $objData[$key];

                        if (is_array($objData[$key]) && in_array('Все', $data)) {
                            continue;
                        }

                        //Ограничения по виду деятельности
                        if (is_array($objData[$key]) && $key == 'type_of_activity') {
                            if (isset($objData['except_type_of_activity']) && is_array($objData['except_type_of_activity']) && !empty($objData['except_type_of_activity'])) {
                                $data = array_diff($data, $objData['except_type_of_activity']);
                            }
                        }

                        if (is_array($objData[$key]) && empty(array_intersect($ordersValues, $data))) {
                            return false;
                        }
                    } else {//test
                        return false;
                    }

                    if (!empty($objData['findByAll'])) {
                        return true;
                    }
                }
            }
        }

        //Значение/массив / Адресная программа / тип клиента
        foreach (['address_program','address_program_send','client_type','near_metro'] as $key) {
            if (!empty($objData[$key])) {
                $ordersValues = $this->getValue($key, $customs, $customFields);

                if (is_array($ordersValues)) {
                    $ordersValues = array_diff(array_map('trim', $ordersValues), ['']);

                    if (!empty($ordersValues)) {
                        $mainChecker = 1;

                        if (!in_array($objData[$key], $ordersValues)) {
                            return false;
                        }
                    } else {//test
                        return false;
                    }

                    if (!empty($objData['findByAll'])) {
                        return true;
                    }
                }
            }
        }

        //Улица, Дом, район
        foreach (['district','street','street_house'] as $key) {
            if (empty($objData[$key])) {//Если пустое значение поля
                continue;
            }

            $valueArray = array_map('trim', explode(',',trim(mb_strtolower($objData[$key]))));//Значение в фильтре

            if (!isset( $customFields['city'])) {
                continue;
            }

            if (!isset( $customFields['city'][$key])) {
                continue;
            }

            $customArray = $customFields['city'][$key][$typeOfObjectAddress];//Значения в поле

            //Проверяем наличие
            if (!isset($customs[$customArray['custom']]) || empty($customs[$customArray['custom']])) {
                return false;
            }

            //проверяем по городам
            $checker = 0;

            $objectValue = $customs[$customArray['custom']];//Значение в заявке

            if ($customArray['type'] != 'array' || is_string($objectValue)) {
                $objectValue = array_diff(array_map('trim', explode(',',trim(mb_strtolower($objectValue)))),['']);
            } else {
                $objectValue = array_diff(array_map('mb_strtolower', $objectValue),['']);
            }

            if (empty($objectValue)) {
//                continue;
                return false;
            }

            $mainChecker = 1;

            foreach ($objectValue as $objVal) {//Поиск по полю в заявке
                foreach ($valueArray as $value) {//Значение в фильтре
                    if (strpos($objVal, $value) !== false) {
                        $checker = 1;

                        if (!empty($objData['findByAll'])) {
                            return true;
                        }
                    }
                }
            }

            if ($checker == 0) {
                return false;
            }
        }

        //Проверяем метро
        if (!empty($objData['metro'])) {
            if (isset($customFields['city'])) {
                $valueArray = $objData['metro'];//Значение в фильтре

                $objectValue = CustomHelper::issetField($customs, $customFields['city']['metro'][$typeOfObjectAddress]['custom'], []);
                $objectValue = array_diff($objectValue, ['']);//Значение в заявке

                if (!empty($objectValue)) {
                    $mainChecker = 1;

                    if (empty(array_intersect($objectValue, $valueArray))) {
                        return false;
                    }
                } else {//test
                    return false;
                }

                if (!empty($objData['findByAll'])) {
                    return true;
                }
            }
        }

//        проверяем по площади
        foreach (['footage','budget_volume','budget_footage'] as $key) {
            if (empty($objData[$key])) {//Если пустое значение поля
                continue;
            }

            if (!isset($customFields['ranges'][$key])) {
                continue;
            }

            $ranges = $customFields['ranges'][$key];//from/to

            if (isset($ranges['value'])) {
                $value = intval(CustomHelper::issetField($customs, $ranges['value']));

                //Корректировка тысяч
                if ($key == 'budget_volume') {
                    $value = $value * 1000;
                }

                if (empty($value)) {
                    return false;//test
//                    continue;
                }

                $mainChecker = 1;

                $crossInterval = $this->crossingIntervalByValue($value, $objData[$key][0], $objData[$key][1]);
            } else {
                $from = intval(CustomHelper::issetField($customs, $ranges['from']));
                $to = intval(CustomHelper::issetField($customs, $ranges['to']));

//                //Корректировка тысяч
//                if ($key == 'budget_volume') {
//                    $from = $from * 1000;
//                    $to = $to * 1000;
//                }

                if (empty($from) && empty($to)) {
//                    continue;
                    return false;//test
                }


                if (empty($from)) {
                    $from = 0;
                }

                if (empty($to)) {
                    $to = 99999999999;
                }

                $mainChecker = 1;

//                if ($order['id'] == 714395 && $key == 'budget_volume') {
//                    dd($from.' '.$to);
//                }

                $crossInterval = $this->crossingInterval($objData[$key][0], $objData[$key][1], $from, $to);

            }

            if (empty($crossInterval)) {
                return false;
            }

            if (!empty($objData['findByAll'])) {
                return true;
            }
        }

//        dd($mainChecker);

        //Предполагаемый срок окупаемости в мес
        if (!empty($objData['payback_period'])) {
            if (isset($customFields['ranges']['payback_period'])) {
                $paybackValue = intval(CustomHelper::issetField($customs, $customFields['ranges']['payback_period']));//payback_period

                if (!empty($paybackValue)) {
                    $mainChecker = 1;

                    $from = intval($objData['payback_period'][0]);
                    $to = intval($objData['payback_period'][1]);

                    if ($paybackValue < $from || $to < $paybackValue) {
                        return false;
                    }
                } else {
                    return false;//test
                }

                if (!empty($objData['findByAll'])) {
                    return true;
                }
            }
        }

        if ($mainChecker == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param $object
     * @param $objData
     * @param int $typeOfObjectAddress
     * @return bool
     */
    public function filterProperty($property, $objData, $typeOfObjectAddress = 1, $object_type)
    {
        $customFields = $this->customPropertyFields[$object_type];//Массив с ключами

        $attributes = json_decode($property['attributes'], true);
        $customs = json_decode($property['customs'], true);
        $transformationRelation = json_decode($property['transformation_relation'], true);

        $mainChecker = 0;
//        dd(json_decode($property['relationships'], true));
        //Тип недвижимости / Адресная программа / тип клиента / вид деятельности
        foreach (['type_of_property','type_of_activity','metro'] as $key) {
            if (!empty($objData[$key])) {
                $ordersValues = array_diff($this->getValue($key, $customs, $customFields), ['']);

                if (!empty($ordersValues)) {
                    $mainChecker = 1;
                    if (empty(array_intersect($ordersValues, $objData[$key]))) {
                        return false;
                    }
                } else {//test
                    return false;
                }

                if (!empty($objData['findByAll'])) {
                    return true;
                }
            }
        }

        if (!empty($objData['near_metro'])) {
            $ordersValues = $this->getValue('near_metro', $customs, $customFields);

            if (!empty($ordersValues)) {
                $mainChecker = 1;

                if ($ordersValues > 3) {
                    return false;
                }

                if (!empty($objData['findByAll'])) {
                    return true;
                }
            }
        }

        if (!empty($objData['property_status']) && isset($transformationRelation['status'])) {
            $mainChecker = 1;

            if (!in_array($transformationRelation['status'], $objData['property_status'])) {
                return false;
            }

            if (!empty($objData['findByAll'])) {
                return true;
            }
        }

        //проверка есть ли арендатор
        foreach (['is_landlord', 'select_vip'] as $key) {
            if (!empty($objData[$key])) {
                $value = trim($objData[$key]);

                $ordersValues = array_diff($this->getValue($key, $customs, $customFields), ['']);
                if (!empty($ordersValues)) {
                    $mainChecker = 1;
                    if (!in_array($value, $ordersValues)) {
                        return false;
                    }
                } else {//test
                    return false;
                }

                if (!empty($objData['findByAll'])) {
                    return true;
                }
            }
        }

        //проверяем по площади
        if (!empty($objData['footage'])) {//Если пустое значение поля
            $value = intval($attributes['total-area']);
            if (!empty($value)) {
                $mainChecker = 1;

                $crossInterval = $this->crossingIntervalByValue($value, $objData['footage'][0], $objData['footage'][1]);

                if (empty($crossInterval)) {
                    return false;
                }
            } else {//test
                return false;
            }

            if (!empty($objData['findByAll'])) {
                return true;
            }
        }

        foreach (['budget_volume','budget_footage'] as $key) {
            if (empty($objData[$key])) {//Если пустое значение поля
                continue;
            }

            $value = intval(CustomHelper::issetField($customs,$customFields[$key]));

            if (empty($value)) {
//                continue;
                return false;
            }

            $mainChecker = 1;

            //Корректировка тысяч
//            if ($key == 'budget_volume') {
//                $value = $value / 1000;
//            }

            $crossInterval = $this->crossingIntervalByValue($value, $objData[$key][0], $objData[$key][1]);

            if (empty($crossInterval)) {
                return false;
            }

            if (!empty($objData['findByAll'])) {
                return true;
            }
        }

        //район
        foreach (['district'] as $key) {
            if (empty($objData[$key])) {//Если пустое значение поля
                continue;
            }

            $valueArray = array_map('trim', explode(',',trim(mb_strtolower($objData[$key]))));//Значение в фильтре

            if (!isset( $customFields[$key])) {
                continue;
            }

            $customArray = $customFields[$key];//Значения в поле

            //проверяем по городам
            $checker = 0;

            //Проверяем наличие
//            if (!isset([$customs])) {
//                continue;
//            }

            $objectValue = CustomHelper::issetField($customs, $customArray, []);//Значение в заявке
            $objectValue = array_diff(array_map('mb_strtolower', $objectValue),['']);

            if (empty($objectValue)) {
//                continue;
                return false;
            }

            $mainChecker = 1;

            foreach ($objectValue as $objVal) {//Поиск по полю в заявке
                foreach ($valueArray as $value) {//Значение в фильтре
                    if (empty($value)) {
                        continue;
                    }

                    if (strpos($objVal, $value) !== false) {
                        $checker = 1;

                        if (!empty($objData['findByAll'])) {
                            return true;
                        }
                    }
                }
            }

            if ($checker == 0) {
                return false;
            }
        }

        //Проверяем по адресу
        foreach (['street','region'] as $key) {
            if (empty($objData[$key])) {//Если пустое значение поля
                continue;
            }

            $valueArray = array_map('trim', explode(',',trim(mb_strtolower($objData[$key]))));//Значение в фильтре

            if (empty($valueArray)) {//Если пустое значение поля
                continue;
            }

            $checker = 0;
            $mainChecker = 1;

            $objectValue = trim(mb_strtolower($attributes['address']));

//            $objectValue = array_map('trim', explode(',',trim(mb_strtolower($attributes['address']))));//Значение в поле
//            $objectValue = array_diff(array_map('mb_strtolower', $objectValue),['']);
//
            if (empty($objectValue)) {
//                continue;
                return false;
            }

            foreach ($valueArray as $value) {//Значение в фильтре
                if (empty($value)) {
                    continue;
                }

                //Разбиваем по пробелу
                $bspArr = array_map('trim', explode(' ',trim($value)));
                $bspChecker = 1;

                foreach ($bspArr as $bspValue) {
                    if (strpos($objectValue, $bspValue) === false) {
                        $bspChecker = 0;
                    }
                }

                if ($bspChecker == 1) {
                    $checker = 1;

                    if (!empty($objData['findByAll'])) {
                        return true;
                    }
                }
            }

//            dd($valueArray);

//            foreach ($objectValue as $objVal) {//Поиск по полю в заявке
//                foreach ($valueArray as $value) {//Значение в фильтре
//                    if (empty($value)) {
//                        continue;
//                    }
//
//                    if (strpos($objVal, $value) !== false) {
//                        $checker = 1;
//                    }
//                }
//            }

            if ($checker == 0) {
                return false;
            }
        }

        foreach (['payback_period','actual_payback','payback_mpo','payback_yield','actual_yield'] as $key) {
            if (!empty($objData[$key])) {//Если пустое значение поля
                $value = intval(CustomHelper::issetField($customs, $customFields[$key]));

                if (!empty($value)) {
//                    $interval = explode()
                    $mainChecker = 1;
                    $crossInterval = $this->crossingIntervalByValue($value, (int)$objData[$key][0], (int)$objData[$key][1]);

                    if (empty($crossInterval)) {
                        return false;
                    }
                } else {//test
                    return false;
                }

                if (!empty($objData['findByAll'])) {
                    return true;
                }
            }
        }

        if ($mainChecker == 0) {
            return false;
        }

        return true;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function getCustomArray($type, $field)
    {
        if (isset($this->customFields[$type][$field])) {
            return $this->customFields[$type][$field];
        }

        return null;
    }

    /**
     * @param $type
     * @param $field
     * @return mixed|null
     */
    public function getCustomPropertyArray($type)
    {
        if (isset($this->customPropertyFields[$type])) {
            return $this->customPropertyFields[$type];
        }

        return null;
    }

    /**
     * @param $startInt
     * @param $finishInt
     * @param $startValue
     * @param $finishValue
     * @return int
     */
    public function crossingInterval($startInt, $finishInt, $startValue, $finishValue) {
        if (
            ($startValue >= $startInt && $startValue <= $finishInt) ||
            ($finishValue <= $finishInt && $finishValue >= $startInt) ||
            ($startValue <= $finishInt && $finishValue >= $startInt) ||
            ($startValue >= $startInt && $finishValue <= $finishInt)
        ) {
            return 1;
        }

//        if (
//            ($startValue >= $startInt && $startValue <= $finishInt) &&
//            ($finishValue <= $finishInt && $finishValue >= $startInt)
//        ) {
//            return 1;
//        }

        return 0;
    }

    /**
     * @param $field
     * @param $startInt
     * @param $finishInt
     * @return int
     */
    public function crossingIntervalByValue($field, $startInt, $finishInt) {
        if (
            $field >= $startInt && $field <= $finishInt
        ) {
            return 1;
        }

        return 0;
    }

    /**
     * @param $key
     * @param $customs
     * @param $customArray
     * @return mixed
     */
    protected function getValue($key, $customs, $customArray)
    {
        if (!isset($customArray[$key])) {
            return false;
        }

        return CustomHelper::issetField($customs, $customArray[$key]);
    }
}
