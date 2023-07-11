<?php

namespace OrderAlertifyView;


class TelegramSettingsView{
    
    public function __construct(){
        $this->loadJsAndCSS();
    }

    public function loadJsAndCSS(){
        wp_enqueue_script( 'telegramSettingsScript', plugin_dir_url(__FILE__).'js/TelegramSettings.js', array(), '', true);
        wp_localize_script( 'telegramSettingsScript', 'telegramSettingsScript', [] );
        wp_enqueue_style( 'telegramSettingsStyle', plugin_dir_url(__FILE__).'css/telegramSettingsStyle.css');
    }

    public function render(){
        include_once (__DIR__.'/partials/index.php');
    }

}



?>