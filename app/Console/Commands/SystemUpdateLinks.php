<?php
namespace App\Console\Commands;

use App\Classes\ARinvest\LinkGenerator;
use App\Classes\Google\Sheet2S2;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CustomHelper;
use App\Helpers\PopulationHelper;
use App\Properties;
use Illuminate\Console\Command;
use DB;

/**
 * Class SystemUpdateGoogleSheets
 * @package App\Console\Commands
 */
class SystemUpdateLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update_links';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление ссылок';

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

        $counter = 1;

        Properties::chunk(100, function($records) use ($methods, $counter) {
            foreach ($records as $record) {
                $customs = json_decode($record->customs, true);
                $attributes = json_decode($record->attributes, true);

                $longitude = $attributes['longitude'];
                $latitude = $attributes['latitude'];
                $data = [];

                try {
                    if ((empty($longitude) || empty($latitude)) && !empty($attributes['address'])) {
                        $dadataResult = PopulationHelper::getDadata($data, $attributes['address']);

                        $longitude = $dadataResult['lon'];
                        $latitude = $dadataResult['lat'];
                    }

                    //Ссылка на arinvest
                    $linkGenerator = new LinkGenerator();
                    $propertyARlink = CustomHelper::issetField($customs, 'custom-88347', '');
                    $arlink = $linkGenerator->make($latitude, $longitude);

                    if ($propertyARlink != $arlink) {
                        $arData = [
                            'attributes' => [
                                'customs' => [
                                    'custom-88347' => $arlink,
                                    'custom-88346' => $arlink,
                                ]
                            ]
                        ];

                        $objResponse = $methods->objectGeneralUpdate($arData['attributes'], $record['id']);
                    }
                } catch (\Exception $exception) {

                }

            }
        });
    }
}
