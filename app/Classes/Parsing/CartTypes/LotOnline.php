<?php

namespace App\Classes\Parsing\CartTypes;

use App\Classes\Parsing\CartTypes\CartInterface;
use App\Classes\Zenrows\ZenrowsRequest;
use App\ParsingQueue;
use App\Properties;
use PHPHtmlParser\Dom;

/**
 * Class LotOnline
 * @package App\Classes\CartTypes
 */
class LotOnline implements CartInterface
{

    private static $ajaxDetails;

    /**
     * @var ZenrowsRequest
     */
    private $zenrowsRequest;

    /**
     * LotOnline constructor.
     */
    public function __construct()
    {
        $this->zenrowsRequest = new ZenrowsRequest();
        $this->zenrowsRequest->setPremiumProxy();

        static::$ajaxDetails = null;
    }

    /**
     * @param Dom $dom
     * @return bool
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function checkErrorFlag(Dom $dom):bool
    {
        $records = $dom->find('.ty-exception__code-txt');

        foreach ($records as $record) {

            if ($record->innerHtml == 'Ошибка') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Dom $dom
     * @return string
     */
    public function getTotal(Dom $dom)
    {
        $ajaxDom = $this->getAjaxDetails($dom);
        $priceStart = $ajaxDom->find('#priceStart');

        $total = 0;
        if (!empty($priceStart)) {
            $total = $priceStart->innerHtml;

            $total = str_replace(' ', '', $total);
            $total = str_replace('RUB', '', $total);
        }


        return $total;
    }

    /**
     * @param Dom $dom
     * @return
     */
    public function getAddress(Dom $dom)
    {
        $records = $dom->find('.ty-product-block_product_main', 0)
            ->find('div');

        $address = '';

        foreach ($records as $record) {
            $dt = $record->find('dt', 0);

            if (isset($dt) && $dt->innerHtml == 'Адрес') {
                $address = $record->find('dd', 0)->innerHtml;
            }
        }

        return $address;
    }

    /**
     * @param Dom $dom
     * @return mixed
     */
    public function getDescription(Dom $dom)
    {
        $record = $dom->find('.ty-product__full-description', 0)
            ->innerHtml;

        return $record;
    }

//    /**
//     * @param Dom $dom
//     * @return mixed
//     */
//    protected function getStartDate(Dom $dom)
//    {
//        $oldVer = $dom->find('#oldVersionLink')->find('a', 0);
//        dd($oldVer->href);
//
//        $records = $dom->find('.ty-control-group');
//
//        $date = '';
//
//        foreach ($records as $record) {
//            echo $record->innerHtml."\r\n";
//            $label = $record->find('h4', 0);
//
//            echo $label;
////            dd($record);
//
//            if ($label->innerHtml != 'Время проведения') {
//                continue;
//            }
//
//            $date = strip_tags($record->find('.dash_off_when_stopped', 0)->innerHtml);
//        }
//
//        dd($date);
//
//        return $date;
//    }

    /**
     * @param Dom $dom
     * @return mixed
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function getName(Dom $dom)
    {
        $record = $dom->find('.ty-product-block-title', 0)
            ->find('bdi', 0)
            ->innerHtml;

        return $record;
    }

    /**
     * @param Dom $dom
     * @return mixed
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function getStatus(Dom $dom)
    {
         $status = $dom->find('.auction_status_name', 0);

         if (empty($status)) {
             return '';
         }

        $record = $status->find('.ty-control-group__label', 0)
            ->innerHtml;

        return trim($record);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return Properties::CUSTOM_SOURCE_LOT_ONLINE;
    }

    /**
     * @param string $url
     * @return Dom
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     */
    public function getHtml(string $url): Dom
    {
        if (!empty(env('ZENROWS_ENABLED'))) {
            $html = $this->zenrowsRequest->get($url);
        } else {
            $html = file_get_contents($url);
        }

        $dom = new Dom();
        $dom->loadStr($html);

        return $dom;
    }

