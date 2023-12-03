<?php

namespace App\Classes\SalesUp;

use App\Classes\UnisenderApi;
use Illuminate\Support\Facades\Log;
use CURLFile;
use Exception;

/**
 * Class SalesupMethods
 * @package App\Classes\SalesUp;
 */
class SalesupMethods
{
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $url;
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $token;

    /**
     * @var \Illuminate\Config\Repository
     */
    private $upload_url;

    const CUSTOM_CONTACT_DUBLICATION = 'custom-82020';
    const CUSTOM_CONTACT_STATUS = 'custom-63791';

    /**
     * SalesupHandler constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->url = config('main.url');
        $this->upload_url = config('main.upload_url');
        $this->token = $token;
    }

    /**
     * @param int $dealId
     */
    public function getDeal(int $dealId)
    {
        $path = 'deals/'.$dealId;

        $data = [
            'include' => 'companies,contacts,documents,document-template-renders',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDeals.');

        return $response['data'];
    }

    /**
     * @param int $id
     */
    public function getDocument(int $id)
    {
        $path = 'document-template-renders/'.$id;

        $data = [

        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDocument.');

        return $response['data'];
    }

    /**
     * @param int $id
     */
    public function getMainDocument(int $id)
    {
        $path = 'documents/'.$id;

        $data = [

        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDocument.');

        return $response['data'];
    }

    /**
     * @param int $id
     */
    public function readDocumentPdf(int $id)
    {
        $extension = '.pdf';
        $path = 'document-template-renders/'.$id.$extension;

        $fileName = $id.'_'.time();
        $filePath = 'documents/'.$fileName.$extension;
        $fp = fopen($filePath, 'w+');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.$path);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/pdf',
            'Content-Transfer-Encoding: binary',
            'User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:34.0) Gecko/20100101 Firefox/34.0'
        ]);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        curl_close($ch);

        fclose($fp);

        return $filePath;
    }

