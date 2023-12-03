<?php

namespace App\Classes\ApiParsing;

use App\Classes\ApiParsing\CartTypes\CartInterface;
use App\Classes\ApiParsing\CartTypes\Torgi;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CurlHelper;
use App\Helpers\CustomHelper;
use App\ParsingQueue;
use App\Properties;
use Exception;
use App\Helpers\PopulationHelper;

/**
 * Class StatusParser
 * @package App\Classes\ApiParsing
 */
class StatusParser
{
    /**
     * @var ParsingQueue
     */
    public ParsingQueue $record;
    /**
     * @var CartInterface|Torgi
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
            $property = $methods->getObject($this->record->property_id);

            if (empty($property)) {
                $this->record->status = ParsingQueue::STATUS_GONE;
                $this->record->save();

                return false;
            }

            $result = CurlHelper::request($this->record->link);

            $status = $this->cart->getStatus($result);
            $successStatus = $this->cart->getSuccessStatuses();

            $propertyStatus = CustomHelper::issetFieldIncludeArray($property['attributes']['customs'], 'custom-88239', '');

            if ($this->cart->getStatusName($status) != $propertyStatus) {
                $data = [
                    'attributes' => [
                        'customs' => [
                            'custom-88239' => $this->cart->getStatusName($status),
                            'custom-74193' => $this->cart->getStatusName($status)
                        ]
                    ]
                ];

                $methods->objectGeneralUpdate($data['attributes'], $this->record->property_id);
            }

            if (!in_array($status, $successStatus)) {
                $this->record->status = ParsingQueue::STATUS_GONE;
                $this->record->save();
            }

            return true;
        } catch (Exception $e) {
            $this->record->status = ParsingQueue::STATUS_ERROR;

            $details = [
                'type' => 'StatusParser',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ];

            $this->record->details = json_encode($details);
            $this->record->save();
        }

        return true;
    }
}
