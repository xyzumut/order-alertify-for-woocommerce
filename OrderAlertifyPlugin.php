<?php 
/**
 * @package OrderAlertify 
 * @version 1.7.2
 */
/*
Plugin Name: OrderAlertify 
Description: OrderAlertify Eklentisi
Author: Umut Gedik
Version: 1.0.0
Text Domain: @@@@
Domain Path: /lang
*/


    include (__DIR__.'/view/').'general-settings-view/GeneralSettingsView.php' ;
    include (__DIR__.'/view/').'mail-settings-view/MailSettingsView.php' ;
    include (__DIR__.'/view/').'telegram-settings-view/TelegramSettingsView.php' ;
    include (__DIR__.'/view/').'sms-settings-view/SmsSettingsView.php' ;
    include (__DIR__).'/Mail/MailManager.php';

    use OrderAlertify\Tools\MailManager;
    use OrderAlertifyView\GeneralSettingsView;
    use OrderAlertifyView\MailSettingsView;
    use OrderAlertifyView\SmsSettingsView;
    use OrderAlertifyView\TelegramSettingsView;


    final class OrderAlertifyPlugin{

        const EVENT = 'woocommerce_order_status_changed';

        public $mailManager;

        public function __construct(){
            add_action('admin_menu', [$this, 'renderAllPages']);
            add_action(OrderAlertifyPlugin::EVENT, [$this, 'woocommerceListener'], 10, 3);
            add_action('wp_ajax_orderAlertifyAjaxListener', [$this, 'orderAlertifyAjaxListener']);
            add_action('wp_ajax_nopriv_orderAlertifyAjaxListener', [$this, 'orderAlertifyAjaxListener']);
            $this->mailEditorFormatter();
            // wp_enqueue_script( 'orderAlertifyScript', plugin_dir_url(__FILE__).'js/orderAlertifyScript.js', array(), '', true);
            // wp_localize_script( 'orderAlertifyScript', 'orderAlertifyScript', $this->returnLocalizeScript());
            wp_enqueue_style( 'orderAlertifyGeneralStyle', plugin_dir_url(__FILE__).'css/orderAlertifyGeneralStyle.css');
            wp_enqueue_style( 'orderAlertifyTailwindStyle', plugin_dir_url(__FILE__).'css/orderAlertifyTailwind.css');
            wp_enqueue_script( 'orderAlertifyGeneralScript', plugin_dir_url(__FILE__).'js/orderAlertifyGeneralScript.js', array(), '', true);

        }

        public function renderAllPages() {
            add_menu_page( __('OrderAlertify Plugin', '@@@'), __('Order Alertify', '@@@'), 'manage_options', 'OrderAlertifyGeneralSettings', [$this, 'renderMainMenuPage'], 'dashicons-admin-settings', 67);
		    add_submenu_page( 'OrderAlertifyGeneralSettings', __('Order Alertify', '@@@')   , __('General Settings', '@@@') , 'manage_options', 'OrderAlertifyGeneralSettings');
		    add_submenu_page( 'OrderAlertifyGeneralSettings', __('Mail Settings', '@@@')    , __('Mail Settings', '@@@')    , 'manage_options', __('MailSettings', '@@@')    , [$this, 'renderMailSettings']    , 2);
		    add_submenu_page( 'OrderAlertifyGeneralSettings', __('Telegram Settings', '@@@'), __('Telegram Settings', '@@@'), 'manage_options', __('TelegramSettings', '@@@'), [$this, 'renderTelegramSettings'], 3);
		    add_submenu_page( 'OrderAlertifyGeneralSettings', __('SMS Settings', '@@@')     , __('SMS Settings', '@@@')     , 'manage_options', __('SmsSettings', '@@@')     , [$this, 'renderSmsSettings']     , 4);
        }

        public function renderMainMenuPage(){
            $view = new GeneralSettingsView();
            $view->render();
        }
        public function renderMailSettings(){
            $view = new MailSettingsView();
            $view->render();
        }
        public function renderTelegramSettings(){
            $view = new TelegramSettingsView();
            $view->render();
        }
        public function renderSmsSettings(){
            $view = new SmsSettingsView();
            $view->render();
        }

        public function woocommerceListener($order_id, $old_status, $new_status){
            $old_status = 'wc-'.$old_status;
            $new_status = 'wc-'.$new_status;
            $mailRuleLength = json_decode(get_option('mailRuleTemp'));
            if ($mailRuleLength === false || $mailRuleLength === 1) {
                update_option('mailRuleTemp', 1);
                return; // kural yoksa mailde atılmasın gerek yok
            }
            $selectedMailOption = get_option('enableMailOption');
            if ($selectedMailOption === false || $selectedMailOption === 'dontUseMail') {
                update_option('enableMailOption', 'dontUseMail');
                return; // mail opsiyonu bilgisi yoksa veya mail kullanma dediysekte mail atmayacak
            }
            $orderRule = $old_status. ' > '. $new_status;
            $validRuleIndex = -1;
            for ($i=1; $i < $mailRuleLength ; $i++) { 
                $rule = get_option('mailRule-'.$i);
                $ruleOldStatusInBackend = explode(' > ', $rule)[0];
                $ruleNewStatusInBackend = explode(' > ', $rule)[1];
                update_option('[FB]DEBUG'.'-oldStatusInOrder', $old_status);
                update_option('[FB]DEBUG'.'-newStatusInOrder', $new_status);
                update_option('[FB]DEBUG'.'-oldStatusInBackend', $ruleOldStatusInBackend);
                update_option('[FB]DEBUG'.'-newStatusInBackend', $ruleNewStatusInBackend);


                if ($rule === $orderRule) {
                    $validRuleIndex = $i;
                    break;
                }
                if ($ruleOldStatusInBackend === '*' && $ruleNewStatusInBackend === $new_status) {
                    # Eski statü All iken yeni statü uyuşuyor ise ilgili kuralı seç
                    $validRuleIndex = $i;
                    break;
                }

                if ($ruleNewStatusInBackend === '*' && $ruleOldStatusInBackend === $old_status) {
                    # Yeni statü All iken eski statü uyuşuyor ise ilgili kuralı seç
                    $validRuleIndex = $i;
                    break;
                }
                
            }
            if ($validRuleIndex === -1) {
                return;
            }
            
            $targetTemplateIndex = 'mailRule-'.$i.'-';
            
            $mailSubject = get_option($targetTemplateIndex.'mailSubject');
            $mailContent = get_option($targetTemplateIndex.'mailContent');
            $recipients = get_option($targetTemplateIndex.'recipients');
            if ($recipients !== 'false' && $recipients !== false) {
                $recipients = explode('{|}', $recipients);
            }

            $order = wc_get_order( $order_id );

            $shortCodes = [
                '{customer_note}@customer_note', '{order_id}@id', '{customer_id}@customer_id', '{order_key}@order_key', 
                '{bil_first}@billing@first_name', '{bil_last}@billing@last_name', '{bil_add1}@billing@address_1', '{bil_add2}@billing@address_2', '{bil_city}@billing@city',
                '{bil_mail}@billing@email', '{bil_phone}@billing@phone', '{ship_first}@shipping@first_name', '{ship_last}@shipping@last_name', '{ship_add1}@shipping@address_1', 
                '{ship_add2}@shipping@address_2', '{ship_city}@shipping@city', '{ship_phone}@shipping@phone'
            ];

            foreach ($shortCodes as $shortCode) {
                $shortCode = explode('@', $shortCode);
                if (count($shortCode) === 3) {
                    # short code + arrayindex1 + arrayindex2
                    $mailContent = str_replace($shortCode[0], $order->get_data()[$shortCode[1]][$shortCode[2]], $mailContent);
                }
                else if (count($shortCode) === 2) {
                    # short code + arrayindex1 
                    $mailContent = str_replace($shortCode[0], $order->get_data()[$shortCode[1]], $mailContent);
                }
            }

            array_push($recipients, $order->get_data()['billing']['email']);


            // $mail, $password
            $mailAddress = get_option('orderAlertifyMail');
            $mailPassword = get_option('orderAlertifyPassword');
            if ($mailAddress === false || $mailPassword === false) {
                return;
            }

            $mailManager = new MailManager($selectedMailOption, $recipients, $mailSubject, $mailContent, $mailAddress, $mailPassword, 'Gri WooCommerce');
            $mailManager->sendMail();
        }

        public function mailEditorFormatter(){

            function custom_editor_size($init_array) {
                $init_array['height'] = '450'; // Yükseklik değerini burada belirleyin
                $init_array['width'] = '500'; // Genişlik değerini burada belirleyin
                return $init_array;
            }

            add_filter('tiny_mce_before_init', 'custom_editor_size');

            remove_action('media_buttons', 'media_buttons');
            
            add_filter('mce_buttons', function ($buttons) {
                $buttons = array_diff($buttons, array('formatselect', 'blockquote', 'wp_more', 'fullscreen', 'link', 'wp_adv'));
                return $buttons;
            });
        }

        public function orderAlertifyAjaxListener(){
            $response = array('status' => false, 'message' => __('Something Went Wrong'), 'data' => null, 'debug' => ['Başladı']);
            $post = $_POST;
            $temp = true;
            if (isset($post['_operation'])) {
                switch ($post['_operation'])
                {
                    case 'addMailRule':

                        if (get_option( 'mailRuleTemp') === false) {
                            # daha önce admin rule kaydı olmamış demektir
                            update_option('mailRuleTemp', '1');
                        }

                        $mailRuleTemp = json_decode(get_option( 'mailRuleTemp'));

                        $definedRules = [];
                        for ($i = 1; $i < $mailRuleTemp; $i++){
                            $definedRules[$i-1] = get_option('mailRule-'.$i);
                        }

                        $newRule = $post['oldStatusSlug'].' > '.$post['newStatusSlug'];

                        for ($i = 0; $i < count($definedRules); $i++){
                            $definedRule = $definedRules[$i];
                            if ($definedRule == $newRule) {
                                // kurallar eşleşmiş demektir demekki yeni kayıt yapmayacağız
                                $response['message'] = __('This match already exists', '@@@');
                                $mailRuleTemp = false;
                                break;
                            }
                        }

                        if ($mailRuleTemp !== false) {
                            $response['data'] = $newRule;
                            $response['message'] = __('New Rule Added', '@@@');
                            $response['status'] = true;
                            update_option('mailRule-'.$mailRuleTemp , $newRule);
                            update_option( 'mailRuleTemp', $mailRuleTemp+1);
                        }
                        break;
                    case 'deleteMailRule':

                        if (get_option( 'mailRuleTemp') === false){ 
                            $temp = false;
                            break;
                        }
                        $isDeleteted = false;
                        $mailRuleTemp = json_decode(get_option('mailRuleTemp'));
                        for ($i = 1; $i < $mailRuleTemp; $i++){
                            $definedRule = get_option('mailRule-'.$i);
                            if ($post['rule'] === $definedRule) {
                                delete_option( 'mailRule-'.$i );
                                delete_option( 'mailRule-'.$i.'-mailSubject' );
                                delete_option( 'mailRule-'.$i.'-mailContent' );
                                delete_option( 'mailRule-'.$i.'-recipients' );
                                $mailRuleTemp = json_decode(get_option('mailRuleTemp'));
                                $mailRuleTemp = $mailRuleTemp-1;
                                update_option('mailRuleTemp', $mailRuleTemp);
                                $isDeleteted = true;
                                $response['status'] = true;
                                $response['message'] = __('Rule deleted', '@@@');
                            }
                            if ($isDeleteted) {
                                update_option(('mailRule-'.($i)), get_option('mailRule-'.$i+1));
                                delete_option('mailRule-'.($i+1));
                                update_option(('mailRule-'.($i).'-mailContent'), get_option('mailRule-'.($i+1).'-mailContent'));
                                delete_option('mailRule-'.($i+1).'-mailContent');
                                update_option(('mailRule-'.($i).'-mailSubject'), get_option('mailRule-'.($i+1).'-mailSubject'));
                                delete_option('mailRule-'.($i+1).'-mailSubject');
                                update_option(('mailRule-'.($i).'-recipients'), get_option('mailRule-'.($i+1).'-recipients'));
                                delete_option('mailRule-'.($i+1).'-recipients');
                            }

                        }
                        break;
                    case 'getMailTemplate':

                        if (get_option( 'mailRuleTemp') === false){ 
                            $temp = false;
                        }
                        
                        $mailRuleTemp = json_decode(get_option('mailRuleTemp'));
                        $targetTemplateIndex; // optionlarda şablonu tutacak olan index
                        for ($i = 1; $i < $mailRuleTemp; $i++){
                            $definedRule = get_option('mailRule-'.$i);
                            if ($post['rule'] === $definedRule) {
                                $targetTemplateIndex = 'mailRule-'.$i;
                            }
                        }

                        if (get_option($targetTemplateIndex.'-mailContent') === false) {
                            update_option($targetTemplateIndex.'-mailContent', __('Not Added Yet', '@@@'));
                            update_option($targetTemplateIndex.'-mailSubject', __('Not Added Yet', '@@@'));
                            update_option($targetTemplateIndex.'-recipients', 'false');
                        }
                        
                        $response['message'] = __('Mail template brought', '@@@'); 
                        $response['status'] = true;
                        $response['data'] = [
                            'mailSubject' => get_option($targetTemplateIndex.'-mailSubject'),
                            'mailContent' => get_option($targetTemplateIndex.'-mailContent'),
                            'recipients' => get_option($targetTemplateIndex.'-recipients')
                        ];
                        break;
                    case 'saveMailTemplate':
                        $mailRuleTemp = json_decode(get_option('mailRuleTemp'));
                        $targetTemplateIndex; 
                        for ($i = 1; $i < $mailRuleTemp; $i++){
                            $definedRule = get_option('mailRule-'.$i);
                            if ($post['target'] === $definedRule) {
                                $targetTemplateIndex = 'mailRule-'.$i;
                            }
                        }
                        update_option(($targetTemplateIndex.'-recipients') , $post['recipients']);
                        update_option(($targetTemplateIndex.'-mailContent'), $post['newContent']);
                        update_option(($targetTemplateIndex.'-mailSubject'), $post['newSubject']);
                        $response['message'] = __('Template Saved', '@@@');
                        $response['status'] = true;
                        break;
                    case 'generalMailSettingsInit':
                        $enableMailOption = get_option('enableMailOption');
                        $mail = get_option('orderAlertifyMail');
                        $password = get_option('orderAlertifyPassword');
                        if ( $enableMailOption === false) {
                            $enableMailOption = 'dontUseMail';
                            update_option('enableMailOpiton', $enableMailOption);
                        }
                        if ( $mail === false) {
                            $mail = '';
                            update_option('orderAlertifyMail', $mail);
                        }
                        if ( $password === false) {
                            $password = '';
                            update_option('orderAlertifyPassword', $enableMailOption);
                        }

                        $response['status'] = true;
                        $response['data'] = array('selectedMailOption' => $enableMailOption, 'mail' => $mail, 'password' => $password);
                        $response['message'] = __('Mail General Settings Brought', '@@@');
                        break;
                    case 'generalMailSettingsUpdate':
                        $enableMailOption = $post['enableMailOption'];
                        $orderAlertifyMail = $post['orderAlertifyMail'];
                        $orderAlertifyPassword = $post['orderAlertifyPassword'];
                        update_option('enableMailOption', $enableMailOption);
                        update_option('orderAlertifyMail', $orderAlertifyMail);
                        update_option('orderAlertifyPassword', $orderAlertifyPassword);
                        $response['status'] = true;
                        $response['message'] = __('Mail General Settings Saved', '@@@');
                        break;
                    default:
                        $temp = false;
                        break;
                }
                if ($temp) {
                    wp_send_json($response);
                }
            }
            wp_send_json($response);
        }

    }

    add_action( 'plugins_loaded', function(){
        $orderPlugin = new OrderAlertifyPlugin();
    });

?>