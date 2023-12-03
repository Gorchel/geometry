<?php
namespace App\Console\Commands;
use App\Classes\SalesUp\SalesupHandler;
use App\Contact;
use App\Helpers\CustomHelper;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Orders;
use App\Company;
use App\Classes\Form\PropertyForm;
use App\Classes\SalesUp\SalesupMethods;
use App\UpdateTables as UpdateTablesModel;

/**
 * Class UpdateTables
 * @package App\Console\Commands
 */
class UpdateTables extends Command
{
    /**
     *
     */
    const COUNT_PER_PAGE = 100;
    const COUNT_ORDERS_PER_PAGE = 50;

    const ORDERS_TYPE = 'orders';
    const PROPERTIES_TYPE = 'property';
    const COMPANY_TYPE = 'company';
    const CONTACT_TYPE = 'contact';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_tables:init {type} {getFullUpdate?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет таблицу';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
//        \Log::info('run '.$this->argument('type').' '.$this->argument('dayUpdated'));
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $getFullUpdate = $this->argument('getFullUpdate');

        $type = $this->argument('type');
        $filters = [];

        if (empty($getFullUpdate)) {
            $now = Carbon::now('Africa/Nairobi');
            $now->subHours(24);
            $filters['updated-at-gte'] = $now->format('Y.m.d H:s');
        }

        switch ($type) {
            case self::ORDERS_TYPE:
                //Получаем Список заявок
                $ordersData = $methods->getOrders(1, self::COUNT_ORDERS_PER_PAGE, $filters);

                if (!empty($ordersData['data'])) {
                    $this->eachOrders($ordersData['data']);

                    $pageNumber = $ordersData['meta']['page-count'];

                    if ($pageNumber > 1) {
                        for ($page = 2; $page<=$pageNumber; $page++) {
                            $ordersData = $methods->getOrders($page, self::COUNT_ORDERS_PER_PAGE, $filters);
                            if (isset($ordersData['data']) && !empty($ordersData['data'])) {
                                $this->eachOrders($ordersData['data']);
                            }
                        }
                    }
                }
                break;
            case self::PROPERTIES_TYPE:
                //Получаем Список недвижки
                $propertyData = $methods->getPaginationObjects(1, self::COUNT_PER_PAGE, $filters);
                if (!empty($propertyData['data'])) {
                    $this->eachProperties($propertyData['data']);

                    $pageNumber = $propertyData['meta']['page-count'];

                    if ($pageNumber > 1) {
                        for ($page = 2; $page<=$pageNumber; $page++) {
                            $propertyData = $methods->getPaginationObjects($page, self::COUNT_PER_PAGE, $filters);
                            if (isset($propertyData['data']) && !empty($propertyData['data'])) {
                                $this->eachProperties($propertyData['data']);
                            }
                        }
                    }
                }
                break;
            case self::COMPANY_TYPE:
                //Получаем Список недвижки
                $companyData = $methods->getPaginationCompany(1, self::COUNT_PER_PAGE, $filters);

                if (!empty($companyData['data'])) {
                    $this->eachCompany($companyData['data']);

                    $pageNumber = $companyData['meta']['page-count'];

                    if ($pageNumber > 1) {
                        for ($page = 2; $page<=$pageNumber; $page++) {
                            $companyData = $methods->getPaginationCompany($page, self::COUNT_PER_PAGE, $filters);
                            if (isset($companyData['data']) && !empty($companyData['data'])) {
                                $this->eachCompany($companyData['data']);
                            }
                        }
                    }
                }
                break;
            case self::CONTACT_TYPE:
                //Получаем Список недвижки
                $contactData = $methods->getPaginationContact(1, self::COUNT_PER_PAGE, $filters);

                if (!empty($contactData['data'])) {
                    $this->eachContact($contactData['data']);

                    $pageNumber = $contactData['meta']['page-count'];

                    if ($pageNumber > 1) {
                        for ($page = 2; $page<=$pageNumber; $page++) {
                            $contactData = $methods->getPaginationContact($page, self::COUNT_PER_PAGE, $filters);
                            if (isset($contactData['data']) && !empty($contactData['data'])) {
                                $this->eachContact($contactData['data']);
                            }
                        }
                    }
                }
                break;
            default:
                $this->info('Correct commands: '.self::ORDERS_TYPE.', '.self::PROPERTIES_TYPE.', '.self::COMPANY_TYPE.', '.self::CONTACT_TYPE);
        }

        UpdateTablesModel::updateTable($type);

        return true;
    }

    /**
     * @param $orders
     */
    protected function eachOrders($orders)
    {
        if (!empty($orders)) {
            foreach ($orders as $orderKey => $order) {
                $this->storeOrders($order);
            }
        }
    }

    /**
     * @param $properties
     */
    protected function eachProperties($properties)
    {
        $propertyForm = new PropertyForm();

        if (!empty($properties)) {
            foreach ($properties as $orderKey => $property) {
                $propertyForm->storeProperty($property);
            }
        }
    }

    /**
     * @param $companies
     */
    protected function eachCompany($companies)
    {
        if (!empty($companies)) {
            foreach ($companies as $orderKey => $company) {
                $this->storeCompany($company);
            }
        }
    }

