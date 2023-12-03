<?php

namespace App\Classes\Parsing;

use App\Classes\Parsing\CartTypes\CartInterface;
use App\Classes\Parsing\CartTypes\LotOnline;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CurlHelper;
use App\Helpers\CustomHelper;
use App\ParsingQueue;
use Exception;
use PHPHtmlParser\Dom;

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
     * @var CartInterface|LotOnline
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
            case ParsingQueue::LOT_ONLINE:
                $this->cart = new LotOnline();
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

            $dom = new Dom();
            $dom->loadFromUrl($this->record->link);
            $ajaxDom = $this->cart->getAjaxDetails($dom);

            $status = $this->cart->getStatus($ajaxDom);
            $successStatus = $this->cart->getSuccessStatuses();

            $propertyStatus = CustomHelper::issetFieldIncludeArray($property['attributes']['customs'], 'custom-74193', '');

            if ($status != $propertyStatus) {
                $data = [
                    'attributes' => [
                        'customs' => [
                            'custom-74193' => $status,
                            'custom-88239' => $status
                        ]
                    ]
                ];

                $methods->objectGeneralUpdate($data['attributes'], $this->record->property_id);
            }

            if (in_array($status, $successStatus)) {
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
