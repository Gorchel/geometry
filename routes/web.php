<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Classes\SalesUp\SalesupHandler;
use PHPHtmlParser\Dom;

$router->get('/', function () use ($router) {
    return app()->version();
});

$router->get('help', function () use ($router) {
    $handler = new SalesupHandler(env('API_TOKEN'));
    $methods = $handler->methods;

    dd($methods->getObject(338795));
});

$router->get('/test', ['uses' => 'TestController@index']);
$router->get('/getLogs', ['uses' => 'WebhookController@getLogs']);
$router->get('/webhook', ['uses' => 'WebhookController@webhook']);
$router->get('/webhook_objects', ['uses' => 'WebhookController@webhookObjects']);
$router->post('/webhook_objects', ['uses' => 'WebhookController@webhookPostObjects']);
$router->get('/webhook_objects_get_bst_statistic', ['uses' => 'WebhookObjectsController@getBstStatistic']);
$router->get('/webhook_objects_update_bst_statistic', ['uses' => 'WebhookObjectsController@updateBstStatistic']);

$router->post('/webhook_sheet_scenario', ['uses' => 'WebhookSheetController@updateSheetScenario']);
$router->get('/webhook_sheet_population', ['uses' => 'WebhookSheetController@updatePopulation']);

$router->get('/copy_property', ['uses' => 'PropertyCopyController@index']);
$router->post('/copy_property', ['uses' => 'PropertyCopyController@copyProperty']);

Route::get('/webhook/blanks', 'WebhookBlanksController@generate');
$router->get('/webhook/blanks/{id}/{template}', ['uses' => 'WebhookBlanksController@getTemplate']);
$router->get('/kp/{link}', ['uses' => 'WebhookBlanksController@getTemplateByName']);
$router->post('/webhook/blanks-downloads/pdf', ['uses' => 'WebhookBlanksController@pdfDownload']);
$router->get('/webhook/blanks/generate-check', ['uses' => 'WebhookBlanksController@pdfGenerateCheck']);
$router->post('/webhook/blanks-attach/pdf', ['uses' => 'WebhookBlanksController@pdfAttach']);
$router->post('/webhook/blanks-send/pdf', ['uses' => 'WebhookBlanksController@pdfSend']);
$router->post('/webhook/blanks-downloads/doc', ['uses' => 'WebhookBlanksController@docDownload']);

$router->get('/webhook_deals_objects', ['uses' => 'WebhookObjectsController@webhookUpdateObjectsContacts']);
$router->get('/weebhook_estate_filter', ['uses' => 'WebhookObjectsController@webhookEstateFilter']);
$router->get('/weebhook_estate_get', ['uses' => 'WebhookObjectsController@webhookEstateGet']);

$router->get('/copy', ['uses' => 'WebhookController@copyContactsView']);

$router->get('/weebhook_orders_filter', ['uses' => 'WebhookOrdersController@webhookOrdersFilter']);
$router->get('/weebhook_orders_get', ['uses' => 'WebhookOrdersController@webhookOrdersGet']);

$router->get('/weebhook_documents/{sendType}', ['uses' => 'WebhookDocumentsController@webhook']);
$router->post('/weebhook_documents_send', ['uses' => 'WebhookDocumentsController@webhook_send']);
$router->get('/weebhook_documents_attach/{filename}', ['uses' => 'WebhookDocumentsController@attach']);
$router->post('/weebhook_documents_attach', ['uses' => 'WebhookDocumentsController@attachPost']);

$router->get('/weebhook_whatsapp/{sendType}', ['uses' => 'WebhookWaController@webhook']);
$router->post('/weebhook_whatsapp_send', ['uses' => 'WebhookWaController@webhook_send']);

$router->get('/documents/feedback', ['uses' => 'WebhookDocumentsController@feedback']);

$router->get('/documents/passed', ['uses' => 'WebhookDocumentsController@passed']);
$router->get('/documents/download', ['uses' => 'WebhookDocumentsController@download']);

$router->get('/statistic_update_google', ['uses' => 'StatisticController@updateGoogleStatistic']);

$router->get('/update/{type}', ['uses' => 'MainController@updateServer']);

$router->get('/bst', ['uses' => 'BstController@index']);
$router->get('/bst/list', ['uses' => 'BstController@getList']);
$router->get('/bst/property_list', ['uses' => 'BstController@getPropertiesList']);
