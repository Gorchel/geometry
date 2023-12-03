<?php

namespace App\Classes\Parsing\CartTypes;

use PHPHtmlParser\Dom;

/**
 * Class CartInterface
 * @package App\Classes\Types
 */
interface CartInterface
{
    public function checkErrorFlag(Dom $dom):bool;
    public function getTotal(Dom $dom);
    public function getAddress(Dom $dom);
    public function getDescription(Dom $dom);
    public function getName(Dom $dom);
    public function getInventory(Dom $dom);
    public function getType(): string;
    public function customData(array &$data, Dom $dom): array;
    public function getHtml(string $url): Dom;
}
