<?php

namespace App\Classes\Property;

use App\Classes\SalesUp\SalesupHandler;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Class CopyPropertyForm
 * @package App\Classes\Property
 */
class CopyPropertyForm
{
    /**
     *
     */
    protected const RELATIONS_LIST = ['company','status',
        'source','responsible','contacts',
        'orders', 'diaries','responsible',
        'performers'];

    /**
     * @var int
     */
    public $id;

    /**
     * @var array
     */
    public $params;


    /**
     * @var bool
     */
    public $compare;

    /**
     * @var \App\Classes\SalesUp\SalesupMethods
     */
    public $methods;

    public function __construct()
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $this->methods = $handler->methods;
    }

    /**
     * @param int $id
     */
    public function setPropertyId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function copy()
    {
        $this->setCompare();

        $property = $this->methods->getFullObject($this->id);

        if (empty($property)) {
            throw new Exception('CopyPropertyForm@copy property not found');
        }

        $this->unsetFields($property);
        $this->replaceFields($property);

        $data = [
            'attributes' => $property['attributes'],
        ];

        $relationships = $this->makeRelations($property);

        if (!empty($relationships)) {
            $data['relationships'] = $relationships;
        }

        $copiedProperty = $this->methods->objectCreate($data);

        if (empty($copiedProperty)) {
            throw new Exception('CopyPropertyForm@copy property not create');
        }

        $this->copyDocuments($property, $copiedProperty);

        return ['status' => true, 'response' => $copiedProperty];
    }

    /**
     * @param $property
     * @param $copiedProperty
     */
    protected function copyDocuments($property, $copiedProperty)
    {
        $documents = $property['relationships']['documents']['data'];

        if (!empty($documents)) {
            foreach($documents as $document) {
                try {
                    $fullDocument = $this->methods->getMainDocument($document['id']);

                    if (!empty($fullDocument)) {
                        $name = $fullDocument['attributes']['name'];

                        $name = str_replace(' ','_', trim($name));
                        $name = str_replace(' ','_', trim($name));
                        $name = str_replace('/','_', trim($name));
                        $name = str_replace(',','_', trim($name));
                        $name = str_replace('__','_', trim($name));

                        $this->methods->downloadFile($name, $fullDocument['attributes']['download-link'], 'estate-properties', $copiedProperty['id']);
                    }
                } catch (\Throwable $exception) {
                    Log::error($exception->getMessage());
                    continue;
                }
            }
        }
    }

    /**
     * @param $property
     * @return array
     */
    protected function makeRelations($property)
    {
        $relationships = [];

        foreach (static::RELATIONS_LIST as $key) {
            if (isset($property['relationships'][$key]['data']) && !empty($property['relationships'][$key]['data'])) {
                $relationships[$key]['data'] = $property['relationships'][$key]['data'];
            }
        }

        return $relationships;
    }

    /**
     * @param $property
     */
    protected function replaceFields(&$property)
    {
        $property['attributes']['customs']['custom-87985'] = 'Копия';

        if (empty($this->compare)) {
            $property['attributes']['customs']['custom-62518'] = $this->params['property_copied_type'];
        }
    }

    /**
     * @param $property
     */
    protected function unsetFields(&$property)
    {
        unset($property['attributes']['updated-at']);
        unset($property['attributes']['cached-at']);
        unset($property['attributes']['as-string']);
        unset($property['attributes']['cover-image']);
        unset($property['attributes']['previous-responsible-id']);
        unset($property['attributes']['name']);
        unset($property['attributes']['customs']['custom-87933']);
    }

    /**
     * Set type compare
     */
    protected function setCompare()
    {
        $this->compare = $this->params['property_type'] == $this->params['property_copied_type'];
    }
}
