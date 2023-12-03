<?php

namespace App\Classes\Google;

use App\Classes\SalesUp\SalesupHandler;
use App\Properties;
use Exception;
use App\Helpers\CustomHelper;
use Carbon\Carbon;
use Google\Service\Sheets;
use Google\Service\Sheets\CopySheetToAnotherSpreadsheetRequest;

/**
 * Class Sheet2S2
 * @package App\Classes\Google
 */
class Sheet2S2
{
    /**
     * @return \Illuminate\Config\Repository|mixed
     */
    public function getConfig()
    {
        return config('sheet2s2');
    }

    /**
     * @param string $list
     * @return string
     */
    public function getRange()
    {
        return  $this->getConfig()['ranges'];
    }

    /**
     * @param string $list
     * @return array
     */
    public function getCategories()
    {
        return $this->getConfig()['categories'];
    }

    /**
     * @param string $list
     * @return array
     */
    public function getTotal()
    {
        return $this->getConfig()['total'];
    }

//    /**
//     * @param string $list
//     * @return mixed
//     */
//    public function getFields()
//    {
//        return $this->getConfig()['fields'];
//    }

    /**
     * @param string $list
     * @return mixed
     */
    public function getUpdateFields()
    {
        return $this->getConfig()['updateFields'];
    }

    /**
     * @param $list
     * @param null $range
     * @return string
     */
    public function getFullRange($list, $range = null)
    {
        return $list.'!'.(!empty($range) ? $range : $this->getRange());
    }

//    /**
//     * @param string $list
//     * @param array $values
//     * @return array
//     */
//    public function makeCustomArr(string $list, array $values)
//    {
//        $customs = [];
//
//        foreach ($this->getFields($list) as $fieldArr) {
//            $customs[$fieldArr['field']] = $values[$fieldArr['key']];
//        }
//
//        return $customs;
//    }

    /**
     * @param int $id
     * @param array $additionalData
     * @return string|null
     * @throws Exception
     */
    public function updateSheet(int $id)
    {
        $token = env('API_TOKEN');

        $handler = new SalesupHandler($token);
        $methods = $handler->methods;

        //@todo избавится от дубляжа после корректного обновления
        $property = Properties::where('id', $id)
            ->first();

        if (empty($property)) {
            return false;
        }

        $object = $methods->getObject($id);

        $attributes = $object['attributes'];

        $objCustoms = $object['attributes']['customs'];

        //Получение собственного листа
        $propertySpreadsheet = new PropertySpreadsheet($property);
        $propertySheet = $propertySpreadsheet->create();

        $list = $propertySheet['sheet_name'];
        $spreadsheetId = $propertySheet->sheet->spreadsheet_id;

        $range = $this->getFullRange($list);

        $apiClient = new ApiClient();
        try {
            $client = $apiClient->getOAuthClient();

            //Обновляем Google Sheet
            foreach ($this->getUpdateFields() as $key => $field) {
                $value = 0;
                if (isset($field['customAttribute'])) {
                    $value = $attributes[$field['customAttribute']];
                }

                if (empty($value) && isset($field['customField'])) {
                    $value = CustomHelper::issetField($objCustoms, $field['customField'], 0);
                }

                if (isset($field['stopIfNull']) && !empty($field['stopIfNull']) && empty($value)) {
                    break;
                }

                $updateValues = [];
                for($i=0; $i<$field['count']; $i++) {
                    $updateValues[] = [$value];
                }

                $result = $apiClient->setValues($client, $spreadsheetId, $this->getFullRange($list, $field['fields']), $updateValues);
            }

            //Update Fields
            $values = $apiClient->getValues($client, $spreadsheetId, $range);

            //Updates
            $customs = [];

            if (!empty($values)) {
                foreach ($this->getCategories() as $key => $category) {
                    if (!isset($values[$key])) {
                        continue;
                    }

                    $row = $values[$key];

                    foreach ($category['updates'] as $customCategory) {
                        $customs[$customCategory['key']] = intval(str_replace(' ', '', $row[$customCategory['value']]));
                    }
                }

                foreach ($this->getTotal() as $key => $total) {
                    if (!isset($values[$total['key']])) {
                        continue;
                    }

                    $row = $values[$total['key']];
                    $customs[$total['field']] = intval(str_replace(' ', '', $row[$total['value']]));
                }
            }

            $customs[Properties::LINK_TO_GOOGLE_ANALYTICS] = PropertySpreadsheet::generateLink($spreadsheetId, $propertySheet->sheet_id);

            //Обновляем время расчета
            $endOfCalc = Carbon::now('Africa/Nairobi');
            $startOfCalc = CustomHelper::issetField($objCustoms, Properties::BST_START_CALC, 0);

            if (!empty($startOfCalc)) {
                $startOfCalc = Carbon::parse($startOfCalc);
                $avgCalc = $startOfCalc->diffInMinutes($endOfCalc);
                $customs[Properties::BST_AVG_CALC] = $avgCalc;
            }

            $customs[Properties::BST_END_CALC] = $endOfCalc->format('d.m.Y H:i:s');

            $attributes = [
                'customs' => $customs,
            ];

            $methods->objectGeneralUpdate($attributes, $id);
        } catch (Exception $e) {
            $result = $e->getMessage();
            dd($result);
            return false;
        }

        return true;
    }

