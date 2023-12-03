<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\RestApp\Import;
use App\Jobs\RestAppJob;
use App\RestAppBridge as RestAppBridgeModel;

/**
 * Class RestAppBridge
 * @package App\Console\Commands
 */
class RestAppBridge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restapp:collect {limit=100} {type=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация с rest app';

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
        $limit = $this->argument('limit');
        $type = $this->argument('type');

        $import = new Import($type);
        $objectsChunk = $import->getObjects($limit);

        if (empty($objectsChunk)) {
            return true;
        }

        $removedLinks = [];
        array_walk($objectsChunk, function ($item, $key) use (&$removedLinks) {
            $removedLinks = array_merge($removedLinks, array_column($item, RestAppBridgeModel::HEADER_FILE['link']));
        });

        $import->removeObj($removedLinks);

        foreach ($objectsChunk as $objects) {
            dispatch(new RestAppJob($objects, $type))->onQueue('restapp');
        }
    }
}
