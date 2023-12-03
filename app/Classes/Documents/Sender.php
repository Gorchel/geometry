<?php

namespace App\Classes\Documents;

use App\Classes\SalesUp\SalesupHandler;
use App\Company;
use App\Contact;
use App\Helpers\CustomHelper;
use App\SenderObject;
use Illuminate\Http\Request;
use App\Properties;
use App\Helpers\SendingKPHelper;

/**
 * Class Sender
 * @package App\Classes\Documents;
 */
class Sender
{
    const KP_TYPE = 0;
    const OS_TYPE = 1;
    const CUSTOM_OS_TYPE = 2;
    const KP_SALE_TYPE = 3;
    const PASSED_TYPE = 4;

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

        if ($this->sender->getType() == Sender::PASSED_TYPE) {
            $emails = SenderObject::where('property_id', $object['id'])
                ->pluck('email')->toArray();

            if (empty($emails)) {
                return [
                    'status' => false,
                    'msg' => "Email отсутствуют"
                ];
            }
        } else {
            $emails = $this->getEmails($request, $prepareData['relationships'], $object);
        }

//        $emails['kasatka567@mail.ru'] = [
//            'description' => 'kasatka567@mail.ru (Test)',
//            'relation' => ['type' => 'contact', 'id' => '1234']
//        ];

        $emailsArr = $this->sender->prepareEmails($object, $emails);
        $emails = $emailsArr['emails'];
        $exceptEmails = $emailsArr['exceptEmails'];

        $prepareData['objectName'] = json_decode($object['attributes'], true)['name'];
        $prepareData['token'] = env('API_TOKEN');
        $prepareData['emails'] = $emails;
        $prepareData['exceptEmails'] = $exceptEmails;
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
     * @param array $relationships
     * @param $object
     * @return array
     * @throws \Exception
     */
    protected function getEmails(Request $request, array $relationships, $object)
    {
        $objectCustoms = json_decode($object['customs'], true);
        $notOfferCompanies = CustomHelper::issetField($objectCustoms, Properties::NOT_OFFER_COMPANY, []);

        $district = CustomHelper::issetField($objectCustoms, Properties::DISTRICT_CUSTOM, '');

        $emails = [];
        $isContactEmail = $request->get('contact_email');
        $isCompanyEmail = $request->get('company_email');

        if ((!$request->has('company_email') || !empty($isCompanyEmail)) && !empty($relationships['companies']['data'])) {
            $companiesIds = array_column($relationships['companies']['data'], 'id');

            Company::whereIn('id', $companiesIds)
                ->chunk(1000, function($companies) use (&$emails, $notOfferCompanies) {
                    foreach ($companies as $company) {
                        if (!in_array($company['status'], [Company::TEMPORARY_NOT_DEV, Company::NOT_ACTIVE_STATUS, Company::IN_ARCHIVE])) {
                            continue;
                        }

                        $attributes = json_decode($company['attributes'], true);
                        $customs = json_decode($company['customs'], true);


                        if (!empty($notOfferCompanies) && in_array($attributes['name'], $notOfferCompanies)) {
                            continue;
                        }

                        $email = trim($attributes['email']);
                        $otherEmail = trim($attributes['other-email']);
                        $custom63714 = trim(CustomHelper::issetField($customs, 'custom-63714'));
                        $custom64204 = trim(CustomHelper::issetField($customs, 'custom-64204'));

                        $relation = [
                            'type' => 'company',
                            'id' => $company['id']
                        ];

                        SendingKPHelper::mergeEmails($emails, $email, $relation, $attributes['name']);
                        SendingKPHelper::mergeEmails($emails, $otherEmail, $relation, $attributes['name']);
                        SendingKPHelper::mergeEmails($emails, $custom63714, $relation, $attributes['name']);
                        SendingKPHelper::mergeEmails($emails, $custom64204, $relation, $attributes['name']);
                    }
                });
        }

        if ((!$request->has('contact_email') || !empty($isContactEmail)) && !empty($relationships['contacts']['data'])) {
            $contactsIds = array_column($relationships['contacts']['data'], 'id');

            foreach ($contactsIds as $contactId) {
                $contact = $this->methods->getContact($contactId);
                $attributes = $contact['attributes'];
                $customs = $attributes['customs'];
                $name = trim($attributes['first-name'].' '.$attributes['last-name']);

                if (!$this->sender->filterContactsEmails($contact)) {
                    continue;
                }

                //Не предлогать компаниям
                $companyField = CustomHelper::issetField($customs, Contact::COMPANY_FIELD, '');

                if (!empty($notOfferCompanies) && !empty($companyField) && in_array($companyField, $notOfferCompanies)) {
                    continue;
                }

                //Не подходит район
                $districtsField = CustomHelper::issetField($customs, Contact::DISTRICT_FIELD, []);

                if (!empty($districtsField) && !in_array('Все', $districtsField)) {
                    if (!in_array($district, $districtsField)) {
                        continue;
                    }
                }

                $email = trim($attributes['email']);
                $otherEmail = trim($attributes['other-email']);
                $custom63026 = trim(CustomHelper::issetField($customs, 'custom-63026'));
                $custom66519 = trim(CustomHelper::issetField($customs, 'custom-66519'));
                $custom66520 = trim(CustomHelper::issetField($customs, 'custom-66520'));
                $custom66828 = trim(CustomHelper::issetField($customs, 'custom-66828'));
                $custom66842 = trim(CustomHelper::issetField($customs, 'custom-66842'));
                $custom66847 = trim(CustomHelper::issetField($customs, 'custom-66847'));
                $custom66859 = trim(CustomHelper::issetField($customs, 'custom-66859'));

                $relation = [
                    'type' => 'contacts',
                    'id' => $contact['id']
                ];

                SendingKPHelper::mergeEmails($emails, $email, $relation, $name);
                SendingKPHelper::mergeEmails($emails, $otherEmail, $relation, $name);
                SendingKPHelper::mergeEmails($emails, $custom63026, $relation, $name);
                SendingKPHelper::mergeEmails($emails, $custom66519, $relation,  $name);
                SendingKPHelper::mergeEmails($emails, $custom66520, $relation, $name);
                SendingKPHelper::mergeEmails($emails, $custom66828, $relation, $name);
                SendingKPHelper::mergeEmails($emails, $custom66842, $relation,  $name);
                SendingKPHelper::mergeEmails($emails, $custom66847, $relation, $name);
                SendingKPHelper::mergeEmails($emails, $custom66859, $relation, $name);
            }
        }

        return $emails;
    }
}
