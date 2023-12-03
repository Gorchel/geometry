<?php

namespace App\Classes\Filters;

/**
 * Class FilterOrders
 * @package App\Classes\Filters;
 */
class Documents
{
    public $propertyMailCustomFields = [
        'kontinentRent' => [
            ['name' => 'Адрес объекта', 'field' => 'custom_64790'],
            ['name' => 'Тип сделки', 'field' => 'custom_62518'],
            ['name' => 'Тип недвижимости', 'field' => 'custom_61755'],
            ['name' => 'Карта месторасположения', 'field' => 'custom_67342'],
            ['name' => 'Ссылка на Яндекс панораму', 'field' => 'custom_61775'],
            ['name' => 'Площадь', 'field' => 'custom_64803'],
            ['name' => 'Этаж/Этажность ', 'field' => 'custom_68794'],
            ['name' => 'Электрическая мощность', 'field' => 'custom_62859'],
            ['name' => 'Высота потолков', 'field' => 'custom_64805'],
            ['name' => 'Коммерческие условия (в мес.)', 'field' => 'custom_76734'],
            ['name' => 'Коммерческие условия (за 1 кв.м.)', 'field' => 'custom_76733'],
            ['name' => 'Фото объекта (1 фото)', 'field' => 'custom_65702'],
            ['name' => 'Фото объекта (2 фото)', 'field' => 'custom_65703'],
            ['name' => 'Контактная информация', 'field' => 'custom_66779'],
            ['name' => 'Тел.', 'field' => 'custom_77293'],
            ['name' => 'e-mail', 'field' => 'custom_77294'],
            ['name' => 'Дата рассылки КП', 'field' => 'custom_68793'],
        ],
        'kontinentBuy' => [
            ['name' => 'Адрес объекта','field' => 'custom_64790'],
            ['name' => 'Тип сделки','field' => 'custom_62518'],
            ['name' => 'Тип недвижимости','field' => 'custom_61755'],
            ['name' => 'Карта месторасположения','field' => 'custom_67342'],
            ['name' => 'Ссылка на Яндекс панораму','field' => 'custom_61775'],
            ['name' => 'Площадь','field' => 'custom_64803'],
            ['name' => 'Этаж/Этажность','field' => 'custom_68794'],
            ['name' => 'Электрическая мощность','field' => 'custom_62859'],
            ['name' => 'Высота потолков','field' => 'custom_64805'],
            ['name' => 'Коммерческие условия (руб.)','field' => 'custom_76997'],
            ['name' => 'Коммерческие условия (руб. за кв.м)','field' => 'custom_76998'],
            ['name' => 'Аренда в мес.','field' => 'custom_66770'],
            ['name' => 'Окупаемость (мес.)','field' => 'custom_61718'],
            ['name' => 'Окупаемость (лет)','field' => 'custom_61719'],
            ['name' => 'Комиссионное вознаграждение (%)','field' => 'custom_61715'],
            ['name' => 'Комиссионное вознаграждение (в руб.)','field' => 'custom_61716'],
            ['name' => 'Фото объекта (1 фото)','field' => 'custom_65702'],
            ['name' => 'Фото объекта (2 фото)','field' => 'custom_65703'],
            ['name' => 'Контактная информация','field' => 'custom_66779'],
            ['name' => 'Тел.','field' => 'custom_77293'],
            ['name' => 'e-mail','field' => 'custom_77294'],
            ['name' => 'Дата рассылки КП','field' => 'custom_68793']
        ],
        'geometryRent' => [
            ['name' => 'Тип сделки', 'field' => 'custom_62518'],
            ['name' => 'Адрес', 'field' => 'custom_64790'],
            ['name' => 'Район города', 'field' => 'custom_64791'],
            ['name' => 'Метро', 'field' => 'custom_64792'],
            ['name' => 'До метро м', 'field' => 'custom_74760'],
            ['name' => 'Тип недвижимости', 'field' => 'custom_61755'],
            ['name' => 'Тип объекта', 'field' => 'custom_74278'],
            ['name' => 'Общая площадь', 'field' => 'custom_64803'],
            ['name' => 'Этаж/Этажность', 'field' => 'custom_68794'],
            ['name' => 'Описание планировки', 'field' => 'custom_67055'],
            ['name' => 'Варианты деления', 'field' => 'custom_74279'],
            ['name' => 'Высота потолков', 'field' => 'custom_64805'],
            ['name' => 'Вход', 'field' => 'custom_73199'],
            ['name' => 'Электрическая мощность', 'field' => 'custom_62859'],
            ['name' => 'Ремонт', 'field' => 'custom_66767'],
            ['name' => 'Вентиляция', 'field' => 'custom_66768'],
            ['name' => 'Аренда в месяц', 'field' => 'custom_61758'],
            ['name' => 'Аренда кв.м', 'field' => 'custom_76733'],
            ['name' => 'Плотность населения (количество квартир, радиус 300 м)', 'field' => 'custom_67347'],
            ['name' => 'Плотность населения (количество квартир, радиус 500 м)', 'field' => 'custom_66776'],
            ['name' => 'Ссылка на Яндекс панораму', 'field' => 'custom_61775'],
            ['name' => 'Ссылка на обзорное видео', 'field' => 'custom_65161'],
            ['name' => 'Ссылка на видео пешеходный трафик', 'field' => 'custom_65160'],
            ['name' => 'Дата рассылки КП', 'field' => 'custom_68793'],
            ['name' => 'Карта', 'field' => 'custom_67342'],
            ['name' => 'Фото 1', 'field' => 'custom_65702'],
            ['name' => 'Фото 2', 'field' => 'custom_65703'],
            ['name' => 'План 1', 'field' => 'custom_61772'],
            ['name' => 'План 2', 'field' => 'custom_61773'],
            ['name' => 'План 3', 'field' => 'custom_61771'],
            ['name' => 'Должность', 'field' => 'custom_66779'],
            ['name' => 'Мобильный телефон', 'field' => 'custom_66780'],
            ['name' => 'E-mail', 'field' => 'custom_66781'],
            ['name' => 'Сайт', 'field' => 'custom_66782']
        ],
        'geometryBuy' => [
            ['name' => 'Тип сделки', 'field' => 'custom_62518'],
            ['name' => 'Адрес', 'field' => 'custom_64790'],
            ['name' => 'Район города', 'field' => 'custom_64791'],
            ['name' => 'Метро', 'field' => 'custom_64792'],
            ['name' => 'До метро м', 'field' => 'custom_74760'],
            ['name' => 'Тип недвижимости', 'field' => 'custom_61755'],
            ['name' => 'Тип объекта', 'field' => 'custom_74278'],
            ['name' => 'Общая площадь', 'field' => 'custom_64803'],
            ['name' => 'Этаж/Этажность', 'field' => 'custom_68794'],
            ['name' => 'Описание планировки', 'field' => 'custom_67055'],
            ['name' => 'Варианты деления', 'field' => 'custom_74279'],
            ['name' => 'Высота потолков', 'field' => 'custom_64805'],
            ['name' => 'Вход', 'field' => 'custom_73199'],
            ['name' => 'Электрическая мощность', 'field' => 'custom_62859'],
            ['name' => 'Ремонт', 'field' => 'custom_66767'],
            ['name' => 'Вентиляция', 'field' => 'custom_66768'],
            ['name' => 'Стоимость', 'field' => 'custom_72707'],
            ['name' => 'Стоимость кв.м', 'field' => 'custom_72709'],
            ['name' => 'Арендаторы на объекте'],
            ['name' => '1.', 'field' => ['custom_72952', 'custom_61705', 'custom_61706']],
            ['name' => '2.', 'field' => ['custom_72953', 'custom_63793', 'custom_63794']],
            ['name' => '3.', 'field' => ['custom_72954', 'custom_63799', 'custom_63800']],
            ['name' => '4.', 'field' => ['custom_72955', 'custom_63805', 'custom_63806']],
            ['name' => '5.', 'field' => ['custom_72956', 'custom_63811', 'custom_63812']],
            ['name' => 'Аренданая плата всех арендаторов', 'field' => 'custom_66770'],
            ['name' => 'Аренда в мес.', 'field' => 'custom_66770'],
            ['name' => 'Аренда кв.м', 'field' => 'custom_72599'],
            ['name' => 'Предполагаемая аренда кв.м', 'field' => 'custom_76288'],
            ['name' => 'Предполагаемая аренда в мес.', 'field' => 'custom_76732'],
            ['name' => 'Окупаемость инвестиций, мес.', 'field' => 'custom_61718'],
            ['name' => 'Доходность % в год', 'field' => 'custom_61720'],
            ['name' => 'Предполагаемая аренда в мес.', 'field' => 'custom_66772'],
            ['name' => 'Предполагаемая окупаемость, мес.', 'field' => 'custom_66771'],
            ['name' => 'Плотность населения (количество квартир, радиус 300 м)', 'field' => 'custom_67347'],
            ['name' => 'Плотность населения (количество квартир, радиус 500 м)', 'field' => 'custom_66776'],
            ['name' => 'Ссылка на Яндекс панораму', 'field' => 'custom_61775'],
            ['name' => 'Ссылка на обзорное видео', 'field' => 'custom_65161'],
            ['name' => 'Ссылка на видео пешеходный трафик', 'field' => 'custom_65160'],
            ['name' => 'Дата рассылки КП', 'field' => 'custom_68793'],
            ['name' => 'Карта', 'field' => 'custom_67342'],
            ['name' => 'Фото 1', 'field' => 'custom_65702'],
            ['name' => 'Фото 2', 'field' => 'custom_65703'],
            ['name' => 'План 1', 'field' => 'custom_61772'],
            ['name' => 'План 2', 'field' => 'custom_61773'],
            ['name' => 'План 3', 'field' => 'custom_61771'],
            ['name' => 'Должность', 'field' => 'custom_66779'],
            ['name' => 'Мобильный телефон', 'field' => 'custom_66780'],
            ['name' => 'E-mail', 'field' => 'custom_66781'],
            ['name' => 'Сайт', 'field' => 'custom_66782'],
        ],
    ];
}

