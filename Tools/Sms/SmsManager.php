<?php
    namespace OrderAlertify\Tools;

    class SmsManager{
        
        public $token;
        public $url;
        public $refreshUrl;

        public function __construct($token, $url, $refreshUrl){
            $this->token = $token;
            $this->url = $url;
            $this->refreshUrl = $refreshUrl;
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

            $response = wp_remote_post($this->url, (object)$requestOptions);

            $response =  json_decode( wp_remote_retrieve_body( $response ), true);
                

            if ($response['Result']['message'] === 'Unauthorized User') {
                
                $refreshResponse = $this->refreshToken();

                if ($refreshResponse['status'] === true) {
                    return $this->sendSMS($message, $target);
                }

                return ['apiResponse' => 'null', 'apiMessage' => $response['Result']['message'], 'target' => $target];
            }

            return  ['apiResponse' => $response, 'apiMessage' => $message, 'target' => $target];
        }

        public function refreshToken() {

            $username = get_option('smsLoginUsername', false);
            $password = get_option('smsLoginPassword', false);


            if ($username === false || $password === false) {
                return;
            }

            $requestOptions = array(
                'method' => 'POST',
                'headers' => array(
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ),
                'body' => wp_json_encode(array(
                    'username' => $username,
                    'password' => $password
                )),
            );

            $response = wp_remote_post($this->refreshUrl, (object)$requestOptions);

            $response =  json_decode( wp_remote_retrieve_body( $response ), true);

            $return = array(
                'status' => false,
                'token' => null,
            );

            if (isset($response['JwtToken'])){
                update_option('smsJwt', $response['JwtToken']);
                $this->token = $response['JwtToken'];
                $return['status'] = true;
                $return['token'] = $response['JwtToken'];
                return $return;
            }

            return $return;
        }
    }
?>