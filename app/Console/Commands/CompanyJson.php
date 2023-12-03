<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\Companies\CompaniesList;
use DB;

/**
 * Class ContactJson
 * @package App\Console\Commands
 */
class CompanyJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:make_json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Формирование json файла';

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
        $companiesGetter = new CompaniesList();

        $assocNames = json_encode($companiesGetter->getList());

        file_put_contents(app()->basePath().'/public/companies.json', $assocNames);
    }
}
