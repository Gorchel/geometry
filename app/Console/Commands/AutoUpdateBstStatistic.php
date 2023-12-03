<?php

namespace App\Console\Commands;

use App\Properties;
use App\Classes\SalesUp\SalesupHandler;
use Illuminate\Console\Command;
use App\Classes\RestApp\Import;

/**
 * @deprecated
 * Class AutoUpdateBstStatistic
 * @package App\Console\Commands
 */
class AutoUpdateBstStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bst:autoUpdate {debug?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Авто Обновление статистики по bst';

    public const LIMIT_UPDATE = 10;

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
        $propertiesQuery = Properties::select('id', 'is_statistic', 'transformation_relation')
            ->where('is_statistic', Properties::IS_NOT_STATISTIC)
            ->where(function($query) {
                $query->whereNotIn('transformation_relation',['{"status":"'.Properties::ARCHIVE_ADVERT.'"}', '{"status":"'.Properties::ARCHIVE.'"}']);
                $query->orWhereNull('transformation_relation');
            })
            ->orderBy('transformation_relation');

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
            echo $property->id."\n\r";
            $object = $this->methods->getObject($property->id);

            if (empty($object)) {
                continue;
            }

            $import = new Import();
            $response = $import->simpleBstImport($object);

            if ($response['status'] == true) {
                $property->is_statistic = Properties::IS_STATISTIC;
            } else {
                $property->is_statistic = Properties::IS_STATISTIC_ERROR;
            }

            $property->statistic_text = $response['text'];

            $property->save();
        }
    }
}
