<?php 
namespace OrderAlertifyView;

class SmsSettingsView{
    
    public function __construct(){
        $this->loadJsAndCSS();
    }

    public function loadJsAndCSS(){
        wp_enqueue_script( 'smsSettingsScript', plugin_dir_url(__FILE__).'js/SmsSettings.js', array(), '', true);
        wp_localize_script( 'smsSettingsScript', 'smsSettingsScript', $this->returnLocalizeScript() );
        wp_enqueue_style( 'orderNotificationStyle', plugin_dir_url(__FILE__).'css/SmsStyle.css');
    }


    public function returnLocalizeScript(){
        return [
            'definedSmsRules' => $this->prepareSmsRules(),
        ];
    }



    public function prepareSmsRules(){
        if (get_option( 'smsRuleTemp') === false) 
            return [];

        $smsRuleTemp = json_decode(get_option( 'smsRuleTemp'));
        
        $return = array();

        for ($i = 0; $i < $smsRuleTemp -1; $i++){
            $return[$i] = get_option( 'smsRule-'.$i+1 );
        }

        return $return;
    }
    public function render(){
        include_once (__DIR__.'/partials/index.php');
    }
    
}



?>