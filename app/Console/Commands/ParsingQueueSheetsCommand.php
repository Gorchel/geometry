<?php

namespace App\Console\Commands;

use App\ParsingQueue;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use App\Classes\Google\Sheet2S2;

/**
 * Class ParsingQueueSheetsCommand
 * @package App\Console\Commands
 */
class ParsingQueueSheetsCommand extends Command
{
    public const LIMIT_FOR_UPDATING = 10;

        /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parsing:sheets {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление плотности';

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

        $records = ParsingQueue::where('type', $type)
            ->where('status', ParsingQueue::STATUS_DONE)
            ->where('property_id', '>', 0)
            ->limit(static::LIMIT_FOR_UPDATING)
            ->orderBy('updated_at')
            ->get();

        if (!empty($records)) {
            $sheet2S2 = new Sheet2S2();

            foreach ($records as $record) {
                /** @var Model $record */
                $result = $sheet2S2->updateSheet($record->property_id);

                if (empty($result)) {
                    $record->touch();
                    continue;
                }

                $record->status = ParsingQueue::STATUS_SHEET_UPDATE;
                $record->save();
            }
        }

        return true;
    }
}
