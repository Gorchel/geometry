<?php

namespace App\Classes\Blanks;

use App\Helpers\CustomHelper;

/**
 * Class BuyWhitelistBlanks
 * @package App\Classes\Blanks;
 */
class BuyWhitelistBlanks implements BlanksInterface
{
    /**
     * @var array
     */
    public $object;

    /**
     * @var array
     */
    public $params;

    /**
     * RentBlanks constructor.
     * @param array $object
     * @param array $params
     */
    public function __construct(array $object, array $params = [])
    {
        $this->object = $object;
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function config(): array
    {
        $attributes = $this->object['attributes'];
        $customs = $attributes['customs'];

        return [
            ['type' => 'record', 'title' => 'Тип сделки', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-62518', ''), 'tag' => 'strong', 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;'],
            ['type' => 'title', 'title' => 'Без комиссии!', 'tag' => 'strong', 'firstStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Адрес', 'value' => CustomHelper::issetFieldIncludeArray($attributes, 'address', '')],
            ['type' => 'nbsp'],
            ['type' => 'link', 'title' => 'Ссылка на Яндекс панораму', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61775', '')],
            ['type' => 'nbsp'],
            ['type' => 'link', 'title' => 'Ссылка на фото объекта', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-87808', '')],
            ['type' => 'nbsp'],
            ['type' => 'link', 'title' => 'Ссылка на видео пешеходный трафик', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-65160', '')],
            ['type' => 'nbsp'],
            ['type' => 'link', 'title' => 'Ссылка на обзорное видео', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-65161', '')],
            ['type' => 'nbsp'],
            ['type' => 'record', 'title' => 'Метро', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64792', '')],
            ['type' => 'record', 'title' => 'До метро м', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-74760', ''), 'postfix' => 'метров'],
            ['type' => 'record', 'title' => 'Общая площадь', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64803', ''), 'postfix' => 'кв.м.', 'secondStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Этаж/Этажность', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-68794', ''), 'secondStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Высота потолков', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64805', ''), 'postfix' => 'м'],
            ['type' => 'record', 'title' => 'Электрическая мощность', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-62859', ''), 'postfix' => 'кВт'],
            ['type' => 'record', 'title' => 'Вход', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-73199', '')],
            ['type' => 'nbsp'],
            ['type' => 'record', 'title' => 'Ремонт', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66767', '')],
            ['type' => 'record', 'title' => 'Вентиляция', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66768', '')],
            ['type' => 'nbsp'],
            CustomHelper::makeRentArr($customs),
            ['type' => 'record', 'title' => 'Аренданая плата всех арендаторов (факт в мес)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-66770', '')), 'postfix' => 'руб./мес.','firstStyles' => 'color: #ff0000;','tag' => 'strong'],
            ['type' => 'nbsp'],
            ['type' => 'record', 'title' => 'Стоимость', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-72707', ''), 0, ','), 'postfix' => 'руб.', 'tag' => 'strong', 'secondStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Стоимость кв.м', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-72709', '')), 'postfix' => 'руб./кв.м.'],
            ['type' => 'record', 'title' => 'Окупаемость (мес.)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88031', '')), 'postfix' => 'мес.', 'secondStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Окупаемость (лет)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88032', ''), 1), 'postfix' => 'лет', 'secondStyles' => 'color: #ff0000;'],
            ['type' => 'nbsp'],
            ['type' => 'title', 'title' => 'Описание', 'tag' => 'strong'],
            ['type' => 'title', 'title' => CustomHelper::issetFieldIncludeArray($attributes, 'description', '')],
        ];
    }

    /**
     * @return array
     */
    public function footerConfig(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function imgConfig(): array
    {
        $attributes = $this->object['attributes'];
        $customs = $attributes['customs'];

        return [
            'map' => ['type' => 'img', 'title' => 'Карта', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-67342', '')],
            'photo_one' => ['type' => 'img', 'title' => 'Фото 1', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-65702', '')],
            'photo_two' => ['type' => 'img', 'title' => 'Фото 2', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-65703', '')],
            'photo_three' => ['type' => 'img', 'title' => 'Фото 3', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-88194', '')],
            'plan_one' => ['type' => 'img', 'title' => 'План 1', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61772', '')],
            'plan_two' => ['type' => 'img', 'title' => 'План 2', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61773', '')],
            'plan_three' => ['type' => 'img', 'title' => 'План 3', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61771', '')],
        ];
    }
}

