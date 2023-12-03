<?php

namespace App\Classes\ApiParsingJs;

use App\Classes\ApiParsingJs\CatalogsTypes\TypesInterface;
use App\ParsingQueue;
use App\Helpers\CurlHelper;
use Carbon\Carbon;

/**
 * Class Parser
 * @package App\Classes\ApiParsing
 */
class Parser
{
    /**
     * @var TypesInterface
     */
    public TypesInterface $type;

    /**
     * Parser constructor.
     * @param TypesInterface $type
     */
    public function __construct(TypesInterface $type)
    {
        $this->type = $type;
    }

    /**
     *
     */
    public function run()
    {
        ini_set('max_execution_time', 900);

        $this->getObjects();

        return true;
    }

    /**
     * @return array
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \Exception
     */
    protected function getObjects(): array
    {
        $objects = [];

        foreach ($this->type->getCatalogs() as $catalog) {
            $pagination = $this->getModels($objects, 1, $catalog, true);
            $hour = (int) date('H');

            if ($pagination > 0) {
                $pages = (int) ceil($pagination / $this->type->getSize());

                $pagesPerHour = (int) ceil($pages / 24);
                $minPage = $pagesPerHour + $hour;
                $maxPage = $minPage + $pagesPerHour;

                for ($i=$minPage; $i<=$maxPage; $i++) {
                    $objects = [];

                    $this->getModels($objects, $i, $catalog);
                    $this->storeLinks($objects);
                }
            }
        }

        return $objects;
    }

    /**
     * @param array $links
     * @param int|null $page
     * @return bool|mixed|string
     * @throws \Exception
     */
    protected function getModels(array &$objects, int $page = 1, string $catalog = '', bool $withPaginatioon = false)
    {
        $url = $this->type->getUrl().$catalog.$this->type->getDefaultFilters($page);
        $options = [
            'http' => [
                "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad
            ]
        ];

        $context = stream_context_create($options);

        $html = file_get_contents($url, false, $context);

        $key = 'window._cianConfig[\'legacy-commercial-serp-frontend\'] || []).concat(';
        $html = str_replace($key, '', strstr($html, $key));

        $key = '"offers":';
        $offersJson = str_replace($key, '', strstr($html, $key));

        //paggination
        if ($withPaginatioon) {
            $startKey = '"aggregatedOffers":';
            $finishKey = ',"extendedOffersCount"';
            $offersWithPaginationJson = str_replace($startKey, '', strstr($offersJson, $startKey));
            $offersWithPaginationJson = str_replace($finishKey, '', strstr($offersWithPaginationJson, $finishKey, true));

            return (int) $offersWithPaginationJson;
        }

        $key = ',"aggregatedOffers"';
        $offersJson = str_replace($key, '', strstr($offersJson, $key, true));

        $offers = json_decode($offersJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $objects;
        }

        if (!empty($offers)) {
            $objects = array_merge($objects, $offers);
        }

        return $objects;
    }

    /**
     * @param array $objects
     */
    protected function storeLinks(array $objects)
    {
        $insertData = [];
        $now = Carbon::now('Africa/Nairobi');

        foreach ($objects as $object) {
            $link = $object['fullUrl'];

            $existingLinks = ParsingQueue::select('link')
                ->where('type', $this->type->getType())
                ->where('link', $link)
                ->first();

            if (!empty($existingLinks)) {
                $existingLinks->updated_at = $now->format('Y-m-d H:i:s');
                $existingLinks->json = json_encode($object);
                $existingLinks->save();
                continue;
            }

            $insertData[] = [
                'status' => ParsingQueue::STATUS_PENDING,
                'type' => $this->type->getType(),
                'link' => htmlspecialchars_decode($link),
                'json' => json_encode($object),
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ];
        }

        if (!empty($insertData)) {
            ParsingQueue::insert($insertData);
        }

        return true;
    }
}
