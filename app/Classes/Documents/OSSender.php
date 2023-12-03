<?php

namespace App\Classes\Documents;

use App\Classes\SalesUp\SalesupHandler;
use App\Contact;
use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use App\Properties;
use App\SenderObject;

/**
 * Class OSSender
 * @package App\Classes\Documents;
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

        $objectAttributes = json_decode($object['attributes'], true);
        $objectCustoms = json_decode($object['customs'], true);

        //Тип рассылки
        $brand = CustomHelper::issetField($objectCustoms, 'custom-84903', []);

        if  (!isset($brand[0])) {
            return [
                'status' => false,
                'msg' => 'Поле Геометрия/Континент не заполнено'
            ];
        }

        if ($brand[0] == 'Геометрия') {
            $brandName = 'geometry';
        } else {
            $brandName = 'kontinent';
        }

        //Формирование КП
        $subject = '';
        $description = 'Здравствуйте! Просим дать обратную связь по объекту - '.$objectAttributes['address'];
        $description .= ", предложение по которому было отправлено Вам ранее.";

        $sendDate = CustomHelper::issetField($objectCustoms, 'custom-79731', '');

        if (!empty($sendDate)) {
            $description .= $sendDate;
        }

        $description .= ":<br/><br/>";

        $obgType = CustomHelper::issetField($objectCustoms, Properties::CUSTOM_TYPE, []);
        $obgTypeName = 'rent';

        if (!empty($obgType)) {
            $description .= $obgType[0].', ';
            $subject .= $obgType[0].', ';

            if ($obgType[0] != 'Аренда') {
                $obgTypeName = 'sale';
            }
        }

        $description .= $objectAttributes['address'].',<br/>';
        $subject .= $objectAttributes['address'].', ';

        if (!empty($objectAttributes['total-area'])) {
            $description .= 'Площадь: '.$objectAttributes['total-area'].' кв. м.,<br/>';
        }

        //Площадь Аренда кп Общая, Аренда кп кв м, или Продажа кп кв.м. Продажа Общая
        if ($obgTypeName == 'rent') {
            $rentMonth = CustomHelper::issetField($objectCustoms, 'custom-76734', '');
            if (!empty($rentMonth)) {
                $description .= 'Стоимость в месяц: '.number_format(intval($rentMonth), 0, ',', ' ').' руб.,<br/>';
            }

            $rentMonthMeter = CustomHelper::issetField($objectCustoms, 'custom-76733', '');
            if (!empty($rentMonthMeter)) {
                $description .= 'Стоимость в месяц кв/м: '.number_format(intval($rentMonthMeter), 0, ',', ' ').' руб.,<br/>';
            }
        } else {
            $buyPrice = CustomHelper::issetField($objectCustoms, 'custom-76977', '');
            if (!empty($buyPrice)) {
                $description .= 'Стоимость в месяц: '.number_format(intval($buyPrice), 0, ',', ' ').' руб.,<br/>';
            }

            $buyMonthMeter = CustomHelper::issetField($objectCustoms, 'custom-76978', '');
            if (!empty($buyMonthMeter)) {
                $description .= 'Стоимость в месяц кв/м: '.number_format(intval($buyMonthMeter), 0, ',', ' ').' руб.,<br/>';
            }
        }

        $stage = CustomHelper::issetField($objectCustoms, 'custom-68794', '');
        if (!empty($stage)) {
            $description .= 'Этаж/этажность: '.$stage.', <br/>';
        }

        $senderObj = SenderObject::where('property_id', $object['id'])
            ->where('link', '!=', null)
            ->first();

        if (!empty($senderObj) && !empty($senderObj['link'])) {
            $description .= "<a href='".$senderObj['link']."' target='_blank'>Ссылка на коммерческое предложение</a><br/>";
        }

        $yaLink = CustomHelper::issetField($objectCustoms, 'custom-61775', '');

        if (!empty($yaLink)) {
            $description .= "<a href='".$yaLink."' target='_blank'>Ссылка на панораму</a><br/>";
        }

        $description .= "<br/>Мы стараемся сделать наш сервис по получению обратной связи удобнее для Вас, чтобы не отвлекать Вас звонками по телефону, отправляем данное письмо";

        $responsibility = [
            'name' => CustomHelper::issetFieldIncludeArray($objectCustoms, 'custom-84553', ''),
            'phone' => CustomHelper::issetFieldIncludeArray($objectCustoms, 'custom-84554', ''),
        ];

        $params = [
            'description' => $description,
            'responsibility' => $responsibility,
        ];

        $mail = view('templates.'.$brandName.'_feedback_mail', $params)->render();
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

        if (in_array($statusValue, Contact::FORBIDDEN_OS_STATUSES)) {
            return false;
        }

        return true;
    }


    /**
     * @return int
     */
    public function getType(): int
    {
        return Sender::OS_TYPE;
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
