<?php

namespace App\Console\Commands;

use App\Classes\Property\SaldoForm;
use App\Classes\Google\Sheet2S2;
use App\Classes\SalesUp\SalesupHandler;
use App\Classes\ARinvest\LinkGenerator;
use App\Helpers\CustomHelper;
use App\Helpers\PopulationHelper;
use App\Properties;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Throwable;

/**
 * Class RestAppBridge
 * @package App\Console\Commands
 */
class UpdatePopulation extends Command
{
    public const LIMIT_RECORDS = 10;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:update_population {limit?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление плотности';

    public $methods;

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
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $limit = static::LIMIT_RECORDS;
        $argumentLimit = $this->argument('limit');

        if (isset($argumentLimit)) {
            $limit = $argumentLimit;
        }

        $records = Properties::where('is_statistic', '=', Properties::IS_NOT_STATISTIC)
            ->limit($limit)
            ->get();


        if (!empty($records)) {
            foreach ($records as $record) {
                $object = $methods->getObject($record['id']);

                $data = [];
                $longitude = $object['attributes']['longitude'];
                $latitude = $object['attributes']['latitude'];

                try {
                    //Проверка координат
                    if ((empty($longitude) || empty($latitude)) && !empty($object['attributes']['address'])) {
                        $dadataResult = PopulationHelper::getDadata($data, $object['attributes']['address']);

                        $longitude = $dadataResult['lon'];
                        $latitude = $dadataResult['lat'];
                    }

                    if (empty($longitude) || empty($latitude)) {

                        $record->is_statistic = Properties::IS_STATISTIC;
                        $record->statistic_text = 'not long';
                        $record->save();

                        continue;
                    }

                    //Ссылка на arinvest
                    $linkGenerator = new LinkGenerator();
                    $propertyARlink = CustomHelper::issetField($object['attributes']['customs'], 'custom-88347', '');
                    $arlink = $linkGenerator->make($latitude, $longitude);

                    if ($propertyARlink != $arlink) {
                        $arData = [
                            'attributes' => [
                                'customs' => [
                                    'custom-88347' => $arlink,
                                    'custom-88346' => $arlink,
                                ]
                            ]
                        ];

                        $objResponse = $methods->objectGeneralUpdate($arData['attributes'], $record['id']);
                    }

//                    if (empty($object['attributes']['customs'][Properties::CUSTOM_POPULATION_RESIDENTS])) {
                        $residents = PopulationHelper::getResidents($data, $longitude, $latitude);
                        $population = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_POPULATION, '');//Вид деятельности

                        $yandexLink = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_YANDEX_PANORAMA, '');
                        $twoGisLink = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_2GIS_PANORAMA, '');

                        if (empty($yandexLink)) {
                            $yandexLink = Properties::makeYaLink($longitude, $latitude);

                            if (!isset($data['attributes'])) {
                                $data['attributes'] = [];
                            }

                            $data['attributes']['customs'][Properties::CUSTOM_YANDEX_PANORAMA] = $yandexLink;
                        }

                        if (empty($twoGisLink)) {
                            $twoGisLink = Properties::make2gisLink($longitude, $latitude);

                            if (!isset($data['attributes'])) {
                                $data['attributes'] = [];
                            }

                            $data['attributes']['customs'][Properties::CUSTOM_2GIS_PANORAMA] = $twoGisLink;
                        }

                        if (!empty($data['attributes']) && isset($data['attributes']['customs']) &&
                            (isset($data['attributes']['customs'][Properties::CUSTOM_POPULATION_RESIDENTS]) && $data['attributes']['customs'][Properties::CUSTOM_POPULATION_RESIDENTS] != $population) ||
                            (isset($data['attributes']['customs'][Properties::CUSTOM_YANDEX_PANORAMA]))
                        ) {
                            $objResponse = $methods->objectGeneralUpdate($data['attributes'], $record['id']);
                        }

                        $population = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_POPULATION, '');//Вид деятельности
                        $populationResidents = CustomHelper::issetField($object['attributes']['customs'], Properties::CUSTOM_POPULATION_RESIDENTS, '');//Вид деятельности

                        if (!empty($population) || !empty($populationResidents)) {
                            $sheet2s2 = new Sheet2S2();
                            $result = $sheet2s2->updateSheet($record['id']);
                        }
//                    }

                    $saldoForm = new SaldoForm();
                    $saldoForm->setPropertyId($record['id']);
                    $saldoForm->save();

                    $record->is_statistic = Properties::IS_STATISTIC;
                    $record->save();
                } catch (Throwable $e) {
                    $record->is_statistic = Properties::IS_STATISTIC_ERROR;
                    $record->statistic_text = mb_substr($e->getMessage(), 0, 100);
                    $record->save();
                }
            }
        }

        return true;
    }
}
