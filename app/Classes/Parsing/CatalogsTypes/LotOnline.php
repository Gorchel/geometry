<?php

namespace App\Classes\Parsing\CatalogsTypes;

use App\ParsingQueue;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\Collection;

/**
 * Class LotOnline
 * @package App\Classes\CatalogsTypes
 */
class LotOnline implements TypesInterface
{
    /**
     * @return string
     */
    public function getUrl(): string
    {
        return 'https://catalog.lot-online.ru/index.php';
    }

    /**
     * @return string
     */
    public function getType(): int
    {
        return ParsingQueue::LOT_ONLINE;
    }

    /**
     * @return string
     */
    public function getDefaultFilters(): string
    {
        return 'personal_area=&features_hash=171-323380-323379_174-31371&filter_fields[is_archive]=false&items_per_page=100&page=1';
    }

    /**
     * @return array
     */
    public function getCatalogs(): array
    {
        return [
            'dispatch=categories.view&category_id=23',
            'dispatch=categories.view&category_id=19',
            'dispatch=categories.view&category_id=21',
            'dispatch=categories.view&category_id=24',
        ];
    }

    /**
     * @return array
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\CircularException
     * @throws \PHPHtmlParser\Exceptions\ContentLengthException
     * @throws \PHPHtmlParser\Exceptions\LogicalException
     * @throws \PHPHtmlParser\Exceptions\StrictException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function getObjectLinks(): array
    {
        $links = [];

        foreach ($this->getCatalogs() as $catalogQuery)
        {
            $url = $this->getUrl().'?'.$this->getDefaultFilters().'&'.$catalogQuery;

            $dom = new Dom();
            $dom->loadFromUrl($url);

            $catalogLinks = $this->findObjectLinks($dom);

            if (!empty($catalogLinks)) {
                $links = array_merge($links, $catalogLinks);
            }
        }

        return $links;
    }

    /**
     * @param Dom $dom
     * @return array
     * @throws \PHPHtmlParser\Exceptions\ChildNotFoundException
     * @throws \PHPHtmlParser\Exceptions\NotLoadedException
     */
    public function findObjectLinks(Dom $dom): array
    {
        $links = [];

        /** @var Collection $objectsDom */
        $objectsDom = $dom->find('.product-title');

        if (empty($objectsDom)) {
            return [];
        }

        foreach ($objectsDom as $itemDom) {
            /** @var Dom Collection */
            $links[] = $itemDom->getAttribute('href');
        }

        return $links;
    }
}
