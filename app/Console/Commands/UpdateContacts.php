<?php
namespace App\Console\Commands;

use App\Classes\SalesUp\SalesupHandler;
use App\Contact;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Command;

/**
 * Class UpdateContacts
 * @package App\Console\Commands
 */
class UpdateContacts extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновляет кннтакты';

    public const TEST_CUSTOM_FIELD = 'custom-88119';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
//        \Log::info('run '.$this->argument('type').' '.$this->argument('dayUpdated'));
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $customs[static::TEST_CUSTOM_FIELD] = 1;

        $attributes = [
            'customs' => $customs,
        ];

        $contactsQuery = Contact::where('is_update', 0);

        $contactsCountQuery = clone $contactsQuery;

        echo "Found ".$contactsCountQuery->count()."\n\r";

        $contacts = $contactsQuery
            ->limit(100)
            ->get();

        foreach ($contacts as $contact) {
            $response = $methods->contactGeneralUpdate($attributes, $contact->id);

            if (!isset($response['id'])) {
                Log::info($contact->id.' update error. '.json_encode($response));
                echo $contact->id.' update error. '.json_encode($response);
            } else {
                echo $contact->id." is updated\n\r";
            }
            
            $contact->is_update = 1;
            $contact->save();
        }
    }
}