    /**
     * @param int $id
     * @param array $additionalData
     * @return string|null
     * @throws Exception
     *
     * @deprecated
     */
    public function updateS2Object(int $id, array $additionalData = [])
    {
        $token = env('API_TOKEN');

        $handler = new SalesupHandler($token);
        $methods = $handler->methods;

        //@todo избавится от дубляжа после корректного обновления
        $object = $methods->getObject($id);
        $property = Properties::where('id', $id)
            ->first();

        $attributes = $object['attributes'];

        if (isset($additionalData['attributes'])) {
            $attributes = array_merge($attributes, $additionalData['attributes']);
        }

        $objCustoms = $object['attributes']['customs'];

        if (isset($additionalData['customs'])) {
            $objCustoms = array_merge($objCustoms, $additionalData['customs']);
        }

        //Получение собственного листа
        $propertySpreadsheet = new PropertySpreadsheet($property);
        $propertySheet = $propertySpreadsheet->create();

        $list = $propertySheet['sheet_name'];
        $spreadsheetId = $propertySheet->sheet->spreadsheet_id;

        $range = $this->getFullRange($list);

        $apiClient = new ApiClient();
        try {
            $client = $apiClient->getOAuthClient();

            //Обновляем Google Sheet
            foreach ($this->getUpdateFields() as $key => $field) {
                if (isset($field['customAttribute'])) {
                    $value = $attributes[$field['customAttribute']];
                } else {
                    $value = CustomHelper::issetField($objCustoms, $field['customField'], 0);
                }

                if (isset($field['stopIfNull']) && !empty($field['stopIfNull']) && empty($value)) {
                    break;
                }

                $updateValues = [];
                for($i=0; $i<$field['count']; $i++) {
                    $updateValues[] = [$value];
                }

                $result = $apiClient->setValues($client, $spreadsheetId, $this->getFullRange($list, $field['fields']), $updateValues);
            }

            //Update Fields
            $values = $apiClient->getValues($client, $spreadsheetId, $range);

            //Updates
            $customs = [];

            if (!empty($values)) {
                foreach ($this->getCategories() as $key => $category) {
                    if (!isset($values[$key])) {
                        continue;
                    }

                    $row = $values[$key];

                    foreach ($category['updates'] as $customCategory) {
                        $customs[$customCategory['key']] = str_replace(' ', '', $row[$customCategory['value']]);
                    }
                }

                foreach ($this->getTotal() as $key => $total) {
                    if (!isset($values[$total['key']])) {
                        continue;
                    }

                    $row = $values[$total['key']];
                    $customs[$total['field']] = str_replace(' ', '', $row[$total['value']]);
                }
            }

            $customs[Properties::LINK_TO_GOOGLE_ANALYTICS] = PropertySpreadsheet::generateLink($spreadsheetId, $propertySheet->sheet_id);

            //Обновляем время расчета
            $endOfCalc = Carbon::now('Africa/Nairobi');
            $startOfCalc = CustomHelper::issetField($objCustoms, Properties::BST_START_CALC, 0);

            if (!empty($startOfCalc)) {
                $startOfCalc = Carbon::parse($startOfCalc);
                $avgCalc = $startOfCalc->diffInMinutes($endOfCalc);
                $customs[Properties::BST_AVG_CALC] = $avgCalc;
            }

            $customs[Properties::BST_END_CALC] = $endOfCalc->format('d.m.Y H:i:s');

            $attributes = [
                'customs' => $customs,
            ];

            $result = $methods->objectGeneralUpdate($attributes, $id);
        } catch (Exception $e) {
            $result = $e->getMessage();

            dd($result);
        }

        return $result;
    }

    /**
     * export csv
     * @param null $path
     * @return bool|string|null
     */
    public function exportCSV($filename = null)
    {
        $apiClient = new ApiClient();
        try {
            $client = $apiClient->getOAuthClient();
            $values = $apiClient->getValues($client, 'A1:AC14');

            if (empty($filename)) {
                $filename = time().$this->generateRandomString().'.csv';
            }

            $path = app()->basePath('public/exports/'.$filename);

            $fp = fopen($path,'w');

            foreach ($values as $fields) {
                fputcsv($fp, $fields);
            }

            fclose($fp);

            return $filename;
        } catch (\Google\Exception $e) {
            return false;
        }
    }

    /**
     * @param int $length
     * @return string
     */
    protected function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

