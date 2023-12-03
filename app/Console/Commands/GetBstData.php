<?php

namespace App\Console\Commands;

use App\Classes\BST\ApiPlaceClass;
use App\Classes\Form\PropertyForm;
use App\Classes\SalesUp\SalesupHandler;
use Illuminate\Console\Command;
use App\BstBridge;
use Carbon\Carbon;

/**
 * Class GetBstData
 * @package App\Console\Commands
 */
class GetBstData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bst:get_data {dayUpdated?} {bstId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет таблицу bst';

    /**
     *
     */
    protected const LIMIT = 100;

    /**
     * @var ApiPlaceClass
     */
    protected $bstClass;

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
        $this->bstClass = new ApiPlaceClass;
        $tokenResponse = $this->bstClass->getAuthToken();

        if (empty($tokenResponse)) {
            throw new \Exception('Token error');
        }

        $now = Carbon::now('Africa/Nairobi');
        $records = [];

        $total = $this->getLimitMarket($records);

        if ($total > self::LIMIT) {
            $iteration = ceil($total / self::LIMIT);
            $offset = 0;

            for ($i=0; $i<$iteration; $i++) {
                $offset +=  self::LIMIT;
                $this->getLimitMarket($records, $offset);
            }
        }

//        $bstModel = BstBridge::truncate();

        /** var array $records **/
        foreach ($records as $record)
        {
            $bstId = $this->argument('bstId');
            if (!empty($bstId) && $record['id'] != $bstId) {
                continue;
            }

            $propertyId = null;
            $bstModel = BstBridge::where('bst_id', $record['id'])
                ->first();

            if (!empty($bstModel)) {
                $dateModification = Carbon::parse($record['date_modification'], 'Africa/Nairobi')->startOfDay();

                if ($now->diffInDays($dateModification) === 0)
                {
                    continue;
                }

                if (!isset($bstModel->property)) {
                    continue;
                }

                $propertyId = $bstModel->property->id;
            }

            try {
                $property_id = $this->storeProperty($record, $propertyId);

                if (empty($property_id)) {
                    continue;
                }

                if (empty($bstModel)) {
                    $bstModel = new BstBridge;
                    $bstModel->property_id = $property_id;
                    $bstModel->bst_id = $record['id'];
                }

                $bstModel->response = json_encode($record);
                $bstModel->save();
            } catch (\Exception $e) {
                \Log::info('BST error record '.$e->getMessage());
                continue;
            }
        }
    }

    /**
     * @param $records
     * @param int $offset
     * @return int
     */
    protected function getLimitMarket(&$records, $offset = 0): int
    {
        $markets = $this->bstClass->getMarkets(self::LIMIT, $offset);

        if (!isset($markets['data']) || !isset($markets['data']['places'])) {
            $this->error('Bst not updated');
            return false;
        }

        $records = array_merge($records, $markets['data']['places']['records']);

        return $markets['data']['places']['total'];
    }

    /**
     * @param array $record
     * @param ?int $propertyId
     * @return int
     * @throws \Exception
     */
    protected function storeProperty(array $record, ?int $propertyId = null)
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $data = [
            'customs' => []
        ];

        BstBridge::bst2salsup($data, $record);

        //geoEnv
        $response = $this->bstClass->getGeoEnv($record['id']);

        if (empty($response)) {
            return false;
        }

        BstBridge::bstGeo2salesup($data, $response);

        //coordinates
        $coordinates = $this->bstClass->getCoordinates($record['id']);
        if (!empty($coordinates)) {
            BstBridge::bstCoordinates2salesup($data, $coordinates, $this->bstClass);
            BstBridge::bstGetHitMaps($data, $coordinates, $this->bstClass);
        }

        if (empty($data['customs'])) {
            unset($data['customs']);
        }

        if (empty($propertyId)) {
            $response = $methods->propertyCreate($data);

            if (!isset($response['id'])) {
                return false;
            }

            $propertyForm = new PropertyForm();
            $propertyResponse = $propertyForm->storeProperty($response);

            return $propertyResponse['id'];
        } else {
            $response = $methods->objectUpdate($propertyId, $data);
        }

        if (!isset($response['id'])) {
            return false;
        }

        return $response['id'];
    }
}
