<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Class CustomHelper
 * @package App\Helpers
 */
class CustomHelper
{
    /**
     * @param array $customs
     * @param string $field
     * @param string $default
     * @return string|array
     */
    public static function issetField(array $customs, string $field, $default = '') {
        if (isset($customs[$field]) && !empty($customs[$field])) {
            return $customs[$field];
        }

        return $default;
    }

    /**
     * @param array $customs
     * @param string $field
     * @param string $default
     * @param bool $full
     * @return mixed|string
     */
    public static function issetFieldIncludeArray(array $customs, string $field, $default = '', $full = false) {
        if (isset($customs[$field]) && !empty($customs[$field])) {
            if (is_array($customs[$field])) {
                if (!empty($full)) {
                    return implode(',', $customs[$field]);
                }

                return $customs[$field][0];
            } else {
                return $customs[$field];
            }
        }

        return $default;
    }

    /**
     * @param $amount
     * @param int $decimal
     * @return string
     */
    public static function numberFormat($amount, $decimal = 0, $thousandsSep = ' ')
    {
        if (empty($amount)) {
            return 0;
        }

        return number_format((float)$amount, $decimal, ',', $thousandsSep);
    }

    /**
     * @param $date
     * @return string
     */
    public static function dateFormat($date)
    {
        if (empty($date)) {
            return '';
        }

        $carbonDate = Carbon::create($date);

        return $carbonDate->format('d.m.Y');
    }

    /**
     * @param $customs
     * @param array $fields
     * @param array $replaces
     * @return array
     */
    public static function generateArrayItem($customs, $fields = [], $replaces = [])
    {
        $itemsArray = [];
        $titlesArray = [];

        foreach ($fields as $field) {

            $value = CustomHelper::issetFieldIncludeArray($customs, $field['field'], '');

            if (empty($value)) {
                continue;
            }

            if (isset($field['decimal'])) {
                $value = CustomHelper::numberFormat($value, $field['decimal']);
            }

            if (isset($field['date'])) {
                $date = Carbon::create($value);
                $value = $date->format('Y-m-d');
            }

            if (isset($field['prefix'])) {
                $value = $field['prefix'].' '.$value;
            }

            if (isset($field['postfix'])) {
                $value .= ' '.$field['postfix'];
            }

//            if (!empty($replaces)) {
//                foreach ($replaces as $search => $text) {
//                    $value =  str_replace($search, $text, $value);
//                }
//            }

            $value = str_replace("&nbsp;",'',strip_tags($value));

            if (isset($field['title'])) {
                $titlesArray[] = $value;
            } else {
                $itemsArray[] = $value;
            }
        }

        return [
            'titles' => $titlesArray,
            'items' => $itemsArray
        ];
    }

    /**
     * @param $customs
     * @return array
     */
    public static function makeRentArr($customs)
    {
        return ['type' => 'array', 'tag' => 'strong', 'default' => 'Отсутствуют', 'title' => 'Арендаторы на объекте', 'firstStyles' => 'color: #ff0000;', 'child' => [
            [
                'value' => CustomHelper::generateArrayItem($customs, [
                    ['field' => 'custom-74678', 'title' => true],
                    ['field' => 'custom-88160', 'prefix' => 'этаж - '],
                    ['field' => 'custom-61705', 'decimal' => 1, 'postfix' => 'кв. м.'],
                    ['field' => 'custom-61706', 'decimal' => 0, 'postfix' => 'руб.'],
                    ['field' => 'custom-61708', 'decimal' => 0, 'postfix' => 'руб./кв.м.'],
                    ['field' => 'custom-61710', 'prefix' => 'Договор аренды до: ', 'date' => true],
                ], ['Аренда' => 'Аренда за кв. м.'])
            ],
            [
                'value' => CustomHelper::generateArrayItem($customs, [
                    ['field' => 'custom-74679', 'title' => true],
                    ['field' => 'custom-88161', 'prefix' => 'этаж - '],
                    ['field' => 'custom-63793', 'decimal' => 1, 'postfix' => 'кв. м.'],
                    ['field' => 'custom-63794', 'decimal' => 0, 'postfix' => 'руб.'],
                    ['field' => 'custom-63795', 'decimal' => 0, 'postfix' => 'руб./кв.м.'],
                    ['field' => 'custom-63797', 'prefix' => 'Договор аренды до: ', 'date' => true],
                ], ['Аренда' => 'Аренда за кв. м.'])
            ],
            [
                'value' => CustomHelper::generateArrayItem($customs, [
                    ['field' => 'custom-74680', 'title' => true],
                    ['field' => 'custom-88162', 'prefix' => 'этаж - '],
                    ['field' => 'custom-63799', 'decimal' => 1, 'postfix' => 'кв. м.'],
                    ['field' => 'custom-63800', 'decimal' => 0, 'postfix' => 'руб.'],
                    ['field' => 'custom-63801', 'decimal' => 0, 'postfix' => 'руб./кв.м.'],
                    ['field' => 'custom-63803', 'prefix' => 'Договор аренды до: ', 'date' => true],
                ], ['Аренда' => 'Аренда за кв. м.'])
            ],
            [
                'value' => CustomHelper::generateArrayItem($customs, [
                    ['field' => 'custom-74681', 'title' => true],
                    ['field' => 'custom-88163', 'prefix' => 'этаж - '],
                    ['field' => 'custom-63805', 'decimal' => 1, 'postfix' => 'кв. м.'],
                    ['field' => 'custom-63806', 'decimal' => 0, 'postfix' => 'руб.'],
                    ['field' => 'custom-63807', 'decimal' => 0, 'postfix' => 'руб./кв.м.'],
                    ['field' => 'custom-63809', 'prefix' => 'Договор аренды до: ', 'date' => true],
                ], ['Аренда' => 'Аренда за кв. м.'])
            ],
            [
                'value' => CustomHelper::generateArrayItem($customs, [
                    ['field' => 'custom-74682', 'type' => 'name'],
                    ['field' => 'custom-88164', 'prefix' => 'этаж - '],
                    ['field' => 'custom-63811', 'decimal' => 1, 'postfix' => 'кв. м.'],
                    ['field' => 'custom-63812', 'decimal' => 0, 'postfix' => 'руб.'],
                    ['field' => 'custom-63813', 'decimal' => 0, 'postfix' => 'руб./кв.м.'],
                    ['field' => 'custom-63815', 'prefix' => 'Договор аренды до: ', 'date' => true],
                ], ['Аренда' => 'Аренда за кв. м.'])
            ],
        ]];
    }
}
