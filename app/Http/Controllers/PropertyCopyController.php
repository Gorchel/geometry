<?php

namespace App\Http\Controllers;

use App\Classes\Property\CopyPropertyForm;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CustomHelper;
use App\Traits\UpdatedTablesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Properties;

/**
 * Class PropertyCopyController
 * @package App\Http\Controllers
 */
class PropertyCopyController extends Controller
{
    use UpdatedTablesTrait;

    /**
     * @param Request $request
     */
    public function index(Request $request)
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;
        $id = (int)$request->get('ids')[0];

        $property = $methods->getObject($id);

        if (empty($property)) {
            throw new Exception('CopyPropertyForm@copy property not found');
        }

        $propertyType = CustomHelper::issetField($property['attributes']['customs'], Properties::CUSTOM_TYPE, '');

        if (!empty($propertyType)) {
            $propertyType = $propertyType[0];
        }

        $data = [
            'property' => $property,
            'propertyType' => $propertyType,
            'propertyTypeList' => Properties::CUSTOM_TYPE_LIST,
            'updatedTablesRecords' => static::getUpdatedRecords()
        ];

        return view('copy.index', $data);
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function copyProperty(Request $request)
    {
        $copyForm = new CopyPropertyForm();
        $copyForm->setPropertyId((int)$request->get('id'));
        $copyForm->setParams($request->all());
        $response = $copyForm->copy();

        return view('copy.success', ['result' => $response]);
    }
}
