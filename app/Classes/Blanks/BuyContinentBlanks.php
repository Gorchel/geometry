<?php

namespace App\Classes\Blanks;

use App\Helpers\CustomHelper;
use Carbon\Carbon;

/**
 * Class BuyContinentBlanks
 * @package App\Classes\Blanks;
 */
class BuyContinentBlanks implements BlanksInterface
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

        $data = [
            'first' => [
                ['type' => 'record', 'title' => 'Адрес Обьекта', 'value' => CustomHelper::issetFieldIncludeArray($attributes, 'address', ''), 'firstStyles' => 'color: #ff0000; margin-top: 24px;', 'secondTag' => 'strong'],
                ['type' => 'title', 'title' => 'Без комиссии!', 'tag' => 'strong', 'styles' => 'color: #ff0000;'],
                ['type' => 'record', 'title' => 'Тип сделки', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-62518', ''), 'firstStyles' => 'color: #ff0000; margin-top: 24px;'],
                ['type' => 'record', 'title' => 'Тип недвижимости', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61755', ''), 'firstStyles' => 'color: #ff0000; margin-top: 24px;'],
            ],
            'second' => [
                ['type' => 'link', 'title' => 'Ссылка на Яндекс панораму', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61775', ''), 'firstStyles' => 'color: #4e74c0;'],
            ],
        ];

        $thirty = [
            ['type' => 'title', 'title' => 'Характеристики объекта', 'styles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Общая площадь', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64803', ''), 'postfix' => 'кв.м.'    ],
            ['type' => 'record', 'title' => 'Этаж/Этажность', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-68794', '')],
            ['type' => 'record', 'title' => 'Электрическая мощность', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-62859', ''), 'postfix' => 'кВт'],
            ['type' => 'record', 'title' => 'Высота потолков', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64805', ''), 'postfix' => 'м'],
            ['type' => 'link', 'title' => 'Ссылка на фото объекта', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-87808', ''), 'firstStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Описание', 'value' => CustomHelper::issetFieldIncludeArray($attributes, 'description', ''), 'firstStyles' => 'color: #ff0000'],
        ];

        if (!isset($this->params['analytic'])) {
            $thirty = array_merge($thirty, [
                ['type' => 'title', 'title' => 'Расчет окупаемости', 'styles' => 'color: #ff0000;'],
                ['type' => 'record', 'title' => 'Аренда в мес.', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-66770', '')), 'postfix' => 'руб.'],
                ['type' => 'record', 'title' => 'Окупаемость (мес.)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88031', '')), 'postfix' => 'мес.'],
                ['type' => 'record', 'title' => 'Окупаемость (лет)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88032', ''))],
                ['type' => 'record', 'title' => 'Доходность % в год (факт)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88033', ''), 1), 'postfix' => '%'],
                ['type' => 'record', 'title' => 'Плотность населения (количество квартир, радиус 300 м)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-69093', '')), 'postfix' => 'квартир'],
                ['type' => 'record', 'title' => 'Плотность населения (количество квартир, радиус 500 м)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87916', '')), 'postfix' => 'квартир'],
                ['type' => 'title', 'title' => 'Аналитика. Предполагаемая окупаемость', 'styles' => 'color: #ff0000;'],
                ['type' => 'record', 'title' => 'Предполагаемая аренда кв.м', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87923', '')), 'postfix' => 'руб.'],
                ['type' => 'record', 'title' => 'Предполагаемая аренда в мес.', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87924', '')), 'postfix' => 'руб.'],
                ['type' => 'record', 'title' => 'Предполагаемая окупаемость, мес.', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88034', '')), 'postfix' => 'мес.'],
                ['type' => 'record', 'title' => 'Предполагаемая окупаемость, лет.', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88035', ''), 1), 'postfix' => 'лет'],
                ['type' => 'record', 'title' => 'Предполагаемая доходность % в год', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88036', ''), 1), 'postfix' => '%'],
            ]);
        }

        $thirty = array_merge($thirty, [
            CustomHelper::makeRentArr($customs)
        ]);

        $data['thirty'] = $thirty;

        return $data;
    }

    /**
     * @return array
     */
    public function footerConfig(): array
    {
        $attributes = $this->object['attributes'];
        $customs = $attributes['customs'];

        return [
            ['type' => 'title', 'title' => 'Контактная информация', 'styles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Менеджер по работе с клиентами', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-84553', '')],
            ['type' => 'record', 'title' => 'Мобильный телефон', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-84554', '')],
            ['type' => 'link', 'title' => 'E-mail', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-84555', '')],
            ['type' => 'record', 'title' => 'Дата КП', 'value' => Carbon::now()->format('d.m.Y'), 'firstStyles' => 'color: #ff0000; margin-top: 24px;'],
            ['type' => 'link', 'title' => 'Написать в WhatsApp', 'value' => 'wa.me/79052774455', 'logo' => 'https://i.ibb.co/pfLbcCN/whatsup.png'],
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
            'plan_two' => ['type' => 'img', 'title' => 'План 2', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61771', '')],
            'plan_three' => ['type' => 'img', 'title' => 'План 3', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61773', '')],
        ];
    }
}

