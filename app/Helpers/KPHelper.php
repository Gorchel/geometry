<?php

namespace App\Helpers;

use App\Classes\Blanks\BuyContinentBlanks;
use App\Classes\Blanks\BuyGeometryBlanks;
use App\Classes\Blanks\BuyWhitelistBlanks;
use App\Classes\Blanks\BuyWhitelistAnalyticBlanks;
use App\Classes\Blanks\RentContinentBlanks;
use App\Classes\Blanks\RentGeometryBlanks;
use App\Classes\Blanks\RentWhitelistBlanks;
use App\Classes\Blanks\BuyWhitelistNewBlanks;
use App\Classes\Blanks\TorgiAnalyticBlanks;
use App\Classes\Blanks\TorgiWithoutAddressBlanks;
use App\Classes\SalesUp\SalesupHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\SenderLinks;

/**
 * Class KPHelper
 * @package App\Helpers
 */
class KPHelper
{
    /**
     * @return array
     */
    public static function getList()
    {
        return [
            'buy_geometry' => ['name' => "Продажа (Геометрия) c аналитикой", 'class' => BuyGeometryBlanks::class, 'url' => env('APP_URL')],
            'buy_geometry_without_analytic' => ['name' => "Продажа (Геометрия)  без аналитики", 'class' => BuyGeometryBlanks::class, 'url' => env('APP_URL'), 'params' => ['analytic' => false]],
            'buy_geometry_sale' => ['name' => "Продажа (Геометрия) акция", 'class' => BuyGeometryBlanks::class, 'params' => ['sale' => true], 'url' => env('APP_URL')],
            'buy_geometry_sale_without_analytic' => ['name' => "Продажа (Геометрия) акция без аналитики", 'class' => BuyGeometryBlanks::class, 'params' => ['sale' => true, 'analytic' => false], 'url' => env('APP_URL')],
            'rent_geometry' => ['name' => "Аренда (Геометрия)", 'class' => RentGeometryBlanks::class, 'url' => env('APP_URL')],
            'rent_geometry_sale' => ['name' => "Аренда (Геометрия) акция", 'class' => RentGeometryBlanks::class, 'params' => ['sale' => true], 'url' => env('APP_URL')],
            'buy_continent' => ['name' => "Продажа (Континент) c аналитикой", 'class' => BuyContinentBlanks::class, 'url' => env('APP_URL_KONTINENT')],
            'buy_continent_without_analytic' => ['name' => "Продажа (Континент) без аналитики", 'class' => BuyContinentBlanks::class, 'url' => env('APP_URL_KONTINENT'), 'params' => ['analytic' => false]],
            'buy_continent_sale' => ['name' => "Продажа (Континент) акция", 'class' => BuyContinentBlanks::class, 'params' => ['sale' => true], 'url' => env('APP_URL_KONTINENT')],
            'buy_continent_sale_without_analytic' => ['name' => "Продажа (Континент) акция  без аналитики", 'class' => BuyContinentBlanks::class, 'params' => ['sale' => true, 'analytic' => false], 'url' => env('APP_URL_KONTINENT')],
            'rent_continent' => ['name' => "Аренда (Континент)", 'class' => RentContinentBlanks::class, 'url' => env('APP_URL_KONTINENT')],
            'rent_continent_sale' => ['name' => "Аренда (Континент) акция", 'class' => RentContinentBlanks::class, 'params' => ['sale' => true], 'url' => env('APP_URL_KONTINENT')],
            'buy_whitelist_analytic' => ['name' => "Продажа (Белый бланк) c аналитикой", 'class' => BuyWhitelistAnalyticBlanks::class, 'url' => env('APP_URL')],
            'buy_whitelist' => ['name' => "Продажа (Белый бланк без аналитики)", 'class' => BuyWhitelistBlanks::class, 'url' => env('APP_URL')],
            'rent_whitelist' => ['name' => "Аренда (Белый бланк)", 'class' => RentWhitelistBlanks::class, 'url' => env('APP_URL')],
            'buy_whitelist_new' => ['name' => "Продажа Торги Аналитика", 'class' => BuyWhitelistNewBlanks::class, 'url' => env('APP_URL')],
            'torgi_without_address' => ['name' => "Продажа Торги без адреса (рук-ль)", 'class' => TorgiWithoutAddressBlanks::class, 'url' => env('APP_URL')],
            'torgi_analytic' => ['name' => "Продажа Торги с адресом расчет (рук-ль)", 'class' => TorgiAnalyticBlanks::class, 'url' => env('APP_URL')],
        ];

    }

    /**
     * @param $object
     * @return array
     */
    public static function makeLinks($object)
    {
        if (empty($object)) {
            throw new NotFoundHttpException();
        }

        $links = [];

        foreach (static::getList() as $type => $value) {
            $linkModel = SenderLinks::generateLink($object['id'], $type, $object, $value['name']);

            if (empty($linkModel)) {
                continue;
            }

            $links[$value['name']] = $value['url'].'kp/'.$linkModel->link;
        }

        return $links;
    }
}
