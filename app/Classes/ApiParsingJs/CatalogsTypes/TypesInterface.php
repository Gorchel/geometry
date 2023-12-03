<?php

namespace App\Classes\ApiParsingJs\CatalogsTypes;
/**
 * Class TypesInterface
 * @package App\Classes\ApiParsing\CatalogsTypes
 */
interface TypesInterface
{
    public const DEFAULT_SIZE = 100;

    public function getUrl(): string;
//    public function getCartUrl(): string;
    public function getType(): int;
    public function getSize(): int;
    public function getCatalogs(): array;
    public function getDefaultFilters(int $page = 1, int $size = self::DEFAULT_SIZE): string;
}
