<?php

namespace App\Classes\Blanks;

use App\Helpers\CustomHelper;

/**
 * Class BuyWhitelistNewBlanks
 * @package App\Classes\Blanks;
 */
class BuyWhitelistNewBlanks implements BlanksInterface
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
            ['type' => 'record', 'title' => 'Тип сделки', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-62518', ''), 'tag' => 'strong', 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;','postfix' => ' с торгов!'],
            ['type' => 'nbsp'],
            ['type' => 'record', 'title' => 'Адрес', 'value' => CustomHelper::issetFieldIncludeArray($attributes, 'address', '')],
            ['type' => 'link', 'title' => 'Ссылка на Яндекс панораму', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-61775', '')],
            ['type' => 'link', 'title' => 'Ссылка на объект Торги', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-88195', '')],
            ['type' => 'link', 'title' => 'Ссылка на электронную площадку', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-88250', '')],
            ['type' => 'link', 'title' => 'Ссылка на фото объекта', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-87808', '')],
            ['type' => 'link', 'title' => 'Ссылка на видео пешеходный трафик', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-65160', '')],
            ['type' => 'link', 'title' => 'Ссылка на обзорное видео', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-65161', '')],
            ['type' => 'record', 'title' => 'Метро', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64792', '')],
            ['type' => 'record', 'title' => 'До метро м', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-74760', ''), 'postfix' => 'метров'],
            ['type' => 'record', 'title' => 'Общая площадь', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64803', ''), 'postfix' => 'кв.м.', 'secondStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Этаж/Этажность', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-68794', ''), 'secondStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Высота потолков', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-64805', ''), 'postfix' => 'м'],
            ['type' => 'record', 'title' => 'Электрическая мощность', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-62859', ''), 'postfix' => 'кВт'],
            ['type' => 'record', 'title' => 'Вход', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-73199', '', true)],
            ['type' => 'nbsp'],
            ['type' => 'record', 'title' => 'Ремонт', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66767', '')],
            ['type' => 'record', 'title' => 'Вентиляция', 'value' => CustomHelper::issetFieldIncludeArray($customs, 'custom-66768', '')],
            ['type' => 'nbsp'],
            CustomHelper::makeRentArr($customs),
            ['type' => 'record', 'title' => 'Аренданая плата всех арендаторов (факт в мес)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-66770', '')), 'postfix' => 'руб./мес.','firstStyles' => 'color: #ff0000;','tag' => 'strong'],
            ['type' => 'record', 'title' => 'Аренданая плата всех арендаторов (факт кв.м.)', 'value' => CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-72599', '')), 'postfix' => 'руб./кв.м.','firstStyles' => 'color: #ff0000;','tag' => 'strong'],
            ['type' => 'nbsp'],
            ['type' => 'title', 'title' => 'Расчет №1 - Покупка без повышения цены выкупа:', 'tag' => 'strong'],
            ['type' => 'record', 'title' => 'Шаг аукциона', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88246', ''), 0, ' ')],
            ['type' => 'record', 'title' => 'Цена покупки', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87644', ''), 0, ' ')],
            ['type' => 'record', 'title' => 'Цена покупки кв.м.', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87647', ''), 0, ' ')],
            ['type' => 'record', 'title' => 'Коэффициент перепродажи', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87927', ''), 0, ' '), 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;','postfix' => 'мес.'],
            ['type' => 'record', 'title' => 'Предполагаемая перепродажа с учетом коэффициента', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87928', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Предполагаемая перепродажа за кв.м', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88549', ''), 0, ' '), 'postfix' => 'руб/кв.м.'],
            ['type' => 'record', 'title' => 'Сальдо Перепродажа', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87929', ''), 0, ' '), 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;','postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Процент перепродажи', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88483', ''), 0, ' '), 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;','postfix' => '%'],
            ['type' => 'nbsp'],
            ['type' => 'title', 'title' => 'Расчет №2 - Покупка +20% к цене:', 'tag' => 'strong'],
            ['type' => 'record', 'title' => 'Повышение стоимости', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88575', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Расчет шагов', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88576', ''), 0, ' '), 'postfix' => 'шаг'],
            ['type' => 'record', 'title' => 'Начальная цена Обьекта (общая)', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87644', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Начальная цена кв.м.', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87647', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Шаг аукциона', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88246', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Цена покупки', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88362', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Цена покупки за кв.м.', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88363', ''), 0, ' '), 'postfix' => 'руб/кв.м.'],
            ['type' => 'record', 'title' => 'Коэффициент перепродажи', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87927', ''), 0, ' '), 'postfix' => 'мес.', 'secondStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Предполагаемая перепродажа с учетом коэффициента', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87928', ''), 0, ' '),'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;', 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Предполагаемая перепродажа за кв.м.', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88549', ''), 0, ' '), 'postfix' => 'руб/кв.м.'],
            ['type' => 'record', 'title' => 'Сальдо перепродажа', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88366', ''), 0, ' '), 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;', 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Процент перепродажи', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88483', ''), 0, ' '), 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;', 'postfix' => '%'],
            ['type' => 'nbsp'],
            ['type' => 'title', 'title' => 'Расчет №3 - Фактическая покупка:', 'tag' => 'strong'],
            ['type' => 'record', 'title' => 'Начальная цена Обьекта (общая)', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87644', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Начальная цена кв.м.', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87647', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Шаг аукциона', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88246', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Количество шагов по аукциону', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88550', ''), 0, ' ')],
            ['type' => 'record', 'title' => 'Фактическая покупка', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom--88552', ''), 0, ' '), 'postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Фактическая покупка (кв.м.)', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88359', ''), 0, ' '), 'postfix' => 'руб/кв.м.'],
            ['type' => 'record', 'title' => 'Коэффициент перепродажи', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87927', ''), 0, ' '), 'postfix' => 'мес.', 'secondStyles' => 'color: #ff0000;'],
            ['type' => 'record', 'title' => 'Предполагаемая перепродажа с учетом коэффициента', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-87928', ''), 0, ' '), 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;','postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Предполагаемая перепродажа за кв.м.', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88549', ''), 0, ' '), 'postfix' => 'руб/кв.м.'],
            ['type' => 'record', 'title' => 'Полученная аренда (кол-во месяцев)', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88569', ''), 0, ' '),'tag' => 'strong'],
            ['type' => 'nbsp'],
            ['type' => 'record', 'title' => 'Налог с продажи (доход-расход)', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88543', ''), 0, ' '), 'postfix' => 'руб/кв.м.'],
            ['type' => 'record', 'title' => 'Сальдо Перепродажа', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88367', ''), 0, ' '), 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;','postfix' => 'руб.'],
            ['type' => 'record', 'title' => 'Процент перепродажи', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88492', ''), 0, ' '), 'postfixStyles' => 'color: #ff0000;','secondStyles' => 'color: #ff0000;', 'firstStyles' => 'color: #ff0000;','postfix' => '%'],
            ['type' => 'nbsp'],
            ['type' => 'record', 'title' => 'Окупаемость с учетом аренды', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88360', ''), 0, ' '), 'postfix' => 'мес.'],
            ['type' => 'record', 'title' => 'Годовая доходность', 'value' =>  CustomHelper::numberFormat(CustomHelper::issetFieldIncludeArray($customs, 'custom-88361', ''), 0, ' '), 'postfix' => '%'],
            ['type' => 'nbsp'],
            ['type' => 'record', 'title' => 'Комментарий по расчету', 'value' => "<br>Расчеты по Сальдо (Прибыль) :  <br>1. Коэфициент перепродажи (ставим оптимальный, для оперативной перепродажи)<br>2. Налоги  Доход минус Расход учитываем с прибыли <br>3. Полученную Аренду за время перепродажи учитываем с учетом количесва месяцев (примерное) по предполагаемому поиску покупетеля. <br><span style='color:blue;'>Сальдо (=) Коэфициент Перепродажи (х) на Аренду в месяц (=) Предполагаемая перепродажа (-) сумма покупки (-) Налог (+) Аренда полученная за время перепродажи.</span>",'tag' => 'strong','enable_tags' => true],
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

