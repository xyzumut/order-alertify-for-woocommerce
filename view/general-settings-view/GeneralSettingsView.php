<?php 
namespace OrderAlertifyView;


final class GeneralSettingsView{

    public function __construct(){
        $this->loadJsAndCSS();
    }

    public function loadJsAndCSS(){
        wp_enqueue_script( 'orderAlertifyScript', plugin_dir_url(__FILE__).'js/orderAlertifyScript.js', array(), '', true);
        wp_localize_script( 'orderAlertifyScript', 'orderAlertifyScript', [] );
        wp_enqueue_style( 'orderNotificationStyle', plugin_dir_url(__FILE__).'css/orderAlertifyStyle.css');
    }

    public function render(){
        include_once (__DIR__.'/partials/index.php');
    }   

}


?>