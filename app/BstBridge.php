<?php

namespace App;

use App\Classes\BST\ApiPlaceClass;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BstBridge
 * @package App
 */
class BstBridge extends Model
{
    /**
     *
     */
    protected const COORDINATES_OFFSET = 0.000000000000010;

    public const STATUS_DONE = 1;
    public const STATUS_PENDING = 0;

    /**
     * @var string
     */
    protected $table      = 'bst_bridge';
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
    protected $fillable = [
        'property_id', 'bst_id', 'updated_at', 'response'
    ];

    protected static $hintMapsList = [
        [
            'column' => "pred_fast_food_exp",//Фастфуд
            'field' => 'custom-76819',
        ],
        [
            'column' => "pred_bistro_exp",//Бистро
            'hint' => "pred_bistro_exp",
            'field' => 'custom-76825',
        ],
        [
            'column' => "pred_confectionery_exp",//Кондитерская
            'hint' => "pred_confectionery_exp",
            'field' => 'custom-76826',
        ],
        [
            'column' => "pred_alco_drinks_exp",//Алкомаркет
            'hint' => "pred_alco_drinks_exp",
            'field' => 'custom-76827',
        ],
        [
            'column' => "pred_parfume_and_cosmetics_exp",//Косметика
            'hint' => "pred_parfume_and_cosmetics_exp",
            'field' => 'custom-76828',
        ],
        [
            'column' => "pred_orto_exp",//Ортопедия
            'hint' => "pred_orto_exp",
            'field' => 'custom-76829',
        ],
        [
            'column' => "pred_bakery_exp",//Пекарня
            'hint' => "pred_bakery_exp",
            'field' => 'custom-76834',
        ],
        [
            'column' => "pred_pizza_exp",//Пицца
            'hint' => "pred_pizza_exp",
            'field' => 'custom-76835',
        ],
        [
            'column' => "pred_optic_exp",//Оптика
            'hint' => "pred_optic_exp",
            'field' => 'custom-76836',
        ],
        [
            'column' => "pred_farm_exp",//Фермер
            'hint' => "pred_farm_exp",
            'field' => 'custom-76837',
        ],
//        [
//            'column' => "pred_var_2_exp",//Аптеки
//            'minValueLimit' => 15,
//            'maxValueLimit' => 74,
//            'field' => 'custom-76819',
//        ],
    ];

    /**
     * Get the phone associated with the user.
     */
    public function property()
    {
        return $this->hasOne(Properties::class, 'id', 'property_id');
    }

    /**
     * @param array $data
     * @param array $record
     */
    public static function bst2salsup(array &$data, array $record)
    {
        $data['name'] = isset($record['address']) ? $record['address'] : '';
        $data['address'] = isset($record['address']) ? $record['address']:  '';
        $data['region'] = isset($record['region__name']) ? $record['address'] : '';
        $data['subway-name'] = isset($record['metro_name']) ? $record['address'] : '';
        $data['total-area'] = isset($record['total_area']) ? $record['address'] : '';

        $data['customs']['custom-74193'] = isset($record['status__name']) ? $record['status__name'] : ''; //Статус объявления
        $data['customs']['custom-64792'] = isset($record['metro_name']) ? $record['status__name'] : ''; //Метро

        if (isset($record['base'])) {
            $base = $record['base'];

            $data['customs']['custom-64803'] = isset($base['area']) ? $base['area'] : '';//*Площадь, кв.м.
        }
    }

