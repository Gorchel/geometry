<?php

namespace App\Classes\ApiParsing;

use App\Classes\ApiParsing\CatalogsTypes\TypesInterface;
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

        $this->getObjectLinks();

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
    protected function getObjectLinks(): bool
    {
        $result = $this->getModels();

        if ($result['totalPages'] > 1) {
            for ($i = 2; $i <= $result['totalPages']; $i++) {
                $this->getModels($i);
            }
        }

        return true;
    }

    /**
     * @param array $links
     * @param int|null $page
     * @return bool|mixed|string
     * @throws \Exception
     */
    protected function getModels(int $page = null)
    {
        $objects = [];
        $url = $this->type->getUrl().'?'.$this->type->getDefaultFilters($page);

        $result = CurlHelper::request($url);

        if (!empty($result['content'])) {
            foreach ($result['content'] as $property) {
                $objects[] = $property;
            }
        }

        if (!empty($objects)) {
            $this->storeLinks($objects);
        }

        return $result;
    }

    /**
     * @param array $objects
     */
    protected function storeLinks(array $objects)
    {
        $insertData = [];
        $now = Carbon::now('Africa/Nairobi');

        foreach ($objects as $object) {
            $link = $this->type->getCartUrl().'/'.$object['id'];

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
