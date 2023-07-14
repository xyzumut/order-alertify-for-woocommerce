<?php
    namespace OrderAlertify\Tools;

    // TODO SmsManager sınıfı İşleme sokulacak
    class SmsManager{
        
        public $token;

        const MESSAGE_HEADER = 'paymendo'; 

        public function __construct($token){
            $this->token = $token;
        }

        public function sendMessage($message, $targetPhoneNumber){


            $body = json_encode(['message' => $message, 'phone' => $targetPhoneNumber, 'header' => SmsManager::MESSAGE_HEADER]);

            $options = [
                'body' => $body,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ]
            ];
            
            $response = wp_remote_post($url, (object)$options);

            $response =  json_decode( wp_remote_retrieve_body( $response ), true);
        }
    }
?>