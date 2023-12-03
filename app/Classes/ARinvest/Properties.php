<?php

namespace App\Classes\ARinvest;

use App\Helpers\CurlHelper;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Class Properties
 * @package App\Classes\ARinvest;
 */
class Properties
{
    /**
     * @var mixed
     */
    public $url;

    /**
     * @var |null
     */
    public $token;

    /**
     * @var array
     */
    public $params;
    /**
     * @var
     */
    public $validator;

    /**
     * LinkGenerator constructor.
     */
    public function __construct()
    {
        $this->url = env('AR_INVEST_API');

//        $auth = new Auth();
//        $this->token = $auth->run();
        $this->token = env('AR_INVEST_TOKEN');
    }


    /**
     *
     */
    public function setSRID()
    {
        $params['location'] = "SRID=4326;POINT (".$this->params['coordinates_longitude']." ".$this->params['coordinates_width'].")";
    }

    /**
     * https://docs.google.com/document/d/1hf2LXkc-NrZJM-HryAgE2ANqK93YCvlG-Cy0vhFDYCE/edit
     * @param array $params
     * @return bool|\Illuminate\Support\MessageBag
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        if (isset($this->params['source'])) {
            $source = $this->crm2Source($this->params['source']);
            if (!empty($source)) {
                $this->params['source'] = $source;
            } else {
                unset($this->params['source']);
            }
        }

        if (isset($this->params['type_ads'])) {
            $types = $this->crm2Type($this->params['type_ads']);
            if (!empty($types)) {
                $this->params['type_ads'] = $types;
            } else {
                unset($this->params['type_ads']);
            }
        }

        if (isset($this->params['object_ads'])) {
            $ads = $this->crm2Obj($this->params['object_ads']);
            if (!empty($ads)) {
                $this->params['object_ads'] = $ads;
            } else {
                unset($this->params['object_ads']);
            }
        }

        if (isset($this->params['floor'])) {
            $floor = $this->crm2Floor($this->params['floor']);
            if (!empty($floor)) {
                $this->params['floor'] = $floor;
            } else {
                unset($this->params['floor']);
            }
        }

        if (isset($this->params['category'])) {
            $arCat = $this->crm2Cat($this->params['category']);
            if (!empty($arCat)) {
                $this->params['category'] = $arCat;
            } else {
                unset($this->params['category']);
            }
        }

        return true;
    }

    /**
     * @return bool|\Illuminate\Support\MessageBag
     */
    public function validate()
    {
        $this->validator = Validator::make($this->params, [
            'coordinates_longitude' => 'required',
            'coordinates_width' => 'required',
            'name' => 'required',
            'addres' => 'required',
            'category' => Rule::in(['autoshop','bijouterie','haberdashery','presents','alcohol','bank','bookmakers','veterinary','hypermarketAppliances','hypermarketChildren','hypermarketProducts','hypermarketBuilding','home','loans','pharmacy','optics','orthopedist','petSupplies','onlineStore','chancellery','cosmetic','eliteCosmetics','pawnshop','furniture','medicalLaboratory','medicalCenter','hookahBar','bar','bistro','cafe','coffeeHouse','confectionery','bakery','pizzeria','restaurant','diningRoom','fastFood','sushi','lingerie','shoes','sportGoods','economyClothing','federal','meatFarmer','honeyTea','beer','cigarettes','manufacturedGoods','other','constructionTeam','beautySalon','phone','dentistry','fabrics','fitnessClub','flowers','jewelry']),
            'source' => Rule::in(['avito', 'cyanide', 'komned', 'yandex']),
            'type_ads' => Rule::in(['rent', 'sale', 'ppa']),
            'status' => Rule::in(['active', 'archived']),
            'object_ads' => Rule::in(['retail', 'publicCatering', 'free', 'office', 'build', 'warehouses', 'busuness']),
            'floor' => Rule::in(['plinth', 'one', 'two', 'three', 'basement', 'dresscircle']),
        ]);

        if ($this->validator->fails()) {
            return false;
        }

        return true;
    }

    /**
     *
     */
    public function create()
    {
        if (!$this->validate()) {
            return false;
        }

        $url = trim($this->url, '/').'/api/ads/';

        $responseJson = CurlHelper::request($url, json_encode($this->params), [
            'Content-Type: application/json',
            'Authorization: Token '.$this->token
        ]);

        return $responseJson;
    }

    /**
     *
     */
    public function update(int $id)
    {
        $url = trim($this->url, '/').'/api/ads/'.$id.'/';
    }

