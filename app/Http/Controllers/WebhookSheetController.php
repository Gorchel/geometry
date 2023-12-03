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
use App\Company;
use App\Contact;
use App\Helpers\PopulationHelper;
use App\Orders;
use App\Properties;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Helpers\CustomHelper;
use App\Traits\UpdatedTablesTrait;

/**
 * Class WebhookSheetController
 * @package App\Http\Controllers
 */
class WebhookSheetController extends AbstractFilterController
{
    use UpdatedTablesTrait;

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function updateSheetScenario(Request $request)
    {
        Log::info(json_encode($request->all()));
        // {"token":"_VM2aY4gXZMr384_-6UHBZZ-fE5pPYQcGDXy6a6elwQ","user_id":54273,"ids":[333559],"type":"estate-properties"}

        $id = $request->get('ids')[0];

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;
        $object = $methods->getObject($id);

        $population = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_POPULATION, '');//Вид деятельности
        $populationResidents = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_POPULATION_RESIDENTS, '');//Вид деятельности

        if (!empty($population) || !empty($populationResidents)) {
            $sheet2s2 = new Sheet2S2();
            $result = $sheet2s2->updateSheet($id);

            return "Google Sheet is updated!";
        }

        return 'Google Sheet is not updated';
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function updatePopulation(Request $request)
    {
        Log::info(json_encode($request->all()));

        $id = $request->get('ids')[0];

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;
        $object = $methods->getObject($id);

        $data = [];
        $longitude = $object['attributes']['longitude'];
        $latitude = $object['attributes']['latitude'];
        //Проверка координат
        if (empty($longitude) || empty($latitude)) {
            $dadataResult = PopulationHelper::getDadata($data, $object['attributes']['address']);
            $longitude = $dadataResult['lon'];
            $latitude = $dadataResult['lat'];
        }

        if (empty($longitude) || empty($latitude)) {
            throw new \Exception('Empty lon and lat');
        }

        $residents = PopulationHelper::getResidents($data, $longitude, $latitude);
        $population = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_POPULATION, '');//Вид деятельности

        if (!empty($data['attributes']) && $residents != $population) {
            $objResponse = $methods->objectGeneralUpdate($data['attributes'], $id);
        }

        $populationResidents = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_POPULATION_RESIDENTS, '');//Вид деятельности

        if (!empty($residents) || !empty($populationResidents)) {
            $sheet2s2 = new Sheet2S2();
            $result = $sheet2s2->updateSheet($id);

            return "Google Sheet is updated!";
        }

        return 'Google Sheet is not updated';
    }
}
