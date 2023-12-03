<?php

namespace App\Classes\ApiParsing;

use App\Classes\ApiParsing\CartTypes\CartInterface;
use App\Classes\ApiParsing\CartTypes\Torgi;
use App\Classes\ARinvest\PropertySender;
use App\Classes\Parsing\CartTypes\LotOnline;
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
     * @var CartInterface|Torgi|LotOnline
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
            case ParsingQueue::TORGI_GOV:
                $this->cart = new Torgi();
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

        try {
            $result = CurlHelper::request($this->record->link);

            $total = (int)$this->cart->getTotal($result);
            $address = $this->cart->getAddress($result);
            $description = $this->cart->getDescription($result);
            $inventory = $this->cart->getInventory($result);

            if (!empty($inventory)) {
                $oldRecord = ParsingQueue::where('inventory', $inventory)
                    ->where('property_id', '>', 0)
                    ->where('status', ParsingQueue::STATUS_DONE)
                    ->where('id', '!=', $this->record->id)
                    ->first();

                if (!empty($oldRecord)) {
                    $this->complete($oldRecord['property_id'], $inventory);

                    return true;
                }
            }

            $data['attributes'] = [
                'address' => $address,
                'description' => $description,
//                'purchase-price' => $total,
                'customs' => [
                    Properties::CUSTOM_SOURCE => $this->cart->getType(),
                    Properties::CUSTOM_ADVERT_PRICE => $total,
                    Properties::CUSTOM_PROPERTY_LINK => $this->cart->getLink($result),
                ],
            ];

            $data['relationships'] = [];

            $this->cart->customData($data, $result);

            $dadataResult = PopulationHelper::getDadata($data, $address);

            if (!empty($dadataResult['lon']) && !empty($dadataResult['lat'])) {
                $residents = PopulationHelper::getResidents($data, $dadataResult['lon'], $dadataResult['lat']);
            }

            if (empty($this->record->property_id)) {
                $objResponse = $methods->objectCreate($data);

                if (!isset($objResponse['id'])) {
                    $this->record->status = ParsingQueue::STATUS_ERROR;
                    $this->record->save();

                    return false;
                }

                /** Send in site */
//                $sender = new PropertySender();
//                $sender->send($objResponse['id']);

                $this->complete($objResponse['id'], $inventory);
            } else {
                $objResponse = $methods->objectGeneralUpdate($data['attributes'], $this->record->property_id, $data['relationships']);
                $this->complete($this->record->property_id, $inventory);
            }



            return true;
        } catch (Exception $e) {
            $this->record->status = ParsingQueue::STATUS_ERROR;

            $details = [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];

            $this->record->details = json_encode($details);
            $this->record->save();
        }

        return true;
    }

    /**
     * @param int $propertyId
     * @param string $inventory
     */
    protected function complete(int $propertyId, string $inventory)
    {
        $this->record->status = ParsingQueue::STATUS_DONE;
        $this->record->inventory = $inventory;
        $this->record->property_id = $propertyId;
        $this->record->details = $propertyId;
        $this->record->save();
    }
}
