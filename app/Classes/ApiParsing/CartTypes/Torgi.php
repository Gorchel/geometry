<?php

namespace App\Classes\ApiParsing\CartTypes;

use App\Properties;

/**
 * Class Torgi
 * @package App\Classes\ApiParsing\CartTypes
 *
 * https://torgi.gov.ru/new/api/public/lotcards/22000159970000000044_1
 */
class Torgi implements CartInterface
{

    /**
     * @param array $result
     * @return int|mixed
     */
    public function getTotal(array $result)
    {
        return isset($result['priceMin']) ? $result['priceMin'] : 0;
    }

    /**
     * @param array $result
     * @return string
     */
    public function getStatus(array $result = []): string
    {
        return isset($result['lotStatus']) ? $result['lotStatus'] : '';
    }

    /**
     * @return array
     */
    public function getSuccessStatuses(): array
    {
        return ['SUCCEED','APPLICATIONS_SUBMISSION','PUBLISHED','FAILED'];
    }

    /**
     * @param array $result
     * @return int|mixed
     */
    public function getAddress(array $result)
    {
        return isset($result['estateAddress']) ? $result['estateAddress'] : 0;
    }

    /**
     * @param array $result
     * @return int|mixed
     */
    public function getDescription(array $result)
    {
        return isset($result['lotName']) ? $result['lotName'] : 0;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return Properties::CUSTOM_SOURCE_TORGI;
    }

    /**
     * @param array $result
     * @return string
     */
    public function getLink(array $result = []): string
    {
        return 'https://torgi.gov.ru/new/public/lots/lot/'.$result['id'];
    }

    /**
     * @param array $result
     * @return string
     */
    public function getInventory(array $result): string
    {
        if (isset($result['characteristics'])) {
            foreach ($result['characteristics'] as $characteristic) {
                switch($characteristic['code']) {
                    case 'cadastralNumberObjectRealty':
                    case 'cadastralNumberRealty':
                        if (!isset($characteristic['characteristicValue']) || empty($characteristic['characteristicValue'])) {
                            break;
                        }

                        return trim($characteristic['characteristicValue']);//Кадастровый номер
                }
            }
        }

        return '';
    }

