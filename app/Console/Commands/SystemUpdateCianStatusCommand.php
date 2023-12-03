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
 * Class SystemUpdateCianStatusCommand
 * @package App\Console\Commands\System
 */
class SystemUpdateCianStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update_cian_status';

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
                try {
                    $object = $methods->getObject($record->property_id);
                } catch (\Throwable $exception) {
                    continue;
                }

                if (empty($object)) {
                    continue;
                }

                $status = CustomHelper::issetField($object['attributes']['customs'], 'custom-74193', null);

                if (is_array($status)) {
                    $status = $status[0];
                }

                if (in_array($status, Cian::getStatuses()) || is_null($status)) {
                    continue;
                }

                $cianClass = new Cian();

                if (empty($objectType)) {
                    $data = [
                        'customs' => [
                            'custom-74193' => $cianClass->getStatusName($status),
                        ],
                    ];

                    $obj = $methods->objectGeneralUpdate($data, $record->property_id);
                    echo $key.": #".$record->property_id." updated!\n\r";
                }
            }
        });
    }
}
