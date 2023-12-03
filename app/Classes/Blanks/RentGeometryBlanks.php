<?php

namespace App\Classes\Blanks;

use App\Helpers\CustomHelper;
use Carbon\Carbon;

/**
 * Class BuyBlanks
 * @package App\Classes\Blanks;
 */
class RentGeometryBlanks implements BlanksInterface
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

        $rules = [
            ['type' => 'record', 'title' => 'Тип сделки', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-62518', ''), 'firstStyles' => 'background-color: #a9baf6;'],
            ['type' => 'title', 'title' => 'Без комиссионных и скрытых платежей', 'styles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Адрес', 'value' => CustomHelper::issetFieldIncludeArray($attributes, 'address', ''), 'firstStyles' => 'background-color: #a9baf6;'],
            ['type' => 'record', 'title' => 'Район города', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64791', '')],
            ['type' => 'record', 'title' => 'Метро', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64792', '')],
            ['type' => 'record', 'title' => 'До метро м', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-74760', ''), 'postfix' => 'метров'],
            ['type' => 'title', 'title' => 'Характеристики объекта', 'styles' => 'background-color: #a9baf6;'],
            ['type' => 'record', 'title' => 'Тип недвижимости', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61755', '')],
            ['type' => 'record', 'title' => 'Тип объекта', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-74278', '')],
            ['type' => 'record', 'title' => 'Этаж/Этажность', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-68794', '')],
            ['type' => 'record', 'title' => 'Общая площадь', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64803', ''), 'postfix' => 'кв.м.'],
            ['type' => 'record', 'title' => 'До метро, м', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-74760', '')]
        ];

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-63254', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Наличие рядом метро', 'value' => 'Да'],
            ]);
        }

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-63255', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Наличие перекрестка', 'value' => 'Да'],
            ]);
        }

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-63256', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Угловое помещения', 'value' => 'Да'],
            ]);
        }

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-63257', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Пешеходный переход', 'value' => 'Да'],
            ]);
        }

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-63258', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'В доме сетевые компании', 'value' => 'Да'],
            ]);
        }

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-63259', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Парковка', 'value' => 'Да'],
            ]);
        }

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-63260', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Удобная планировка', 'value' => 'Да'],
            ]);
        }

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-63261', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Витринные окна', 'value' => 'Да'],
            ]);
        }

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-63262', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Вход 1 этаж', 'value' => 'Да'],
            ]);
        }

        if (CustomHelper::issetFieldIncludeArray($customs, 'custom-73222', '') == 'Да') {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Остановка транспорта', 'value' => 'Да'],
            ]);
        }

        if (!empty(CustomHelper::issetFieldIncludeArray($customs, 'custom-63263', ''))) {
            $rules = array_merge($rules, [
                ['type' => 'record', 'title' => 'Остановка транспорта', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-63263', '')],
            ]);
        }

        $rules = array_merge($rules, [
            ['type' => 'record', 'title' => 'Описание планировки', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-67055', '')],
            ['type' => 'record', 'title' => 'Варианты деления', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-74279', '')],
            ['type' => 'record', 'title' => 'Высота потолков', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64805', ''), 'postfix' => 'м'],
            ['type' => 'record', 'title' => 'Вход', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-73199', '')],
            ['type' => 'record', 'title' => 'Электрическая мощность', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-62859', ''), 'postfix' => 'кВт'],
            ['type' => 'record', 'title' => 'Ремонт', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66767', '')],
            ['type' => 'record', 'title' => 'Вентиляция', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66768', '')],
            ['type' => 'title', 'title' => 'Коммерческие условия', 'styles' => 'background-color: #a9baf6;'],
            ['type' => 'record', 'title' => 'Аренда в месяц', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-76734', '')), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Аренда кв.м', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-76733', '')), 'postfix' => 'руб./кв.м.'],

            ['type' => 'title', 'title' => 'Аналитика', 'styles' => 'background-color: #a9baf6;'],
            ['type' => 'record', 'title' => 'Плотность населения (количество квартир, радиус 300 м)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-69093', '')), 'postfix' => 'квартир'],
            ['type' => 'record', 'title' => 'Плотность населения (количество квартир, радиус 500 м)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87916', '')), 'postfix' => 'квартир'],
            ['type' => 'link', 'title' => 'Ссылка на Яндекс панораму', 'name' => 'Яндекс панорама','value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61775', '')],
            ['type' => 'link', 'title' => 'Ссылка на фото объекта', 'name' => 'Фото','value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-87808', '')],
            ['type' => 'link', 'title' => 'Ссылка на видео пешеходный трафик', 'name' => 'Пешеходный трафик','value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-65160', '')],
            ['type' => 'link', 'title' => 'Ссылка на обзорное видео', 'name' => 'Обзорное видео','value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-65161', '')],
            ['type' => 'title', 'title' => 'Дополнительно', 'styles' => 'background-color: #a9baf6;'],
            ['type' => 'oneRecord', 'title' => CustomHelper::issetFieldIncludeArray($attributes, 'description', '')],
            ['type' => 'record', 'title' => 'Дата КП', 'value' => Carbon::now()->format('d.m.Y')],
        ]);

        return $rules;
    }

    /**
     * @return array
     */
    public function footerConfig(): array
    {
        $attributes = $this->object['attributes'];
        $customs = $attributes['customs'];

        return [
            ['type' => 'record', 'title' => 'Менеджер по работе с клиентами', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66779', '')],
            ['type' => 'record', 'title' => 'Мобильный телефон', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66780', '')],
            ['type' => 'link', 'title' => 'E-mail', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66781', '')],
            ['type' => 'link', 'title' => 'Сайт', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66782', '')],
            ['type' => 'link', 'title' => 'Написать в WhatsApp', 'value' => 'wa.me/79112617788', 'logo' => 'https://i.ibb.co/pfLbcCN/whatsup.png'],
        ];
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
            'plan_three' => ['type' => 'img', 'title' => 'План 2', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61773', '')],
            'plan_two' => ['type' => 'img', 'title' => 'План 3', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61771', '')],
        ];
    }
}

