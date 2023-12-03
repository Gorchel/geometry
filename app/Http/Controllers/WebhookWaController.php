<?php

namespace App\Http\Controllers;

use App\Classes\Whatsapp\OSSender;
use App\Classes\Whatsapp\Sender;
use App\Jobs\WhatsappJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Classes\ChatAPI\API;
use Exception;

/**
 * Class WebhookWaController
 * @package App\Http\Controllers
 */
class WebhookWaController extends Controller
{
    /**
     * @param Request $request
     * @param string $sendType
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function webhook(Request $request, string $sendType = Sender::OS_TYPE)
    {
        Log::info(json_encode($request->all()));

        if ($sendType == Sender::OS_TYPE) {
            $senderClass = new OSSender();
        } else {
            $msg = "Тип рассылки неопределен";
            return view('whatsapp.error_page', [
                'msg' => $msg,
                'request' => $this->prepareRequest($request)
            ]);
        }

        $sender = new Sender($senderClass);
        $params = $sender->startSending($request);

        if (empty($params['status'])) {
            return view('whatsapp.error_page', [
                'msg' => $params['msg'],
                'request' => $this->prepareRequest($request)
            ]);
        }

        return view('whatsapp.form', $params);
    }

    /**
     * @param Request $request
     * @return \Illuminate\View\View
     * @throws \Throwable
     */
    public function webhook_send(Request $request) {
        if (empty($request->get('phones'))) {
            return view('documents.error_page', [
                'msg' => 'Телефоны отсутствуют',
                'request' => $this->prepareRequest($request)
            ]);
        }

        $sendType = $request->get('sendType');

        if ($sendType == Sender::OS_TYPE) {
            $senderClass = new OSSender();
        } else {
            $msg = "Тип рассылки неопределен";
            return view('whatsapp.error_page', [
                'msg' => $msg,
                'request' => $this->prepareRequest($request)
            ]);
        }

        $sender = new Sender($senderClass);
        $params = $sender->finishSending($request);

        if (empty($request->get('kpType'))) {
            return view('documents.error_page', [
                'msg' => 'КП отсутствует',
                'request' => $this->prepareRequest($request)
            ]);
        }

        $kpType = $request->get('kpType');

        $phones = $request->get('phones');
        $phonesJson = $request->get('phones_json');

        if (count($phones) >= 500) {
            return view('documents.error_page', [
                'msg' => 'Рассылка не может превышать 500 контактов',
                'request' => $this->prepareRequest($request)
            ]);
        }

        $body = $params['description'];

        $body = str_replace('{{kplink}}', $kpType, $body);

        if ($request->has('test_phone') && !empty($request->get('test_phone'))) {
            $phones = explode(',', $request->get('test_phone'));
        }

        $result = [];
        $errors = [];

        $chatApi = new API();

        foreach ($phones as $phone) {
            try  {
                $result[] =  $chatApi->sendMessage($body, $phone);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        dispatch(new WhatsappJobs($sendType, [], $phones, $phonesJson));

        return view('whatsapp.success', ['result' => $result, 'errors' => $errors]);
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
}
