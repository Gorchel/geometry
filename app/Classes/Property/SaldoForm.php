<?php

namespace App\Classes\Property;

use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CustomHelper;
use App\ParsingQueue;
use App\Properties;
use Google\Service\AdExchangeBuyerII\Deal;
use Illuminate\Support\Facades\Log;
use App\Classes\Deals\Constants as DealConstants;
use Exception;

/**
 * Class SaldoForm
 * @package App\Classes\Property
 */
class SaldoForm
{
    /**
     * @var
     */
    protected $id;

    /**
     * @var \App\Classes\SalesUp\SalesupMethods
     */
    public $methods;

    /**
     * SaldoForm constructor.
     */
    public function __construct()
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $this->methods = $handler->methods;
    }

    /**
     * @param int $id
     */
    public function setPropertyId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function save()
    {
        $object = $this->methods->getFullObject($this->id);

        if (empty($object)) {
            Log::error('SaldoForm property #'.$this->id.' not found');
            return false;
        }

        if (!$this->validate($object)) {
            return false;
        }

        $attributes = $object['attributes'];
        $customs = $attributes['customs'];

        $address = $attributes['address'];

        $objectType = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_TYPE, null);

        if (!empty($objectType) && isset($objectType[0])) {
            if ($objectType[0] == 'Продажа') {
                //Сальдо продажа
                $saldoBuy = CustomHelper::issetField($customs, Properties::CUSTOM_SALDO_RESALE, 0);

                if ($saldoBuy == 0) {
                    return false;
                }

                if (!empty($saldoBuy)) {
                    $stage = $this->getStage(DealConstants::MPO_BUY_STAGE);

                    if ($saldoBuy > 0) {
                        $name = 'Сальдо +, '.$address;
                    } else {
                        $name = 'Сальдо -, '.$address;
                    }

                    $this->createDeal($object, $stage, $name);
                }
            } else {
                //Сальдо аренда
                $saldoRent = CustomHelper::issetField($customs, Properties::CUSTOM_SALDO_RENT, 0);

                if ($saldoRent == 0) {
                    return false;
                }

                if (!empty($saldoRent)) {
                    $stage = $this->getStage(DealConstants::MPO_RENT_STAGE);

                    if ($saldoRent > 0) {
                        $name = 'Сальдо +, '.$address;
                    } else {
                        $name = 'Сальдо -, '.$address;
                    }

                    $this->createDeal($object, $stage, $name);
                }
            }
        } else {
            Log::error('SaldoForm property #'.$this->id.' type error');
            return false;
        }

        return true;
    }

    /**
     * @param $object
     * @return bool
     */
    protected function validate($object): bool
    {
        $attributes = $object['attributes'];
        $customs = $attributes['customs'];

        $source = CustomHelper::issetField($customs, Properties::CUSTOM_SOURCE, '');
        $status = CustomHelper::issetField($customs, Properties::CUSTOM_STATUS, '');
        $statusTorgi = CustomHelper::issetField($customs, Properties::CUSTOM_STATUS_TORGI, '');
        $statusCian = CustomHelper::issetField($customs, 'custom-74193', '');

        if (isset($source[0]) && $source[0] == Properties::CUSTOM_SOURCE_CIAN) {
            //&& isset($statusCian[0]) && $statusCian[0] == 'published'
            return true;
        }

        if (isset($source[0]) && $source[0] == Properties::CUSTOM_SOURCE_TORGI && isset($statusTorgi[0]) && in_array($statusTorgi[0], [
            'Не состоялся', 'Опубликован', 'Прием заявок'
        ])) {
            return true;
        } elseif (isset($status[0]) && in_array($status[0], [
            'Активный', 'Горящий', 'В разработке'
        ])) {
            return true;
        }

        return false;
    }

    /**
     * @param array $object
     * @param int $stage
     * @param $name
     * @return mixed
     * @throws Exception
     */
    protected function createDeal(array $object, int $stage, $name)
    {
        $attributes = $object['attributes'];
        $objectRelationships = $object['relationships'];
        $address = $attributes['address'];

        $dealRelationships = [
            'stage-category' => [
                'data' => [
                    'type' => 'deal-stage-categories',
                    'id' => $stage,//Воронка Аренда/Продажа
                ]
            ],
        ];

        if (isset($objectRelationships['contacts']['data']) && !empty($objectRelationships['contacts']['data'])) {
            $dealRelationships['contacts']['data'] = $objectRelationships['contacts']['data'];
        }

        if (isset($objectRelationships['responsible']['data']) && !empty($objectRelationships['responsible']['data'])) {
            $dealRelationships['responsible']['data'] = $objectRelationships['responsible']['data'];
        }

//        if (isset($objectRelationships['source']['data']) && !empty($objectRelationships['source']['data'])) {
//            $dealRelationships['source']['data'] = $objectRelationships['source']['data'];
//        }

        $source = CustomHelper::issetField($attributes['customs'], Properties::CUSTOM_SOURCE, '');

        $data = [
            'attributes' => [
                'name' => $name,
                'customs' => [
                    'custom-87819' => $address,
                    'custom-65822' => CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_TYPE),
                    'custom-88285' => CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_STATUS),
                    DealConstants::PROPERTY_SOURCE => $source,
                ],
            ],
            'relationships' => $dealRelationships
        ];

        $source = CustomHelper::issetField($attributes['customs'], Properties::CUSTOM_SOURCE, '');

        if ($source == Properties::CUSTOM_SOURCE_TORGI) {
            $data['attributes']['customs']['custom-88283'] =  CustomHelper::issetField($object['attributes']['customs'], 'custom-88215');//Дата торгов
        }

        $dealResponse = $this->methods->dealCreate($data);

        if (empty($dealResponse)) {
            return false;
        }

        $objDeals = [];

        if (isset($objectRelationships['deals']['data'])) {
            foreach ($objectRelationships['deals']['data'] as $objDeal) {
                $objDeals[$objDeal['id']] = $objDeal['id'];
            }
        }

        $objDeals[$dealResponse['id']] = $dealResponse['id'];

        if (!empty($objDeals)) {
            $this->methods->attachDealsToObject($objDeals, $object['id']);
        }

        return true;
    }

    /**
     * @param int $defaultStage
     * @return int
     */
    protected function getStage(int $defaultStage) {
        $parsingQueue = ParsingQueue::where('property_id', $this->id)
            ->first();

        if (empty($parsingQueue)) {
            return $defaultStage;
        }

        switch ($parsingQueue->type){
            case ParsingQueue::TORGI_GOV:
                return DealConstants::TORGI_STAGE;
                break;
        }

        return $defaultStage;
    }
}
