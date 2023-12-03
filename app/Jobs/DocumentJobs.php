<?php

namespace App\Jobs;

use App\Classes\Documents\Sender;
use App\Classes\Form\SenderForm;
use App\Classes\SalesUp\SalesupHandler;
use App\Contact;
use Carbon\Carbon;

class DocumentJobs extends Job
{
    public int $type;
    public array $options;
    public array $emails;
    public array $emailsJson;

    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @param int $type
     * @param array $options
     * @param array $emails
     */
    public function __construct(int $type, array $options, array $emails, string $emailsJson)
    {
        $this->type = $type;
        $this->options = $options;
        $this->emails = $emails;
        $this->emailsJson = json_decode($emailsJson, true);
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        if (in_array($this->type, [Sender::KP_TYPE, Sender::KP_SALE_TYPE])) {
            foreach ($this->emails as $email) {
                if (!isset($this->emailsJson[$email]) || !isset($this->emailsJson[$email]['relation'])) {
                    continue;
                }

                if ($this->emailsJson[$email]['relation']['type'] != 'contacts') {
                    continue;
                }

                $relation = $this->emailsJson[$email]['relation'];

                $dataAttributes = [
                    'customs' => [
                        Contact::SENDING_MESSAGE_FIELD => Contact::SENDING_EMAIL,
                    ]
                ];

                $methods->contactGeneralUpdate($dataAttributes, $relation['id']);
            }
        }

        if (in_array($this->type, [Sender::KP_TYPE, Sender::OS_TYPE, Sender::KP_SALE_TYPE])) {
            $deal = $methods->getDeal($this->options['id']);

            if (!empty($deal)) {
                $now = Carbon::now('Africa/Nairobi');
                $createdAt = Carbon::parse($deal['attributes']['created-at'], 'Africa/Nairobi');

                $dataAttributes = [
                    'customs' => [
                        'custom-83994' => $now->diffInDays($createdAt),
                        'custom-79731' => $now->format('d.m.Y'),
                    ]
                ];

                $methods->dealDataUpdate($this->options['id'], [], $dataAttributes);
            }

            $objAttributes = [
                'customs' => [
                    'custom-87806' => ['Да']
                ]
            ];

            $response = $methods->objectGeneralUpdate($objAttributes, $this->options['objectId']);

            //store sender
            $senderForm = new SenderForm();
            $senderForm->setObjectId($this->options['objectId']);
            $senderForm->setType($this->options['type']);

            if ($this->options['customDocuments']) {
                $senderForm->setLink($this->options['customDocuments']);
            }

            foreach ($this->emails as $email) {
                $senderForm->store($email);
            }
        }
    }
}
