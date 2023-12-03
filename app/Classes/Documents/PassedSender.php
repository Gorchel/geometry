<?php

namespace App\Classes\Documents;

use App\Classes\SalesUp\SalesupHandler;
use App\Contact;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use App\Properties;

/**
 * Class PassedSender
 * @package App\Classes\Documents;
 */
class PassedSender implements SenderInterface
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
        $id = $request->get('ids')[0];

        $deal = $this->methods->getDeal($id);
        $dealRelationships = $deal['relationships'];

        $objectAttributes = json_decode($object['attributes'], true);
        $objectCustoms = json_decode($object['customs'], true);

        //Формирование КП
        $subject = 'Объект сдан!';
        $description = 'Объект сдан!! '.$objectAttributes['address'].', ';

        $obgType = CustomHelper::issetField($objectCustoms, Properties::CUSTOM_TYPE, []);
        $obgTypeName = 'rent';

        if (!empty($obgType)) {
            $description .= $obgType[0].', ';
        }

        if (!empty($objectAttributes['total-area'])) {
            $description .= $objectAttributes['total-area'].' кв. м., ';
        }

        //Площадь Аренда кп Общая, Аренда кп кв м, или Продажа кп кв.м. Продажа Общая
        if ($obgTypeName == 'rent') {
            $rentMonth = CustomHelper::issetField($objectCustoms, 'custom-76734', '');
            if (!empty($rentMonth)) {
                $description .= intval($rentMonth).' руб., ';
            }
        } else {
            $buyPrice = CustomHelper::issetField($objectCustoms, 'custom-76977', '');
            if (!empty($buyPrice)) {
                $description .= intval($buyPrice).' руб., ';
            }
        }

        $yaLink = CustomHelper::issetField($objectCustoms, 'custom-61775', '');

        if (!empty($yaLink)) {
            $description .= "<a href='".$yaLink."' target='_blank'>Ссылка на панораму</a><br/>,";
        }

        $description = trim($description, ',');

        $responsibility = [
            'name' => CustomHelper::issetFieldIncludeArray($objectCustoms, 'custom-84553', ''),
            'phone' => CustomHelper::issetFieldIncludeArray($objectCustoms, 'custom-84554', ''),
        ];

        $params = [
            'description' => $description,
            'responsibility' => $responsibility,
        ];

        $mail = view('templates.passed_mail', $params)->render();
        $header = $subject;

        return [
            'status' => true,
            'subject' => $subject,
            'objectAddress' => $header,
            'objectDescription' => $mail,
            'relationships' => $dealRelationships
        ];
    }

    /**
     * @param array $object
     * @param array $emails
     * @return array
     */
    public function prepareEmails(array $object, array $emails): array
    {
        return [
            'emails' => $emails,
            'exceptEmails' => [],
        ];
    }

    /**
     * @param array $contact
     * @return bool
     */
    public function filterContactsEmails(array $contact): bool
    {
        $attributes = $contact['attributes'];
        $customs = $attributes['customs'];

        $statusValue = CustomHelper::issetField($customs, Contact::STATUS_FIELD, []);

        if (isset($statusValue[0])) {
            $statusValue = $statusValue[0];
        } else {
            $statusValue = null;
        }

        if (in_array($statusValue, Contact::FORBIDDEN_PASSED_STATUSES)) {
            return false;
        }

        return true;
    }


    /**
     * @return int
     */
    public function getType(): int
    {
        return Sender::PASSED_TYPE;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getObject(Request $request): ?array
    {
        $id = $request->get('ids')[0];

        $object = null;

        Properties::chunk(1000, function($properties) use (&$object, $id) {
            foreach ($properties as $property) {
                $relations = json_decode($property['relationships'], true);

                if (isset($relations['deals']) && in_array($id, $relations['deals'])) {
                    $object = $property->toArray();
                }
            }
        });

        if (empty($object)) {
            return null;
        }

        return $object;
    }
}
