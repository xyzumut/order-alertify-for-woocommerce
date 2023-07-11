<?php

    class TelegramBot{
        
        const API_URL = 'https://api.telegram.org/bot';
        const TELEGRAM_TOKEN = '5892830677:AAEpiDSns66y7Vvo8rtooYrozvhcWFKNJZ8';
        public $chat_id;


        public function request($method, $posts){
            $ch = curl_init();
            $url = self::API_URL .TelegramBot::TELEGRAM_TOKEN . '/' . $method;

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded',
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($posts));
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }

        public function getData(){
            $data = json_decode(file_get_contents('php://input'));
            $this->chat_id = $data->message->chat->id;
            return $data->message;
        }
        public function sendMessage($message){
            return $this->request('sendMessage', ['chat_id' => $this->chat_id, 'text' => $message]);
        }
    }

    // $telegram = new TelegramBot();
	/*
    $data = $telegram->getData();

    if ($data->text == 'Selam') {
        $telegram->sendMessage('Cevap');
    }
	*/
?>