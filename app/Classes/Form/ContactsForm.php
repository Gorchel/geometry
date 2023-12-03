<?php

namespace App\Classes\Form;

use App\Classes\SalesUp\SalesupHandler;
use App\Classes\SalesUp\SalesupMethods;
use App\Contact;
use Exception;
use App\Helpers\CustomHelper;
use Illuminate\Support\Facades\Log;

/**
 * Class PropertyForm
 * @package App\Classes\Form;
 */
class ContactsForm
{
    const RESPONSIBLE_TYPE = 'responsible';
    /**
     * @var \App\Classes\SalesUp\SalesupMethods
     */
    protected $methods;

    /**
     * @var
     */
    protected $dealId;

    /**
     * @var
     */
    protected $responsible;

    /**
     * ContactsForm constructor.
     */
    public function __construct()
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $this->methods = $handler->methods;
    }

    /**
     * @param int $deal
     */
    public function setDealId(int $deal)
    {
        $this->dealId = $deal;
    }

    /**
     * @param int $responsible
     */
    public function setResponsible(int $responsible)
    {
        $this->responsible = $responsible;
    }

    /**
     * @param array $contacts
     * @return bool
     * @throws \Exception
     */
    public function storeContacts(array $contacts): bool
    {
        if (empty($contacts)) {
            return true;
        }

        $contacts = array_unique($contacts, SORT_REGULAR);
        $contactsChunk = array_chunk($contacts, 50);

        foreach ($contactsChunk as $contactsArr) {
            $contactAttributes = [
                "data" => [],
            ];

            foreach ($contactsArr as $contact) {
                $localData = [
                    "method" => "post",
                    "type" => "contacts",
                ];

                $contactModel = Contact::where('id', $contact['contact_id'])
                    ->first();

                if (empty($contactModel)) {
                    Log::info('ContactsForm ' . $contact['contact_id'] . ' is not found');
                    continue;
                }

                $attributes = json_decode($contactModel->attributes, true);
                $contactRelationships = json_decode($contactModel->relationships, true);

                unset($attributes['updated-at']);
                unset($attributes['cached-at']);
                unset($attributes['as-string']);
                unset($attributes['previous-responsible-id']);
                unset($attributes['note']);

                //create contact
                $localData['attributes'] = $attributes;

                $customs = json_decode($contactModel->customs, true);
                unset($customs[SalesupMethods::CUSTOM_CONTACT_STATUS]);

                $localData['attributes']['customs'] = $customs;
                $localData['attributes']['customs'][SalesupMethods::CUSTOM_CONTACT_DUBLICATION] = 1;


                $relationships = [];
                $estateProperties = [];

                foreach (['orders', 'companies', static::RESPONSIBLE_TYPE] as $key) {
                    if (isset($contactRelationships[$key]['data']) && !empty($contactRelationships[$key]['data'])) {
                        $relationships[$key]['data'] = $contactRelationships[$key]['data'];
                    }
                }

                foreach ($contact['types'] as $key => $type) {
                    if (!isset($type['type'])) {
                        continue;
                    }

                    if ($type['type'] == 'estate-properties') {
                        //Недвижка пока не учитывается
                        $estateProperties[] = $type;
                        continue;
                    }

                    if (!is_string($key)) {
                        $key = $type['type'];
                    }

                    if (!isset($relationships[$key])) {
                        $relationships[$key] = [
                            'data' => []
                        ];
                    }

                    if (!empty($relationships[$key]['data'])) {
                        $continue = false;
                        //Для подмассива
                        if (isset($relationships[$key]['data']['id'])) {
                            if ($relationships[$key]['data']['id'] == $type['id']) {
                                $continue = true;
                            }
                        } else {
                            foreach ($relationships[$key]['data'] as $item) {
                                if ($item['id'] == $type['id']) {
                                    $continue = true;
                                }
                            }
                        }

                        if ($continue) {
                            continue;
                        }
                    }

                    if (in_array($key, [static::RESPONSIBLE_TYPE])) {
                        $relationships[$key]['data'] = array_merge($relationships[$key]['data'], $type);
                    } else {
                        $relationships[$key]['data'][] = $type;
                    }
                }

                $relationships['deals']['data'][] = [
                    'type' => 'deals',
                    'id' => $this->dealId
                ];

                if (!empty($this->responsible)) {
                    $relationships['responsible'] = [
                        'data' => [
                            'type' => 'users',
                            'id' => $this->responsible,
                        ]
                    ];
                }

                $localData['relationships'] = $relationships;

                $contactAttributes['data'][] = $localData;
            }

            $response = $this->methods->bulk($contactAttributes);
        }

        return true;
    }

    /**
     * @param $contacts
     * @param int $contactId
     */
    public static function setContacts(&$contacts, int $contactId, string $type, int $typeId)
    {
        if (!isset($contacts[$contactId])) {
            $contacts[$contactId] = [
                'contact_id' => $contactId,
                'types' => []
            ];
        }

        $contacts[$contactId]['types'][] = [
            'type' => $type,
            'id' => $typeId,
        ];
    }

    /**
     * @param $contacts
     * @param array $notOfferCompanies
     * @param array $defaultResponsible
     * @param array $objData
     * @param bool $ignoreSendingEntities
     * @return array
     */
    public static function prepareContacts($contacts, $notOfferCompanies = [], $defaultResponsible = [], $objData = [], $ignoreSendingEntities = false)
    {
        /** @var lluminate\Database\Eloquent\Model $modelContacts */
        $modelContacts = Contact::select('id', 'customs','relationships');

        if (empty($ignoreSendingEntities)) {
            $modelContacts->doesntHave('sending_entities');
        }

        $modelContacts = $modelContacts->whereIn('id', array_keys($contacts))
            ->where('double', 0)
            ->get()
            ->toArray();

        $preparedContacts = [];

        foreach ($modelContacts as $contact) {
            $customs = json_decode($contact['customs'], true);
            $relationships = json_decode($contact['relationships'], true);

            //Не предлогать компаниям
            $companyField = CustomHelper::issetField($customs, Contact::COMPANY_FIELD, '');

            if (!empty($notOfferCompanies) && !empty($companyField) && in_array($companyField, $notOfferCompanies)) {
                continue;
            }

            // По виду деятельности
            $typeOfActivityField = CustomHelper::issetField($customs, Contact::TYP_OF_ACTIVITY, []);

            if (isset($objData['type_of_activity']) && isset($typeOfActivityField[0])) {
                if (!empty($typeOfActivityField) && !empty($objData['type_of_activity']) && !in_array($typeOfActivityField[0], $objData['type_of_activity'])) {
                    continue;
                }
            }

            //Поиск ответственного
            $responsible = [];

            if (isset($relationships['responsible']['data']) && !empty($relationships['responsible']['data'])) {
                $responsible = $relationships['responsible']['data'];
            } elseif (!empty($defaultResponsible)) {
                $responsible = $defaultResponsible;
            }

            if (!empty($responsible)) {
                $contacts[$contact['id']]['types'][static::RESPONSIBLE_TYPE] = $responsible;
            }

            $preparedContacts[$contact['id']] = $contacts[$contact['id']];
        }

        return $preparedContacts;
    }
}