    /**
     * @param int $id
     */
    public function readDocumentDocx(int $id)
    {
        $path = 'document-template-renders/'.$id.'.docx';

        $fp = fopen('document.docx', 'w+');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/vnd.api+json',
        ]);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response['data'];
    }


    public function getDocumentRender(int $id)
    {
        $path = 'document-template-renders';

        $data = [

        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDocumentRender.');

        return $response['data'];
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getOrders($number = 1, $size = 100, $filters = [])
    {
        $path = 'orders';

        $data = [
            'include' => 'companies,contacts',
            'page' => [
                'number' => $number,
                'size' => $size,
            ],
        ];

        if (!empty($filters)) {
            $data['filter'] = $filters;
        }

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getOrders.');

        return $response;
    }

    public function getPropertyImages(int $id)
    {
        $path = 'estate-properties/'.$id.'/images';

        $data = [];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getPropertyImages.');

        return $response;
    }

    public function getPropertyCover(int $id)
    {
        $path = 'estate-properties/'.$id.'/cover';

        $data = [];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getPropertyCover.');

        return $response;
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaginationObjects($number = 1, $size = 100, $filters = [])
    {
        $path = 'estate-properties';

        $data = [
            'include' => 'companies,contacts,status,deals',
            'page' => [
                'number' => $number,
                'size' => $size,
            ],
        ];

        if (!empty($filters)) {
            $data['filter'] = $filters;
        }

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getPaginationObjects.');

        return $response;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaginationCompany($number = 1, $size = 100, $filters = [])
    {
        $path = 'companies';

        $data = [
            'include' => 'contacts,status',
            'page' => [
                'number' => $number,
                'size' => $size,
            ],
        ];

        if (!empty($filters)) {
            $data['filter'] = $filters;
        }

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getPaginationCompany.');

        return $response;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaginationContact($number = 1, $size = 100, $filters = [])
    {
        $path = 'contacts';

        $data = [
            'include' => 'companies,orders,responsible',
            'page' => [
                'number' => $number,
                'size' => $size,
            ],
        ];

        if (!empty($filters)) {
            $data['filter'] = $filters;
        }

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getPaginationContacts.');

        return $response;
    }

    /**
     * @param $orderId
     * @return mixed
     * @throws \Exception
     */
    public function getOrder($orderId)
    {
        $path = 'orders/'.$orderId;

        $data = [
            'include' => 'company,contact',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getOrders.');

        return $response['data'];
    }

    /**
     * @param int $dealId
     */
    public function dealUpdate(int $dealId, array $contacts)
    {
        $path = 'deals/'.$dealId;

        $data = [];

        foreach ($contacts as $contact) {
            $data[] = [
                'type' => 'contacts',
                'id' => $contact
            ];
        }

        $body = [
            'data' => [
                'type' => 'deals',
                'id' => $dealId,
                'relationships' => [
                    'contacts' => [
                        'data' => $data,
                    ],
                ],
            ],
        ];

        $jsonResponse = $this->patchRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return $response['data'];
    }

    /**
     * @param int $dealId
     * @param array $relationships
     * @return
     * @throws \Exception
     */
    public function dealDataUpdate(int $dealId, array $relationships = [], array $attributes = [])
    {
        $path = 'deals/'.$dealId;

        $data = [
            'type' => 'deals',
            'id' => $dealId,
        ];

        if (!empty($relationships)) {
            $data['relationships'] = $relationships;
        }

        if (!empty($attributes)) {
            $data['attributes'] = $attributes;
        }

        $body = [
            'data' => $data,
        ];

//        Log::info(json_encode($body));

        $jsonResponse = $this->patchRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return isset($response['data']) ? $response['data'] : null;
    }

    /**
     * @param array $data
     * @return
     * @throws \Exception
     */
    public function dealCreate(array $data)
    {
        $path = 'deals';

        $data['type'] = 'deals';

        $body = ['data' => $data];

//        Log::info(json_encode($body));

        $jsonResponse = $this->postRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return $response['data'];
    }

    /**
     * @param int $id
     * @return mixed
     * @throws \Exception
     */
    public function deleteDeal(int $id)
    {
        $path = 'deals/'.$id;

        $jsonResponse = $this->deleteRequest($path);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return $response;
    }

    /**
     * @param array $data
     * @return
     * @throws \Exception
     */
    public function contactCreate(array $data)
    {
        $path = 'contacts';

        $data['type'] = 'contacts';

        $body = ['data' => $data];

//        Log::info(json_encode($body));

        $jsonResponse = $this->postRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return $response;
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    public function propertyCreate(array $attributes)
    {
        $path = 'estate-properties';

        $data['type'] = 'estate-properties';
        $data['attributes'] = $attributes;

        $body = ['data' => $data];

        $jsonResponse = $this->postRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return $response['data'];
    }

    /**
     * @param int $propertyId
     * @return mixed
     * @throws \Exception
     */
    public function propertyDelete(int $propertyId)
    {
        $path = 'estate-properties/'.$propertyId;

        $data['type'] = 'estate-properties';

        $jsonResponse = $this->deleteRequest($path);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return $response;
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Exception
     */
    public function companyCreate(array $data)
    {
        $path = 'companies';

        $data['type'] = 'companies';
        $body = ['data' => $data];

        $jsonResponse = $this->postRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return $response['data'];
    }

    /**
     * @param int $companyId
     */
    public function getCompany(int $companyId)
    {
        $path = 'companies/'.$companyId;

        $data = [
            'include' => 'contacts,status',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDeals.');

        return $response['data'];
    }

    public function getUsers()
    {
        $path = 'users';

        $data = [
//            'include' => 'contacts,status',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getUsers.');

        return $response['data'];
    }

    /**
     * @param array|null $filter
     * @return
     * @throws \Exception
     */
    public function getCompanies(array $filter = null)
    {
        $path = 'companies';

        $data = [
            'include' => 'contacts',
        ];

        if (!empty($filter)) {
            $data['filter'] = $filter;
        }

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getCompanies.');

        return $response['data'];
    }

    /**
     * @param array|null $filter
     * @return
     * @throws \Exception
     */
    public function getCompaniesStatuses(array $filter = null)
    {
        $path = 'company-statuses';

        $data = [

        ];

        if (!empty($filter)) {
            $data['filter'] = $filter;
        }

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getCompanies.');

        return $response['data'];
    }



    /**
     * @param array|null $filter
     * @return
     * @throws \Exception
     */
    public function getContactsStatuses(array $filter = null)
    {
        $path = 'contact-statuses';

        $data = [

        ];

        if (!empty($filter)) {
            $data['filter'] = $filter;
        }

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getCompanies.');

        return $response['data'];
    }

    /**
     * @param int $contactId
     * @return mixed
     * @throws \Exception
     */
    public function getContact(int $contactId)
    {
        $path = 'contacts/'.$contactId;

        $data = [];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDeals.');

        return $response['data'];
    }

    /**
     * @param array $filters
     * @return mixed
     * @throws \Exception
     */
    public function getContacts(array $filters = [])
    {
        $path = 'contacts';

        $data = [
            'filter' => $filters
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDeals.');

        return $response['data'];
    }

    /**
     * @param int $objectId
     */
    public function getObject(int $objectId)
    {
        $path = 'estate-properties/'.$objectId;

        $data = [
            'include' => 'deals,images,cover,contacts,documents,document-template-renders,responsible',
        ];

        try {
            $jsonResponse = $this->getRequest($path, $data);
        } catch (\Throwable $e) {
            return null;
        }


        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getObject.');

        if (!isset($response['data'])) {
            return null;
        }

        return $response['data'];
    }

    /**
     * @param int $objectId
     */
    public function getFullObject(int $objectId)
    {
        $path = 'estate-properties/'.$objectId;

        $data = [
            'include' => 'deals,images,status,source,diaries,orders,documents,responsible,company,contacts,performers',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getObject.');

        return $response['data'];
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    public function getObjects()
    {
        $path = 'estate-properties';

        $data = [
            'include' => 'companies,contacts',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getObjects.');

        return $response['data'];
    }

    /**
     * @param int $objectId
     */
    public function getDealStagesCategories()
    {
        $path = 'deal-stage-categories';

        $data = [
//            'include' => 'deals',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getObject.');

        return $response['data'];
    }

    /**
     * @param int $objectId
     */
    public function getDealStages()
    {
        $path = 'deal-stages';

        $data = [
//            'include' => 'deals',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getObject.');

        return $response['data'];
    }

    /**
     * @param int $objectId
     */
    public function getDealStatuses()
    {
        $path = 'deal-statuses';

        $data = [
//            'include' => 'deals',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDealStatuses.');

        return $response['data'];
    }

    /**
     * @param int $objectId
     */
    public function getDocuments()
    {
        $path = 'document-template-renders';

        $data = [
//            'include' => 'deals',
            'document-template-id' => 1
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDocuments.');

        return $response['data'];
    }


    public function getPropertyStatuses()
    {
        $path = 'estate-property-statuses';

        $data = [
//            'include' => 'deals',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDealStatuses.');

        return $response['data'];
    }

    public function getSources()
    {
        $path = 'sources';

        $data = [
//            'include' => 'deals',
        ];

        $jsonResponse = $this->getRequest($path, $data);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getDealStatuses.');

        return $response['data'];
    }

    /**
     * @param int $dealId
     * @param int $objectId
     * @return mixed
     * @throws \Exception
     */
    public function attachDealToObject(int $dealId, int $objectId) {
        $path = 'estate-properties/'.$objectId;

        $body = [
            'data' => [
                'type' => 'estate-properties',
                'id' => $objectId,
                'relationships' => [
                    'deals' => [
                        'data' => [
                            [
                                'id' => $dealId,
                                'type' => 'deals',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $jsonResponse = $this->patchRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return $response['data'];
    }

    /**
     * @param array $deals
     * @param int $objectId
     * @return mixed
     * @throws \Exception
     */
    public function attachDealsToObject(array $deals, int $objectId)
    {
        $data = [];

        foreach ($deals as $deal) {
            $data[] = [
                'id' => $deal,
                'type' => 'deals',
            ];
        }

        $relationships = [
            'deals' => [
                'data' => $data,
            ],
        ];

        return $this->attachToObject($relationships, $objectId);
    }

    /**
     * @param array $relationships
     * @param int $objectId
     * @return mixed
     * @throws \Exception
     */
    public function attachToObject(array $relationships, int $objectId)
    {
        $path = 'estate-properties/'.$objectId;

        $body = [
            'data' => [
                'type' => 'estate-properties',
                'id' => $objectId,
                'relationships' => $relationships,
            ],
        ];

        $jsonResponse = $this->patchRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return isset($response['data']) ? $response['data'] : null;
    }

    /**
     * @param array $data
     */
    public function objectCreate(array $data)
    {
        $path = 'estate-properties';

        $data['type'] = 'estate-properties';

        $body = ['data' => $data];

//        Log::info(json_encode($body));

        $jsonResponse = $this->postRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        return $response['data'];
    }

    /**
     * @param $filename
     * @param $filepath
     * @param $resourceType
     * @param $resourceId
     * @return mixed
     * @throws \Exception
     */
    public function downloadFile($filename, $filepath, $resourceType, $resourceId)
    {
        $path = 'files';

        $data = [];

        $data['type'] = 'files';
        $data['data'] = [
            'filename' => $filename,
            'resource-type' => $resourceType,
            'resource-id' => (int)$resourceId
        ];

        $responseS2Json = $this->postRequest($path, json_encode($data), $this->upload_url);

        $responseS2 = json_decode($responseS2Json, true);

        if (!isset($responseS2['data']['form-fields'])) {
            throw new Exception(json_encode([
                'msg' => 'Photo not loaded',
                'response' => $responseS2
            ]));
        }

        $formFields = $responseS2['data']['form-fields'];

        $formFields['file'] = new CURLFile($filepath);

        $responseJson = $this->fileRequest($responseS2['data']['url'], $formFields);

        return [
            'url' =>  $responseS2['data']['url'].'/'.$responseS2['data']['form-fields']['key'],
            'response' => $responseJson,
            'responseS2Json' => $responseS2Json
        ];
    }

    /**
     * @param array $data
     * @param int $propertyId
     * @param array $relationships
     * @return |null |null |null |null |null
     * @throws \Exception
     */
    public function objectGeneralUpdate(array $data, int $propertyId, array $relationships = [])
    {
        $path = 'estate-properties/'.$propertyId;

        $body = [
            'data' => [
                'type' => 'estate-properties',
                'id' => $propertyId,
                'attributes' => $data,
            ],
        ];

        if (!empty($relationships)) {
            $body['data']['relationships'] = $relationships;
        }

        $jsonResponse = $this->patchRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        $responseData = null;

        if (isset($response['data'])) {
            $responseData = $response['data'];
        }

        return $responseData;
    }


    /**
     * @param array $data
     * @param int $contactId
     * @return |null |null
     * @throws \Exception
     */
    public function contactGeneralUpdate(array $data, int $contactId)
    {
        $path = 'contacts/'.$contactId;

        $body = [
            'data' => [
                'type' => 'contacts',
                'id' => $contactId,
                'attributes' => $data,
            ],
        ];

        $jsonResponse = $this->patchRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        $responseData = null;

        if (isset($response['data'])) {
            $responseData = $response['data'];
        }

        return $responseData;
    }

    public function ordersGeneralUpdate(array $data, int $orderId)
    {
        $path = 'orders/'.$orderId;

        $body = [
            'data' => [
                'type' => 'orders',
                'id' => $orderId,
                'attributes' => $data,
            ],
        ];

        $jsonResponse = $this->patchRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        $responseData = null;

        if (isset($response['data'])) {
            $responseData = $response['data'];
        }

        return $responseData;
    }

    /**
     * @param int $dealId
     */
    public function objectUpdate(int $objectId, array $updateData)
    {
        $path = 'estate-properties/'.$objectId;

        $data = [];

        if (isset($updateData['district'])) {
            $data['district'] = $updateData['district'];

            if (!isset($data['customs'])) {
                $data['customs'] = [];
            }

            $data['customs']['custom-64791'] = $updateData['district'];
        }

        if (isset($updateData['metro'])) {
            $data['subway-name'] = $updateData['metro'];

            if (!isset($data['customs'])) {
                $data['customs'] = [];
            }

            $data['customs']['custom-64792'] = $updateData['metro'];
        }

        if (isset($updateData['metro_distance'])) {
            if (!isset($data['customs'])) {
                $data['customs'] = [];
            }

            $data['customs']['custom-74760'] = $updateData['metro_distance'];
        }

        if (isset($updateData['latitude'])) {
            $data['latitude'] = $updateData['latitude'];
        }

        if (isset($updateData['longitude'])) {
            $data['longitude'] = $updateData['longitude'];
        }

        if (isset($updateData['latitude']) && isset($updateData['longitude'])) {
            if (!isset($data['customs'])) {
                $data['customs'] = [];
            }

            $data['customs']['custom-65599'] = $updateData['latitude'].','.$updateData['longitude'];
        }

        $body = [
            'data' => [
                'type' => 'estate-properties',
                'id' => $objectId,
                'attributes' => $data,
            ],
        ];

        $jsonResponse = $this->patchRequest($path, json_encode($body));

        $response = json_decode($jsonResponse, true);

        $this->handleError($response);

        $responseData = null;

        if (isset($response['data'])) {
            $responseData = $response['data'];
        }

        return $responseData;
    }

        /**
     * @param $path
     * @param array $params
     * @return bool|string
     */
    protected function getRequest($path, $params = [])
    {
        if ( $params !== false && is_array($params) && count($params) ) {
            $paramsStr = '?'.http_build_query( $params );
        } else {
            $paramsStr = '';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.$path.$paramsStr);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/vnd.api+json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param $path
     * @param array $params
     * @param null $url
     * @return bool|string
     */
    protected function postRequest($path, $params = [], $url = null)
    {
        if (empty($url)) {
            $url = $this->url;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.$path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/vnd.api+json',
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param null $url
     * @param array $params
     * @return bool|string
     */
    protected function fileRequest($url, $params = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: multipart/form-data',
        ]);

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

        /**
     * @param $path
     * @param array $params
     * @return bool|string
     */
    protected function patchRequest($path, $params = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.$path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/vnd.api+json',
        ]);
//        curl_setopt($ch, CURLOPT_POST, 1);

        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param $path
     * @param array $params
     * @return bool|string
     */
    protected function deleteRequest($path)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url.$path);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->token,
            'Content-Type: application/vnd.api+json',
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * @param $response
     * @throws \Exception
     */
    protected function handleError($response, string $text = '')
    {
        if (isset($response['errors'])) {
            throw new \Exception("SalesapSender: ".json_encode($response['errors']).'. '.$text);
        }
    }

    /**
     * @return string
     */
    public function getUnisenderTemplates()
    {
        $unisender = new UnisenderApi(env('UNISENDER_TOKEN'));
        return $unisender->getTemplates();
    }

    /**
     * @param array $attributes
     * @return
     * @throws \Exception
     */
    public function bulk(array $attributes)
    {
        $path = 'bulk-jobs';

        $body = [
            'data' => [
                'type' => 'bulk-jobs',
                'attributes' => $attributes,
            ],
        ];


        $json = json_encode($body, JSON_UNESCAPED_UNICODE);

        $jsonResponse = $this->postRequest($path, $json);

        $response = json_decode($jsonResponse, true);

        $this->handleError($response, '. Method getObject.');

        return $response['data'];
    }
}
