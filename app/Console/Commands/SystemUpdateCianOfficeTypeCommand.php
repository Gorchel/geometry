<?php

namespace App\Console\Commands;

use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CustomHelper;
use App\ParsingQueue;
use App\Properties;
use Illuminate\Console\Command;
use App\Classes\ApiParsingJs\CartTypes\Cian;
use Mockery\Exception;

/**
 * Class SystemUpdateCianOfficeTypeCommand
 * @package App\Console\Commands\System
 */
class SystemUpdateCianOfficeTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update_cian_office_type';

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
        $recordsQueue = ParsingQueue::whereIn('status', [ParsingQueue::STATUS_DONE, ParsingQueue::STATUS_SHEET_UPDATE])
            ->where('type', ParsingQueue::CIAN);

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $recordsQueue->chunk(1000, function($records) use ($methods) {
            foreach ($records as $key => $record) {
                if (empty($record->property_id)) {
                    continue;
                }

                $json = json_decode($record->json, true);

                if (json_last_error() != false) {
                    continue;
                }

                try {
                    $object = $methods->getObject($record->property_id);
                } catch (\Throwable $e) {
                    continue;
                }


                if (empty($object)) {
                    continue;
                }

                $objectType = CustomHelper::issetField($object['attributes']['customs'], 'custom-61755', null);

                $cianClass = new Cian();
                $type = isset($json['officeType']) ? $cianClass::getOfferType($json['officeType']) : '';

                if (isset($objectType[0]) && $objectType[0] == $type) {
                    continue;
                }

                if (!empty($type)) {
                    $data = [
                        'customs' => [
                            'custom-61755' => $type,
                        ],
                    ];

                    try {
                        $obj = $methods->objectGeneralUpdate($data, $record->property_id);
                    } catch (\Throwable $e) {
                        $record->status = ParsingQueue::STATUS_ERROR;

                        $details = [
                            'message' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ];

                        $record->details = json_encode($details);
                        $record->save();
                    }

                    echo $key.": #".$record->property_id." updated!\n\r";
                }
            }
        });
    }
}
