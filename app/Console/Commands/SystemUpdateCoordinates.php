<?php
namespace App\Console\Commands;

use App\Classes\SalesUp\SalesupHandler;
use App\Classes\Yandex\Geocoder;
use App\Properties;
use Illuminate\Console\Command;
use DB;

/**
 * Class SystemUpdateCoordinates
 * @package App\Console\Commands
 */
class SystemUpdateCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update_coordinates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление геоданных по объекту';

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

        $geocoder = new Geocoder();

        Properties::chunk(1000, function($records) use ($geocoder, $methods) {
            foreach ($records as $record) {
                $attributes = json_decode($record->attributes, true);

                if (empty($attributes['longitude']) && empty($attributes['latitude'])) {
                    $address = $attributes['address'];

                    if (empty($address)) {
                        continue;
                    }

                    $geocoder->setGeocode($address);
                    $result = $geocoder->request();

                    if (empty($result)) {
                        continue;
                    }

                    $data = [
                        'longitude' => $result[0],
                        'latitude' => $result[1],
                    ];

                    $methods->objectGeneralUpdate($data, $record['id']);

                    echo "Obj #".$record['id']." updated. Lon ".$result[0].', Lat '.$result[1]."\n\r";
                }
            }
        });
    }
}
