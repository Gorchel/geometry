<?php

namespace App\Helpers;

/**
 * Class SendingKPHelper
 * @package App\Helpers
 */
class SendingKPHelper
{
    /**
     * @param array $emails
     * @param string $email
     * @param array $relation
     * @param string $description
     */
    public static function mergeEmails(array &$emails, string $email, array $relation, string $description = '')
    {
        if (static::checkEmail($email)) {
            $emails[$email] = [
                'relation' => $relation,
                'description' => $email.' ('.$description.')'
            ];
        }
    }

    /**
     * @param array $phones
     * @param string $phone
     * @param array $relation
     * @param string $description
     */
    public static function mergePhones(array &$phones, string $phone, array $relation, string $description = '')
    {
        if (static::checkPhone($phone)) {
            $phones[$phone] = [
                'relation' => $relation,
                'description' => $phone.' ('.$description.')'
            ];
        }
    }

    /**
     * @param $email
     * @return bool
     */
    protected static function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    /**
     * @param $phone
     * @return bool
     */
    protected static function checkPhone($phone)
    {
        if (preg_match('/^[0-9]{11}+$/', $phone)) {
            return true;
        }

        return false;
    }
}
