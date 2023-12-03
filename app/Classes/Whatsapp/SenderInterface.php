<?php

namespace App\Classes\Whatsapp;

use Illuminate\Http\Request;

/**
 * Class Sender
 * @package App\Classes\Whatsapp;
 */
interface SenderInterface
{
    /**
     * SenderInterface constructor.
     */
    public function __construct();

    /**
     * @param array $object
     * @param Request $request
     * @return array
     */
    public function prepareData(array $object, Request $request): array;
    public function prepareBody(array $object): string;
    public function preparePhones(array $object, array $phones): array;
    public function filterContactsPhones(array $contact): bool;
    public function getType(): string;
    public function getObject(Request $request): ?array;
}
