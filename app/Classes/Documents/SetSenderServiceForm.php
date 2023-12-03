<?php

namespace App\Classes\Documents;

/**
 * Class Sender
 * @package App\Classes\Documents;
 */
class SetSenderServiceForm
{
    public array $emails;
    public array $emailsJson;
    public int $type;

    /**
     * @param array $emails
     */
    public function setEmails(array $emails)
    {
        $this->emails = $emails;
    }

    /**
     * @param array $emailsJson
     */
    public function setEmailsJson(array $emailsJson)
    {
        $this->emailsJson = $emailsJson;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    public function save()
    {

    }
}
