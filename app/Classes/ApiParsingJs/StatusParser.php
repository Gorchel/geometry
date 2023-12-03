<?php

namespace App\Classes\ApiParsingJs;

use App\Classes\ApiParsingJs\CartTypes\CartInterface;
use App\Classes\ApiParsingJs\CartTypes\Cian;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CurlHelper;
use App\Helpers\CustomHelper;
use App\ParsingQueue;
use App\Properties;
use Exception;
use App\Helpers\PopulationHelper;

/**
 * Class StatusParser
 * @package App\Classes\ApiParsingJs
 */
class StatusParser
{
    /**
     * @var ParsingQueue
     */
    public ParsingQueue $record;
    /**
     * @var CartInterface|Cian
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

        try {
            $property = $methods->getObject($this->record->property_id);

            if (empty($property)) {
                return false;
            }

            $options = [
                'http' => [
                    "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
                ]
            ];

            $context = stream_context_create($options);
            $html = file_get_contents($this->record->link, false, $context);

            $key = 'window._cianConfig[\'frontend-offer-card\'] || []).concat(';
            $html = str_replace($key, '', strstr($html, $key));

            $key = '"offer":';
            $offerJson = str_replace($key, '', strstr($html, $key));

            $key = ',"seoLinks"';
            $offerJson = str_replace($key, '', strstr($offerJson, $key, true));

            $offer = json_decode($offerJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Json error');
            }

            if (!isset($offer['status'])) {
                throw new Exception('Status not found');
            }

            $status = $offer['status'];
            $successStatus = $this->cart->getSuccessStatuses();

            $propertyStatus = CustomHelper::issetFieldIncludeArray($property['attributes']['customs'], 'custom-74193', '');

            if ($this->cart->getStatusName($status) != $propertyStatus) {
                $data = [
                    'attributes' => [
                        'customs' => [
                            'custom-74193' => $this->cart->getStatusName($status),
                            'custom-88239' => $this->cart->getStatusName($status)
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
