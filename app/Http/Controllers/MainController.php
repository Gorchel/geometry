<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;

/**
 * Class MainController
 * @package App\Http\Controllers
 */
class MainController extends Controller
{
    public function updateServer(string $type)
    {
        Artisan::call('update_tables:init '.$type);

        return view('main.update_server');
    }
}
