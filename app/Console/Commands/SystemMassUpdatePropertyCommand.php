<?php

namespace App\Console\Commands;

use App\Classes\ARinvest\PropertySender;
use App\Classes\ARinvest\Properties as ArProperties;

use App\Classes\SalesUp\SalesupHandler;
use App\Properties;
use Exception;
use Illuminate\Console\Command;
use App\Helpers\CustomHelper;
use App\Classes\ARinvest\LinkGenerator;

/**
 * Class SystemMassUpdatePropertyCommand
 * @package App\Console\Commands\System
 */
class SystemMassUpdatePropertyCommand extends Command
{
    const PROPERTY_LIMIT = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:mass_update_property';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Массовое обновление обьекта';

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

        $linkGenerator =  new LinkGenerator();

        $properties  = Properties::where('is_updated',0)
            ->limit(static::PROPERTY_LIMIT)
            ->get();

        if (empty($properties)) {
            return true;
        }

        foreach ($properties as $property) {
            $customs = json_decode($property->customs, true);
            $attributes = json_decode($property->attributes, true);

            $longitude = $attributes['longitude'];
            $latitude = $attributes['latitude'];

                //Ссылка на arinvest
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

                $objResponse = $methods->objectGeneralUpdate($arData['attributes'], $property['id']);
            }

            if (!empty($property['ar_id'])) {
                $arProperties = new ArProperties();
                $arProperties->delete($property['ar_id']);

                $property->ar_id = null;
                $property->save();
            } else {
                $property->is_updated = 1;
                $property->save();

                $sender = new PropertySender();
                $response = $sender->send($property);
            }
        }
    }
}
