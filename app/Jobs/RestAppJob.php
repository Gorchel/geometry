<?php

namespace App\Jobs;

use App\Classes\RestApp\Import;

class RestAppJob extends Job
{

    public $objects;
    public $type;

    /**
     * Create a new job instance.
     *
     * @param array $objects
     * @param int $type
     */
    public function __construct(array $objects = [], int $type = Import::RENT_TYPE)
    {
        $this->objects = $objects;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $import = new Import($this->type);
        $import->import($this->objects);
    }
}
