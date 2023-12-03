<?php

namespace App\Helpers;

use Telegram\Bot\Exceptions\TelegramSDKException;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

/**
 * Class TelegramBot
 * @package App\Helpers
 */
class TelegramBot
{
    /**
     * get updates
     */
    public static function getUpdates()
    {
        try {
            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            $response = $telegram->getUpdates();

            dd($response);
        } catch (TelegramSDKException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @param $message
     * @param $chat_id
     */
    public static function send($chat_id, $message)
    {
        try {
            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            $telegram->sendMessage(
                [
                    'chat_id' => $chat_id,
                    'text' => $message
                ]
            );
        } catch (TelegramSDKException $e) {
            Log::error($e->getMessage());
        }
    }

    /**
     * @param $filepath
     * @param string $caption
     * @param null $chat_id
     * @return int
     * @throws TelegramSDKException
     */
    public static function sendDocument($filepath, $caption = "Документ от бота", $chat_id = null)
    {
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

        $telegram->sendDocument([
            'chat_id' => $chat_id,
            'document' => InputFile::create($filepath, $caption),
            'caption' => $caption,
        ]);

        return 1;
    }
}