    /**
     * @param array $data
     * @param array $result
     * @return array
     */
    public function customData(array &$data, array $result): array
    {
        $data['attributes']['business-usage-type'] = Properties::BUSINESS_USAGE_TYPE_RETAIL; //Вид торгов
        $data['attributes']['object-type'] = Properties::OBJECT_TYPE_RETAIL; //Вид торгов
        $data['attributes']['customs']['custom-88237'] = isset($result['biddType']['name']) ? $result['biddType']['name'] : ''; //Вид торгов
        $data['attributes']['customs']['custom-88238'] = isset($result['biddForm']['name']) ? $result['biddForm']['name'] : ''; //Категория
        $data['attributes']['customs']['custom-88240'] = isset($result['ownershipForm']['name']) ? $result['ownershipForm']['name'] : ''; //Вид соьственности
        $data['attributes']['customs']['custom-88241'] = isset($result['lotName']) ? $result['lotName'] : ''; //Предмет торгов (наименование лота)
        $data['attributes']['customs']['custom-88242'] = isset($result['lotDescription']) ? $result['lotDescription'] : ''; //Описание лота
        $data['attributes']['customs']['custom-88243'] = isset($result['etpUrl']) ? $result['etpUrl'] : ''; //Извещение (ссылка)
        $data['attributes']['customs']['custom-88244'] = isset($result['prevBiddInfo']) ? $result['prevBiddInfo'] : ''; //Сведения о предыдущих извещениях
        $data['attributes']['customs']['custom-88245'] = isset($result['priceMin']) ? $result['priceMin'] : ''; //Начальная цена
        $data['attributes']['customs']['custom-88246'] = isset($result['priceStep']) ? $result['priceStep'] : ''; //Шаг аукциона
        $data['attributes']['customs']['custom-88247'] = isset($result['prevBiddInfo']) ? $result['prevBiddInfo'] : ''; //Размер задатка

        $data['attributes']['customs']['custom-88248'] = isset($result['noticeNumber']) ? $result['noticeNumber'] : ''; //Извещение (лот)
        $data['attributes']['customs']['custom-88249'] = isset($result['biddType']['name']) ? $result['biddType']['name'] : ''; //Вид торгов (текст)
        $data['attributes']['customs']['custom-88250'] = isset($result['etpUrl']) ? $result['etpUrl'] : ''; //Электронная площадка
        $data['attributes']['customs']['custom-88224'] = isset($result['biddStartTime']) ? $result['biddStartTime'] : ''; //Дата подачи заявки торги
        $data['attributes']['customs']['custom-88251'] = isset($result['biddEndTime']) ? $result['biddEndTime'] : ''; //Дата окончания подачи заявок
        $data['attributes']['customs']['custom-88215'] = isset($result['auctionStartDate']) ? $result['auctionStartDate'] : ''; //Дата торгов
        $data['attributes']['customs']['custom-88231'] = isset($result['ownershipForm']['name']) ? $result['ownershipForm']['name'] : ''; //Форма собственности
//        $data['attributes']['customs']['custom-88230'] = isset($result['ownershipForm']['name']); //Параметры заявок
        $data['attributes']['customs']['custom-87644'] = $this->getTotal($result);
        $data['attributes']['customs']['custom-61713'] = $this->getTotal($result);

        //статус
        $status = isset($result['lotStatus']) ? $result['lotStatus'] : '';
        $data['attributes']['customs']['custom-88239'] = $this->getStatusName($status);
        $data['attributes']['customs']['custom-74193'] = $this->getStatusName($status);
        $data['attributes']['customs']['custom-88286'] = isset($result['category']['name']) ? $result['category']['name'] : ''; //Категория обьекта
        $data['attributes']['customs']['custom-64788'] = [0 => 'Коммерческая недвижимость'];
        $data['attributes']['customs'][Properties::CUSTOM_TYPE] = [0 => 'Продажа'];

//        $data['relationships']['source'] = [
//            'data' => [
//                'type' => 'sources',
//                'id' => Properties::CUSTOM_SOURCE_TORGI,
//            ]
//        ];

        $data['relationships']['responsible'] = [
            'data' => [
                'type' => 'users',
                'id' => Properties::RESPONSIBILITY_USER_TORGI_ID,
            ]
        ];

        //characteristic
        if (isset($result['characteristics'])) {
            foreach ($result['characteristics'] as $characteristic) {
                switch($characteristic['code']) {
                    case 'totalAreaRealty':
                        $data['attributes']['customs']['custom-64803'] = $characteristic['characteristicValue'] ?? 0;
                        $data['attributes']['total-area'] = $characteristic['characteristicValue'] ?? 0;
                        break;
                    case 'cadastralNumberObjectRealty':
                    case 'cadastralNumberRealty':
                        if (!isset($characteristic['characteristicValue'])) {
                            break;
                        }

                        $data['attributes']['customs']['custom-88216'] = $characteristic['characteristicValue'];//Кадастровый номер
                        break;
                }
            }
        }

        //Поиск этажа
        if (isset($result['lotDescription'])) {
            $descriptionArr = explode( ',', $result['lotDescription']);

            if (!empty($descriptionArr)) {

                foreach ($descriptionArr as $string) {
                    $string = trim(mb_strtolower($string));

                    $floorAttr = '';

                    if (strpos($string, 'этаж:') !== false) {
                        $string = trim(str_replace('этаж:', '', $string));
                        $floorAttr .= $string.', ';//Этаж
                    }

                    $floor = strstr($string,'этаж №');

                    if (!empty($floor)) {
                        $floor = $this->replace($floor, ['(', ')', 'этаж №']);
                        $floorAttr .= (int)$floor.', ';//Этаж
                    }

                    if (strpos($string, 'подвал') !== false) {
                        $floorAttr .= 'Подвал, ';//Подвал
                    }

                    if (!empty($floorAttr)) {
                        $data['attributes']['customs']['custom-68794'] = trim($floorAttr, ', ');//Этаж
                    }
                }
            }
        }

        return $data;
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
     * @param string $lotStatus
     * @return mixed|string
     */
    public function getStatusName(string $lotStatus): string
    {
        $statuses = [
            'FAILED' => 'Не состоялся',
            'PUBLISHED' => 'Опубликован',
            'APPLICATIONS_SUBMISSION' => 'Прием заявок',
            'SUCCEED' => 'Состоялся',
            'DETERMINING_WINNER' => 'Определение победителя',
            'CANCELED' => 'Отменен',
        ];

        if (isset($statuses[$lotStatus])) {
            return $statuses[$lotStatus];
        }

        return '';
    }
}
