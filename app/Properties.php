<?php

namespace App;

use Carbon\Carbon;
use App\Helpers\CustomHelper;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Properties
 *
 * @property int id
 * @property string attributes
 *
 * @property string full_name
 * @method full_name(string $postfix = null)
 *
 * @package App
 */
class Properties extends Model
{
    protected $table      = 'properties';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    protected $fillable = [
        'attributes', 'customs', 'relationships','created_at', 'updated_at', 'transformation_relation','system_statistic','current_responsible','update_details','ar_id',
        'is_updated'
    ];

    const ACTIVE_VIP = 10306;
    const ACTIVE = 10041;
    const URGENT_VIP = 10305;
    const URGENT = 10044;
    const ARCHIVE_ADVERT = 10398;
    const ARCHIVE = 10043;
    const ADVERT = 10396;
    const WAS_PASSED = 10315;//сдан
    const IN_PROCESS = 10304;//сдан

    const RENT_TYPE = 4;
    const BUY_TYPE = 3;

    public const CUSTOM_TYPE = 'custom-62518'; //Продажа, Аренда
    public const CUSTOM_FLOOR = 'custom-68794'; //этаж /этажность
    public const CUSTOM_TYPE_OF_PROPERTY = 'custom-61755'; //Продажа, Аренда
    public const CUSTOM_TYPE_OF_ACTIVITY = 'custom-61774';
    public const CUSTOM_TYPE_LIST = [
        'Аренда', 'Продажа', 'ППА'
    ];

    public const DISTRICT_CUSTOM = 'custom-64791'; //Продажа, Аренда

    public const TYPE_OWNER_FROM_CONTACT = 'custom-88334'; //Тип собств из контакта
    public const TYPE_AGENT_FROM_CONTACT = 'custom-88335'; //Тип агента из контакта

    public const IS_NOT_STATISTIC = 0;
    public const IS_STATISTIC = 1;
    public const IS_STATISTIC_ERROR = 2;

    public const IS_NOT_SYSTEM_STATISTIC = 0;
    public const IS_SYSTEM_STATISTIC = 1;
    public const IS_SYSTEM_STATISTIC_ERROR = 2;

    public const EXCEPT_COMPANIES = 'custom-87921';
    public const LINK_TO_ANALYTICS_OLD = 'custom-87932';
    public const LINK_TO_ANALYTICS = 'custom-87946';
    public const LINK_TO_GOOGLE_ANALYTICS = 'custom-87855';
    public const LINK_TO_BST_OLD = 'custom-87944';
    public const LINK_TO_BST = 'custom-87947';
    public const CUSTOM_POPULATION = 'custom-69093';
    public const CUSTOM_POPULATION_RESIDENTS = 'custom-87857';
    public const CUSTOM_SOURCE = 'custom-84066';
    public const CUSTOM_SOURCE_LOT_ONLINE = 'Lot Online';
    public const CUSTOM_SOURCE_TORGI = 'Torgi.gov';
    public const CUSTOM_SOURCE_TORGIRU = 'Торги.ru';
    public const CUSTOM_SOURCE_CIAN = 'Циан';
    public const CUSTOM_ADVERT_PRICE = 'custom-87644';
    public const CUSTOM_ADVERT_RENT_PRICE = 'custom-87646';
    public const CUSTOM_PROPERTY_LINK = 'custom-88195';

    public const CUSTOM_BUDGET_VOLUME_BUY = 'custom-76997';//По бюджету руб в м
    public const CUSTOM_BUDGET_FOOTAGE_BUY = 'custom-76998';//По бюджету кв/м
    public const CUSTOM_BUDGET_VOLUME_RENT = 'custom-76732';//По бюджету руб в м
    public const CUSTOM_BUDGET_FOOTAGE_RENT = 'custom-76288';//По бюджету кв/м

    public const CUSTOM_TOTAL_AREA = 'custom-64803';//По бюджету кв/м

    public const CUSTOM_SALDO_RESALE = 'custom-76292';//По бюджету кв/м
    public const CUSTOM_SALDO_RENT = 'custom-87845';//По бюджету кв/м

    public const CUSTOM_STATUS = 'custom-71235';//По бюджету кв/м
    public const CUSTOM_STATUS_TORGI = 'custom-88239';//По бюджету кв/м

    public const BST_START_CALC = 'custom-88089';
    public const BST_END_CALC = 'custom-88090';
    public const BST_AVG_CALC = 'custom-88091';

    public const NOT_OFFER_COMPANY = 'custom-87921';

    public const DEFAULT_MANAGER_NUMBER = '+7 911 261-77-88';
    public const DEFAULT_FILTER_USER = 61755;
    public const DEFAULT_FILTER_STAGE = 32745;

    public const SOURCE_LOTONLINE = 315341;
    public const CUSTOM_YANDEX_PANORAMA = 'custom-61775';
    public const CUSTOM_2GIS_PANORAMA = 'custom-88498';
    public const BUSINESS_USAGE_TYPE_RETAIL = 'retail';
    public const OBJECT_TYPE_RETAIL = 'commerce:retail';

    public const RESPONSIBILITY_USER_TORGI_ID = 74629;


    /**
     * @param string $postfix
     * @return string|string[]
     */
    public function getFullNameAttribute()
    {
        $attribute = json_decode($this['attributes'], true);
        $customs = json_decode($this['customs'], true);

        $name = $this['id'].'_'.$attribute['address'].'_'.$attribute['total-area'];

        $type = CustomHelper::issetField($customs, Properties::CUSTOM_TYPE, []);

        if (!empty($type)) {
            $name .= '_'.$type[0];
        }

        $name .= '_'.rand(0,10).rand(0,10);

        $name = str_replace(' ','_', trim($name));
        $name = str_replace(' ','_', trim($name));
        $name = str_replace('/','_', trim($name));
        $name = str_replace('.','_', trim($name));
        $name = str_replace(',','_', trim($name));
        $name = str_replace('__','_', trim($name));

        return $name;
    }

    /**
     * @param $longitude
     * @param $latitude
     * @return string
     */
    public static function makeYaLink($longitude, $latitude): string
    {
        return "https://yandex.ru/maps/2/saint-petersburg/?l=stv%2Csta&panorama%5Bpoint%5D={$longitude}%2C{$latitude}&z=13";
    }

    /**
     * @param $longitude
     * @param $latitude
     * @return string
     */
    public static function make2gisLink($longitude, $latitude): string
    {
        return "https://2gis.ru/geo/{$longitude}%2C{$latitude}?m={$longitude}%2C{$latitude}%2F17.82";
    }
}
