<?php

namespace App\Console\Commands;

use App\ParsingQueue;
use Illuminate\Console\Command;
use App\Classes\ApiParsingJs\CatalogsTypes\Cian;
use App\Classes\ApiParsing\CatalogsTypes\Torgi;
use App\Classes\ApiParsing\Parser;
use App\Classes\Parsing\Parser as HtmlParser;
use App\Classes\Parsing\CatalogsTypes\LotOnline;
use App\Classes\Parsing\CatalogsTypes\TorgiRu;
use App\Classes\ApiParsingJs\Parser as ParserJs;

/**
 * Class ParsingQueueListCommand
 * @package App\Console\Commands
 */
class ParsingQueueListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parsing:queueList {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсинг листинга недвижимости';

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

        switch ($type) {
            case ParsingQueue::TORGI_GOV:
                $model = new Torgi();

                $parser = new Parser($model);
                $parser->run();
                break;
            case ParsingQueue::TORGI_RU:
                $model = new TorgiRu();

                $parser = new HtmlParser($model);
                $parser->run();
                break;
            case ParsingQueue::LOT_ONLINE:
                $model = new LotOnline();

                $parser = new HtmlParser($model);
                $parser->run();
                break;
            case ParsingQueue::CIAN:
                $model = new Cian();

                $parser = new ParserJs($model);
                $parser->run();
                break;
            default:
        }

        return true;
    }
}
