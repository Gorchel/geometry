<?php

namespace App\Jobs;

use App\Classes\Whatsapp\Sender;
use App\Classes\SalesUp\SalesupHandler;
use App\Contact;

class WhatsappJobs extends Job
{
    public string $type;
    public array $options;
    public array $phones;
    public array $phonesJson;

    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @param string $type
     * @param array $options
     * @param array $phones
     * @param string $phonesJson
     */
    public function __construct(string $type, array $options, array $phones, string $phonesJson)
    {
        $this->type = $type;
        $this->options = $options;
        $this->phones = $phones;
        $this->phonesJson = json_decode($phonesJson, true);
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

        if (in_array($this->type, [Sender::OS_TYPE])) {
            foreach ($this->phones as $phone) {
                if (!isset($this->phonesJson[$phone]) || !isset($this->phonesJson[$phone]['relation'])) {
                    continue;
                }

                if ($this->phonesJson[$phone]['relation']['type'] != 'contacts') {
                    continue;
                }

                $relation = $this->phonesJson[$phone]['relation'];

                $dataAttributes = [
                    'customs' => [
                        Contact::SENDING_MESSAGE_FIELD => Contact::SENDING_WHATSAPP,
                    ]
                ];

                $methods->contactGeneralUpdate($dataAttributes, $relation['id']);
            }
        }
    }
}
