<?php

namespace App\Classes\ApiParsing\CatalogsTypes;

use App\ParsingQueue;
use App\Classes\ApiParsing\CatalogsTypes\TypesInterface;

/**
 * Class LotOnline
 * @package App\Classes\ApiParsing\CatalogsTypes
 */
class Torgi implements TypesInterface
{
    /**
     * @return string
     */
    public function getUrl(): string
    {
        return 'https://torgi.gov.ru/new/api/public/lotcards/search';
    }

    /**
     * @return string
     */
    public function getCartUrl(): string
    {
        return 'https://torgi.gov.ru/new/api/public/lotcards';
    }


    /**
     * @return string
     */
    public function getType(): int
    {
        return ParsingQueue::TORGI_GOV;
    }

    /**
     * @param int $size
     * @param int|null $page
     * @return string
     */
    public function getDefaultFilters(?int $page = null, int $size = self::DEFAULT_SIZE): string
    {
        $url = 'dynSubjRF=78,79&chars=dec-totalAreaRealty:40~10000&biddType=1041PP,178FZ,229FZ&biddForm=EA,PrP,PP,EK,PA,PPZ,GHP&lotStatus=SUCCEED,APPLICATIONS_SUBMISSION,PUBLISHED,FAILED&catCode=11&byFirstVersion=true&withFacets=true&size='.$size.'&sort=updateDate,desc';

        if (!empty($page)) {
            $url .= '&page='.$page;
        }

        return $url;
    }
}
