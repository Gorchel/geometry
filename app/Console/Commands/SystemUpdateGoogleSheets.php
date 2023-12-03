<?php
namespace App\Console\Commands;

use App\Classes\Google\Sheet2S2;
use App\Helpers\CustomHelper;
use App\Properties;
use Illuminate\Console\Command;
use DB;

/**
 * Class SystemUpdateGoogleSheets
 * @package App\Console\Commands
 */
class SystemUpdateGoogleSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update_google_sheets';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление гугл таблиц по плотности';

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
        Properties::chunk(1000, function($records) {
            foreach ($records as $record) {
                $customs = json_decode($record->customs, true);

                $population = CustomHelper::issetField($customs, Properties::CUSTOM_POPULATION, '');//Вид деятельности

                if (!empty($population)) {
                    $sheet2s2 = new Sheet2S2();
                    $result = $sheet2s2->updateS2Object($record->id);

                    echo "Update property #".$record->id."\n\r";
                }
            }
        });
    }
}
