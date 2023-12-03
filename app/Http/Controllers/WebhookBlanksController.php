<?php

namespace App\Http\Controllers;

use App\Classes\Documents\Sender;
use App\Helpers\CustomHelper;
use App\Properties;
use App\Helpers\TelegramBot;
use App\SenderLinks;
use Illuminate\Http\Request;
use App\Classes\SalesUp\SalesupHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use FontLib\Font;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use App\Classes\Blanks\BlanksFactory;
use Carbon\Carbon;
use App\Helpers\KPHelper;

/**
 * Class WebhookBlanksController
 * @package App\Http\Controllers
 */
class WebhookBlanksController extends Controller
{

    /**
     *
     */
    public const SCENARIO_DEFAULT = 'default';
    /**
     *
     */
    public const SCENARIO_DOWNLOAD = 'download';


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function generate(Request $request)
    {
        header("Referer: nowhere");

        $id = null;

        if ($request->has('ids')) {
            $id = $request->get('ids')[0];
        } elseif ($request->has('id')) {
            $id = $request->get('id');
        }

        $data = [
            'list' => KPHelper::getList()
        ];

        if (!empty($id)) {
            $data['id'] = $id;

            $handler = new SalesupHandler(env('API_TOKEN'));
            $methods = $handler->methods;
            $object = $methods->getObject($data['id']);

            if (empty($object)) {
                throw new NotFoundHttpException();
            }

            $links = [];

            foreach (KPHelper::getList() as $type => $value) {
                $linkModel = SenderLinks::generateLink($data['id'], $type, $object, $value['name']);

                if (empty($linkModel)) {
                    continue;
                }

                $links[$value['name']] = ['link' => $value['url'].'kp/'.$linkModel->link, 'template' => $type];
            }

            $data['links'] = $links;
        }

        return view('blanks.generate', $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pdfGenerateCheck(Request $request)
    {
        header("Referer: nowhere");
        return view('blanks.generateCheck', ['request' => $request->all()]);
    }

    /**
     * @param $link
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTemplateByName($link)
    {
        $linkModel = SenderLinks::findLinkByName(urldecode($link));

        if (empty($linkModel)) {
            throw new NotFoundHttpException();
        }

        return $this->getTemplate($linkModel->property_id, $linkModel->type);
    }

    /**
     * @param $id
     * @param $template
     * @param string $scenario
     * @param bool $desktop
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTemplate($id, $template, $scenario = self::SCENARIO_DEFAULT, bool $desktop = false)
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;
        $object = $methods->getObject($id);

        if (empty($object)) {
            throw new NotFoundHttpException();
        }

        $config = KPHelper::getList()[$template];

        if (empty($config)) {
            throw new NotFoundHttpException();
        }

        $params = [];

        if (isset($config['params'])) {
            $params = $config['params'];
        }

        $blanksModel = new $config['class']($object, $params);

        $blanksFactory = new BlanksFactory($blanksModel);
        $renderConfig = $blanksFactory->getConfig();
        $renderFooterConfig = $blanksFactory->getFooterConfig();
        $renderImgConfig = $blanksFactory->getImgConfig();

        $data = [
            'object' => $object,
            'renderConfig' => $renderConfig,
            'renderFooterConfig' => $renderFooterConfig,
            'renderImgConfig' => $renderImgConfig,
            'id' => $id,
            'template' => $template,
            'scenario' => $scenario,
            'templateParams' => $params,
            'desktop' => $desktop,
        ];

        return view('blanks.templates.'.$template, $data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Throwable
     */
    public function pdfSend(Request $request)
    {
        $id = $request->get('id');

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;
        $object = $methods->getObject($id);

        $template = $request->get('template');

        if (empty($object) || empty($template)) {
            throw new NotFoundHttpException();
        }

        if (!isset(KPHelper::getList()[$template])) {
            throw new Exception('Wrong template '.$template);
        }

        $name = KPHelper::getList()[$template]['name'];

        $linkModel = SenderLinks::generateLink($id, $request->has('template'), $object, $name);

        $params = [
            'fileName' => $linkModel->link,
            'filePath' => env('APP_URL').'kp/',
            'createdAt' => Carbon::parse($linkModel->created_at)->format('Y-m-d H:i:s'),
        ];

        $params['ids'][] = $request->get('id');

        $url = '/weebhook_documents/'.Sender::CUSTOM_OS_TYPE.'?'.http_build_query($params);

        return redirect()->to($url);
    }

    /**
     * @param Request $request
     * @throws \FontLib\Exception\FontNotFoundException
     * @throws Exception
     * @throws \Throwable
     */
    public function pdfDownload(Request $request)
    {
        $storePdf = $this->storePdf($request, true);

        $url = $storePdf['filePath'].'/'.$storePdf['fileName'];
//        $url = 'https://geominvest.ru/blanks/pdf/дом:_СПб_поселок_Левашово_Новоселки_дом_80_корпус_2_литера_А;_зем_уч_:_СПб_поселок_Левашово_Новоселки_уч_80_А_58_3_Продажа.pdf';
        TelegramBot::sendDocument($url, $storePdf['fileName'], env('TELEGRAM_CHAT_ID'));

        return $storePdf;
    }

    /**
     * @param Request $request
     * @throws Exception
     * @throws \Throwable
     */
    public function pdfAttach(Request $request)
    {
        $storePdf = $this->storePdf($request);

//        $fileName = 'buy_geometry_1653921732.pdf';
//        $filePath = app()->basePath('public/blanks/pdf');

        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $response = $methods->downloadFile($storePdf['fileName'], $storePdf['filePath'].'/'.$storePdf['fileName'], 'estate-properties', $request->get('id'));

        return $response;
    }

    /**
     * @param Request $request
     * @param bool $lightName
     * @return array
     * @throws \Throwable
     */
    protected function storePdf(Request $request, $lightName = false)
    {
        $html = $this->generateHtml($request, true);

        $options = new Options();
        $options->setDefaultFont('DejaVu Sans');
//        $options->set('isHtml5ParserEnabled', true);
//        $options->set('isRemoteEnabled', true);
        $options->setIsRemoteEnabled(true);

        $contxt = stream_context_create([
            'ssl' => [
                'verify_peer' => FALSE,
                'verify_peer_name' => FALSE,
                'allow_self_signed'=> TRUE
            ]
        ]);

        $propertyName = $this->makeBlankName($request->get('id'), $lightName);

        $fileName = $propertyName.'.pdf';
        $fileShortPath = '/blanks/pdf';
        $filePath = app()->basePath('public'.$fileShortPath);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->setHttpContext($contxt);
        $dompdf->render();

        $file = $filePath.'/'.$fileName;

        if (file_exists($file)) {
            unlink($file);
        }

        file_put_contents($file, $dompdf->output());

        return [
            'fileName' => $fileName,
            'filePath' => $filePath,
            'fileShortPath' => $fileShortPath,
        ];
    }

    /**
     * @param Request $request
     * @param bool $desktop
     * @return array|string
     * @throws \Throwable
     */
    protected function generateHtml(Request $request, bool $desktop = false)
    {
        if (!$request->has('template') || !$request->has('id')) {
            throw new NotFoundHttpException();
        }

        $html = $this->getTemplate($request->get('id'), $request->get('template'), self::SCENARIO_DOWNLOAD, $desktop)->render();

        if (empty($html)) {
            throw new Exception('Html not found');
        }

        return $html;
    }

    /**
     * @param Request $request
     * @throws \Throwable
     */
    public function docDownload(Request $request)
    {
        if (!$request->has('template') || !$request->has('id')) {
            throw new NotFoundHttpException();
        }

        $html = $this->getTemplate($request->get('id'), $request->get('template'), '-doc')->render();

        if (empty($html)) {
            throw new Exception('Html not found');
        }

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        Html::addHtml($section, $html, true, false);

        $phpWord->save(app()->basePath('public/blanks/docs/')."html-to-doc.docx", "Word2007");
        dd($phpWord);
//        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($pw, 'Word2007');
    }

    /**
     * @param int $id
     * @param bool $lightName
     * @return string
     */
    protected function makeBlankName(int $id, bool $lightName = false): string
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $object = $methods->getObject($id);

        if (empty($object)) {
            throw new NotFoundHttpException();
        }

        $attribute = $object['attributes'];

        if (!empty($lightName)) {
            $type = CustomHelper::issetField($attribute['customs'], Properties::CUSTOM_TYPE, []);
            $name = '';

            if (!empty($type)) {
                $name = $type[0];
            }

            $name .= "КП ".$attribute['address'].' ('.$attribute['total-area'].' кв.)';
        } else {
            $name = $attribute['address'].'_'.$attribute['total-area'];

            $type = CustomHelper::issetField($attribute['customs'], Properties::CUSTOM_TYPE, []);

            if (!empty($type)) {
                $name .= '_'.$type[0];
            }
        }

        $name = str_replace(' ','', trim($name));
        $name = str_replace(' ','', trim($name));
        $name = str_replace('/','', trim($name));
        $name = str_replace('.','', trim($name));
        $name = str_replace(',','', trim($name));
        $name = str_replace('__','', trim($name));

        return $name;
    }
}
