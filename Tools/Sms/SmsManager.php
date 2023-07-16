<?php
    namespace OrderAlertify\Tools;

    class SmsManager{
        
        public $token;
        public $url;

        public function __construct($token, $url){
            $this->token = $token;
            $this->url = $url;
        }

        public function sendSMS($message, $target){

            $requestOptions = array(
                'method' => 'POST',
                'headers' => array(
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                ),
                'body' => wp_json_encode(array(
                    'phone' => $target,
                    'message' => $message,
                    'header' => 'paymendo'
                )),
            );

            $response = wp_remote_post($this->url, $requestOptions);

            // if (is_wp_error($response)) {
            //     // İstek hatası oluştu
            //     $error_message = $response->get_error_message();
            //     print_r($error_message);
            //     die;
            // } 
            // else {
            //     $response_code = wp_remote_retrieve_response_code($response);
            //     $response_body = wp_remote_retrieve_body($response);
                
            //     // İstek başarılı, yanıtı işleme
            //     print_r($response_code);
            //     print_r($response_body);
            //     die;
            // }
        }
    }
?>