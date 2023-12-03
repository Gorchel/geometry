<?php

namespace App\Classes\RestApp;

use App\BstBridge;
use App\Classes\SalesUp\SalesupHandler;
use App\Properties;
use Illuminate\Support\Facades\Log;
use App\Classes\BST\ApiPlaceClass;
use Carbon\Carbon;
use App\RestAppBridge;
use Exception;

/**
 * Class Import
 * @package App\Classes\RestApp
 */
class Import
{
    public const RENT_URL = 'https://rest-app.net/client/realty/0.csv';
    public const TEST_RENT_URL = __DIR__.'/../../../public/test/0.csv';
    public const BUY_URL = 'https://rest-app.net/client/realty/2.csv';
    public const RENT_TYPE = 1;
    public const BUY_TYPE = 2;

    public $type;
    public $url;
    public $methods;
    public $s2Id = 1;
    public $bstMethods;

    public function __construct(int $type = self::RENT_TYPE)
    {
        $this->type = $type;

        if ($type == self::RENT_TYPE) {
            $this->url = self::RENT_URL;
//            $this->url = self::TEST_RENT_URL;
        } else {
            $this->url = self::BUY_TYPE;
        }

        $handler = new SalesupHandler(env('API_TOKEN'));
        $this->methods = $handler->methods;

        $this->bstMethods = new ApiPlaceClass();
    }

    /**
     * @param array $object
     * @return array
     * @throws Exception
     */
    public function simpleBstImport(array $object)
    {
        if (empty($object)) {
            return ['status' => false, 'text' => 'method not found'];
        }

        $bridgeModel = BstBridge::where('property_id', $object['id'])
            ->first();

        $lon = $object['attributes']['longitude'];
        $lat = $object['attributes']['latitude'];
        $address = $object['attributes']['address'];
//        $city = $object['attributes']['locality'];
        $city = 'Санкт-Петербург';

        if (empty($address)) {
            return ['status' => false, 'text' => 'Wrong address', 'object' => json_encode($object)];
        }

        if (empty($city)) {
            return ['status' => false, 'text' => 'Wrong city', 'object' => json_encode($object)];
        }

        if (empty($lon) || empty($lat)) {
            return ['status' => false, 'text' => 'Wrong geo', 'object' => json_encode($object)];
        }

        if (empty($bridgeModel)) {
            $bstId = $this->bstMethods->createMarket($lon, $lat, $address, $city);

            if (empty($bstId)) {
                return ['status' => false, 'text' => 'Not store bst', 'object' => json_encode($object)];
            }

            $bridgeModel = new BstBridge;
            $bridgeModel->property_id = $object['id'];
            $bridgeModel->bst_id = $bstId;
            $bridgeModel->response = json_encode($object);

            if (!$bridgeModel->save()) {
                return ['status' => false, 'text' => 'Not $bridgeModel', 'object' => json_encode($object)];
            }
        } else {
            $this->bstMethods->updateMarket($bridgeModel->bst_id, $lon, $lat, $address);
        }

        $data['customs'] = [
            Properties::LINK_TO_BST => 'https://geometry-invest.bst.digital/coverage/market/'.$bridgeModel->bst_id,
            Properties::BST_START_CALC => Carbon::now('Africa/Nairobi')->format('d.m.Y H:i:s'),
        ];

        $this->methods->objectGeneralUpdate($data, $object['id']);

        $this->bstMethods->updateStatistic($bridgeModel->bst_id);

        $bridgeModel->status = BstBridge::STATUS_PENDING;
        $bridgeModel->save();

        return ['status' => true, 'text' => 'BST id #'.$bridgeModel->bst_id];
    }

    /**
     * @param array $objects
     * @return bool
     * @throws Exception
     */
    public function import(array $objects)
    {
        if (empty($objects)) {
            return false;
        }

        $this->createOrUpdateObj($objects);

        return true;
    }

    /**
     * @param array $objects
     * @return bool
     * @throws \Exception
     */
    private function createOrUpdateObj(array $objects)
    {
        foreach ($objects as $object) {
            $bridgeModel = RestAppBridge::where('link', $object[RestAppBridge::HEADER_FILE['link']])
                ->withTrashed()
                ->first();

            if (empty($bridgeModel)) {
                $response = $this->create($object);
            } else {
                $response = $this->update($object, $bridgeModel);
            }

            if (!$response) {
                Log::error('Rental import error '.json_encode($object));
            }
        }

        return true;
    }

