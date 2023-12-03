<?php
namespace App\Console\Commands;

use App\BstBridge;
use App\Classes\SalesUp\SalesupHandler;
use App\Properties;
use Illuminate\Console\Command;
use DB;

/**
 * Class FindSameOrders
 * @package App\Console\Commands
 */
class SystemUpdateBstLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update_bst_link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление ссылок по bst';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        BstBridge::chunk(1000, function($records) use ($methods) {
           foreach ($records as $record) {
               $data['customs'] = [
                   Properties::LINK_TO_BST => 'https://geometry-invest.bst.digital/coverage/market/'.$record['bst_id']
               ];

               $methods->objectGeneralUpdate($data, $record['property_id']);
           }
        });
    }
}
