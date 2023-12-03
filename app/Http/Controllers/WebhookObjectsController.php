<?php

namespace App\Http\Controllers;

use App\BstBridge;
use App\Classes\Filters\FilterOrders;
use App\Classes\Filters\MainFilter;
use App\Classes\Form\ContactsForm;
use App\Classes\Google\Sheet2S2;
use App\Classes\RestApp\Import;
use App\Classes\SalesUp\SalesupHandler;
use App\Classes\Companies\CompaniesList;
use App\Jobs\CopyContactNameJobs;
use App\Company;
use App\Contact;
use App\Orders;
use App\PropertiesSendingEntities;
use App\Properties;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\CustomHelper;
use App\Traits\UpdatedTablesTrait;

/**
 * Class WebhookObjectsController
 * @package App\Http\Controllers
 */
class WebhookObjectsController extends AbstractFilterController
{
    use UpdatedTablesTrait;

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
     * @var array
     */
    protected $status2id = [
        'ППА' => 112196,
        'Аренда' => 112197,
        'Продажа' => 112198,
        'Управление' => 112199,
        'Комиссия' => 120414,
    ];

    /**
     * @var string
     */
    protected $objectProfileOfCompany = 'custom-61774';
    /**
     * @var string
     */
    protected $exceptObjectProfileOfCompany = 'custom-86215';
    /**
     * @var string
     */
    protected $typeOfPropertyField = 'custom-61755';

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function webhookEstateFilter(Request $request)
    {
        Log::info(json_encode($request->all()));

        $id = $request->get('ids')[0];
        $token = env('API_TOKEN');
        $type = $request->get('type');

        $filterClass = new MainFilter;

        $handler = new SalesupHandler($token);
        $methods = $handler->methods;
        $object = $methods->getObject($id);

        $users = $methods->getUsers();

        $stages = $methods->getDealStagesCategories();

        $address = $object['attributes']['address'];
        $addressHouse = $object['attributes']['address'];

        $objectType = $request->get('object_type');

        if (empty($objectType)) {
            $typeArr = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_TYPE, []);//Слайдеры

            if (!empty($typeArr) && isset($typeArr[0])) {
                if ($typeArr[0] == 'Продажа') {
                    $objectType = Properties::BUY_TYPE;
                } else {
                    $objectType = Properties::RENT_TYPE;
                }
            } else {
                $objectType = Properties::RENT_TYPE;
            }
        }

        $cityTypeId = $request->has('city_type') ? $request->get('city_type') : 2;
        $findByAll = $request->has('find_by_all') ? $request->get('find_by_all') : 0;
//        $filterClass = new MainFilter();

        //Конфиги
        $metroSelect = config('metro')[$cityTypeId];//Метро по городу
        $companyTypes = config('company_types');//Вид деятельности
        $typeOfProperties = config('type_of_property')[$objectType];//Тип недвижимости

        //Подготовка значений
        $metro = trim(mb_strtolower($object['attributes']['subway-name']));//Метро
//        $disabledCompanies = strip_tags(str_replace('&nbsp;','',$object['attributes']['customs'][$this->disabledCompaniesNameField]));//Не предлагать компаниям

        $districtArray = explode(',', $filterClass->updateStreet($object['attributes']['district']));
        $districtArray = array_map('trim', $districtArray);//Район

        $addressArray = explode(',', $filterClass->updateStreet($address));//Адрес
        $addressArray = array_map('trim', $addressArray);

        if (count($addressArray) == 3) {
            $address = $addressArray[1];
        } else if (count($addressArray) == 4) {
            $address = $addressArray[2];
        } else {
            $address = implode(' ', $addressArray);
        }//Адрес

        if (count($addressArray) == 3) {
            $addressHouse = $addressArray[1].' '.$addressArray[2];
        } else if (count($addressArray) == 4) {
            $addressHouse = $addressArray[2].' '.$addressArray[3];
        } else {
            $addressHouse = implode(' ', $addressArray);
        }//А

        $profileCompanies = CustomHelper::issetField($object['attributes']['customs'], $this->objectProfileOfCompany, []);//Вид деятельности
        $exceptProfileCompanies = CustomHelper::issetField($object['attributes']['customs'], $this->exceptObjectProfileOfCompany, []);//Вид деятельности

        $objectSlider = [];
        $objectSlider['footage'] = !empty($object['attributes']['total-area']) ? $object['attributes']['total-area'] : 100;//Слайдеры

