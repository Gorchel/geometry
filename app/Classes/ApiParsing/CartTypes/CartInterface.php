<?php

namespace App\Classes\ApiParsing\CartTypes;

/**
 * Class CartInterface
 * @package App\Classes\ApiParsing\CartTypes
 */
interface CartInterface
{
    public function getTotal(array $result);
    public function getAddress(array $result);
    public function getDescription(array $result);
    public function getLink(array $result = []): string;
    public function getStatus(array $result = []): string;
    public function getSuccessStatuses(): array;
    public function getStatusName(string $lotStatus): string;
//    public function getName(Dom $dom);
    public function getType(): string;
    public function getInventory(array $result): string;
    public function customData(array &$data, array $result): array;
}
