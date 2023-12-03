<?php

namespace App\Console\Commands;

use App\ParsingQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use App\Helpers\TelegramBot;

/**
 * Class ParsingQueueReportCommand
 * @package App\Console\Commands
 */
class ParsingQueueReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parsing:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отчет по парсерам';

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
        $types = [
            ParsingQueue::LOT_ONLINE, ParsingQueue::TORGI_GOV, ParsingQueue::CIAN, ParsingQueue::TORGI_RU
        ];

        foreach ($types as $type) {
            $msg = "\r\n".ParsingQueue::type2Label($type).": \r\n";

            $statuses = ParsingQueue::select(['status', DB::raw("COUNT(id) as count")])
                ->where('type', $type)
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            if (empty($statuses)) {
                continue;
            }

            foreach ($statuses as $status => $count) {
                $msg .= ParsingQueue::status2Label($status).' - '.$count."\n\r";
            }

            TelegramBot::send(env('TELEGRAM_CHAT_ID'), $msg);
        }

        return true;
    }
}
