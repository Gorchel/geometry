<?php

namespace App\Classes\Parsing\CartTypes;

use App\Properties;
use PHPHtmlParser\Dom;

/**
 * Class TorgiRu
 * @package App\Classes\CartTypes
 */
class TorgiRu implements CartInterface
{
    /**
     * @param Dom $dom
     * @return bool
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function checkErrorFlag(Dom $dom):bool
    {
        return false;
    }

    /**
     * @param Dom $dom
     * @return string
     */
    public function getTotal(Dom $dom)
    {
        $records = $dom->find('.pt-price');

        foreach ($records as $record) {
            $offers = $record->getAttribute('itemprop');

            if ($offers != 'offers') {
                continue;
            }

            foreach ($record->find('meta') as $meta) {
                $price = $meta->getAttribute('itemprop');

                if ($price != 'price') {
                    continue;
                }

                return $meta->getAttribute('content');
            }
        }

        return 0;
    }

    /**
     * @param Dom $dom
     * @return
     */
    public function getAddress(Dom $dom)
    {
        $address = $dom->find('.pc-inner', 0);

        if (empty($address)) {
            return '';
        }

        $address = $address->find('p', 0);

        if (empty($address)) {
            return '';
        }

        return $address->innerHtml;
    }

    /**
     * @param Dom $dom
     * @return mixed
     */
    public function getDescription(Dom $dom)
    {
        $records = $dom->find('.pr-info', 0);

        if (empty($records)) {
            return '';
        }

        foreach ($records->find('div') as $record) {
            $description = $record->getAttribute('itemprop');

            if ($description != 'description') {
                continue;
            }

            return $record->innerHtml;
        }


        return '';
    }

    /**
     * @param Dom $dom
     * @return mixed
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function getName(Dom $dom)
    {
        $address = $dom->find('.pc-inner', 0);

        if (empty($address)) {
            return '';
        }

        $address->find('h1', 0);

        return $address->innerHtml;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return Properties::CUSTOM_SOURCE_TORGIRU;
    }

    /**
     * @param array $data
     * @return array
     */
    public function customData(array &$data, Dom $dom): array
    {
        $data['attributes']['customs'][Properties::CUSTOM_TYPE] = 'Продажа';
        $data['attributes']['object-type'] = Properties::OBJECT_TYPE_RETAIL; //Вид торгов
        $data['attributes']['customs']['custom-84066'] = 'torgi-ru.ru';
        $data['attributes']['customs']['custom-88251'] = $this->getDetails($dom, 'Дата окончания торгов'); //Дата торгов
//        $data['attributes']['customs']['custom-88216'] = $this->getDetails($dom, 'Кадастровый номер');
//        $data['attributes']['customs']['custom-73199'] = $this->getDetails($dom, 'Вход');
        $stage = $this->getDetails($dom, 'этаж');
        $stages = $this->getDetails($dom, 'всего этажей');

        $totalArea =  $this->getDetails($dom, 'общая площадь');

        if (!empty($totalArea)) {
            $totalArea = strip_tags($totalArea);
            $totalArea = str_replace('м2', '', $totalArea);
            $totalArea = str_replace(',', '.', $totalArea);
            $data['attributes']['total-area'] = floatval($totalArea);
        }

        $data['attributes']['customs']['custom-68794'] = $stage . '/' . $stages;

        $data['attributes']['object-type'] = Properties::OBJECT_TYPE_RETAIL; //Вид торгов
//        $data['relationships']['source'] = [
//            'data' => [
//                'type' => 'sources',
//                'id' => Properties::CUSTOM_SOURCE_TORGIRU,
//            ]
//        ];

        //Поиск площади
//        $description = $this->getDescription($dom);
//        if (isset($description)) {
//            $string = trim(mb_strtolower($description));
//
//            $pattern = '~\площадь\K.+?(?=\кв. м)~';
//            preg_match_all($pattern, $string, $search);
//
//            if (isset($search[0][0])) {
//                $search = $search[0][0];
//                $search = str_replace('ю', '', $search);
//
//                $data['attributes']['total-area'] = trim($search);
//            }
//        }

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
        $records = $dom->find('.pc-inner', 0);

        if (empty($records)) {
            return '';
        }

        $records->find('custom', 0);

        if (empty($records)) {
            return '';
        }

        $details = '';

        foreach ($records->find('li') as $record) {
            $label = $record->find('span', 0);

            if (isset($label) && $label->innerHtml == $field) {
                $details = $record->find('b', 0)->innerHtml;
            }
        }

        return $details;

    }

    /**
     * @param Dom $dom
     */
    public function getInventory(Dom $dom) {
        return null;
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
        $html = file_get_contents($url);

        $dom = new Dom();
        $dom->loadStr($html);

        return $dom;
    }
}
