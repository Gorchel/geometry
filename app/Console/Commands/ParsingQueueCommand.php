<?php

namespace App\Console\Commands;

use App\ParsingQueue;
use Illuminate\Console\Command;
use App\Classes\ApiParsing\CartParser as ApiCartParser;
use App\Classes\ApiParsingJs\CartParser as ApiJsCartParser;
use App\Classes\Parsing\CartParser as HtmlCartParser;

/**
 * Class ParsingQueueCommand
 * @package App\Console\Commands
 */
class ParsingQueueCommand extends Command
{
    const LIMIT_RECORDS = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parsing:queueCart {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсинг недвижимости';

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

        if (!in_array($type, [ParsingQueue::LOT_ONLINE, ParsingQueue::TORGI_GOV, ParsingQueue::CIAN, ParsingQueue::TORGI_RU])) {
            throw new \Exception('Wrong type '.$type);
        }

        $recordsQueue = ParsingQueue::where('status', ParsingQueue::STATUS_PENDING);

        if (in_array($type, [ParsingQueue::LOT_ONLINE, ParsingQueue::TORGI_GOV, ParsingQueue::CIAN, ParsingQueue::TORGI_RU])) {
            $recordsQueue->where('type', $this->argument('type'));
        }

        $records = $recordsQueue->limit(static::LIMIT_RECORDS)
            ->get();

        if (empty($records)) {
            return true;
        }

        foreach ($records as $record) {
            $record->status = ParsingQueue::STATUS_PROCESSING;
            $record->save();
        }

        foreach ($records as $record) {
            if (in_array($type, [ParsingQueue::LOT_ONLINE, ParsingQueue::TORGI_RU])) {
                $parser = new HtmlCartParser($record);
            } elseif (in_array($type, [ParsingQueue::TORGI_GOV])) {
                $parser = new ApiCartParser($record);
            } elseif (in_array($type, [ParsingQueue::CIAN])) {
                $parser = new ApiJsCartParser($record);
            } else {
                $parser = new HtmlCartParser($record);
            }

            $parser->run();
        }
    }
}
