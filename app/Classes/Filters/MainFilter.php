<?php

namespace App\Classes\Filters;

use Illuminate\Http\Request;
use App\Helpers\CustomHelper;

/**
 * Class MainFilter
 * @package App\Classes\Filters;
 */
class MainFilter
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    public $objectFields = [
//        'footage' => 'custom-64803',
        'budget_volume' => 'custom-61759', 'budget_footage' => 'custom-61758'
    ];

    /**
     * @return array
     */
    public function prepareData(Request $request, $order, $type = "order", $object_type = null)
    {
        $data = [];

        foreach (array_keys(config('filter_params')) as $key) {
            if (!empty($request->get($key.'_check')) && !empty($request->get($key))) {
                $data[$key] = $request->get($key);
            }
        }

        $orderCustoms = $order['attributes']['customs'];

        $sliderData = $this->getSliderOrderData($object_type, $orderCustoms, $type, $order);

        $totalArea = 100;

        if (isset($data['footage']) && !empty($data['footage'])) {
            $data['footage'] = [
                $this->removeBsp($request->get('footage_start_input')),
                $this->removeBsp($request->get('footage_finish_input'))
            ];
        }

        $budgetVolume = 150000;

        if (!empty($sliderData['budget_volume'])) {
            $budgetVolume = $sliderData['budget_volume'];
        }

        if (isset($data['budget_volume']) && !empty($budgetVolume)) {
            $data['budget_volume'] = [
                $this->removeBsp($request->get('budget_volume_start_input')),
                $this->removeBsp($request->get('budget_volume_finish_input'))
            ];
        }

        $budgetFootage = 1500;

        if (!empty($sliderData['budget_footage'])) {
            $budgetFootage = $sliderData['budget_footage'];
        }

        if (isset($data['budget_footage']) && !empty($budgetFootage)) {
            $data['budget_footage'] = [
                $this->removeBsp($request->get('budget_footage_start_input')),
                $this->removeBsp($request->get('budget_footage_finish_input'))
            ];
        }

        foreach (['payback_period', 'actual_payback', 'payback_mpo', 'payback_yield', 'actual_yield'] as $key) {
            if (isset($data[$key])) {
                $data[$key] = explode(',', $data[$key]);
            }
        }

        $data['findByAll'] = $request->get('find_by_all');

        return $data;
    }

    /**
     * @param $value
     * @return int
     */
    protected function removeBsp($value)
    {
        return (int) str_replace(' ','', $value);
    }

    /**
     * @param $objectTypeId
     * @param $orderCustoms
     * @param string $type
     * @param null $order
     * @return array
     */
    public function getSliderOrderData($objectTypeId, $orderCustoms, $type = 'order', $order = null)
    {
        $objectSlider = [];

        $filterOrdersClass = new FilterOrders;

        if ($type == 'order') {
            $ranges = $filterOrdersClass->getCustomArray($objectTypeId, 'ranges');

            $defaultData = [
                'footage' => 100,
                'budget_volume' => 150000,
                'budget_footage' => 1500,
                'rent' => 24,
                'income' => 150000,
                'payback_period' => 130,
                'actual_payback' => 130,
                'payback_mpo' => 130,
                'payback_yield' => 130,
                'actual_yield' => 130,
            ];

            foreach (['footage', 'budget_volume', 'budget_footage','rent','income'] as $key) {
                if (isset($ranges[$key])) {
                    if (isset($ranges[$key]['value']) && !empty($ranges[$key]['value'])) {
                        $keyValue = CustomHelper::issetField($orderCustoms, $ranges[$key]['value']);

                        if (empty($keyValue)) {
                            $keyValue = $defaultData[$key];
                        }

                        $objectSlider[$key] = [
                            'avg' => $keyValue,
                        ];
                    } else {
                        $from = (int) CustomHelper::issetField($orderCustoms, $ranges[$key]['from']);
                        $to = (int) CustomHelper::issetField($orderCustoms, $ranges[$key]['to']);

                        $value = ($from + $to) / 2;

                        if (empty($value)) {
                            $value = $defaultData[$key];
                        }

                        $objectSlider[$key] = [
                           'from' => $from,
                           'to' => $to,
                           'avg' => $value,
                        ];
                    }
                }
            }//Слайдеры
        } else {
            if ($objectTypeId == 1) {
                $objectTypeId = 4;
            } else if ($objectTypeId == 2) {
                $objectTypeId = 3;
            }

            $ranges = $filterOrdersClass->getCustomPropertyArray($objectTypeId);

            foreach (['budget_volume', 'budget_footage'] as $key) {
                if (isset($ranges[$key])) {
                    $objectSlider[$key] = CustomHelper::issetField($orderCustoms, $ranges[$key]);
                }
            }
        }

        return $objectSlider;
    }

    /**
     * @param $address
     * @return int
     */
    public function checkCity($address)
    {
        if (strpos($address,'Петербург') == true) {
            return 2;
        }
        return 1;
    }

    /**
     * @param $value
     * @param string $key
     * @param array $data
     * @return array
     */
    protected function getArrayByPercent($value, string $key, $data)
    {
        if (empty($value)) {
            return [];
        }

        $percentArr = explode(',', $data[$key]);

        return [
            $this->percent(intval($value), intval($percentArr[0])),
            $this->percent(intval($value), intval($percentArr[1])),
        ];
    }

    /**
     * @param $number
     * @param $percent
     * @return float|int
     */
    protected function percent($number, $percent) {
        $numberPercent = ($number / 100) * $percent;

        return intval($number + $numberPercent);
    }

    /**
     * @param $str
     * @return string|string[]
     */
    public function updateStreet($str)
    {   $arr = ['пр-кт', 'район', 'ул', 'ул.'];

        foreach ($arr as $repl) {
            $str = str_replace($repl,'', $str);
        }

        return $str;
    }

    /**
     * @param array $users
     * @return array
     */
    public function convertUsers(array $users): array
    {
        $convertUsers = [];

        foreach ($users as $user) {
            $convertUsers[] = [
                'id' => $user['id'],
                'name' => $user['attributes']['first-name'].' '.$user['attributes']['last-name'],
            ];
        }

        return $convertUsers;
    }

    /**
     * @param array $stages
     * @return array
     */
    public function convertStagesCategories(array $stages): array
    {
        $convertStages = [];

        foreach ($stages as $stage) {
            $convertStages[] = [
                'id' => $stage['id'],
                'name' => $stage['attributes']['name']
            ];
        }

        return $convertStages;
    }

    /**
     * @param int $id
     * @param array $stages
     * @return string
     */
    public function getStageName(int $id, array $stages): string
    {
        foreach ($stages as $stage) {
            if ($id == $stage['id']) {
                return  $stage['attributes']['name'];
            }
        }

        return 'Воронка не найдена';
    }
}
