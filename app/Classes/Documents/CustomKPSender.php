<?php

namespace App\Classes\Documents;

use App\Classes\Filters\FilterOrders;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\KPHelper;
use App\SenderObject;
use Illuminate\Http\Request;
use App\Properties;

/**
 * Class CustomKPSender
 * @package App\Classes\Documents;
 */
class CustomKPSender implements SenderInterface
{
    /**
     * @var \App\Classes\SalesUp\SalesupMethods
     */
    public $methods;

    /**
     * KPSender constructor.
     */
    public function __construct()
    {
        $token = env('API_TOKEN');
        $handler = new SalesupHandler($token);

        $this->methods = $handler->methods;
    }

    /**
     * @param array $object
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function prepareData(array $object, Request $request): array
    {
        $objectAttributes = json_decode($object['attributes'], true);
        $objectCustoms = json_decode($object['customs'], true);
        $objectRelationships = json_decode($object['relationships'], true);

        //Тема письма
        $subject = '';

        if (isset($objectCustoms['custom-62518']) && !empty($objectCustoms['custom-62518'])) {
            if (is_array($objectCustoms['custom-62518'])) {
                $typeOfDeal = implode(',', $objectCustoms['custom-62518']);
            } else {
                $typeOfDeal = $objectCustoms['custom-62518'];
            }
            $subject .= ($typeOfDeal.', ');
        }

        if (!empty($objectAttributes['total-area'])) {
            $subject .= ($objectAttributes['total-area'].' кв.м., ');
        }

        if (!empty($objectAttributes['address'])) {
            $subject .= ($objectAttributes['address'].', ');
        }

        $subject = trim($subject, ',');

        $objParams = [
            'objectDescription' => $objectAttributes['description'],
            'customText' => $request->get('email_body'),
        ];

        $filterData = new FilterOrders;

        if (!empty($object['type'])) {
            if ($object['type'] == Properties::RENT_TYPE) {
                $key = 'rent';
                $objParams['rent_month'] = !empty($objectCustoms['custom-76734']) ? strip_tags($objectCustoms['custom-76734']) : '';
                $objParams['rent_month'] = number_format($objParams['rent_month'], 0, '.', ' ');
                $objParams['rent_month_meter'] = !empty($objectCustoms['custom-76733']) ? strip_tags($objectCustoms['custom-76733']) : '';
                $objParams['rent_month_meter'] = number_format($objParams['rent_month_meter'], 0, '.', ' ');
            } else {
                $key = 'buy';
                $objParams['buy_price'] = !empty($objectCustoms['custom-76977']) ? strip_tags($objectCustoms['custom-76977']) : '';
                $objParams['buy_price'] = number_format($objParams['buy_price'], 0, '.', ' ');
                $objParams['buy_price_meter'] = !empty($objectCustoms['custom-76978']) ? strip_tags($objectCustoms['custom-76978']) : '';
                $objParams['buy_price_meter'] = number_format($objParams['buy_price_meter'], 0, '.', ' ');
            }

            $cutomsData = $filterData->getCustomPropertyArray(Properties::BUY_TYPE);
            $objParams['type'] = $key;
            $objParams['area'] = $objectAttributes['total-area'];
            $objParams['metro'] = !empty($objectCustoms[$cutomsData['metro']]) ? $objectCustoms[$cutomsData['metro']][0] : '';
            $objParams['metro_on_foot'] = !empty($objectCustoms[$cutomsData['metro_on_foot']]) ? $objectCustoms[$cutomsData['metro_on_foot']] : '';
            $objParams['address'] = $objectAttributes['address'];
            $objParams['stage'] = !empty($objectCustoms['custom-68794']) ? strip_tags($objectCustoms['custom-68794']) : '';
            $objParams['ceiling'] = !empty($objectCustoms['custom-64805']) ? strip_tags($objectCustoms['custom-64805']) : '';
            $objParams['enter'] = !empty($objectCustoms['custom-73199']) ? strip_tags($objectCustoms['custom-73199'][0]) : '';
            $objParams['electric'] = !empty($objectCustoms['custom-62859']) ? strip_tags($objectCustoms['custom-62859']) : '';
            $objParams['ventilation'] = !empty($objectCustoms['custom-66768']) ? strip_tags($objectCustoms['custom-66768'][0]) : '';
            $objParams['link_panoram'] = !empty($objectCustoms['custom-61775']) ? strip_tags($objectCustoms['custom-61775']) : '';
            $objParams['plan_one'] = !empty($objectCustoms['custom-61772']) ? strip_tags($objectCustoms['custom-61772']) : '';
            $objParams['plan_two'] = !empty($objectCustoms['custom-61771']) ? strip_tags($objectCustoms['custom-61771']) : '';
            $objParams['description_additional'] = $objectAttributes['description'];
        } else {
            throw new \Exception('Not set field custom-62518 (Type of deal)');
        }

        $customDocuments = [];

        if ($request->has('fileName') && $request->has('filePath')) {
            $customDocuments[] = [
                'name' => $request->get('fileName').' - '.$request->get('createdAt'),
                'params' => json_encode([
                    'fileName' => $request->get('fileName'),
                    'filePath' => $request->get('filePath')
                ])
            ];
        }

        $mail = view('templates.mailDescription', $objParams)->render();
        $header = 'Добрый день! Предлагаем вашему вниманию помещение расположенное по адресу: '.$objectAttributes['address'];

        return [
            'status' => true,
            'subject' => $subject,
            'objectAddress' => $header,
            'objectDescription' => $mail,
            'customDocuments' => $customDocuments,
            'relationships' => $objectRelationships,
            'links' => KPHelper::makeLinks($object)
        ];
    }

    /**
     * @param array $object
     * @param array $emails
     * @return array
     */
    public function prepareEmails(array $object, array $emails): array
    {
        $exceptEmails = SenderObject::getModelByEmails($object['id'], $object['type'], $emails);

        if (!empty($exceptEmails)) {
            $emails = array_diff($emails, $exceptEmails);
        }

        return [
            'emails' => $emails,
            'exceptEmails' => $exceptEmails,
        ];
    }

    /**
     * @param array $contact
     * @return bool
     */
    public function filterContactsEmails(array $contact): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return Sender::CUSTOM_OS_TYPE;
    }

    /**
     * @param string $name
     * @return bool
     */
    private function isValidName(string $name)
    {
        if (stripos($name, '.pdf') !== false || stripos($name, '.doc') !== false
            || stripos($name, 'КП') !== false)  {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getObject(Request $request): ?array
    {
        $id = $request->get('ids')[0];

        $object = Properties::where('id', $id)->first()->toArray();

        if (empty($object)) {
            return null;
        }

        return $object;
    }
}
