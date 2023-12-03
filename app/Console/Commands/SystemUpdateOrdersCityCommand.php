<?php

namespace App\Console\Commands;

use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CustomHelper;
use App\Orders;
use App\ParsingQueue;
use App\Properties;
use Exception;
use Illuminate\Console\Command;

/**
 * Class SystemUpdateOrdersCityCommand
 * @package App\Console\Commands\System
 */
class SystemUpdateOrdersCityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update_orders_city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление города в заявке';

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

        $orders = Orders::select('id')
            ->orderBy('updated_at')
            ->chunk(100, function($orders) use ($methods) {
            foreach ($orders as $order) {
                $data['customs'] = [
                    "custom-74603" => [//город сниму
                        "Санкт-Петербург"
                    ],
                    "custom-74049" => [//город куплю
                        "Санкт-Петербург"
                    ]
                ];

                try {
                    $methods->ordersGeneralUpdate($data, $order['id']);

                    echo $order['id']." updated\n\r";
                    $order->touch();
                } catch (Exception $e) {
                    continue;
                }
            }
        });
    }
}
