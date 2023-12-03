<?php

namespace App\Classes\Blanks;

use App\Classes\SalesUp\SalesupHandler;
use Throwable;

/**
 * Class RentBlanks
 * @package App\Classes\Blanks;
 */
class BlanksFactory
{
    /**
     * @var BlanksInterface
     */
    public $blank;

    /**
     * BlanksFactory constructor.
     * @param BlanksInterface $blank
     */
    public function __construct(BlanksInterface $blank)
    {
        $this->blank = $blank;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->blank->config();
    }

    /**
     * @return array
     */
    public function getFooterConfig(): array
    {
        return $this->blank->footerConfig();
    }

    /**
     * @param array $preparedImages
     * @return array
     */
    public function getImgConfig(): array
    {
        $imageData = $this->blank->imgConfig();

        return $this->prepareImages($imageData);
    }

    /**
     * @param array $imageData
     * @return array
     */
    protected function prepareImages($imageData = [])
    {
        $handler = new SalesupHandler(env('API_TOKEN'));
        $methods = $handler->methods;

        $images = [];

        foreach ($imageData as $key => $data) {
            $dataArr = explode(':', $data['value']);

            if (isset($dataArr[0])) {
                try {
                    $document = $methods->getMainDocument((int) $dataArr[0]);

                    if (isset($document['attributes'])) {
                        $images[$key] = $data;
                        $images[$key]['value'] = $document['attributes']['download-link'];
                    }
                } catch (Throwable $e) {

                }
            }
        }

        return $images;
    }
}
