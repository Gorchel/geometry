<?php

namespace App\Jobs;

use App\Classes\SalesUp\SalesupHandler;

class CopyContactNameJobs extends Job
{
    public string $name;
    public array $contacts;
    public int $dealId;

    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @param array $contacts
     * @param int $dealId
     * @param string $name
     */
    public function __construct(array $contacts, int $dealId, string $name)
    {
        $this->contacts = $contacts;
        $this->dealId = $dealId;
        $this->name = $name;
    }

    /**
     * Execute the job.
     *
     * @return bool
     * @throws \Exception
     */
    public function handle()
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        if (empty($this->contacts)) {
            return true;
        }

        foreach ($this->contacts as $id) {
            $dataAttributes = [
                'customs' => [
                    'custom-88454' => $this->name,
                ]
            ];

            $methods->contactGeneralUpdate($dataAttributes, $id);
        }
    }
}
