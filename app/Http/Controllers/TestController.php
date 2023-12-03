<?php

namespace App\Http\Controllers;

use App\Helpers\CustomHelper;
use Illuminate\Http\Request;
use App\Classes\SalesUp\SalesupHandler;
use App\Classes\ARinvest\PropertySender;
use App\Properties;

/**
 * Class TestController
 * @package App\Http\Controllers
 */
class TestController extends Controller
{
    public function index(Request $request) {
//        $handler = new SalesupHandler(env('API_TOKEN'));
//        $methods = $handler->methods;
//        $object = $methods->getObject(353121);

        $property = Properties::where('id', 353121)
            ->first();

        $sender = new PropertySender();
        $sender->send($property);
    }
}
