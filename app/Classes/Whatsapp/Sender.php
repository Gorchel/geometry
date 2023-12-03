<?php

namespace App\Classes\Whatsapp;

use App\Classes\SalesUp\SalesupHandler;
use App\Company;
use App\Contact;
use App\Helpers\CustomHelper;
use App\Helpers\SendingKPHelper;
use Illuminate\Http\Request;
use App\Properties;

/**
 * Class Sender
 * @package App\Classes\Whatsapp;
 */
class Sender
{
    const OS_TYPE = 'os';

    /**
     * @var \App\Classes\SalesUp\SalesupMethods
     */
    public $methods;

    /**
     * @var SenderInterface
     */
    public $sender;

    /**
     * Sender constructor.
     * @param SenderInterface $sender
     */
    public function __construct(SenderInterface $sender)
    {
        $this->sender = $sender;

        $handler = new SalesupHandler(env('API_TOKEN'));

        $this->methods = $handler->methods;
    }

    /**
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function startSending(Request $request)
    {
        $id = $request->get('ids')[0];
        $object = $this->sender->getObject($request);

        if (empty($object)) {
            return [
                'status' => false,
                'msg' => "Объект недвижимости отсутствует"
            ];
        }

        $prepareData = $this->sender->prepareData($object, $request);

        if (empty($prepareData['status'])) {
            return $prepareData;
        }

        $phones = $this->getPhones($request, $prepareData['relationships'], $object);

//        $phones['+79782099787'] = [
//            'description' => '+79782099787 (Test)',
//            'relation' => ['type' => 'contact', 'id' => '1234']
//        ];

        $phonesArr = $this->sender->preparePhones($object, $phones);
        $phones = $phonesArr['phones'];

        $prepareData['objectName'] = json_decode($object['attributes'], true)['name'];
        $prepareData['token'] = env('API_TOKEN');
        $prepareData['phones'] = $phones;
        $prepareData['request'] = $request;
        $prepareData['objectId'] = $object['id'];
        $prepareData['objectType'] = $object['type'];
        $prepareData['sendType'] = $this->sender->getType();
        $prepareData['id'] = $id;
        $prepareData['status'] = true;

        return $prepareData;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function finishSending(Request $request)
    {
        $object = $this->sender->getObject($request);

        if (empty($object)) {
            return [
                'status' => false,
                'msg' => "Объект недвижимости отсутствует"
            ];
        }

        $description = $this->sender->prepareBody($object);

        return [
            'description' => $description,
        ];
    }

    /**
     * @param Request $request
     * @param array $relationships
     * @param $object
     * @return array
     * @throws \Exception
     */
    protected function getPhones(Request $request, array $relationships, $object)
    {
        $objectCustoms = json_decode($object['customs'], true);
        $notOfferCompanies = CustomHelper::issetField($objectCustoms, Properties::NOT_OFFER_COMPANY, []);

        $phones = [];
        $isContactPhone = $request->get('contact_phone');
        $isCompanyPhone = $request->get('company_phone');

        if ((!$request->has('company_phone') || !empty($isCompanyPhone)) && !empty($relationships['companies']['data'])) {
            $companiesIds = array_column($relationships['companies']['data'], 'id');

            Company::whereIn('id', $companiesIds)
                ->chunk(1000, function($companies) use (&$phones, $notOfferCompanies) {
                    foreach ($companies as $company) {
                        if (!in_array($company['status'], [Company::TEMPORARY_NOT_DEV, Company::NOT_ACTIVE_STATUS, Company::IN_ARCHIVE])) {
                            continue;
                        }

                        $attributes = json_decode($company['attributes'], true);

                        if (!empty($notOfferCompanies) && in_array($attributes['name'], $notOfferCompanies)) {
                            continue;
                        }

                        $relation = [
                            'type' => 'company',
                            'id' => $company['id']
                        ];

                        $generalPhone = trim($attributes['general-phone']);
                        $workPhone = trim($attributes['work-phone']);
                        $mobilePhone = trim($attributes['mobile-phone']);
                        $otherPhone = trim($attributes['other-phone']);

                        SendingKPHelper::mergePhones($phones, $otherPhone, $relation, $attributes['name']);
                        SendingKPHelper::mergePhones($phones, $mobilePhone, $relation, $attributes['name']);
                        SendingKPHelper::mergePhones($phones, $workPhone, $relation, $attributes['name']);
                        SendingKPHelper::mergePhones($phones, $generalPhone, $relation, $attributes['name']);
                    }
                });
        }

        if ((!$request->has('contact_phone') || !empty($isContactPhone)) && !empty($relationships['contacts']['data'])) {
            $contactsIds = array_column($relationships['contacts']['data'], 'id');

            foreach ($contactsIds as $contactId) {
                $contact = $this->methods->getContact($contactId);
                $attributes = $contact['attributes'];
                $customs = $attributes['customs'];
                $name = trim($attributes['first-name'].' '.$attributes['last-name']);

                if (!$this->sender->filterContactsPhones($contact)) {
                    continue;
                }

                $companyField = CustomHelper::issetField($customs, Contact::COMPANY_FIELD, '');

                if (!empty($notOfferCompanies) && !empty($companyField) && in_array($companyField, $notOfferCompanies)) {
                    continue;
                }

                $relation = [
                    'type' => 'contacts',
                    'id' => $contact['id']
                ];

                $workPhone = trim($attributes['work-phone']);
                $mobilePhone = trim($attributes['mobile-phone']);
                $otherPhone = trim($attributes['other-phone']);

                SendingKPHelper::mergePhones($phones, $otherPhone, $relation, $name);
                SendingKPHelper::mergePhones($phones, $mobilePhone, $relation, $name);
                SendingKPHelper::mergePhones($phones, $workPhone, $relation, $name);
            }
        }

        return $phones;
    }
}
