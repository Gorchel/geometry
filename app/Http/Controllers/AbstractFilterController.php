<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Class AbstractFilterController
 * @package App\Http\Controllers
 */
abstract class AbstractFilterController extends Controller
{
    /**
     * @param $objectData
     * @return array
     */
    protected function getErrors(array $objectData)
    {
        $errors = [];
        $configParams = config('filter_params');

        foreach ($objectData as $key => $value) {
            if (in_array($key, ['object_type'])) {
                continue;
            }

            if ($key == 'findByAll') {
                $errors[] = [
                    'name' => 'Поиск по всем',
                    'text' => $value,
                ];

                continue;
            }

            if (is_array($value)) {
                $value = implode(',', $value);
            }

            $errors[] = [
                'name' => $configParams[$key],
                'text' => $value,
            ];
        }

        return $errors;
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function prepareRequest($request)
    {
        return [
            'token' => $request->get('token'),
            'ids' => [$request->get('id')],
        ];
    }

    /**
     * @param array $params
     * @return string
     */
    protected function getParamsMsg(array $params)
    {
        $msg = '';

        foreach ($params as $param) {
            $msg .= $param['name'].': '.$param['text'].'; ';
        }

        return $msg;
    }
}
