<?php
namespace App\Console\Commands;
use App\Classes\SalesUp\SalesupHandler;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Orders;
use App\SameDeals;
use App\Company;
use App\Classes\Form\PropertyForm;
use App\Classes\SalesUp\SalesupMethods;
use App\Classes\Filters\FilterOrders;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class FindSameOrders
 * @package App\Console\Commands
 */
class FindSameOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find_same_orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Поиск схожих заявок';

    /** @var SalesupMethods */
    protected $methods;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $handler = new SalesupHandler(env('API_TOKEN'));
        $this->methods = $handler->methods;
    }

    /**
     *
     */
    const DEAL_STAGE_CATEGORY = 36769;
    /**
     *
     */
    const DEAL_STAGE_METRO = 267445;
    /**
     *
     */
    const DEAL_STAGE_STREET = 267446;
    /**
     *
     */
    const DEAL_STAGE_STREET_HOUSE = 267447;

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $this->deleteOldDeals();

        $response = [];
        $filterOrdersClass = new FilterOrders;
        $customFields = $filterOrdersClass->customFields;

        Orders::select('id', 'attributes', 'customs', 'type')
            ->where('type', '=', Orders::RENT_TYPE)
            ->chunk(1000, function($orders) use (&$response, $customFields) {
                $cityField = $customFields[Orders::RENT_TYPE]['city_field'];

                foreach ($orders as $order) {
                    $customs = json_decode($order['customs'], true);

                    if (!isset($customs[$cityField][0])) {
                        continue;
                    }

                    $city = $customs[$cityField][0];

                    if ($city == 'Санкт-Петербург') {
                        $city = 2;
                    } else {
                        $city = 1;
                    }

                    if (!isset($response[$city])) {
                        $response[$city] = [];
                    }

                    $localArray = [
                        'id' => $order['id'],
                    ];

                    foreach (['district', 'metro', 'street'] as $type) {
                        $value = CustomHelper::issetField($customs, $customFields[Orders::RENT_TYPE]['city'][$type][$city]['custom']);

                        if (is_array($value)) {
                            $value = array_diff($value, ['']);
                        }

                        if (empty($value)) {
                            continue;
                        }

                        if ($type == 'street') {
                            $value = strip_tags($value);
                            $value = trim(html_entity_decode($value), " \t\n\r\0\x0B\xC2\xA0");

                            if (empty($value)) {
                                continue;
                            }

                            $valueArr = explode(';', $value);

                            if (empty($valueArr)) {
                                continue;
                            }

                            foreach ($valueArr as $fullStreet) {
                                $simpleArr = explode(',', $fullStreet);

                                if (isset($simpleArr[0])) {
                                    if (!isset($localArray['street'])) {
                                        $localArray['street'] = [];
                                    }

                                    $localArray['street'][] = trim($simpleArr[0]);
                                }

                                if (isset($simpleArr[0]) && isset($simpleArr[1])) {
                                    if (!isset($localArray['street_house'])) {
                                        $localArray['street_house'] = [];
                                    }

                                    $localArray['street_house'][] = trim($simpleArr[0]) . ' ' . trim($simpleArr[1]);
                                }
                            }
                        } else {
                            $localArray[$type] = $value;
                        }
                    }

                    if (count($localArray) <= 1) {
                        continue;
                    }

                    foreach (['street', 'street_house'] as $key) {
                        if (isset($localArray[$key])) {
                            $localArray[$key] = array_diff($localArray[$key], ['']);
                        }
                    }

                    $response[$city][] = $localArray;
                }
            });

        //Подсчет
        if (empty($response)) {
            return true;
        }

        foreach ($response as $city => $orderResponseData) {
            $districts = $this->getJsonData('district', $city);
            $metro = $this->getJsonData('metro', $city);

//            $this->searchNewDeals($orderResponseData, $districts, 'district');
//            $this->searchNewDeals($orderResponseData, $metro, 'metro', self::DEAL_STAGE_METRO);
            $this->searchNewDealsByStreet($orderResponseData,'street', self::DEAL_STAGE_STREET);
            $this->searchNewDealsByStreet($orderResponseData,'street_house', self::DEAL_STAGE_STREET_HOUSE);
        }

        return true;
    }

    /**
     * @param array $orderResponseData
     * @param $key
     * @param int|null $stage
     * @return bool
     * @throws \Exception
     */
    protected function searchNewDealsByStreet(array $orderResponseData, $key, int $stage = null)
    {
        $stopStreet = [];

        foreach ($orderResponseData as $order) {
            if (!isset($order[$key])) {
                continue;
            }

            foreach($order[$key] as $street) {
                $dealRelationships = $this->getRelations($stage);

                if (in_array($street, $stopStreet)) {
                    continue;
                }

                foreach ($orderResponseData as $findOrder) {
                    if ($order['id'] == $findOrder['id']) {
                        continue;
                    }

                    if (!isset($findOrder[$key])) {
                        continue;
                    }

                    if (in_array($street, $findOrder[$key])) {
                        if (!isset($dealRelationships['orders']['data'])) {
                            $dealRelationships['orders']['data'] = [];
                        }

                        $dealRelationships['orders']['data'][] = [
                            'type' => 'orders',
                            'id' => $findOrder['id']
                        ];
                    }
                }

                $stopStreet[] = $street;

                $result = $this->createDeal($dealRelationships, $street);
            }
        }

        return true;
    }

    /**
     * @param array $orderResponseData
     * @param array $searchData district/metro
     * @param string $key
     * @param int|null $stage
     * @return bool
     * @throws \Exception
     */
    protected function searchNewDeals(array $orderResponseData, array $searchData, string $key, int $stage = null)
    {
        foreach ($searchData as $searchValue) {
            $dealRelationships = $this->getRelations($stage);

            foreach ($orderResponseData as $order) {
                if (!isset($order[$key])) {
                    continue;
                }

                if (in_array($searchValue, $order[$key])) {
                    if (!isset($dealRelationships['orders']['data'])) {
                        $dealRelationships['orders']['data'] = [];
                    }

                    $dealRelationships['orders']['data'][] = [
                        'type' => 'orders',
                        'id' => $order['id']
                    ];
                }
            }

            $this->createDeal($dealRelationships, $searchValue);
        }
    }

    /**
     * @param $dealRelationships
     * @param $searchValue
     * @return bool
     * @throws \Exception
     */
    protected function createDeal($dealRelationships, $searchValue)
    {
        if (!isset($dealRelationships['orders'])) {
            return false;
        }

        if (count($dealRelationships['orders']['data']) <= 1) {
            return false;
        }

        $dealData = [
            'relationships' => $dealRelationships,
            'attributes' => [
                'name' => 'Сделка по совпадению "'.$searchValue.'"',
            ],
        ];

        $deal = $this->methods->dealCreate($dealData);

        if (isset($deal['id'])) {
            //Store SameDeal
            $sameDeal = new SameDeals();
            $sameDeal->deal_id = $deal['id'];
            $sameDeal->save();
        }

        return true;
    }

    /**
     * @param $stage
     * @return array
     */
    protected function getRelations($stage)
    {
        $dealRelationships = [
            'stage-category' => [
                'data' => [
                    'type' => 'deal-stage-categories',
                    'id' => self::DEAL_STAGE_CATEGORY
                ]
            ]
        ];

        if (!empty($stage)) {
            $dealRelationships['stage'] = [
                'data' => [
                    'type' => 'deal-stages',
                    'id' => $stage
                ]
            ];
        }

        return $dealRelationships;
    }

    /**
     * @param string $type
     * @param int $city
     * @return array
     */
    protected function getJsonData(string $type, int $city) {
        $path = app()->basePath('public/'.$type.'.json');
        $data = json_decode(file_get_contents($path), true);

        if ($city == 1) {
            $data = $data['msk'];
        } else {
            $data = $data['spb'];
        }

        return array_column($data, 'name');
    }

    /**
     * @throws \Exception
     */
    protected function deleteOldDeals()
    {
        $yesterday = Carbon::yesterday();

        $yesterdayDeals = SameDeals::whereDate('created_at', '<=', $yesterday->format('Y-m-d'))
            ->get();

        if (!empty($yesterdayDeals)) {
            foreach ($yesterdayDeals as $sameDeal) {
                try {
                    $this->methods->deleteDeal($sameDeal->deal_id);
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                }

                $sameDeal->delete();
            }
        }
    }
}