    /**
     * @param Dom $dom
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function getAjaxDetails(Dom $dom)
    {
        if (empty(static::$ajaxDetails)) {
            $record = $dom->find('#oldVersionLink', 0)
                ->find('a')
                ->getAttribute('href');

            $record = str_replace('sales', 'catalog', $record);
            $record = str_replace('.xhtml', '.v.xhtml', $record);

            $record = str_replace('privatization', 'catalog', $record);
            $record = str_replace('lot/details.html', 'privatization/lot/details.html', $record);

            $record = str_replace('lease', 'catalog', $record);

            static::$ajaxDetails = $this->getHtml($record);
        }


        return static::$ajaxDetails;
    }

    /**
     * @param array $data
     * @return array
     */
    public function customData(array &$data, Dom $dom): array
    {
        $ajaxDom = $this->getAjaxDetails($dom);

        $data['attributes']['customs'][Properties::CUSTOM_TYPE] = 'Продажа';
        $data['attributes']['object-type'] = Properties::OBJECT_TYPE_RETAIL; //Вид торгов
//        $data['attributes']['customs']['custom-88215'] = $this->getStartDate($dom); //Дата торгов
        $data['attributes']['customs']['custom-88216'] = $this->getDetails($dom, 'Кадастровый номер');
        $data['attributes']['customs']['custom-73199'] = $this->getDetails($dom, 'Вход');
        $data['attributes']['customs']['custom-66767'] = $this->getDetails($dom, 'Состояние отделки помещения');
        $data['attributes']['customs']['custom-67055'] = $this->getDetails($dom, 'Планировка');
        $data['attributes']['customs']['custom-74193'] = $this->getStatus($ajaxDom); //Статус
        $data['attributes']['customs']['custom-88239'] = $this->getStatus($ajaxDom); //Статус
        $this->getAdditionalDetails($ajaxDom, $data);

        $stage = $this->getDetails($dom, 'Этаж');
        $stages = $this->getDetails($dom, 'Количество этажей в здании');

        $data['attributes']['customs']['custom-68794'] = $stage . '/' . $stages;

        $data['attributes']['object-type'] = Properties::OBJECT_TYPE_RETAIL; //Вид торгов
        $data['relationships']['source'] = [
            'data' => [
                'type' => 'sources',
                'id' => Properties::SOURCE_LOTONLINE,
            ]
        ];

        //Поиск площади
        $description = $this->getDescription($dom);
        if (isset($description)) {
            $string = trim(mb_strtolower($description));

            $pattern = '~\площадь\K.+?(?=\кв. м)~';
            preg_match_all($pattern, $string, $search);

            if (isset($search[0][0])) {
                $search = $search[0][0];
                $search = str_replace('ю', '', $search);

                $data['attributes']['total-area'] = trim($search);
            }
        }

        //Accordion
        $accordion = $dom->find('.ty-accordion');

        if (!empty($accordion)) {
            foreach ($accordion as $accordionBlock) {
                foreach ($accordionBlock->find('.product-list-field') as $product) {
                    $label = $product->find('.ty-control-group__label', 0);

                    if (empty($label)) {
                        continue;
                    }

                    $field = trim($product->find('.ty-control-group__item', 0)->innerHtml);

                    if (empty($field)) {
                        continue;
                    }

                    switch (trim($label->innerHtml)) {
                        case 'Площадь':
                            $data['attributes']['total-area'] = $field;
                            break;
                        case 'Кадастровый номер':
                            $data['attributes']['customs']['custom-88216'] = $field;
                            break;
                        case 'Наличие арендаторов':
                            $data['attributes']['customs']['custom-84033'] = $field;
                            break;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param Dom $dom
     * @return string
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    protected function getDetails(Dom $dom, string $field)
    {
        $records = $dom->find('.ty-control-group');

        $details = '';

        foreach ($records as $record) {
            $label = $record->find('.ty-control-group__label', 0);

            if (isset($label) && $label->innerHtml == $field) {
                $details = $record->find('.ty-control-group__item', 0)->innerHtml;
            }
        }

        return $details;

    }

    /**
     * @param Dom $dom
     * @param array $data
     * @return array
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    protected function getAdditionalDetails(Dom $dom, array &$data)
    {
        $block = $dom->find('.ty-product-block_auction_info__body', 0);

        if (empty($block)) {
            return $data;
        }

        foreach ($block->find('.auction_request') as $request) {
            $label = $request->find('.ty-control-group__label', 0);

            if (empty($label)) {
                continue;
            }

            switch (trim($label->innerHtml)) {
                case "Период приёма заявок":
                    $dates = [];
                    $requestBlock = $request->find('.ty-control-group__item', 0);

                    if (empty($requestBlock)) {
                        break;
                    }

                    foreach ($requestBlock->find('span') as $span) {
                        $dates[] = trim($span->innerHtml);
                    }

                    if (isset($dates[1])) {
                        $data['attributes']['customs']['custom-88251'] = $dates[1];
                    }

                    break;
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getSuccessStatuses(): array
    {
        return ['Процедура завершена', 'Процедура по лоту проведена'];
    }

    /**
     * @param Dom $dom
     */
    public function getInventory(Dom $dom) {
        $records = $dom->find('.ty-product-block_product_main', 0)
            ->find('div');

        $inventoryCode = null;

        foreach ($records as $record) {
            $dt = $record->find('dt', 0);

            if (isset($dt) && $dt->innerHtml == 'Код процедуры') {
                $dd = $record->find('dd', 0);

                if (!empty($dd)) {
                    $inventoryCode = $dd->find('a', 0)->innerHtml;
                }
            }
        }

        return $inventoryCode;
    }
}
