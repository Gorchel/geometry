<?php

namespace App\Jobs;

use App\Classes\SalesUp\SalesupHandler;

class DealJobs extends Job
{
    public $dealId;
    public $relationships;
    public $properties;

    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @param $dealId
     * @param $relationships
     * @param $properties
     */
    public function __construct($dealId, $relationships, $properties = null)
    {
        $this->dealId = $dealId;
        $this->relationships = $relationships;
        $this->properties = $properties;
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

        $methods->dealDataUpdate($this->dealId, $this->relationships);

        if (!empty($this->properties)) {
            $methods->attachDealsToObject($this->properties['deals'], $this->properties['object']['id']);
        }
    }
}