    /**
     * @param array $data
     * @param array $response
     */
    public static function bstGeo2salesup(array &$data, ?array $response)
    {
        if (!isset($response['data']['market']['geoEnv']['items'])) {
            return;
        }

        $checker = false;

        foreach ($response['data']['market']['geoEnv']['items'] as $item)
        {
            switch ($item['sub_title']) {
                case 'Домохозяйства (квартиры)':
//                    if ($item['sub_title'] == 'Трафик в радиусе 500м') {
//                        foreach($item['data'] as $itemData) {
//                             if ($itemData['label'] == 'Проживающие') {
//                                 $data['customs']['custom-69316'] = isset($itemData['value']) ? $itemData['value'] : ''; //радиус 500м количество человек по GPS
//                             }
//                        }
//                    } elseif ($item['sub_title'] == 'Трафик в радиусе 300м') {
//                        foreach($item['data'] as $itemData) {
//                            if ($itemData['label'] == 'Проживающие') {
//                                $value = isset($itemData['value']) ? $itemData['value'] : '';
//
//                                if ($value <= 100) {
//                                    $checker = false;
//                                }
//
//                                $data['customs']['custom-63145'] = (string)$value; //радиус 500м количество человек по GPS (Проверил)
//                            } else if ($itemData['label'] == 'Пешеходы') {
//                                $value = isset($itemData['value']) ? $itemData['value'] : '';
//
//                                if ($value <= 100) {
//                                    $checker = false;
//                                }
//
//                                $data['customs']['custom-87857'] = (string)$value; //радиус 500м количество человек по GPS (Проверил)
//                            }
//                        }
//                    } elseif ($item['sub_title'] == 'Gps трафик') {
//                        foreach($item['data'] as $itemData) {
//                            if ($itemData['label'] == 'Пешеходы') {
//                                $data['customs']['custom-69439'] = isset($itemData['value']) ? $itemData['value'] : ''; //радиус 500м количество человек по GPS
//                            }
//                        }
//                    if ($item['sub_title'] == 'Домохозяйства (квартиры)') {
                        if (isset($item['calculation_status']) && $item['calculation_status'] == 'not ready') {
                            break;
                        }

                        foreach($item['data'] as $itemData) {
//                            if ($itemData['label'] == 'д/х 500м') {
//                                $data['customs']['custom-69313'] = isset($itemData['value']) ? $itemData['value'] : ''; //радиус 500м количество человек по GPS
//                            }
                            if ($itemData['label'] == 'д/х 300м') {
                                $value = isset($itemData['value']) ? $itemData['value'] : '';

                                $checker = true;

                                $data['customs'][Properties::CUSTOM_POPULATION] = (string)$value; //радиус 500м количество человек по GPS (Проверил)
                                $data['customs']['custom-63145'] = (string)$value; //радиус 500м количество человек по GPS (Проверил)
                            }
                        }
//                    }
            }
        }

        return $checker;
    }

    /**
     * @param array $data
     * @param array $coordinates
     * @param ApiPlaceClass $bstClass
     */
    public static function bstCoordinates2salesup(array &$data, array $coordinates, ApiPlaceClass $bstClass)
    {
        $firstPoint = $coordinates;
        $secondPoint = [
            $coordinates[0] + self::COORDINATES_OFFSET,
            $coordinates[1] + self::COORDINATES_OFFSET
        ];

        $data['customs']['custom-69439'] = $bstClass->getLayersDailyUsers($firstPoint, $secondPoint);
        $data['customs']['custom-68926'] = $bstClass->getResidentPeople($firstPoint, $secondPoint);//проживающие
    }

    /**
     * @param array $data
     * @param array $coordinates
     * @param ApiPlaceClass $bstClass
     */
    public static function bstGetHitMaps(array &$data, array $coordinates, ApiPlaceClass $bstClass)
    {
        $firstPoint = $coordinates;
        $secondPoint = [
            $coordinates[0] + self::COORDINATES_OFFSET,
            $coordinates[1] + self::COORDINATES_OFFSET
        ];

        foreach (self::$hintMapsList as $hintArray) {
            $hint = null;

            if (isset($hintArray['hint'])) {
                $hint = $hintArray['hint'];
            }

            $data['customs'][$hintArray['field']] = $bstClass->bstGetHitMaps($firstPoint, $secondPoint, $hintArray['column'], $hint);
        }
    }
}
