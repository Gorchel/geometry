<?php

namespace App\Classes\ApiParsingJs\CartTypes;

/**
 * Class CartInterface
 * @package App\Classes\ApiParsingJs\CartTypes
 */
interface CartInterface
{
    public function getTotal(array $result);
    public function getAddress(array $result);
    public function getDescription(array $result);
//    public function getLink(array $result = []): string;
    public function getStatus(array $result = []): string;
    public function getSuccessStatuses(): array;
    public function getStatusName(string $status): string;
    public function getPhotos(array $result);
    public function getType(): string;
//    public function getInventory(array $result): string;
    public function customData(array &$data, array $result): array;
}
