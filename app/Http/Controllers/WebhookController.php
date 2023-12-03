<?php

namespace App\Http\Controllers;

use App\Properties;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Classes\SalesUp\SalesupHandler;
use App\Classes\SalesUp\SalesupMethods;

/**
 * Class WebhookController
 * @package App\Http\Controllers
 */
class WebhookController extends Controller
{
    /**
     * Просмотр логов
     * @param Request $request
     */
    public function getLogs(Request $request)
    {
        $logStr = file_get_contents(storage_path('logs/lumen-'.$request->get('date').'.log'));
        echo $logStr;
    }

    /**
     * Обновление Контактов
     * @param Request $request
     * @return int|void
     */
    public function webhook(Request $request)
    {
        return $this->updateDealByType($request, 'company');
    }

    /**
     * @param Request $request
     * @return int|void
     */
    public function webhookUpdateObjectsContacts(Request $request)
    {
        //{"ids":["1678688"],"token":"Ph-AhX3_sc1GGkW2h6QLxiGxnH6DCBA8SnthhTRa6aA","type":"deals","user_id":"54273"}
        return $this->updateDealByType($request, 'object');
    }

    /**
     * @param Request $request
     * @param string $type
     * @return array|void
     */
    protected function updateDealByType(Request $request, string $type)
    {
        Log::info(json_encode($request->all()));

        if ($request->get('type') !== 'deals') {
            return;
        }

        $dealsIdsArrays = $request->get('ids');
        $token = $request->get('token');

        foreach ($dealsIdsArrays as $dealId) {
            $salesupHandler = new SalesupHandler($token);
            $response = $salesupHandler->updateDeals($dealId, $type);
        }

        return $response;
    }

    /**
     * Получение яндекс карты
     * @param Request $request
     */
    public function webhookObjects(Request $request)
    {
        Log::info(json_encode($request->all()));

        $id = $request->get('ids')[0];
        $token = $request->get('token');
        $type = $request->get('type');

        $data = [
            'token' => $token,
            'id' => $id,
            'type' => $type,
        ];

        if (!empty($id)) {
            $salesupHandler = new SalesupHandler($token);
            $response = $salesupHandler->getObjects($id);

            $attribute = $response['attributes'];

            if (!empty($attribute['longitude'])) {
                $data['longitude'] = $attribute['longitude'];
            }

            if (!empty($attribute['latitude'])) {
                $data['latitude'] = $attribute['latitude'];
            }
        }

        return view('objects.ya', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector
     */
    public function webhookPostObjects(Request $request) {
        $salesupHandler = new SalesupHandler($request->get('token'));

        $district = $request->get('district');

        if (strpos($district, 'район') !== false) {
            $districtArray = explode(',', $district);

            foreach ($districtArray as $partDistrict) {
                if (strpos($district, 'район') !== false) {
                    $district = trim(str_replace('район', '', $partDistrict));
                    break;
                }
            }
        }

        $updateData = [
            'district' => $district,
            'latitude' => $request->get('latitude'),
            'longitude' => $request->get('longitude'),
            'metro' => $request->get('metro'),
            'metro_distance' => $request->get('metro_distance'),
        ];

        $salesupHandler->updateObject($request->get('id'), $updateData);

        $property = Properties::where('id', $request->get('id'))
            ->first();

        if (!empty($property)) {
            $property->is_statistic = Properties::IS_NOT_STATISTIC;
            $property->save();
        }

        return view('objects.ya_success');
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function copyContactsView(Request $request)
    {
        Log::info(json_encode($request->all()));

        $id = $request->get('ids')[0];
        $token = $request->get('token');

        $salesupHandler = new SalesupHandler($token);
        $deal = $salesupHandler->methods->getDeal($id);

        $contactEmails = [];

        if (!empty($deal)) {
            $contactsData = $deal['relationships']['contacts']['data'];

            if (!empty($contactsData)) {
                foreach ($contactsData as $data) {
                    $contact = $salesupHandler->methods->getContact($data['id']);

                    if (isset($contact['attributes']) && !empty($contact['attributes']['email'])) {
                        $contactEmails[] = $contact['attributes']['email'];
                    }
                }
            }
        }

        $data = [
            'contactEmails' => $contactEmails,
        ];

        return view('/contacts/copy', $data);
    }
}
