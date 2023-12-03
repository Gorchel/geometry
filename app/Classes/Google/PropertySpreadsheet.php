<?php

namespace App\Classes\Google;

use App\GooglePropertySheet;
use App\GoogleSpreadsheet;
use App\Properties;
use Exception;

/**
 * Class PropertySpreadsheet
 * @package App\Classes\Google
 */
class PropertySpreadsheet
{
    const SHEET_LIMIT = 50;
    const MAX_TITLE_NAME = 50;
    const DEFAULT_SPREADSHEET_NAME = 'ПРОВЕРКА по категориям (300м коэф 2,7)';


    /**
     * @var Properties $property
     */
    public $property;

    public $client;
    public $apiClient;

    /**
     * PropertySpreadsheet constructor.
     * @param Properties $property
     */
    public function __construct(Properties $property)
    {
        $this->property = $property;
        $this->apiClient = new ApiClient();
        $this->client = $this->apiClient->getOAuthClient();
    }

    /**
     * @return GooglePropertySheet
     * @throws Exception
     */
    public function create(): ?GooglePropertySheet
    {
        $propertySheet = GooglePropertySheet::where('property_id', $this->property->id)
            ->first();

        if (!empty($propertySheet)) {
            return $propertySheet;
        }

        /** @var GoogleSpreadsheet $spreadsheet */
        $spreadsheet = $this->getSpreadsheet();
        return $this->copyTemplateSheet($spreadsheet->id, $spreadsheet->spreadsheet_id);
    }

    /**
     * @param string $spreadsheetId
     * @param int $sheetId
     * @return string
     */
    public static function generateLink(string $spreadsheetId, int $sheetId)
    {
        return 'https://docs.google.com/spreadsheets/d/'.$spreadsheetId.'/edit#gid='.$sheetId;
    }

    /**
     * @return GoogleSpreadsheet
     * @throws Exception
     */
    protected function getSpreadsheet(): GoogleSpreadsheet
    {
        $lastSpreadsheet = GoogleSpreadsheet::orderBy('id', 'DESC')
            ->first();

        $lastId = 0;

        if (!empty($lastSpreadsheet)) {
            $lastId = $lastSpreadsheet->id;

            if ($lastSpreadsheet->sheet_count < static::SHEET_LIMIT) {
                return $lastSpreadsheet;
            }
        }

        try {
            $spreadsheetName = $this->getSpreadsheetName($lastId);

            $googleDriveSpreadsheet = $this->apiClient->createDriveSpreadsheet($this->client, $spreadsheetName);
            $spreadsheetId = $googleDriveSpreadsheet['id'];

            foreach(config('google_api')['emails_permissions_access'] as $email) {
                $this->apiClient->setPermissionsDrive($this->client, $spreadsheetId, $email);
            }

            $spreadsheet = new GoogleSpreadsheet();
            $spreadsheet->spreadsheet_id = $spreadsheetId;
            $spreadsheet->spreadsheet_name = $spreadsheetName;

            if (!$spreadsheet->save()) {
                throw new Exception('App\Classes\Google\PropertySpreadsheet spreadsheet not save');
            }

            $this->copyTemplateCatalog($spreadsheetId);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return $spreadsheet;
    }

    /**
     * @param int $lastId
     * @return string
     */
    protected function getSpreadsheetName(int $lastId): string
    {
        $lastId = $lastId + 1;
        $name = static::DEFAULT_SPREADSHEET_NAME.' '.$lastId;

        $existsSpreadSheet = GoogleSpreadsheet::where('spreadsheet_name', $name)
            ->exists();

        if (!empty($existsSpreadSheet)) {
            return $this->getSpreadsheetName($lastId);
        }

        return $name;
    }

    /**
     * @param int $modelSpreadsheetId id from App\GoogleSpreadsheet
     * @param string $spreadsheetId google spreadsheet id
     * @return GooglePropertySheet
     * @throws Exception
     */
    protected function copyTemplateSheet(int $modelSpreadsheetId, string $spreadsheetId): GooglePropertySheet
    {
        try {
            $templateSpreadsheetId = config('google_api')['template_spreadsheet_id'];
            $templateSheetId = config('google_api')['template_sheet_id'];

            $copyRequest = $this->apiClient->copySheet($this->client, $templateSpreadsheetId, $templateSheetId, $spreadsheetId);

            $title = mb_substr($this->property->full_name, 0, static::MAX_TITLE_NAME);

            $this->apiClient->updateSheetTitle($this->client, $spreadsheetId, $copyRequest->sheetId, $title);

            $propertySheet = new GooglePropertySheet();
            $propertySheet->spreadsheet_id = $modelSpreadsheetId;
            $propertySheet->sheet_id = $copyRequest->sheetId;
            $propertySheet->sheet_name = $title;
            $propertySheet->property_id = $this->property->id;

            if (!$propertySheet->save()) {
                throw new Exception('App\Classes\Google\copyTemplateSheet propertySheet not save');
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return $propertySheet;
    }

    /**
     * Copy template catalog
     *
     * @param string $spreadsheetId google spreadsheet id
     * @return mixed
     * @throws Exception
     */
    protected function copyTemplateCatalog(string $spreadsheetId)
    {
        try {
            $templateSpreadsheetId = config('google_api')['template_spreadsheet_id'];
            $templateCatalogId = config('google_api')['template_catalog_id'];
            $templateCatalogName = config('google_api')['template_catalog_name'];

            $copyRequest = $this->apiClient->copySheet($this->client, $templateSpreadsheetId, $templateCatalogId, $spreadsheetId);

            $this->apiClient->updateSheetTitle($this->client, $spreadsheetId, $copyRequest->sheetId, $templateCatalogName);

        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        return true;
    }
}
