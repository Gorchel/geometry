<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Classes\Google\Sheet2S2;

/**
 * Class StatisticController
 * @package App\Http\Controllers
 */
class StatisticController extends Controller
{
    /**
     * @param Request $request
     * http://salesup.local/statistic_update_google?ids[]=327766
     */
    public function updateGoogleStatistic(Request $request) {
        $id = $request->get('ids')[0];
        $sheet2s2 = new Sheet2S2();
        $result = $sheet2s2->updateS2Object($id);

        dd('Данные обновлены');
    }
}
