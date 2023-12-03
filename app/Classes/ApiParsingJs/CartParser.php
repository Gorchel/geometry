<?php

namespace App\Classes\ApiParsingJs;

use App\Classes\ApiParsingJs\CartTypes\CartInterface;
use App\Classes\ApiParsingJs\CartTypes\Cian;
use App\Classes\ARinvest\PropertySender;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CurlHelper;
use App\ParsingQueue;
use App\Properties;
use Exception;
use App\Helpers\PopulationHelper;

/**
 * Class CartParser
 * @package App\Classes\ApiParsing
 */
class CartParser
{
    /**
     * @var ParsingQueue
     */
    public ParsingQueue $record;
    /**
     * @var CartInterface
     */
    public CartInterface $cart;

    /**
     * Parser constructor.
     * @param ParsingQueue $record
     * @throws \Exception
     */
    public function __construct(ParsingQueue $record)
    {
        $this->record = $record;

        switch ($record->type) {
            case ParsingQueue::CIAN:
                $this->cart = new Cian();
                break;
            default:
                throw new \Exception('Wrong cart type');
        }
    }

    /**
     *
     */
    public function run()
    {
        ini_set('max_execution_time', 900);

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $result = json_decode($this->record->json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Json error!');
        }

        $total = (int)$this->cart->getTotal($result);
        $address = $this->cart->getAddress($result);
        $description = $this->cart->getDescription($result);

        $data['attributes'] = [
            'address' => $address,
            'description' => $description,
//                'purchase-price' => $total,
            'customs' => [
                Properties::CUSTOM_SOURCE => $this->cart->getType(),
                Properties::CUSTOM_PROPERTY_LINK => $this->record->link,
            ],
        ];

//            $data['relationships'] = [];

        try {
            $this->cart->customData($data, $result);
        } catch (\Throwable $e) {
            $this->record->status = ParsingQueue::STATUS_ERROR;

            $details = [
                'type' => 'custom',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];

            $this->record->details = json_encode($details);
            $this->record->save();
        }

//            $dadataResult = PopulationHelper::getDadata($data, $address);
        try {
            $residents = PopulationHelper::getResidents($data, $data['attributes']['longitude'], $data['attributes']['latitude']);
        } catch (\Throwable $e) {
            $this->record->status = ParsingQueue::STATUS_ERROR;

            $details = [
                'type' => 'residents',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];

            $this->record->details = json_encode($details);
            $this->record->save();
        }

        if (empty($this->record->property_id)) {
            try {
                $objResponse = $methods->objectCreate($data);

                if (!isset($objResponse['id'])) {
                    $this->record->status = ParsingQueue::STATUS_ERROR;
                    $this->record->save();

                    return false;
                }

                /** Send in site */
//                $sender = new PropertySender();
//                $sender->send($objResponse['id']);

                $this->complete($objResponse['id']);
            } catch (\Throwable $e) {
                $this->record->status = ParsingQueue::STATUS_ERROR;

                $details = [
                    'type' => 'create',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ];

                $this->record->details = json_encode($details);
                $this->record->save();
            }

            try {
                $photos = $this->cart->getPhotos($result);

                if (!empty($photos)) {
                    foreach ($photos as $photo) {
                        $methods->downloadFile($photo['name'], $photo['url'], 'estate-properties', $objResponse['id']);
                    }
                }
            } catch (\Throwable $e) {
                $this->record->status = ParsingQueue::STATUS_DONE;

                $details = [
                    'type' => 'photo',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ];

                $this->record->details = json_encode($details);
                $this->record->save();
            }
        } else {
            try {
                $objResponse = $methods->objectGeneralUpdate($data['attributes'], $this->record->property_id, $data['relationships']);
                $this->complete($this->record->property_id);
            } catch (Exception $e) {
                $this->record->status = ParsingQueue::STATUS_ERROR;

                $details = [
                    'type' => 'isset obj',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ];

                $this->record->details = json_encode($details);
                $this->record->save();
            }
        }

        return true;
    }

    /**
     * @param int $propertyId
     */
    protected function complete(int $propertyId)
    {
        $this->record->status = ParsingQueue::STATUS_DONE;
        $this->record->property_id = $propertyId;
        $this->record->details = $propertyId;
        $this->record->save();
    }
}
