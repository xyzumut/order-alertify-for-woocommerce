<?php 
namespace OrderAlertifyView;

class MailSettingsView{

    CONST outlookMailOptionName = 'woocommerceOrderNotificationOutlookAddress';
    CONST outlookPasswordOptionName = 'woocommerceOrderNotificationOutlookPassword';
    CONST yandexMailOptionName = 'woocommerceOrderNotificationYandexMailAddress';
    CONST yandexAppPasswordOptionName = 'woocommerceOrderNotificationYandexAppPassword';
    CONST brevoTokenOptionName = 'woocommerceOrderNotificationBrevoToken';
    CONST statuesSlugInitOptionName = 'statues_slug_init';


    public function __construct(){
        $this->loadJsAndCSS();
    }

    public function render(){
        include_once (__DIR__.'/partials/index.php');
    }

    public function loadJsAndCSS(){
        wp_enqueue_script( 'mailSettingsScript', plugin_dir_url(__FILE__).'js/MailSettings.js', array(), '', true);
        wp_localize_script( 'mailSettingsScript', 'mailSettingsScript', $this->returnLocalizeScript());
        wp_enqueue_style( 'orderNotificationStyle', plugin_dir_url(__FILE__).'css/MailSettingsStyle.css');
    }

    public function returnLocalizeScript(){
        return [
            'localizeStatuses' => $this->prepareStatusSlug(),
            'adminRules' => $this->prepareMailRules(),
        ];
    }

    public function prepareStatusSlug(){
        $status_slugs = array_keys(wc_get_order_statuses());
        $status_views = array_values(wc_get_order_statuses());
        $statuses = [];
        for ($i = 0; $i < count($status_slugs); $i++){
            $statuses[$i]['slug'] = $status_slugs[$i];
            $statuses[$i]['view'] = $status_views[$i];
        }
        $statuses[count($statuses)] = ['slug' => '*', 'view' => __('All', '@@@')];
        return $statuses;
    }

    public function prepareMailRules(){
        if (get_option( 'mailRuleTemp') === false) 
            return [];

        $mailRuleTemp = json_decode(get_option( 'mailRuleTemp'));
        
        $return = array();

        for ($i = 0; $i < $mailRuleTemp -1; $i++){
            $return[$i] = get_option( 'mailRule-'.$i+1 );
        }

        return $return;
    }
}
?>