    /**
     * @param int $id
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function delete(int $id)
    {
        $url = trim($this->url, '/').'/api/ads/'.$id.'/';

        $responseJson = CurlHelper::request($url, json_encode($this->params), [
            'Content-Type: application/json',
            'Authorization: Token '.$this->token
        ], '', false, 'DELETE');

        return $responseJson;
    }

    /**
     * @param string $type
     * @return mixed|string
     */
    private function crm2Type(string $type)
    {
        $types = [
            'Аренда' => 'rent',
            'Продажа' => 'sale',
            'ППА' => 'ppa',
        ];

        if (!isset($types[$type])) {
            return '';
        }

        return $types[$type];
    }

    /**
     * @param string $obj
     * @return mixed|string
     */
    private function crm2Obj(string $obj)
    {
        $objects = [
            'Торговая (Street retail )' => 'retail',
            'Торговая площадь' => 'retail',
            'Свободное назначение' => 'free',
            'Офисная' => 'office',
            'ТЦ' => 'retail',
            'Test obj_type' => 'retail',
            'Торговая' => 'retail',
            'Торговые' => 'retail',
            'ОСЗ' => 'free',
            'Офисные' => 'office',
            'ТК' => 'free',
            'Складские' => 'warehouses',
            'Павильоны' => 'build',
            'БЦ' => 'retail',
            'commercial' => 'retail',
            'floorSpace' => 'retail',
            'Здание' => 'build',
        ];

        if (!isset($objects[$obj])) {
            return '';
        }

        return $objects[$obj];
    }

    /**
     * @param string $source
     * @return mixed|string
     */
    private function crm2Source(string $source)
    {
        $sources = [
          'Циан' => 'cyanide',
          'Avito' => 'avito',
          'Yandex' => 'yandex',
          'Komned' => 'komned',
        ];

        if (!isset($sources[$source])) {
            return '';
        }

        return $sources[$source];
    }

    /**
     * @param string $category
     * @return mixed|string
     */
    private function crm2Cat(string $category)
    {
        $categories = array_flip(['autoshop' => 'автомагазин','bijouterie' => 'аксесуары - бижутерия','haberdashery' => 'аксесуары - галантерея','presents' => 'аксесуары - подарки','alcohol' => 'алкоголь','bank' => 'банк','bookmakers' => 'букмекеры','veterinary' => 'ветеринарная клиника','hypermarketAppliances' => 'гипермаркет - быт техника','hypermarketChildren' => 'гипермаркет - дет товары','hypermarketProducts' => 'гипермаркет - продукты','hypermarketBuilding' => 'гипермаркет - стройтовары','home' => 'для дома','loans' => 'займы','pharmacy' => 'здоровье - аптека','optics' => 'здоровье - оптика','orthopedist' => 'здоровье - ортопед','petSupplies' => 'зоотовары','onlineStore' => 'интернет-магазин','chancellery' => 'канцелярия','cosmetic' => 'косметика','eliteCosmetics' => 'косметика элит','pawnshop' => 'ломбард','furniture' => 'мебель','medicalLaboratory' => 'мед. лаборатория','medicalCenter' => 'мед. центр','hookahBar' => 'кальянная','bar' => 'общепит - бар','bistro' => 'общепит - бистро','cafe' => 'общепит - кафе','coffeeHouse' => 'общепит - кофейня','confectionery' => 'общепит - кондитерская','bakery' => 'общепит - пекарня','pizzeria' => 'общепит - пиццерия','restaurant' => 'общепит - ресторан','diningRoom' => 'общепит - столовая','fastFood' => 'общепит - Фаст фуд','sushi' => 'общепит - суши','lingerie' => 'одежда - нижнее белье','shoes' => 'одежда - обувь','sportGoods' => 'одежда - спорттовары','economyClothing' => 'одежда эконом','federal' => 'продуктовая федеральная сеть','meatFarmer' => 'продукты - мясо (фермер)','honeyTea' => 'продукты мини - мед/чай','beer' => 'продукты мини - пиво','cigarettes' => 'продукты мини - сигареты','manufacturedGoods' => 'промтовары','other' => 'прочее','constructionTeam' => 'test','beautySalon' => 'салон красоты','phone' => 'сотовые','dentistry' => 'стоматология','fabrics' => 'ткани','fitnessClub' => 'гипермаркет - фитнес клуб','flowers' => 'цветы','jewelry' => 'ювелирка']);
        if (!isset($categories[$category])) {
            return '';
        }
        return $categories[$category];
    }

    /**
     * @param string $floor
     * @return mixed|string
     */
    private function crm2Floor(string $floor)
    {
        $floors = [
//          'Циан' => 'plinth',
          '1' => 'one',
          '2' => 'two',
          '3' => 'three',
//          'Komned' => 'basement',
//          'Komned' => 'dresscircle',
        ];

        if (!isset($floors[$floor])) {
            return '';
        }

        return $floors[$floor];
    }
}