    /**
     * @param array $object
     * @return bool
     * @throws \Exception
     */
    private function create(array $object)
    {
        $jsonObject = json_encode($object);
        $md5 = md5($jsonObject);

        //Create company
        $companyData = [
            'attributes' => [
                'name' => $object[RestAppBridge::HEADER_FILE['landlord']],
                'general-phone' => $object[RestAppBridge::HEADER_FILE['phone']]
            ]
        ];

        $companyResponse = $this->methods->companyCreate($companyData);

        if (!isset($companyResponse['id'])) {
            return false;
        }
        //Create property
        $data = $this->getS2Property($object);

        $data['relationships'] = [
            'company' => [
                'data' => [
                    'type' => 'companies',
                    'id' => $companyResponse['id'],
                ]
            ],
        ];

        $objResponse = $this->methods->objectCreate($data);

        if (!isset($objResponse['id'])) {
            return false;
        }

        $bridgeModel = new RestAppBridge();
        $bridgeModel->property_id = $objResponse['id'];
        $bridgeModel->link = $object[RestAppBridge::HEADER_FILE['link']];
        $bridgeModel->type = $this->type;
        $bridgeModel->json = $jsonObject;
        $bridgeModel->hash = $md5;

        if (!$bridgeModel->save()) {
            return false;
        }

        $lon = $object[RestAppBridge::HEADER_FILE['lon']];
        $lat = $object[RestAppBridge::HEADER_FILE['lat']];
        $address = $object[RestAppBridge::HEADER_FILE['address']];

        $addressArray = explode(',', $address);
        $city = '';

        if (isset($addressArray[0])) {
            $city = $addressArray[0];
        }

        $bstId = $this->bstMethods->createMarket($lon, $lat, $address, $city);

        if (empty($bstId)) {
            return true;
        }

        $bstModel = new BstBridge;
        $bstModel->property_id = $objResponse['id'];
        $bstModel->bst_id = $bstId;
        $bstModel->response = json_encode($objResponse);
        $bstModel->save();

        $this->bstMethods->updateStatistic($bstId);

        return true;
    }

    /**
     * @param array $object
     * @param RestAppBridge $bridgeModel
     * @return bool
     */
    private function update(array $object, RestAppBridge $bridgeModel)
    {
        if (!empty($bridgeModel->deleted_at)) {
            return false;
        }

        $jsonObject = json_encode($object);
        $md5 = md5($jsonObject);

        if ($bridgeModel->hash == $md5) {
            return false;
        }

        $data = $this->getS2Property($object);
        $objResponse = $this->methods->objectGeneralUpdate($data, $bridgeModel->property_id);

        if (!isset($objResponse['id'])) {
            return false;
        }

        $bridgeModel->json = $jsonObject;
        $bridgeModel->hash = $md5;

        return $bridgeModel->save();
    }

    /**
     * @param array $links
     * @return bool
     * @throws \Exception
     */
    public function removeObj(array $links)
    {
        $removedObj = RestAppBridge::whereNotIn('link', $links)
            ->where('type', $this->type)
            ->get();

        if (empty($removedObj)) {
            return true;
        }

        foreach ($removedObj as $obj) {
            try {
                $this->methods->propertyDelete($obj->property_id);
            } catch (Exception $e) {
                Log::error('Remove error #'.$obj['property_id'].'. Error '.$e->getMessage());
            }

            $obj->delete();
        }

        return true;
    }

    /**
     * @param array $object
     * @return array
     */
    private function getS2Property(array $object)
    {
        $data = [
            'attributes' => [
                'name' => $object[RestAppBridge::HEADER_FILE['name']],
                'description' => $object[RestAppBridge::HEADER_FILE['description']],
                'address' => $object[RestAppBridge::HEADER_FILE['address']],
                'longitude' => $object[RestAppBridge::HEADER_FILE['lon']],
                'latitude' => $object[RestAppBridge::HEADER_FILE['lat']],
                'total-area' => (int)$object[RestAppBridge::HEADER_FILE['total']],
                'customs' => [],
            ],
        ];

        if ($this->type == self::RENT_TYPE) {
            $data['attributes']['customs']['custom-62518'] = 'Аренда';
        } else {
            $data['attributes']['customs']['custom-62518'] = 'Продажа';
        }

        return $data;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getObjects(int $limit): array
    {
        $lineOfText = [];

        $file_handle = fopen($this->url, 'r');

        $handle = 0;
        $localArray = [];

        while (!feof($file_handle)) {
            $localArray[] = fgetcsv($file_handle, 0, ',');

            if ($handle >= $limit) {
                $handle = 0;
                $lineOfText[] = $localArray;
                $localArray = [];
            } else {
                $handle++;
            }
        }

        if (!empty($localArray)) {
            $lineOfText[] = $localArray;
        }

        fclose($file_handle);
        return $lineOfText;
    }
}
