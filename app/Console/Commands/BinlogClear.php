<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

/**
 * Class FindSameOrders
 * @package App\Console\Commands
 */
class BinlogClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'binlog:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление старых binlog';

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
        $command = 'sudo rm '.env('BINLOG_PATH').'binlog.*';
        exec($command); ;
    }
}
