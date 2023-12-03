<?php

namespace App\Classes\Whatsapp;

use App\Classes\SalesUp\SalesupHandler;
use App\Contact;
use App\Helpers\CustomHelper;
use App\Helpers\KPHelper;
use Illuminate\Http\Request;
use App\Properties;

/**
 * Class OSSender
 * @package App\Classes\Whatsapp;
 */
class OSSender implements SenderInterface
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

        $actualObject = $this->methods->getObject($object['id']);

        return [
            'status' => true,
            'relationships' => $dealRelationships,
            'links' => KPHelper::makeLinks($actualObject)
        ];
    }

    /**
     * @param array $object
     * @return array
     * @throws \Throwable
     */
    public function prepareBody(array $object): string
    {
        $objectAttributes = json_decode($object['attributes'], true);
        $objectCustoms = json_decode($object['customs'], true);

        //Формирование КП
        $description = "Здравствуйте! Мы компания «Геометрия». Просим дать обратную связь по объекту - ".$objectAttributes['address'];
        $description .= ", предложение по которому было отправлено Вам ранее: {{kplink}} "."\n";

        $sendDate = CustomHelper::issetField($objectCustoms, 'custom-79731', '');

        if (!empty($sendDate)) {
            $description .= $sendDate;
        }

        $obgType = CustomHelper::issetField($objectCustoms, Properties::CUSTOM_TYPE, []);
        $obgTypeName = 'rent';

        if (!empty($obgType)) {
            $description .= $obgType[0].', ';

            if ($obgType[0] != 'Аренда') {
                $obgTypeName = 'sale';
            }
        }

        $description .= $objectAttributes['address'].",\n";

        if (!empty($objectAttributes['total-area'])) {
            $description .= "Площадь: ".$objectAttributes['total-area']." кв. м.,\n";
        }

        //Площадь Аренда кп Общая, Аренда кп кв м, или Продажа кп кв.м. Продажа Общая
        if ($obgTypeName == 'rent') {
            $rentMonth = CustomHelper::issetField($objectCustoms, 'custom-76734', '');
            if (!empty($rentMonth)) {
                $description .= "Стоимость в месяц: ".intval($rentMonth)." руб.,\n";
            }

            $rentMonthMeter = CustomHelper::issetField($objectCustoms, 'custom-76733', '');
            if (!empty($rentMonthMeter)) {
                $description .= "Стоимость в месяц кв/м: ".intval($rentMonthMeter)." руб.,\n";
            }
        } else {
            $buyPrice = CustomHelper::issetField($objectCustoms, 'custom-76977', '');
            if (!empty($buyPrice)) {
                $description .= "Стоимость в месяц: ".intval($buyPrice)." руб.,\n";
            }

            $buyMonthMeter = CustomHelper::issetField($objectCustoms, 'custom-76978', '');
            if (!empty($buyMonthMeter)) {
                $description .= 'Стоимость в месяц кв/м: '.intval($buyMonthMeter)." руб.,\n";
            }
        }

        $yaLink = CustomHelper::issetField($objectCustoms, 'custom-61775', '');

        if (!empty($yaLink)) {
            $description .= 'Ссылка на панораму: '.$yaLink." \n";
        }

        $description .= "*Мы стараемся сделать наш сервис по получению обратной связи удобнее для Вас, чтобы не отвлекать Вас звонками по телефону, отправляем данное письмо.*";
        $description .= "\n\n";
        $description .= "*Данный номер работает только на прием и отправку сообщений Whats App. Для звонков тел: ".CustomHelper::issetField($objectCustoms, 'custom-66780', Properties::DEFAULT_MANAGER_NUMBER)."*";

        return $description;
    }

    /**
     * @param array $object
     * @param array $phones
     * @return array
     */
    public function preparePhones(array $object, array $phones): array
    {
        return [
            'phones' => $phones,
            'exceptEmails' => [],
        ];
    }

    /**
     * @param array $contact
     * @return bool
     */
    public function filterContactsPhones(array $contact): bool
    {
        $attributes = $contact['attributes'];
        $customs = $attributes['customs'];

        $statusValue = CustomHelper::issetField($customs, Contact::STATUS_FIELD, []);

        if (isset($statusValue[0])) {
            $statusValue = $statusValue[0];
        } else {
            $statusValue = null;
        }

        if (in_array($statusValue, Contact::FORBIDDEN_OS_STATUSES)) {
            return false;
        }

        return true;
    }


    /**
     * @return string
     */
    public function getType(): string
    {
        return Sender::OS_TYPE;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getObject(Request $request): ?array
    {
        if ($request->has('ids')) {
            $id = $request->get('ids')[0];
        } else {
            $id = $request->get('id');
        }

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