        if ($objectType == Properties::RENT_TYPE) {
            $objectSlider['budget_volume'] = CustomHelper::issetField($object['attributes']['customs'],Properties::CUSTOM_BUDGET_VOLUME_RENT,  150000);//Слайдеры
        } else if ($objectType == Properties::BUY_TYPE) {
            $objectSlider['budget_volume'] = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_BUDGET_VOLUME_BUY, 150000);//Слайдеры
        }

        if ($objectType == Properties::RENT_TYPE) {
            $objectSlider['budget_footage'] = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_BUDGET_FOOTAGE_RENT, 1500);//Слайдеры
        } else if ($objectType == Properties::BUY_TYPE) {
            $objectSlider['budget_footage'] = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_BUDGET_FOOTAGE_BUY, 72709);//Слайдеры
        }
        $objectSlider['rent'] = CustomHelper::issetField($object['attributes']['customs'],'custom-72598', 24);//Слайдеры
        $objectSlider['income'] = 10;//Слайдеры

        $typeOfPropertyObj = CustomHelper::issetField($object['attributes']['customs'], $this->typeOfPropertyField, []);//Вид деятельности

        $exceptCompanies = CustomHelper::issetField($object['attributes']['customs'], Properties::EXCEPT_COMPANIES, []);//Вид деятельности

        $companiesGetter = new CompaniesList();

        $data = [
            'token' => $token,
            'id' => $id,
            'type' => $type,
            'metroSelect' => $metroSelect,
            'objectTypes' => $companyTypes,
            'typeOfProperties' => $typeOfProperties,
            'attributes' => $object['attributes'],
//            'disabledCompanies' => $disabledCompanies,
            'metro' => $metro,
            'districtArray' => $districtArray,
            'address' => $address,
            'addressHouse' => $addressHouse,
            'profileCompanies' => $profileCompanies,
            'exceptProfileCompanies' => $exceptProfileCompanies,
            'objectSlider' => $objectSlider,
            'typeOfPropertyObj' => $typeOfPropertyObj,
            'objectType' => $objectType,
            'cityTypeId' => $cityTypeId,
            'findByAll' => $findByAll,
            'users' => $filterClass->convertUsers($users),
            'stages' => $filterClass->convertStagesCategories($stages),
            'exceptCompaniesList' => $companiesGetter->getList(),
            'exceptCompanies' => $exceptCompanies,
            'updatedTablesRecords' => static::getUpdatedRecords()
        ];

        return view('objects.filter', $data);
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function webhookEstateGet(Request $request)
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $filterClass = new MainFilter;

        $object = $methods->getObject($request->get('id'));
        $cityTypeId = $request->has('city_type') ? $request->get('city_type') : 2;

        $address = $object['attributes']['address'];
        $object_type = $request->get('object_type');
        $typeOfObject = $filterClass->checkCity($address);

        $ignoreSendingEntities = $request->has('ignore_sending_entities') ? $request->get('ignore_sending_entities') : false;

        //Данные по фильтрам
        $objData = $filterClass->prepareData($request, $object, 'object', $object_type);

        $responsible = $object['relationships']['responsible']['data']['id'] ?? 0;

        $modelProperty = Properties::where('id', $request->get('id'))
            ->first();

        if ($modelProperty->current_responsible != $responsible) {
            $modelProperty->current_responsible = $responsible;
            $modelProperty->save();

            PropertiesSendingEntities::delEntity($request->get('id'));
        }

        if (empty($objData)) {
            $msg = "Выберите фильтры";
            return view('objects.error_page', [
                'msg' => $msg,
                'errors' => $this->getErrors($objData),
                'request' => $this->prepareRequest($request)
            ]);
        }

        $objData['object_type'] = $request->get('object_type');

        //Фильтрация по заявкам
        $filterOrdersClass = new FilterOrders;
        $filterOrders = [];

        //Получаем Список заявок
        $ordersQuery = Orders::query()->where('type', $object_type);

        if (empty($ignoreSendingEntities)) {
            $ordersQuery->doesntHave('sending_entities');
        }

        $ordersQuery->chunk(1000, function($orders) use (&$filterOrders, $filterOrdersClass, $typeOfObject, $objData, &$count, $cityTypeId) {
                foreach ($orders as $order) {
                    $orderResponse = $filterOrdersClass->filter($order, $objData, $typeOfObject, $cityTypeId);

                    if (!empty($orderResponse)) {
                        $filterOrders[] = $order;
                    }
                }
            });

        if (empty($filterOrders)) {
            $msg = "Заявки не найдены";
            return view('objects.error_page', [
                'msg' => $msg,
                'errors' => $this->getErrors($objData),
                'request' => $this->prepareRequest($request)
            ]);
        }

        $dealResponses = [];

