<?php

namespace App\Classes\Parsing;

use App\Classes\ARinvest\PropertySender;
use App\Classes\Parsing\CartTypes\CartInterface;
use App\Classes\Parsing\CartTypes\LotOnline;
use App\Classes\Parsing\CartTypes\TorgiRu;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\PopulationHelper;
use App\Classes\Zenrows\ZenrowsRequest;
use App\ParsingQueue;
use App\Properties;
use Exception;
use PHPHtmlParser\Dom;

/**
 * Class CartParser
 * @package App\Classes\Parser
 */
class CartParser
{
    /**
     * @var ParsingQueue
     */
    public ParsingQueue $record;
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
            case ParsingQueue::LOT_ONLINE:
                $this->cart = new LotOnline();
                break;
            case ParsingQueue::TORGI_RU:
                $this->cart = new TorgiRu();
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
            $dom = $this->cart->getHtml($this->record->link);

            //check 404
            if ($this->cart->checkErrorFlag($dom)) {
                $this->record->status = ParsingQueue::STATUS_ERROR;
                $this->record->save();

                return true;
            }

            $total = $this->cart->getTotal($dom);
            $address = $this->cart->getAddress($dom);
            $description = $this->cart->getDescription($dom);
            $name = $this->cart->getName($dom);
            $inventory = $this->cart->getInventory($dom);

            if (!empty($inventory)) {
                $oldRecord = ParsingQueue::where('inventory', $inventory)
                    ->where('property_id', '>', 0)
                    ->where('status', ParsingQueue::STATUS_DONE)
                    ->where('type', $this->record->type)
                    ->where('id', '!=', $this->record->id)
                    ->first();

                if (!empty($oldRecord)) {
                    $this->complete($oldRecord['property_id'], $inventory);

                    return true;
                }
            }

            $data['attributes'] = [
                'address' => $address,
//                'name' => $name,
                'description' => $description,
                'purchase-price' => $total,
                'customs' => [
                    Properties::CUSTOM_SOURCE => $this->cart->getType(),
                    Properties::CUSTOM_ADVERT_PRICE => $total,
                    Properties::CUSTOM_PROPERTY_LINK => $this->record->link,
                ],
            ];

            $dadataResult = PopulationHelper::getDadata($data, $address);

            try {
                if (!empty($dadataResult['lon']) && !empty($dadataResult['lat'])) {
                    $residents = PopulationHelper::getResidents($data, $dadataResult['lon'], $dadataResult['lat']);
                }
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

//            $data['relationships'] = [];

            $this->cart->customData($data, $dom);

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
