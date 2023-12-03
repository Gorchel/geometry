<?php

namespace App\Classes\Parsing;

use App\Classes\Parsing\CatalogsTypes\TypesInterface;
use App\ParsingQueue;
use PHPHtmlParser\Dom;
use Carbon\Carbon;

/**
 * Class Parser
 * @package App\Classes\Parser
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

        $objectLinks = $this->type->getObjectLinks();

        if (empty($objectLinks)) {
            return true;
        }

        $this->storeLinks($objectLinks);

        return true;
    }

    /**
     * @param array $links
     */
    protected function storeLinks(array $links)
    {
        $insertData = [];

        foreach ($links as $link)
        {
            $linksModel = ParsingQueue::where('link', $link)
                ->first();

            if (!empty($linksModel)) {
                continue;
            }

            $now = Carbon::now('Africa/Nairobi');

            $insertData[] = [
                'status' => ParsingQueue::STATUS_PENDING,
                'type' => $this->type->getType(),
                'link' => htmlspecialchars_decode($link),
                'created_at' => $now->format('Y-m-d H:i:s'),
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ];
        }

        if (!empty($insertData)) {
            ParsingQueue::insert($insertData);
        }
    }
}
