<?php
    namespace OrderAlertify\Tools;

    class TelegramBot{
        
        const API_URL = 'https://api.telegram.org/bot';
        public $token;

        public function __constructor($token){
            $this->token = $token;
        }

        public function sendMessage($message, $chat_id){
            $ch = curl_init();

            $url = self::API_URL .$this->token. '/sendMessage';

            $message = wp_json_encode($message);

            $options = [
                'body' => $message,
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ];

            $response = wp_remote_post($url,(object)$options);

            $response =  json_decode( wp_remote_retrieve_body( $response ), true);
        }
    }
?>