<?php

namespace App\Classes\Parsing\CatalogsTypes;

use PHPHtmlParser\Dom;

/**
 * Class TypesInterface
 * @package App\Classes\Types
 */
interface TypesInterface
{
    public function getUrl(): string;
    public function getType(): int;
    public function getCatalogs(): array;
    public function getDefaultFilters(): string;
    public function findObjectLinks(Dom $dom);
    public function getObjectLinks(): array;
}
