<?php

namespace App\Http\Controllers;

use App\Classes\Filters\FilterOrders;
use App\Classes\Filters\MainFilter;
use App\Classes\Form\ContactsForm;
use App\Classes\SalesUp\SalesupHandler;
use App\Company;
use App\Properties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class WebhookObjectsController
 * @package App\Http\Controllers
 */
class WebhookOrdersController extends AbstractFilterController
{
    /**
     * @var array
     */
    protected $messages = [
        'footage' => 'По площади (кв/м)',
        'budget_volume' => 'Арендная ставка в месяц',
        'budget_footage' => 'Арендная ставка за кв. м в месяц',
        'district' => 'Район', 'metro' => 'Метро', 'street' => 'Улица',
        'type' => 'По профилю компании',
    ];

    /**
     * @var string
     */
    protected $objectDistrictField = 'custom-64791';

    /**
     * @var string
     */
    protected $objectProfileOfCompany = 'custom-61774';
    /**
     * @var string
     */
    protected $typeOfPropertyField = 'custom-61755';

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function webhookOrdersFilter(Request $request)
    {
        Log::info(json_encode($request->all()));

        $id = $request->get('ids')[0];
        $token = env('API_TOKEN');
        $type = $request->get('type');
        $objectTypeId = $request->has('object_type') ? $request->get('object_type') : 4;
        $cityTypeId = $request->has('city_type') ? $request->get('city_type') : 2;
        $findByAll = $request->has('find_by_all') ? $request->get('find_by_all') : 0;

        $filterOrdersClass = new FilterOrders;

        $handler = new SalesupHandler($token);
        $methods = $handler->methods;

        $order = $methods->getOrder($id);
        $orderCustoms = $order['attributes']['customs'];

        $users = $methods->getUsers();
        $stages = $methods->getDealStagesCategories();

        $filterClass = new MainFilter();
//
//        //Конфиги
        $metroSelect = config('metro')[$cityTypeId];//Метро по городу
        $companyTypes = config('company_types');//Вид деятельности
        $typeOfProperties = config('type_of_property')[$objectTypeId];//Тип недвижимости

        $profileCompanies = [];

        $objectSlider = $filterClass->getSliderOrderData($objectTypeId, $orderCustoms);

        $districtArray = [];
        $regionArray = [];

        //С арендодатором
        $isLandlord = [];

        $is_landlord = $filterOrdersClass->getCustomArray($objectTypeId, 'is_landlord');

        if (isset($is_landlord) && !empty($orderCustoms[$is_landlord])) {
            $isLandlord = $orderCustoms[$is_landlord];
        }

        $data = [
            'token' => $token,
            'id' => $id,
            'type' => $type,
            'metroSelect' => $metroSelect,
            'objectTypes' => $companyTypes,
            'typeOfProperties' => $typeOfProperties,
            'metro' => [],
            'districtArray' => $districtArray,
            'regionArray' => $regionArray,
            'address' => null,
            'profileCompanies' => $profileCompanies,
            'objectSlider' => $objectSlider,
            'typeOfPropertyObj' => [],
            'objectType' => $objectTypeId,
            'isLandlord' => $isLandlord,
            'cityTypeId' => $cityTypeId,
            'propertyStatuses' => FilterOrders::$propertyStatuses,
            'findByAll' => $findByAll,
            'users' => $filterClass->convertUsers($users),
            'stages' => $filterClass->convertStagesCategories($stages),
        ];

        return view('orders.filter', $data);
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function webhookOrdersGet(Request $request)
    {
        $handler = new SalesupHandler($request->get('token'));
        $methods = $handler->methods;

        $object_type = $request->get('object_type');
        $cityTypeId = $request->get('cityTypeId');

        $filterClass = new MainFilter;
        $filterOrdersClass = new FilterOrders;

        $order = $methods->getOrder($request->get('id'));

        //Данные по фильтрам
        $objData = $filterClass->prepareData($request, $order, 'order', $object_type);

        if (empty($objData)) {
            return view('orders.error_page', [
                'msg' => "Выберите фильтры",
                'errors' => $this->getErrors($objData),
                'request' => $this->prepareRequest($request)
            ]);
        }

        $objData['object_type'] = $object_type;
        $filterOrders = [];

        //Получаем Список заявок
        Properties::where('type', $object_type)
            ->chunk(1000, function($properties) use (&$filterOrders, $filterOrdersClass, $cityTypeId, $objData, $object_type) {
                foreach ($properties as $property) {
                    $orderResponse = $filterOrdersClass->filterProperty($property, $objData, $cityTypeId, $object_type);

                    if (!empty($orderResponse)) {
                        $filterOrders[] = $property;
                    }
                }
            });

        if (empty($filterOrders)) {
            $msg = "Объекты недвижимости не найдены";
            return view('orders.error_page', [
                'msg' => $msg,
                'errors' => $this->getErrors($objData),
                'request' => $this->prepareRequest($request)
            ]);
        }

        //прописываем связи
        $companiesIds = [];
        $contacts = [];

        foreach ($filterOrders as $filterOrder) {
            $relationships = json_decode($filterOrder['relationships'], true);

            //Компании
            if (!empty($relationships['companies']['data'])) {
                foreach ($relationships['companies']['data'] as $company) {
                    $companiesIds[] = $company['id'];
                }
            }

            //Контакты
            if (!empty($relationships['contacts']['data'])) {
                foreach ($relationships['contacts']['data'] as $contact) {
//                    $contacts[$contact['id']] = $contact['id'];
                    ContactsForm::setContacts($contacts, $contact['id'], 'estate-properties', $filterOrder['id']);
                }
            }
        }

        $orderData = [];

        if (in_array($object_type, [1,2])) {
            foreach ($filterOrders as $order) {
                $orderData[] = [
                    'type' => 'orders',
                    'id' => $order['id'],
                ];
            }
        } else {
            $orderData[] = [
                'type' => 'orders',
                'id' => $order['id'],
            ];
        }

        //Проверяем компании
        //Проверяем компании
        $companiesData = [];

        if (!empty($companiesIds)) {
            Company::whereIn('id', $companiesIds)
                ->chunk(1000, function($companies) use (&$contacts, &$companiesData) {
                    foreach ($companies as $company) {
                        $companiesData[] = [
                            'type' => 'companies',
                            'id' => $company['id'],
                        ];

                        $relationships = json_decode($company['relationships'], true);

                        if (isset($relationships['contacts']['data'])) {
                            foreach ($relationships['contacts']['data'] as $contact) {
//                                    $contacts[$contact['data']['id']] = $contact['data']['id'];
                                ContactsForm::setContacts($contacts, $contact['data']['id'], 'companies', $company['id']);
                            }
                        }
                    }
                });
        }

        ContactsForm::prepareContacts($contacts);
//
//        $contactsData = [];
//
//        if (!empty($companies)) {
//            foreach ($contacts as $contactsId) {
//                $contactsData[] = [
//                    'type' => 'contacts',
//                    'id' => $contactsId,
//                ];
//            }
//        }

        if (!empty($request->get('stage_categories'))) {
            $stage = $request->get('stage_categories');
        } else {
            switch ($object_type) {
                case 1:
                case 2:
                    $stage = 32745;
                    break;
                case 3:
                    $stage = 32747;
                    break;
                default:
                    $stage = 32746;
            }
        }

        $dealName = ($cityTypeId == 1 ? '/' : '*');

        $data = [
            'attributes' => [
                'name' => 'Сделка по заявке '.$dealName,
                'description' => $order['id'],
                'customs' => [
                    'custom-73402' => $this->getParamsMsg($this->getErrors($objData))//Параетры фильтра
                ],
            ],
            'relationships' => [
                'stage-category' => [
                    'data' => [
                        'type' => 'deal-stage-categories',
                        'id' => $stage,//Воронка Аренда/Продажа
                    ]
                ],
            ]
        ];

        $relationships = [
//            'contacts' => [
//                'data' => $contactsData,
//            ],
            'companies' => [
                'data' => $companiesData,
            ],
            'orders' => [
                'data' => $orderData,
            ],
        ];

        if (!empty($request->get('responsible_users'))) {
            $relationships['responsible'] = [
                'data' => [
                    'type' => 'users',
                    'id' => $request->get('responsible_users'),
                ]
            ];
        }

        $dealResponse = $methods->dealCreate($data);
        $dealId = $dealResponse['id'];

        $methods->dealDataUpdate($dealId, $relationships);

        //Сохраняем контакты
        $contactForm = new ContactsForm();
        $contactForm->setDealId($dealId);
        $contactForm->storeContacts($contacts);

        if (in_array($object_type, [3,4])) {
            foreach ($filterOrders as $order) {
                $objDeals = [];

                if (isset($order['relationships']['deals']['data'])) {
                    foreach ($order['relationships']['deals']['data'] as $objDeal) {
                        $objDeals[$objDeal['id']] = $objDeal['id'];
                    }
                }

                $objDeals[$dealResponse['id']] = $dealResponse['id'];

                $methods->attachDealsToObject($objDeals, $order['id']);
            }
        }

        $viewData = [
            'deal' => $dealResponse,
            'objectsCount' => count($filterOrders),
        ];

        return view('orders.success', $viewData);
    }
}
