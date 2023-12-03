<?php

namespace App\Http\Controllers;

use App\Classes\Documents\KPSender;
use App\Classes\Documents\KPSaleSender;
use App\Classes\Documents\CustomKPSender;
use App\Classes\Documents\OSSender;
use App\Classes\Documents\PassedSender;
use App\Classes\Documents\Sender;
use App\Helpers\TelegramBot;
use App\Helpers\CustomHelper;
use App\Helpers\DirHelper;
use App\Properties;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Classes\SalesUp\SalesupHandler;
use App\Classes\UnisenderApi;
use App\Jobs\DocumentJobs;

/**
 * Class WebhookDocumentsController
 * @package App\Http\Controllers
 */
class WebhookDocumentsController extends Controller
{
    /**
     *
     */
    const LIST_ID = 104;
    /**
     *
     */
    const TEMPLATE_ID = 3741347;


    /**
     * @param Request $request
     * @param int $sendType
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function webhook(Request $request, int $sendType = Sender::KP_TYPE)
    {
        Log::info(json_encode($request->all()));

        if ($sendType == Sender::KP_TYPE) {
            $senderClass = new KPSender();
        } elseif($sendType == Sender::OS_TYPE) {
            $senderClass = new OSSender();
        } elseif($sendType == Sender::CUSTOM_OS_TYPE) {
            $senderClass = new CustomKPSender();
        } elseif($sendType == Sender::KP_SALE_TYPE) {
            $senderClass = new KPSaleSender();
        } elseif($sendType == Sender::PASSED_TYPE) {
            $senderClass = new PassedSender();
        } else {
            $msg = "Тип рассылки неопределен";
            return view('documents.error_page', [
                'msg' => $msg,
                'request' => $this->prepareRequest($request)
            ]);
        }

        $sender = new Sender($senderClass);
        $params = $sender->startSending($request);

        if (empty($params['status'])) {
            return view('documents.error_page', [
                'msg' => $params['msg'],
                'request' => $this->prepareRequest($request)
            ]);
        }

        return view('documents.form', $params);
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws \Throwable
     */
    public function webhook_send(Request $request) {
        if (empty($request->get('emails'))) {
            return view('documents.error_page', [
                'msg' => 'Email отсутствуют',
                'request' => $this->prepareRequest($request)
            ]);
        }

        $sendType = $request->get('sendType');
        $emails = $request->get('emails');
        $emailsJson = $request->get('email_json');

        if (in_array($sendType, [Sender::KP_TYPE, Sender::CUSTOM_OS_TYPE, Sender::KP_SALE_TYPE])) {
            if (empty($request->get('documents')) && empty($request->get('customDocuments'))) {
                return view('documents.error_page', [
                    'msg' => 'Документы отсутствуют',
                    'request' => $this->prepareRequest($request)
                ]);
            }
        }

        if (count($emails) >= 500) {
            return view('documents.error_page', [
                'msg' => 'Рассылка не может превышать 500 контактов',
                'request' => $this->prepareRequest($request)
            ]);
        }

        if ($request->has('test_email') && !empty($request->get('test_email'))) {
            $emails = explode(',', $request->get('test_email'));
        }

        $token = env('API_TOKEN');
        $handler = new SalesupHandler($token);

        $methods = $handler->methods;

        $object = Properties::where('id', '=', $request->get('objectId'))
            ->first()->toArray();

        $objectAttributes = json_decode($object['attributes'], true);
        $objectCustoms = json_decode($object['customs'], true);

        if (in_array($sendType,[Sender::KP_TYPE, Sender::CUSTOM_OS_TYPE, Sender::KP_SALE_TYPE])) {
            $images = $methods->getPropertyImages($request->get('objectId'));
            $photos = [];

            $coverLink = null;

            if (!empty($images['data'])) {
                $mainPhoto = explode(':', CustomHelper::issetField($objectCustoms, 'custom-65702'));

                foreach ($images['data'] as $photo) {
                    if (isset($mainPhoto[1]) && $mainPhoto[1] == $photo['attributes']['name']) {
                        $coverLink = $photo['attributes']['download-link'];
                    } else {
                        $photos[] = $photo['attributes']['download-link'];
                    }
                }
            }

            $resultDescription = $request->get('body_body');

            $attachments = [];

            if ($request->has('customDocuments') && !empty($request->get('customDocuments'))) {
                $filename = str_replace(env('APP_URL').'kp/', '', $request->get('customDocuments'));

                $linkHref = "<a target='_blank' href='".$request->get('customDocuments')."'>".$filename."</a>";

                $resultDescription = str_replace('{$kplink}', $linkHref ,$resultDescription);

                $attachments[$filename] = $request->get('customDocuments');
            } elseif ($request->has('documents') && !empty($request->get('documents'))) {
                $documentArr = json_decode($request->get('documents'), true);

                if (json_last_error() != JSON_ERROR_NONE) {
                    return view('documents.error_page', [
                        'msg' => 'CustomDocuments json error',
                        'request' => $this->prepareRequest($request)
                    ]);
                }

                if ($documentArr['type'] == 1) {
                    $document = $methods->getDocument($documentArr['id']);

                    if (empty($document)) {
                        return view('documents.error_page', [
                            'msg' => 'getDocument error',
                            'request' => $this->prepareRequest($request)
                        ]);
                    }

                    $filePath = $methods->readDocumentPdf($documentArr['id']);
                    $fileName = $document['attributes']['name'] . '.pdf';
                } else {
                    $document = $methods->getMainDocument($documentArr['id']);

                    if (empty($document)) {
                        return view('documents.error_page', [
                            'msg' => 'getMainDocument error',
                            'request' => $this->prepareRequest($request)
                        ]);
                    }

                    $link = $document['attributes']['download-link'];

                    $fileName = $document['attributes']['name'];
                    $filePath = 'documents/' . $document['attributes']['name'];

                    file_put_contents($filePath, file_get_contents($link));
                }

                $downloadTitle = '';

                if ($request->get('send_email') == 'info@geometry-invest.ru') {
                    $filePath = env('APP_URL') . $filePath;
                } else {
                    $filePath = env('APP_URL_KONTINENT') . $filePath;
                }

                $attachments[$fileName] = $filePath;
            }

            if ($request->get('type') == Properties::RENT_TYPE) {
                $type = 'Аренда';
            } else {
                $type = 'Продажа';
            }

            //Ответственный
            $responsibility = [
                'name' => CustomHelper::issetFieldIncludeArray($objectCustoms, 'custom-84553', ''),
                'phone' => CustomHelper::issetFieldIncludeArray($objectCustoms, 'custom-84554', ''),
            ];

            $params = [
                'objectAddress' => $request->get('header_body'),
                'resultDescription' => $resultDescription,
                'photos' => array_slice($photos, 0, 2),
                'attachments' => $attachments,
                'coverLink' => $coverLink,
                'responsibility' => $responsibility,
                'type' => $type,
            ];

            if ($request->get('send_email') == 'info@geometry-invest.ru') {
                $mail = view('templates.geometry_mail', $params)->render();
            } else {
                $mail = view('templates.kontinent_mail', $params)->render();
            }

            $messageParams = [
                'type' => $type,
                'send_email' => $request->get('send_email'),
                'subject' => $request->get('subject'),
            ];

            $sendResponse = $this->send($emails, $mail, $messageParams);

            if (empty($sendResponse['status'])) {
                return view(
                    'documents.error_page',
                    [
                        'msg' => $sendResponse['text'],
                        'request' => $this->prepareRequest($request)
                    ]
                );
            }

            $result = $sendResponse['result'];
        } else {
            if ($request->get('type') == Properties::RENT_TYPE) {
                $type = 'Аренда';
            } else {
                $type = 'Продажа';
            }

            $messageParams = [
                'type' => $type,
                'send_email' => $request->get('send_email'),
                'subject' => $request->get('subject'),
            ];

            $sendResponse = $this->send($emails, $request->get('body_body'), $messageParams);

            if (empty($sendResponse['status'])) {
                return view(
                    'documents.error_page',
                    [
                        'msg' => $sendResponse['text'],
                        'request' => $this->prepareRequest($request)
                    ]
                );
            }

            $result = $sendResponse['result'];
        }

        if (isset($result['result']['status'])) {
            $options = [
                'id' => $request->get('id'),
                'objectId' => $request->get('objectId'),
                'type' => $request->get('type'),
                'customDocuments' => $request->get('customDocuments'),
            ];

            dispatch(new DocumentJobs($sendType, $options, $emails, $emailsJson));
        }

        return view('documents.success', ['result' => $result]);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function prepareRequest($request)
    {
        return [
            'token' => $request->get('token'),
            'ids' => [$request->get('id')],
        ];
    }

    /**
     * @param $email
     * @return bool
     */
    public function checkEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $filename
     */
    public function attach(string $filename)
    {
        return view('documents.download', ['filename' => $filename]);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function attachPost(Request $request)
    {
        return response()->download('documents/'.$request->get('filename').'.pdf');
    }

        /**
     * @param $type
     * @param $emails
     * @param $mail
     * @param $request
     * @return array
     */
    private function send($emails, $template, $messageParams)
    {
        $unisender = new UnisenderApi(env('UNISENDER_TOKEN'));

        $type = 'geometry';

        if (isset($params['type'])) {
            $type = $messageParams['type'];
        }

        $params = [
            'title' => $type.'_'.date('Y-m-d-H:i:s'),
        ];

        $result = $unisender->createList($params);
        $listResult = json_decode($result ,true);

        if (!isset($listResult['result']['id'])) {
            return ['status' => false, 'text' => 'Не создан список контактов. '.$result];
        }

        $listId = $listResult['result']['id'];
        $emailsArray = [];

        foreach ($emails as $email) {
            $emailsArray[] = [$email, $listId];
        }

        $params = [
            'field_names' => ['email', 'email_list_ids'],
            'data' => $emailsArray
        ];

        $unisender->importContacts($params);

        $params = [
            'sender_name' => isset($messageParams['send_email']) ? $messageParams['send_email'] : '',
            'sender_email' => isset($messageParams['send_email']) ? $messageParams['send_email'] :  '',
            'list_id' => $listId,
            'body' => $template,
            'subject' => isset($messageParams['subject']) ? $messageParams['subject'] : '',
        ];

        //{"result":{"message_id":152944717}
        $createMessageResult = json_decode($unisender->createEmailMessage($params), true);

        if (!isset($createMessageResult['result']['message_id'])) {
            return ['status' => false, 'text' => 'Ошибка создания письма '.$createMessageResult['code']];
        }

        $params = [
            'message_id' => $createMessageResult['result']['message_id'],
//            'contacts' => implode(',', $emails)
        ];

        $resultJson = $unisender->createCampaign($params);

        $result = json_decode($resultJson, true);

        return ['status' => true, 'result' => $result];
    }

    /**
     * @param Request $request
     * @return void
     * @throws Exception
     */
    public function download(Request $request)
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $id = $request->get('ids')[0];
        $object = $methods->getObject($id);

        if (empty($object)) {
            throw new Exception('Object not found');
        }

        if (!isset($object['relationships']['documents']['data'])) {
            throw new Exception('Documents not found');
        }

        $tmpDir = storage_path('tmpFiles');

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir);
        }

        DirHelper::recursiveClearDir($tmpDir);

        foreach ($object['relationships']['documents']['data'] as $document) {
            try {
                $documentObj = $methods->getMainDocument($document['id']);
                $url = $documentObj['attributes']['download-link'];
                $name = $documentObj['attributes']['name'];

                file_put_contents($tmpDir.'/'.$name, file_get_contents($url));
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        $zipName = 'archive.tar.gz';
        $zipDir = $tmpDir.'/'.$zipName;

        exec("tar -czvf ".$zipDir." -C ".$tmpDir."/ . --exclude={'*/archive.tar.gz'}");

        TelegramBot::sendDocument($zipDir, $zipName, env('TELEGRAM_CHAT_ID'));
    }
}
