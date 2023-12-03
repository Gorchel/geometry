<?php

namespace App\Classes\ApiParsingJs\CatalogsTypes;

use App\ParsingQueue;
use App\Classes\ApiParsingJs\CatalogsTypes\TypesInterface;

/**
 * Class LotOnline
 * @package App\Classes\ApiParsing\CatalogsTypes
 */
class Cian implements TypesInterface
{
    /**
     * @return array
     */
    public function getCatalogs(): array
    {
        return [
            'cat.php?deal_type=rent',
            'cat.php?deal_type=sale'
        ];
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return 28;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return 'https://www.cian.ru/';
    }

    /**
     * @return string
     */
    public function getCartUrl(): string
    {
        return 'https://www.cian.ru/';
    }


    /**
     * @return string
     */
    public function getType(): int
    {
        return ParsingQueue::CIAN;
    }

    /**
     * @param int|null $page
     * @param int $size
     * @return string
     */
    public function getDefaultFilters(int $page = 1, int $size = self::DEFAULT_SIZE): string
    {
        return '&engine_version=2&offer_type=offices&office_type%5B0%5D=2&office_type%5B1%5D=4&office_type%5B2%5D=5&office_type%5B3%5D=10&office_type%5B4%5D=11&office_type%5B5%5D=12&ready_business_types%5B0%5D=1&region=2&page='.$page;
    }
}
