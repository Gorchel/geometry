<?php

namespace App\Classes\ChatAPI;

/**
 * Class API
 * @package App\Classes\ChatAPI;
 */
class API
{
    /**
     * @var string
     */
    private $url = 'https://api.chat-api.com/';
    /**
     * @var mixed
     */
    private $token;

    /**
     * API constructor.
     */
    public function __construct()
    {
        $this->url = env('CHAT_API_INSTANCE');
        $this->token = env('CHAT_API_TOKEN');
    }

    /**
     * @param string $body
     * @param string $phone
     */
    public function sendMessage(string $body, string $phone)
    {
        $post = [
            'body' => $body,
            'phone' => $phone
        ];

        $ch = curl_init($this->url.'sendMessage'.'?token='.$this->token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, (int)!empty($post));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);

        $curlErrorCode = curl_errno($ch);
        $curlError = curl_error($ch);

        return $result;
    }
}