//        foreach (array_chunk($filterOrders, 100) as $filterChunkOrders) {
            //прописываем связи
        $companiesIds = [];
        $contacts = [];
        $orderData = [];

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
                    ContactsForm::setContacts($contacts, $contact['id'], 'orders', $filterOrder['id']);
                }
            }

            $orderData[] = [
                'type' => 'orders',
                'id' => $filterOrder['id'],
            ];
        }

        //не предлогать компаниям
        $notOfferCompanies = $objData['except_companies'] ?? [];

        //Проверяем компании
        $companiesData = [];

        if (!empty($companiesIds)) {
            $companyQuery = Company::whereIn('id', $companiesIds);

            if (empty($ignoreSendingEntities)) {
                $companyQuery->doesntHave('sending_entities');
            }

            $companyQuery->chunk(1000, function($companies) use (&$contacts, &$companiesData, $request, $notOfferCompanies, $objData, $cityTypeId) {
                foreach ($companies as $company) {
                    $attributes = json_decode($company['attributes'], true);
                    $customs = json_decode($company['customs'], true);

                    $filterCompanyValues = CustomHelper::issetField($customs, Company::CUSTOM_NETWORK, []);

                    if (!empty($filterCompanyValues) && isset($objData['filter_companies']) && !empty($objData['filter_companies'])) {
                        $filterCompanyValues = array_diff(array_map('trim', $filterCompanyValues), ['']);

                        if (empty(array_intersect($filterCompanyValues, $objData['filter_companies']))) {
                            continue;
                        }
                    }

                    if (!empty($request->get('except_companies_check')) && !empty($request->get('except_companies')) && in_array($attributes['name'], $request->get('except_companies'))) {
                        continue;
                    }

                    if (!empty($notOfferCompanies) && in_array($attributes['name'], $notOfferCompanies)) {
                        continue;
                    }

                    //город
                    $address = CustomHelper::issetField($customs, 'custom-73127', null);

                    if (!empty($address)) {
                        if ($cityTypeId == 2) {
                            $searchCity = 'Санкт-Петербург';
                        } else {
                            $searchCity = 'Москва';
                        }

                        if (strpos($address, $searchCity) == false) {
                            continue;
                        }
                    }

                    $companiesData[] = [
                        'type' => 'companies',
                        'id' => $company['id'],
                    ];

                    $relationships = json_decode($company['relationships'], true);
                    if (isset($relationships['contacts']['data'])) {
                        foreach ($relationships['contacts']['data'] as $contact) {
                            ContactsForm::setContacts($contacts, $contact['id'], 'companies', $company['id']);
                        }
                    }
                }
            });
        }

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
                'name' => $address.' '.$dealName,
                'description' => $object['id'],
                'customs' => [
                    'custom-65822' => CustomHelper::issetField($object['attributes']['customs'], 'custom-62518'),
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

        $responsible = [];
        if (!empty($request->get('responsible_users'))) {
            $responsible = [
                'data' => [
                    'type' => 'users',
                    'id' => $request->get('responsible_users'),
                ]
            ];

            $relationships['responsible'] = $responsible;
        }

        $contacts = ContactsForm::prepareContacts($contacts, $notOfferCompanies, $responsible, $objData, $ignoreSendingEntities);

        $dealResponse = $methods->dealCreate($data);
        $dealId = $dealResponse['id'];

        $objDeals = [];

        if (isset($object['relationships']['deals']['data'])) {
            foreach ($object['relationships']['deals']['data'] as $objDeal) {
                $objDeals[$objDeal['id']] = $objDeal['id'];
            }
        }

        $objDeals[$dealResponse['id']] = $dealResponse['id'];
        $methods->dealDataUpdate($dealId, $relationships);
//        dd($dealId);
        if (!empty($objDeals)) {
            $methods->attachDealsToObject($objDeals, $object['id']);
        }

//        $dealId = 2514111;

        //Сохраняем контакты
        $contactForm = new ContactsForm();
        $contactForm->setDealId($dealId);

        if (!empty($request->get('responsible_users'))) {
            $contactForm->setResponsible($request->get('responsible_users'));
        }

        $contactForm->storeContacts($contacts);

        $viewData = [
            'deal' => $dealResponse,
            'object' => $object,
            'ordersCount' => count($filterOrders),
        ];

        $stages = $methods->getDealStagesCategories();
        $stageName = $filterClass->getStageName($stage, $stages);

        $contactsIds = array_keys($contacts);
        $contactsChunk = array_chunk($contactsIds, 10);

        if (!empty($contactsChunk)) {
            foreach ($contactsChunk as $ids) {
                dispatch(new CopyContactNameJobs($ids, $dealResponse['id'], $stageName));
            }
        }

        //Фиксируем закрепленные контакты/компании/заявки
        if (isset($filterOrders) && !empty($filterOrders)) {
            foreach ($filterOrders as $order) {
                PropertiesSendingEntities::setEntity(PropertiesSendingEntities::ORDER_ITEM, $order['id'], $object['id']);
            }
        }

        if (isset($companiesData) && !empty($companiesData)) {
            foreach ($companiesData as $company) {
                PropertiesSendingEntities::setEntity(PropertiesSendingEntities::COMPANY_ITEM, $company['id'], $object['id']);
            }
        }

        if (isset($contacts) && !empty($contacts)) {
            foreach ($contacts as $id => $contact) {
                PropertiesSendingEntities::setEntity(PropertiesSendingEntities::CONTACT_ITEM, $id, $object['id']);
            }
        }

        return view('objects.success', $viewData);
    }

    /**
     * @param Request $request
     */
    public function getBstStatistic(Request $request)
    {
        Log::info(json_encode($request->all()));

        $id = $request->get('ids')[0];

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;
        $object = $methods->getObject($id);

        $import = new Import();
        $response = $import->simpleBstImport($object);

        return view('objects.bst_stat', ['response' => $response, 'request' => $request]);
    }

    /**
     * @param Request $request
     */
    public function copyObject(Request $request)
    {
        Log::info(json_encode($request->all()));

        $id = $request->get('ids')[0];

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;
        $object = $methods->getFullObject($id);
//        dd($object);
        unset($object['attributes']['updated-at']);
        unset($object['attributes']['cached-at']);
        unset($object['attributes']['as-string']);
        unset($object['attributes']['cover-image']);
        unset($object['attributes']['previous-responsible-id']);
        unset($object['attributes']['name']);

        $relationships = [];

        $data = [
            'attributes' => $object['attributes'],
        ];

        $objRelationships = $object['relationships'];

        $copyRelations = [
            'company','status',
            'source','responsible','contact',
            'orders', 'diaries','responsible',
            'performers'
        ];

        foreach ($copyRelations as $key) {
            if (isset($objRelationships[$key]['data']) && !empty($objRelationships[$key]['data'])) {
                $relationships[$key]['data'] = $objRelationships[$key]['data'];
            }
        }


        if (!empty($relationships)) {
            $data['relationships'] = $relationships;
        }

//        dd($data);

        $objResponse = $methods->objectCreate($data);

        if (empty($objResponse)) {
            return "Store Error!";
        }

        $documents = $objRelationships['documents']['data'];

        if (!empty($documents)) {
            foreach($documents as $document) {
                try {
                    $fullDocument = $methods->getMainDocument($document['id']);

                    if (!empty($fullDocument)) {
                        $name = $fullDocument['attributes']['name'];

                        $name = str_replace(' ','_', trim($name));
                        $name = str_replace(' ','_', trim($name));
                        $name = str_replace('/','_', trim($name));
                        $name = str_replace(',','_', trim($name));
                        $name = str_replace('__','_', trim($name));

                        $methods->downloadFile($name, $fullDocument['attributes']['download-link'], 'estate-properties', $objResponse['id']);
                    }
                } catch (\Throwable $exception) {
                    Log::error($exception->getMessage());
                    continue;
                }
            }
        }

        return view('objects.copy', ['result' => $objResponse]);
    }

    /**
     * @param array $entities
     * @param int $item
     * @param int $propertyId
     * @return bool
     */
    public function setEntities(array $entities, int $item, int $propertyId) {
        if (empty($entities)) {
            return true;
        }

        foreach ($entities as $itemId) {
            PropertiesSendingEntities::setEntity($item, $itemId, $propertyId);
        }
    }
}
