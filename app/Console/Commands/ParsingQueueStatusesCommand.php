<?php

namespace App\Console\Commands;

use App\ParsingQueue;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Classes\ApiParsing\StatusParser;
use App\Classes\ApiParsingJs\StatusParser as StatusParserJs;
use App\Classes\Parsing\StatusParser as StatusHtmlParser;

/**
 * Class ParsingQueueStatusesCommand
 * @package App\Console\Commands
 */
class ParsingQueueStatusesCommand extends Command
{
    const LIMIT_RECORDS = 1;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parsing:queueStatuses {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсинг статусов недвижимости';

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

        if (!in_array($type, [ParsingQueue::LOT_ONLINE, ParsingQueue::TORGI_GOV, ParsingQueue::CIAN])) {
            throw new \Exception('Wrong type '.$type);
        }

        $recordsQueue = ParsingQueue::where('status', ParsingQueue::STATUS_DONE);

        if (in_array($type, [ParsingQueue::LOT_ONLINE, ParsingQueue::TORGI_GOV, ParsingQueue::CIAN])) {
            $recordsQueue->where('type', $this->argument('type'));
        }

        $now = Carbon::now();
        $now->subDay();

        $records = $recordsQueue->whereNotNull('property_id')
            ->where('created_at', '<=', $now->format('Y-m-d H:i:s'))
            ->limit(static::LIMIT_RECORDS)
            ->orderBy('status_updated_at')
            ->get();

        if (empty($records)) {
            return true;
        }

        foreach ($records as $record) {
            $record->status_updated_at = time();
            $record->save();
        }

        foreach ($records as $record) {
            if (in_array($type, [ParsingQueue::TORGI_GOV])) {
                $parser = new StatusParser($record);
                $parser->run();
            } elseif(in_array($type, [ParsingQueue::CIAN])) {
                $parser = new StatusParserJs($record);
                $parser->run();
            } elseif(in_array($type, [ParsingQueue::LOT_ONLINE])) {
                $parser = new StatusHtmlParser($record);
                $parser->run();
            }
        }

        return true;
    }
}