    /**
     * @param $contacts
     */
    protected function eachContact($contacts)
    {
        if (!empty($contacts)) {
            foreach ($contacts as $contactKey => $contact) {
                $this->storeContact($contact);
            }
        }
    }

    /**
     * @param $order
     */
    public function storeOrders($order)
    {
        $orderModel = Orders::where('id', $order['id'])
            ->first();

        $attributes = $order['attributes'];

        $now = Carbon::now('Africa/Nairobi')->format('Y-m-d H:i:s');

        if (!empty($attributes['discarded-at'])) {
            if (!empty($orderModel)) {
                $orderModel->delete();
            }

            return;
        }

        if (empty($orderModel)) {
            $orderModel = new Orders;
            $orderModel->id = $order['id'];
            $orderModel->created_at = $now;
        } else {
            if ($orderModel->updated_at == $now) {
                return;
            }
        }

        $orderModel->updated_at = $now;
        $orderModel->customs = json_encode($attributes['customs']);

        $customValue = CustomHelper::issetField($attributes['customs'], 'custom-67821', []);
        $type = array_values(array_diff(array_map('trim', $customValue), ['']));

        if (isset($type[0])) {
            $orderModel->type = $this->getType($type[0]);
        }

        unset($attributes['customs']);

        $orderModel->attributes = json_encode($attributes);

        $relationships = [
            'contacts' => $order['relationships']['contacts'],
            'companies' => $order['relationships']['companies'],
        ];

        $orderModel->relationships = json_encode($relationships);
        $orderModel->save();
    }

    protected function getType($str)
    {
        switch($str) {
            case 'Сдам':
                return 1;
            case 'Продам':
                return 2;
            case 'Куплю':
                return 3;
            default:
                return 4;
        }
    }

    /**
     * @param $contact
     */
    public function storeContact($contact)
    {
        $attributes = $contact['attributes'];
        $relationships = $contact['relationships'];
        $customs = $attributes['customs'];

        unset($attributes['customs']);

        $contactModel = Contact::where('id', $contact['id'])
            ->first();

        $status = null;

        if (isset($relationships['status']['data']) && !empty($relationships['status']['data'])) {
            $status = $relationships['status']['data']['id'];

            if (in_array($status, [Contact::ARCHIVE_STATUS])) {//Не актиывный статус не записываем
                if (!empty($contactModel)) {
                    $contactModel->delete();
                }

                return;
            }
        }

        $now = Carbon::now('Africa/Nairobi')->format('Y-m-d H:i:s');

        if (!empty($attributes['discarded-at'])) {
            if (!empty($contactModel)) {
                $contactModel->delete();
            }
            return;
        }

//        if (isset($customs[SalesupMethods::CUSTOM_CONTACT_DUBLICATION]) && $customs[SalesupMethods::CUSTOM_CONTACT_DUBLICATION] == 1)
//        {
//            return;
//        }

        if (empty($contactModel)) {
            $contactModel = new Contact;
            $contactModel->id = $contact['id'];
            $contactModel->created_at = $now;
        } else {
            if ($contactModel->updated_at == $now) {
                return;
            }
        }

        $contactModel->updated_at = $now;
        $contactModel->customs = json_encode($customs);
        $contactModel->attributes = json_encode($attributes);
        $contactModel->relationships = json_encode($relationships);
        $contactModel->status = $status;

        if (!empty(CustomHelper::issetField($customs, SalesupMethods::CUSTOM_CONTACT_DUBLICATION))) {
            $contactModel->double = true;
        } else {
            $contactModel->double = false;
        }

        $contactModel->save();
    }

    /**
     * @param $property
     */
    public function storeCompany($company)
    {
        $attributes = $company['attributes'];
        $relationships = $company['relationships'];

        $companyModel = Company::where('id', $company['id'])
            ->first();

        $status = null;

        if (isset($relationships['status']['data']) && !empty($relationships['status']['data'])) {
            $status = $relationships['status']['data']['id'];

            if (in_array($status, [Company::IN_ARCHIVE, Company::TEMPORARY_NOT_DEV])) {//Не актиывный статус не записываем
                if (!empty($companyModel)) {
                    $companyModel->delete();
                }

                return;
            }
        }

        $now = Carbon::now('Africa/Nairobi')->format('Y-m-d H:i:s');

        if (!empty($attributes['discarded-at'])) {
            if (!empty($companyModel)) {
                $companyModel->delete();
            }
            return;
        }

        if (empty($companyModel)) {
            $companyModel = new Company();
            $companyModel->id = $company['id'];
            $companyModel->created_at = $now;
        } else {
            if ($companyModel->updated_at == $now) {
                return;
            }
        }

        $companyModel->updated_at = $now;
        $companyModel->customs = json_encode($attributes['customs']);
        $companyModel->status = $status;

        unset($attributes['customs']);

        $companyModel->attributes = json_encode($attributes);

        $relationshipsArray = [
            'contacts' => $relationships['contacts'],
            'companies' => $relationships['status'],
        ];

        $companyModel->relationships = json_encode($relationshipsArray);
        $companyModel->save();
    }
}
