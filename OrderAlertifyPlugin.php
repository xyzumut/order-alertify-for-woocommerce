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
    include (__DIR__.'/Tools/').'Mail/MailManager.php';
    include (__DIR__.'/Tools/').'Telegram/TelegramBot.php';

    use OrderAlertify\Tools\MailManager;
    use OrderAlertify\Tools\TelegramBot;

    use OrderAlertifyView\GeneralSettingsView;
    use OrderAlertifyView\MailSettingsView;
    use OrderAlertifyView\SmsSettingsView;
    use OrderAlertifyView\TelegramSettingsView;


    final class OrderAlertifyPlugin{

        const EVENT = 'woocommerce_order_status_changed';

        public $mailManager;
        public $telegramBot;

        public function __construct(){
            add_action('admin_menu', [$this, 'renderAllPages']);
            add_action(OrderAlertifyPlugin::EVENT, [$this, 'woocommerceListener'], 10, 3);
            add_action('wp_ajax_orderAlertifyAjaxListener', [$this, 'orderAlertifyAjaxListener']);
            add_action('wp_ajax_nopriv_orderAlertifyAjaxListener', [$this, 'orderAlertifyAjaxListener']);
            $this->mailEditorFormatter();
            wp_enqueue_style( 'orderAlertifyGeneralStyle', plugin_dir_url(__FILE__).'css/orderAlertifyGeneralStyle.css');
            wp_enqueue_style( 'orderAlertifyTailwindStyle', plugin_dir_url(__FILE__).'css/orderAlertifyTailwind.css');
            wp_enqueue_script( 'orderAlertifyGeneralScript', plugin_dir_url(__FILE__).'js/orderAlertifyGeneralScript.js', array(), '', true);
            wp_localize_script( 'orderAlertifyGeneralScript', 'orderAlertifyGeneralScript', $this->returnLocalizeScript());
            wp_enqueue_script( 'orderAlertifyRuleGenerator', plugin_dir_url(__FILE__).'js/RuleGenerator.js', array(), '', true);
            wp_enqueue_script( 'orderAlertifyShortCodes', plugin_dir_url(__FILE__).'js/ShortCodes.js', array(), '', true);
            wp_enqueue_script( 'menuGenerator', plugin_dir_url(__FILE__).'js/MenuGenerator.js', array(), '', true);
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

        public function returnLocalizeScript(){
            $shordCodes = [
                ['shortCode' => '{customer_note}', 'view' => __('Customer Note', '@@@')],
                ['shortCode' => '{order_id}'     , 'view' => __('Order ID', '@@@')],
                ['shortCode' => '{customer_id}'  , 'view' => __('Customer ID', '@@@')],
                ['shortCode' => '{order_key}'    , 'view' => __('Order Key', '@@@')],
                ['shortCode' => '{bil_first}'    , 'view' => __('Billing First Name', '@@@')],
                ['shortCode' => '{bil_last}'     , 'view' => __('Billing Last Name', '@@@')],
                ['shortCode' => '{bil_add1}'     , 'view' => __('Billing Address 1', '@@@')],
                ['shortCode' => '{bil_add2}'     , 'view' => __('Billing Address 2', '@@@')],
                ['shortCode' => '{bil_city}'     , 'view' => __('Billing City', '@@@')],
                ['shortCode' => '{bil_mail}'     , 'view' => __('Billing Email', '@@@')],
                ['shortCode' => '{bil_phone}'    , 'view' => __('Billing Phone', '@@@')],
                ['shortCode' => '{ship_first}'   , 'view' => __('Shipping First Name', '@@@')],
                ['shortCode' => '{ship_last}'    , 'view' => __('Shipping Last Name', '@@@')],
                ['shortCode' => '{ship_add1}'    , 'view' => __('Shipping Address 1', '@@@')],
                ['shortCode' => '{ship_add2}'    , 'view' => __('Shipping Address 2', '@@@')],
                ['shortCode' => '{ship_city}'    , 'view' => __('Shipping City', '@@@')],
                ['shortCode' => '{ship_phone}'   , 'view' => __('Shipping Phone', '@@@')],
            ];
            return [
                'localizeStatuses' => $this->prepareStatusSlug(),
                'adminUrl' => get_admin_url(),
                'shortcodes' => $shordCodes,
                'copyToText' => __('Copy to Clipboard', '@@@'),
                'loadingText' => __('Loading . . .', '@@@'),
                'shortCodesGeneratorMailHeaderText' => __('Short Codes For Mail Templates', '@@@'),
                'dragAndDropChooseDifferentOptionText' => __('Choose Different Options', '@@@'),
                'mailRecipeWarningMessageText' => __('Please Enter the Appropriate Value', '@@@'),
                'shortCodesGeneratorTelegramHeaderText' => __('Short Codes For Telegram Templates', '@@@')
            ];  
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
            $order = wc_get_order( $order_id );


            $this->woocommerceListenerMail($order_id, $old_status, $new_status, $order);
            $this->woocommerceListenerTelegram($order_id, $old_status, $new_status, $order);
        } 

        public function shortCodesDecryption($text, $order){

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
                    $text = str_replace($shortCode[0], $order->get_data()[$shortCode[1]][$shortCode[2]], $text);
                }
                else if (count($shortCode) === 2) {
                    # short code + arrayindex1 
                    $text = str_replace($shortCode[0], $order->get_data()[$shortCode[1]], $text);
                }
            }
            return $text;
        }

        public function getRuleIndex($old_status, $new_status, $ruleLength, $slug){

            $orderRule = $old_status. ' > '. $new_status;

            for ($i=1; $i < $ruleLength ; $i++) { 

                $rule = get_option($slug.$i);
                $ruleOldStatusInBackend = explode(' > ', $rule)[0];
                $ruleNewStatusInBackend = explode(' > ', $rule)[1];

                if ($rule === $orderRule) {
                    return $i;
                }

                if ($ruleOldStatusInBackend === '*' && $ruleNewStatusInBackend === $new_status) {
                    # Eski statü All iken yeni statü uyuşuyor ise ilgili kuralı seç
                    return $i;
                }

                if ($ruleNewStatusInBackend === '*' && $ruleOldStatusInBackend === $old_status) {
                    # Yeni statü All iken eski statü uyuşuyor ise ilgili kuralı seç
                    return $i;
                }

            }

            return -1;
        }

        public function woocommerceListenerTelegram($order_id, $old_status, $new_status, $order){
            $token = get_option('telegramToken');

            if ($token === false || trim($token, ' ') === '') {
                return;
            }

            $telegramRuleTemp = get_option('telegramRuleTemp');
            if ($telegramRuleTemp === false ||json_decode($telegramRuleTemp) < 2 ) {
                return;
            }
            $telegramRuleTemp = json_decode($telegramRuleTemp);

            $validRuleIndex = $this->getRuleIndex($old_status, $new_status, $telegramRuleTemp, 'telegramRule-');

            if ($validRuleIndex === -1) {
                return;
            }

            $message = get_option('telegramRule-'.$validRuleIndex.'-telegramMessage');

            $message = $this->shortCodesDecryption($message, $order);

            $activeTelegramUsersIndex = get_option('telegramActiveUsersIndex');
            if ($activeTelegramUsersIndex === false || json_decode($activeTelegramUsersIndex) < 2) {
                return;
            }

            $activeTelegramUsersIndex = json_decode($activeTelegramUsersIndex);
            $activeTelegramUsersChatIdList = [];

            for ($i = 1; $i < $activeTelegramUsersIndex ; $i++){   

                $user = get_option('telegramUser-'.$i);
                $user = explode('@', $user);
                $chat_id = $user[2];//2c değer chatid tutuyor
                array_push($activeTelegramUsersChatIdList, $chat_id);
            }
            

            $telegramBot = new TelegramBot($token);

            foreach ($activeTelegramUsersChatIdList as $chat_id) {
                $telegramBot->sendMessage($message, $chat_id);
            }
        }

        public function woocommerceListenerMail($order_id, $old_status, $new_status, $order){

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

            $validRuleIndex = $this->getRuleIndex($old_status, $new_status, $mailRuleLength, 'mailRule-');

            if ($validRuleIndex === -1) {
                return;
            }
            
            $targetTemplateIndex = 'mailRule-'.$validRuleIndex.'-';
            
            $mailSubject = get_option($targetTemplateIndex.'mailSubject');
            $mailContent = get_option($targetTemplateIndex.'mailContent');
            $recipients = get_option($targetTemplateIndex.'recipients');
            if ($recipients !== 'false' && $recipients !== false) {
                $recipients = explode('{|}', $recipients);
            }

            $mailContent = $this->shortCodesDecryption($mailContent, $order);

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
                    case 'telegramMainSettingsInit';
                        $telegramToken = get_option('telegramToken');
                        if ($telegramToken === false) {
                            $telegramToken = __('Not Added Yet', '@@@');
                            update_option('telegramToken', $telegramToken);
                        }

                        $activeTelegramUsersIndex = get_option('telegramActiveUsersIndex');
                        if ($activeTelegramUsersIndex === false) {
                            $activeTelegramUsersIndex = 1;
                            update_option('telegramActiveUsersIndex', $activeTelegramUsersIndex);
                        }
                        $activeUsers = []; // format string => nameSurname@username@chat_id
                        for ($i = 1; $i < $activeTelegramUsersIndex; $i++){
                            $temp = explode('@', get_option('telegramUser-'.$i));
                            array_push($activeUsers, ['nameSurname' => $temp[0], 'username' => $temp[1], 'chatId' => $temp[2]]);
                        }

                        $response['status'] = true;
                        $response['data'] = [
                            'activeUsers' => $activeUsers,
                            'telegramToken' => $telegramToken
                        ];
                        $response['message'] = __('Telegram Settings Arrived', '@@@');
                        break;
                    case 'saveTelegramToken':
                        update_option('telegramToken', $post['newToken']);
                        $response['status'] = true;
                        $response['message'] = __('New Token is Saved.', '@@@');
                        break;
                    case 'checkChatId':
                        $activeTelegramUsersIndex = get_option('telegramActiveUsersIndex');
                        if ($activeTelegramUsersIndex === false) {
                            $activeTelegramUsersIndex = 1;
                            update_option('telegramActiveUsersIndex', $activeTelegramUsersIndex);
                        }
                        $activeTelegramUsersIndex = json_decode($activeTelegramUsersIndex);
                        $activeUsers = []; // format string => nameSurname@username@chat_id

                        for ($i = 1; $i < $activeTelegramUsersIndex; $i++){
                            array_push($activeUsers, get_option('telegramUser-'.$i));
                        }

                        if (count($activeUsers) < 1) {
                            $response['status'] = true;
                        }

                        $status = true; // true için eşleşme yok demek

                        foreach ($activeUsers as $user) {
                            $user = explode('@', $user);
                            if (json_decode($user[2]) === json_decode($post['chat_id'])) {
                                $status=false;
                            }
                        }

                        $response['status'] = $status;
                        break;
                    case 'addTelegramUser':
                        $activeTelegramUsersIndex = get_option('telegramActiveUsersIndex');
                        if ($activeTelegramUsersIndex === false) {
                            $activeTelegramUsersIndex = 1;
                            update_option('telegramActiveUsersIndex', $activeTelegramUsersIndex);
                        }
                        update_option('telegramUser-'.($activeTelegramUsersIndex), $post['newTelegramUser']);
                        update_option('telegramActiveUsersIndex', json_decode($activeTelegramUsersIndex)+1);
                        $response['status'] = true;
                        $response['message'] = __('New Telegram User Added', '@@@');
                        break;
                    case 'deleteTelegramUser':
                        $activeTelegramUsersIndex = json_decode(get_option('telegramActiveUsersIndex'));

                        $deleteTemp = false;
                        for ($i = 1; $i < $activeTelegramUsersIndex ; $i++){
                            $user = get_option('telegramUser-'.$i);
                            if ($user === $post['user']) {
                                $deleteTemp = true;
                                $activeTelegramUsersIndex = $activeTelegramUsersIndex-1;
                                update_option('telegramActiveUsersIndex', $activeTelegramUsersIndex);
                            }

                            if ($deleteTemp) {
                                update_option('telegramUser-'.$i, get_option('telegramUser-'.($i+1)));
                            }

                        }

                        if ($deleteTemp) {
                            delete_option('telegramUser-'.$activeTelegramUsersIndex);
                            $response['message'] = __('Deletion Successful', '@@@');
                        }

                        $response['status'] = $deleteTemp;
                        
                        break;
                    case 'addTelegramRule':
                        if (get_option('telegramRuleTemp') === false) {
                            # daha önce admin rule kaydı olmamış demektir
                            update_option('telegramRuleTemp', '1');
                        }

                        $telegramRuleTemp = json_decode(get_option( 'telegramRuleTemp'));

                        $definedRules = [];
                        for ($i = 1; $i < $telegramRuleTemp; $i++){
                            $definedRules[$i-1] = get_option('telegramRule-'.$i);
                        }

                        $newRule = $post['oldStatusSlug'].' > '.$post['newStatusSlug'];

                        for ($i = 0; $i < count($definedRules); $i++){
                            $definedRule = $definedRules[$i];
                            if ($definedRule == $newRule) {
                                // kurallar eşleşmiş demektir demekki yeni kayıt yapmayacağız
                                $response['message'] = __('This match already exists', '@@@');
                                $telegramRuleTemp = false;
                                break;
                            }
                        }

                        if ($telegramRuleTemp !== false) {
                            $response['data'] = $newRule;
                            $response['message'] = __('New Rule Added', '@@@');
                            $response['status'] = true;
                            update_option('telegramRule-'.$telegramRuleTemp , $newRule);
                            update_option( 'telegramRuleTemp', $telegramRuleTemp+1);
                        }


                        break;
                    case 'deleteTelegramRule':
                        if (get_option( 'telegramRuleTemp') === false){ 
                            $temp = false;
                            break;
                        }
                        $isDeleteted = false;
                        $telegramRuleTemp = json_decode(get_option('telegramRuleTemp'));
                        for ($i = 1; $i < $telegramRuleTemp; $i++){
                            $definedRule = get_option('telegramRule-'.$i);
                            if ($post['rule'] === $definedRule) {
                                delete_option( 'telegramRule-'.$i );
                                $telegramRuleTemp = json_decode(get_option('telegramRuleTemp'));
                                $telegramRuleTemp = $telegramRuleTemp-1;
                                update_option('telegramRuleTemp', $telegramRuleTemp);
                                $isDeleteted = true;
                                $response['status'] = true;
                                $response['message'] = __('Telegram Rule deleted', '@@@');
                            }
                            if ($isDeleteted) {
                                update_option(('telegramRule-'.($i)), get_option('telegramRule-'.$i+1));
                                delete_option('telegramRule-'.($i+1));
                            }
                        }
                        break;
                    
                    case 'getTelegramTemplate':
                        if (get_option( 'telegramRuleTemp') === false){ 
                            $temp = false;
                        }
                        
                        $telegramRuleTemp = json_decode(get_option('telegramRuleTemp'));
                        $targetTemplateIndex; // optionlarda şablonu tutacak olan index
                        for ($i = 1; $i < $telegramRuleTemp; $i++){
                            $definedRule = get_option('telegramRule-'.$i);
                            if ($post['rule'] === $definedRule) {
                                $targetTemplateIndex = 'telegramRule-'.$i;
                            }
                        }

                        if (get_option($targetTemplateIndex.'-telegramMessage') === false) {
                            update_option($targetTemplateIndex.'-telegramMessage', __('Not Added Yet Telegram Message', '@@@'));
                        }
                        
                        $response['message'] = __('Telegram template brought', '@@@'); 
                        $response['status'] = true;
                        $response['data'] = [ 'telegramMessage' => get_option($targetTemplateIndex.'-telegramMessage')];

                        break;
                    case 'telegramMessageSave':
                        $telegramRuleTemp = json_decode(get_option('telegramRuleTemp'));
                        $targetTemplateIndex; 
                        for ($i = 1; $i < $telegramRuleTemp; $i++){
                            $definedRule = get_option('telegramRule-'.$i);
                            if ($post['target'] === $definedRule) {
                                $targetTemplateIndex = 'telegramRule-'.$i;
                                break;
                            }
                        }
                        update_option(($targetTemplateIndex.'-telegramMessage'), $post['newTelegramMessage']);
                        $response['message'] = __('Telegram Message Saved', '@@@');
                        $response['status'] = true;
                        break;
                    //
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