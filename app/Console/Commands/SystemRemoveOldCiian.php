<?php
namespace App\Console\Commands;

use App\Classes\ARinvest\LinkGenerator;
use App\Classes\Form\PropertyForm;
use App\Classes\Google\Sheet2S2;
use App\Classes\SalesUp\SalesupHandler;
use App\Helpers\CustomHelper;
use App\Helpers\PopulationHelper;
use App\ParsingQueue;
use App\Properties;
use Illuminate\Console\Command;
use DB;

/**
 * Class SystemRemoveOldCiian
 * @package App\Console\Commands
 */
class SystemRemoveOldCiian extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:remove_old_cian';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление старых обьектов циан';

    protected $methods;

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
        $ids = ParsingQueue::where('type', ParsingQueue::CIAN)
            ->pluck('property_id')->toArray();

        $handler = new SalesupHandler(env('API_TOKEN'));
        $this->methods = $handler->methods;

        $propertyData = $this->methods->getPaginationObjects(1, 100);
        if (!empty($propertyData['data'])) {
            $this->eachProperties($propertyData['data'], $ids);

            $pageNumber = $propertyData['meta']['page-count'];

            if ($pageNumber > 1) {
                for ($page = 2; $page<=$pageNumber; $page++) {
                    $propertyData = $this->methods->getPaginationObjects($page, 100);
                    if (isset($propertyData['data']) && !empty($propertyData['data'])) {
                        $this->eachProperties($propertyData['data'], $ids);
                    }
                }
            }
        }
    }

    /**
     * @param $properties
     */
    protected function eachProperties($properties, $ids)
    {

        if (!empty($properties)) {
            foreach ($properties as $orderKey => $property) {
                $id = $property['id'];
                $customs = $property['attributes']['customs'];

                $source = CustomHelper::issetField($customs, Properties::CUSTOM_SOURCE, null);//Вид деятельности

                if (isset($source[0]) && strpos('Циан', $source[0])!== false) {
                    if (!in_array($id, $ids)) {
                        $data = [
                            'attributes' => [
                                'customs' => [
                                    'custom-74193' => 'Завершен'
                                ]
                            ]
                        ];

                        $res = $this->methods->objectGeneralUpdate($data['attributes'], $id);
                        echo $id.' updated'."\n";
                    }
                }
            }
        }
    }
}
