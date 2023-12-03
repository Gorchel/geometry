<?php

namespace App\Http\Controllers;

use App\Classes\SalesUp\SalesupMethods;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Classes\BST\ApiPlaceClass;
use App\BstBridge;
use DB;

/**
 * Class BstController
 * @package App\Http\Controllers
 */
class BstController extends Controller
{
    /**
     * @var ApiPlaceClass
     */
    protected $bstClass;

    /**
     * BstController constructor.
     */
    public function __construct()
    {
        $this->bstClass = new ApiPlaceClass;
    }

    /**
     * @param Request $request
     */
    public function index(Request $request) {
        $this->bstClass->getAuthToken();
        $markets = $this->bstClass->getMarkets();
        dd($markets);
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        $bstModels = DB::table('bst_bridge')->orderBy('id', 'desc')
            ->simplePaginate(50);

        $inProcessCount = DB::table('bst_bridge')->where('status', BstBridge::STATUS_PENDING)
             ->count();

        $completedCount = DB::table('bst_bridge')->where('status', BstBridge::STATUS_DONE)
             ->count();

        $data = [
            'models' => $bstModels,
            'completed' => $completedCount,
            'pending' => $inProcessCount,
        ];

        return view('bst.list', $data);
    }

    /**
     * @return mixed
     */
    public function getPropertiesList(Request $request)
    {
         $query = DB::table('properties');

         if (!empty($request->get('property_id'))) {
             $query->where('id', '=', $request->get('property_id'));
         }

         $propertiesModels = $query->orderBy('id', 'desc')
            ->simplePaginate(50);

        $data = [
            'models' => $propertiesModels
        ];

        return view('bst.property_list', $data);
    }
}
