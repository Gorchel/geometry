<?php

namespace App\Classes\Google;

use Google\Client;
use Google\Service\Drive\DriveFile;
use Google\Service\Sheets;
use Google\Service\Drive;
use Google\Service\Drive\Permission;
use Google\Service\Sheets\SheetProperties;
use Google\Service\Sheets\Spreadsheet;
use Google\Service\Sheets\UpdateSheetPropertiesRequest;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;
use Google\Service\Sheets\Request;
use Google\Service\Sheets\CopySheetToAnotherSpreadsheetRequest;


/**
 * Class ApiClient
 * @package App\Classes\Google
 */
class ApiClient
{
    /**
     * Returns an authorized API client.
     * @return Client the authorized client object
     * @throws \Google\Exception
     */
    public function getClient()
    {
        //example two
        $client = new Client();
        $client->setApplicationName("Client_Library_Examples");
        $client->setDeveloperKey("AIzaSyAcEh7O1Qi2sXJFTEQ3SPNME3x68-lKr50");

        return $client;
    }

    /**
     * @return Client
     * @throws \Google\Exception
     */
    public function getOAuthClient()
    {
        $client = new Client();
        $client->addScope([Sheets::SPREADSHEETS, Drive::DRIVE_FILE]);
        $client->setAuthConfig(app()->storagePath('google/credentials.json'));

        return $client;
    }

    /**
     * @param Client $client
     * @param Spreadsheet $spreadsheet
     * @return Spreadsheet
     */
    public function createSpreadsheet(Client $client, Spreadsheet $spreadsheet)
    {
        $service = new Sheets($client);
        return $service->spreadsheets->create($spreadsheet);
    }

    /**
     * @param Client $client
     * @param string $name
     * @return DriveFile
     */
    public function createDriveSpreadsheet(Client $client, string $name)
    {
        $driveFile = new DriveFile();
        $driveFile->mimeType = 'application/vnd.google-apps.spreadsheet';
        $driveFile->name = $name;

        $drive = new Drive($client);
        return $drive->files->create($driveFile);
    }

    /**
     * @param Client $client
     * @param string $fileId
     * @param string $email
     * @return Permission
     */
    public function setPermissionsDrive(Client $client, string $fileId, string $email)
    {
        $permission = new Permission();
        $permission->setEmailAddress($email);
        $permission->setRole(config('google_api')['role']);
        $permission->setType('user');

        $drive = new Drive($client);
        return $drive->permissions->create($fileId, $permission);
    }

    /**
     * @param Client $client
     * @param string $templateSpreadsheetId
     * @param int $templateSheetId
     * @param string $destinationSpreadsheetId
     * @return mixed
     */
    public function copySheet(Client $client, string $templateSpreadsheetId, int $templateSheetId, string $destinationSpreadsheetId)
    {
        $copyRequest = new CopySheetToAnotherSpreadsheetRequest();
        $copyRequest->setDestinationSpreadsheetId($destinationSpreadsheetId);

        $service = new Sheets($client);
        return $service->spreadsheets_sheets->copyTo($templateSpreadsheetId, $templateSheetId, $copyRequest);
    }

    /**
     * @param Client $client
     * @param string $spreadsheetId
     * @return mixed
     */
    public function getSpreadsheet(Client $client, string $spreadsheetId)
    {
        $service = new Sheets($client);
        return $service->spreadsheets->get($spreadsheetId);
    }

    /**
     * @param Client $client
     * @param string $spreadsheetId
     * @param int $sheetId
     * @param string $title
     * @return mixed
     */
    public function updateSheetTitle(Client $client, string $spreadsheetId, int $sheetId, string $title)
    {
        $sheetProperty = new SheetProperties();
        $sheetProperty->setSheetId($sheetId);
        $sheetProperty->setTitle($title);

        $updateRequest = new UpdateSheetPropertiesRequest();
        $updateRequest->fields = 'title';
        $updateRequest->setProperties($sheetProperty);

        $request = new Request();
        $request->setUpdateSheetProperties($updateRequest);

        $batchUpdate = new BatchUpdateSpreadsheetRequest();
        $batchUpdate->setRequests([$request]);

        $service = new Sheets($client);
        return $service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdate);
    }

    /**
     * @param Client $client
     * @param string $spreadsheetId
     * @param string $range
     * @return array[]
     */
    public function getValues(Client $client, string $spreadsheetId, string $range)
    {
        $service = new Sheets($client);
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        return $response->getValues();
    }

    /**
     * @param Client $client
     * @param string $spreadsheetId
     * @param string $range
     * @param array $values
     * @return ValueRange
     */
    public function setValues(Client $client, string $spreadsheetId, string $range, array $values)
    {
        $service = new Sheets($client);

        $options = array('valueInputOption' => 'RAW');

        $body = new ValueRange([
            'values' => $values
        ]);

        $service->spreadsheets_values->update($spreadsheetId, $range, $body, $options);

        return $service->spreadsheets_values->get($spreadsheetId, $range);
    }
}

