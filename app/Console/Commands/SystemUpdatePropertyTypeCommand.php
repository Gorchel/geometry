<?php

namespace App\Console\Commands;

use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CustomHelper;
use App\ParsingQueue;
use App\Properties;
use Illuminate\Console\Command;

/**
 * Class SystemUpdatePropertyTypeCommand
 * @package App\Console\Commands\System
 */
class SystemUpdatePropertyTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update_property_type {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление типа обьекта';

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
        $type = $this->argument('type');

        if (!in_array($type, [ParsingQueue::LOT_ONLINE, ParsingQueue::TORGI_GOV])) {
            throw new \Exception('Wrong type '.$type);
        }

        $recordsQueue = ParsingQueue::whereIn('status', [ParsingQueue::STATUS_DONE, ParsingQueue::STATUS_SHEET_UPDATE]);

        if (in_array($type, [ParsingQueue::LOT_ONLINE, ParsingQueue::TORGI_GOV])) {
            $recordsQueue->where('type', $this->argument('type'));
        }

        $records = $recordsQueue->get();

        if (empty($records)) {
            return true;
        }

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        foreach ($records as $key => $record) {
            if (empty($record->property_id)) {
                continue;
            }

            $object = $methods->getObject($record->property_id);

            if (empty($object)) {
                continue;
            }

            $objectType = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_TYPE, null);

            if (empty($objectType)) {
                $data = [
                    'customs' => [
                        Properties::CUSTOM_TYPE => [0 => 'Продажа'],
                    ],
                ];

                $obj = $methods->objectGeneralUpdate($data, $record->property_id);
                echo $key.": #".$record->property_id." updated!\n\r";
            }
        }
    }
}
