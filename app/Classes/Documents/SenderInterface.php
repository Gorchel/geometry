<?php

namespace App\Classes\Documents;

use Illuminate\Http\Request;

/**
 * Class Sender
 * @package App\Classes\Documents;
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
    public function prepareEmails(array $object, array $emails): array;
    public function filterContactsEmails(array $contact): bool;
    public function getType(): int;
    public function getObject(Request $request): ?array;
}
