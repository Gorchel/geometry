<?php

namespace App\Classes\Parsing\CatalogsTypes;

use App\ParsingQueue;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\Node\Collection;

/**
 * Class TorgiRu
 * @package App\Classes\CatalogsTypes
 */
class TorgiRu implements TypesInterface
{
    /**
     * @return string
     */
    public function getUrl(): string
    {
        return 'https://www.torgi-ru.ru';
    }

    /**
     * @return string
     */
    public function getType(): int
    {
        return ParsingQueue::TORGI_RU;
    }

    /**
     * @return string
     */
    public function getDefaultFilters(): string
    {
        return '/katalog/filter/%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0+30,29,26,25+20000000to12509419323/?section=30&section=29&section=26&section=25';
    }

    /**
     * @return array
     */
    public function getCatalogs(): array
    {
        return [];
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
        $url = $this->getUrl().$this->getDefaultFilters();

        $dom = new Dom();
        $dom->loadFromUrl($url);

        $records = $dom->find('.paste_map', 0)->find('div');

        $links = [];

        foreach ($records as $record) {
            $link = $record->getAttribute('data-link');

            if (empty($link)) {
                continue;
            }

            $links[] = $this->getUrl().$link;
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
        return [];
    }
}
