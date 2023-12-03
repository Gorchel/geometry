<?php

namespace App\Console\Commands;

use App\Contact;
use App\Helpers\CustomHelper;
use App\Properties;
use App\Classes\SalesUp\SalesupHandler;
use Illuminate\Console\Command;

/**
 * Class UpdateContact2PropertyType
 * @package App\Console\Commands
 */
class UpdateContact2PropertyType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:contact2property {debug?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление типа обьекта из контакта';

    public const LIMIT_UPDATE = 50;

    public $methods;

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
        $this->methods = $handler->methods;

        echo 'Start'."\n\r";
        $propertiesQuery = Properties::select('id', 'system_statistic')
            ->where('system_statistic', Properties::IS_NOT_SYSTEM_STATISTIC);

        $debug = $this->argument('debug');

        if (!empty($debug)) {
            dd($propertiesQuery->count());
        }

        $properties = $propertiesQuery->limit(static::LIMIT_UPDATE)
            ->get();

        if (empty($properties)) {
            echo 'Not active properties'."\n\r";
            return null;
        }

        echo count($properties)."\n\r";

        foreach ($properties as $property) {
            $property->system_statistic = Properties::IS_SYSTEM_STATISTIC;
            $property->save();
        }

        foreach ($properties as $property) {
            echo $property->id."\n\r";
            $object = $this->methods->getObject($property->id);

            if (empty($object)) {
                continue;
            }

            if (isset($object['relationships']['contacts']['data']) && !empty($object['relationships']['contacts']['data'])) {
                $contact = $this->methods->getContact($object['relationships']['contacts']['data'][0]['id']);

                $contactType = CustomHelper::issetField($contact['attributes']['customs'], Contact::CONTACT_TYPE, null);

                if (empty($contactType) || !isset($contactType[0])) {
                    continue;
                }

                if (!in_array(trim($contactType[0]), ['Агент', 'Посредник'])) {
                    $propertyType = Properties::TYPE_OWNER_FROM_CONTACT;
                } else {
                    $propertyType = Properties::TYPE_AGENT_FROM_CONTACT;
                }

                $data = [
                    'customs' => [
                        $propertyType => trim($contactType[0]),
                    ],
                ];

                $objResponse = $this->methods->objectGeneralUpdate($data, $object['id']);
            }
        }
    }
}
