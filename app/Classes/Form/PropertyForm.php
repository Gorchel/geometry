<?php

namespace App\Classes\Form;

use App\Helpers\CustomHelper;
use App\Properties;
use Carbon\Carbon;

/**
 * Class PropertyForm
 * @package App\Classes\Form;
 */
class PropertyForm
{
    /**
     * @param $property
     */
    public function storeProperty($property)
    {
        $attributes = $property['attributes'];
        $relationships = $property['relationships'];

        $propertyModel = Properties::where('id', $property['id'])
            ->first();

        $now = Carbon::now('Africa/Nairobi')->format('Y-m-d H:i:s');

        $status = null;

        if (isset($relationships['status']['data']) && !empty($relationships['status']['data'])) {
            $status = $relationships['status']['data']['id'];
//
//            if (!in_array($status, [
//                Properties::ACTIVE_VIP,
//                Properties::ACTIVE,
//                Properties::URGENT_VIP,
//                Properties::URGENT,
//            ])) {
//                if (!empty($propertyModel)) {
//                    $propertyModel->delete();
//                }
//
//                return;
//            }
//        }

//        if (!isset($attributes['customs']['custom-71235'][0]) || !in_array(
//            $attributes['customs']['custom-71235'][0], ['Горящий', 'Активный','Горящий VIP','Активный VIP']
//            )) {
//            if (!empty($propertyModel)) {
//                $propertyModel->delete();
//            }
//
//            return;
        }

        if (!empty($attributes['discarded-at'])) {
            if (!empty($propertyModel)) {
                $propertyModel->delete();
            }
            return;
        }

        if (empty($propertyModel)) {
            $propertyModel = new Properties;
            $propertyModel->id = $property['id'];
            $propertyModel->created_at = $now;
        } else {
            if ($propertyModel->updated_at == $now) {
                return;
            }
        }

        $propertyModel->updated_at = $now;
        $propertyModel->customs = json_encode($attributes['customs']);

        $customValue = CustomHelper::issetField($attributes['customs'], 'custom-62518', []);
        $type = array_values(array_diff(array_map('trim', $customValue), ['']));

        if (isset($type[0])) {
            $propertyModel->type = $this->getProperty($type[0]);
        }

        unset($attributes['customs']);

        $propertyModel->attributes = json_encode($attributes);

        $relationships = [
            'contacts' => $property['relationships']['contacts'],
            'companies' => $property['relationships']['companies'],
        ];

        if (isset($property['relationships']['deals']['data'])) {
            $relationships['deals'] = array_column($property['relationships']['deals']['data'], 'id');
        }

        $propertyModel->relationships = json_encode($relationships);

        if (!empty($status)) {
            $transformationData = [
                'status' => $status
            ];

            $propertyModel->transformation_relation = json_encode($transformationData);
        }

        $propertyModel->save();

        return $propertyModel;
    }

    /**
     * @param $str
     * @return int|null
     */
    protected function getProperty($str)
    {
        switch($str) {
            case 'Аренда':
                return Properties::RENT_TYPE;
            case 'Продажа':
                return Properties::BUY_TYPE;
            default:
                return null;
        }
    }
}
