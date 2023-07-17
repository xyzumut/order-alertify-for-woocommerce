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
    include (__DIR__.'/Tools/').'Telegram/TelegramBot.php';
    include (__DIR__.'/Tools/').'Sms/SmsManager.php';

    use OrderAlertify\Tools\TelegramBot;
    use OrderAlertify\Tools\SmsManager;

    use OrderAlertifyView\GeneralSettingsView;
    use OrderAlertifyView\MailSettingsView;
    use OrderAlertifyView\SmsSettingsView;
    use OrderAlertifyView\TelegramSettingsView;


    final class OrderAlertifyPlugin{
        const EVENT = 'woocommerce_order_status_changed';

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
                ['shortCode' => '{total}'        , 'view' => __('Total Price', '@@@')],
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
                'shortCodesGeneratorTelegramHeaderText' => __('Short Codes For Telegram Templates', '@@@'),
                'shortCodesGeneratorSMSHeaderText' => __('Short Codes For SMS Templates', '@@@')
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

            $isMailEnable = get_option('isMailEnable', 'disable');
            $isSmsEnable = get_option('isSmsEnable', 'disable');
            $isTelegramEnable = get_option('isTelegramEnable', 'disable');

            if ($isMailEnable === 'enable') {
                $this->woocommerceListenerMail($order_id, $old_status, $new_status, $order);
            }

            if ($isSmsEnable === 'enable') {
                $this->woocommerceListenerSMS($order_id, $old_status, $new_status, $order);
            }

            if ($isTelegramEnable === 'enable') {
                $this->woocommerceListenerTelegram($order_id, $old_status, $new_status, $order);
            }
            
        } 

        public function shortCodesDecryption($text, $order){

            $shortCodes = [
                '{total}@total', '{customer_note}@customer_note', '{order_id}@id', '{customer_id}@customer_id', '{order_key}@order_key', 
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

        function editUrlForSms($baseUrl, $endPoint){

            if(strpos($baseUrl, "https://") === false && strpos($baseUrl, "http://") === false){
                $baseUrl = 'https://' . $baseUrl;
            }
            
            if(substr($baseUrl, -1) === '/'){
                $baseUrl = substr($baseUrl, 0, -1);
            }
            
            if(substr($endPoint, 0, 1) !== '/'){
                $endPoint = '/'.$endPoint;
            }
            
            return $baseUrl.$endPoint;
        }

        public function woocommerceListenerSMS($order_id, $old_status, $new_status, $order){
            $token = get_option('smsJwt', false);
            $baseApiUrl = get_option('smsBaseApiUrl', false);
            $sendSmsEndpoint = get_option('smsSendMessageEndpoint', false);

            if ($token === false || $baseApiUrl === false || $sendSmsEndpoint === false ) {
                return;
            }

            $url = $this->editUrlForSms($baseApiUrl, $sendSmsEndpoint);

            $smsRuleLength = get_option('smsRuleTemp');
            if ($smsRuleLength === false || json_decode($smsRuleLength) < 2 ) {
                return;
            }
            $smsRuleLength = json_decode($smsRuleLength);

            $validRuleIndex = $this->getRuleIndex($old_status, $new_status, $smsRuleLength, 'smsRule-');

            if ($validRuleIndex === -1) {
                return;
            }

            $rule = 'smsRule-'.$validRuleIndex;

            $message = get_option($rule.'-smsMessage', '');

            $message = $this->shortCodesDecryption($message, $order);

            $smsRecipients = get_option( $rule.'-recipients', false);

            if ($smsRecipients === false) {
                return;
            }

            $recipientsList = explode('{|}', $smsRecipients);
            array_push($recipientsList, $order->get_data()['billing']['phone']);

            $smsManager = new SmsManager($token, $url);
            
            foreach ($recipientsList as $recipient) {
                $smsManager->sendSMS($message, $recipient);
            }
        }


        public function woocommerceListenerTelegram($order_id, $old_status, $new_status, $order){
            $token = get_option('telegramToken');

            if ($token === false || trim($token, ' ') === '') {
                return;
            }

            $telegramRuleLength = get_option('telegramRuleTemp');
            if ($telegramRuleLength === false ||json_decode($telegramRuleLength) < 2 ) {
                return;
            }
            $telegramRuleLength = json_decode($telegramRuleLength);

            $validRuleIndex = $this->getRuleIndex($old_status, $new_status, $telegramRuleLength, 'telegramRule-');

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
            $mailSubject = $this->shortCodesDecryption($mailSubject, $order);

            array_push($recipients, $order->get_data()['billing']['email']);

            foreach ($recipients as $recipient){
                wp_mail( $recipient, $mailSubject, $mailContent, array('Content-Type: text/html; charset=UTF-8') );
            }
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

                        $mail = get_option('orderAlertifyMail', '');
                        $password = get_option('orderAlertifyPassword', '');
                        $host = get_option('orderAlertifyMailHost', '');
                        $port = get_option('orderAlertifySmtpPort', '');
                        $secure = get_option('orderAlertifySmtpSecure', 'SSL');

                        $response['status'] = true;
                        $response['data'] = array(
                            'mail' => $mail,
                            'password' => $password,
                            'host' => $host,
                            'port' => $port,
                            'secure' => $secure,
                        );
                        $response['message'] = __('Mail General Settings Brought', '@@@');
                        break;
                    case 'generalMailSettingsUpdate':
                        $enableMailOption = $post['enableMailOption'];
                        $orderAlertifyMail = $post['orderAlertifyMail'];
                        $orderAlertifyPassword = $post['orderAlertifyPassword'];
                        $orderAlertifyMailHost = $post['orderAlertifyMailHost'];
                        $orderAlertifySmtpPort = $post['orderAlertifySmtpPort'];
                        $orderAlertifySmtpSecure = $post['orderAlertifySmtpSecure'];

                        update_option('enableMailOption', $enableMailOption);
                        update_option('orderAlertifyMail', $orderAlertifyMail);
                        update_option('orderAlertifyPassword', $orderAlertifyPassword);
                        update_option('orderAlertifyMailHost', $orderAlertifyMailHost);
                        update_option('orderAlertifySmtpPort', $orderAlertifySmtpPort);
                        update_option('orderAlertifySmtpSecure', $orderAlertifySmtpSecure);


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
                                delete_option( 'telegramRule-'.$i.'-telegramMessage' );
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
                                update_option(('telegramRule-'.($i).'-telegramMessage'), get_option('telegramRule-'.($i+1).'-telegramMessage'));
                                delete_option('telegramRule-'.($i+1).'-telegramMessage');
                            }
                        }
                        break;
                    
                    case 'getTelegramTemplate':
                        if (get_option( 'telegramRuleTemp') === false){ 
                            $temp = false;
                        }
                        
                        $telegramRuleTemp = json_decode(get_option('telegramRuleTemp'));
                        $targetTemplateIndex = ''; // optionlarda şablonu tutacak olan index
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
                    case 'saveSmsSettings':
                        update_option('smsJwt', $post['smsJwt']);
                        update_option('smsLoginUsername', $post['smsLoginUsername']);
                        update_option('smsLoginPassword', $post['smsLoginPassword']);
                        update_option('smsBaseApiUrl', $post['smsBaseApiUrl']);
                        update_option('smsLoginEndpoint', $post['smsLoginEndpoint']);
                        update_option('smsSendMessageEndpoint', $post['smsSendMessageEndpoint']);
                        $response['status'] = true;
                        $response['message'] = __('Sms Information Saved', '@@@');
                        if ($post['smsJwt'] === 'noToken') {
                            $response['message'] = __('Sms Information Saved But The Information Is Wrong', '@@@');
                        }
                        break;
                    case 'getSmsSettings':
                        $response['data'] = [
                            'smsJwt' => get_option('smsJwt', ''),
                            'smsLoginUsername' => get_option('smsLoginUsername', ''),
                            'smsLoginPassword' => get_option('smsLoginPassword', ''),
                            'smsBaseApiUrl' => get_option('smsBaseApiUrl', ''),
                            'smsLoginEndpoint' => get_option('smsLoginEndpoint', ''),
                            'smsSendMessageEndpoint' => get_option('smsSendMessageEndpoint', ''),
                        ];
                        $response['status'] = true;
                        $response['message'] = __('Information brought', '@@@');
                        break;
                    case 'addSmsRule':
                        if (get_option('smsRuleTemp') === false) {
                            # daha önce admin rule kaydı olmamış demektir
                            update_option('smsRuleTemp', '1');
                        }

                        $smsRuleTemp = json_decode(get_option( 'smsRuleTemp'));

                        $definedRules = [];
                        for ($i = 1; $i < $smsRuleTemp; $i++){
                            $definedRules[$i-1] = get_option('smsRule-'.$i);
                        }

                        $newRule = $post['oldStatusSlug'].' > '.$post['newStatusSlug'];
                        for ($i = 0; $i < count($definedRules); $i++){
                            $definedRule = $definedRules[$i];
                            if ($definedRule == $newRule) {
                                // kurallar eşleşmiş demektir demekki yeni kayıt yapmayacağız
                                $response['message'] = __('This match already exists', '@@@');
                                $smsRuleTemp = false;
                                break;
                            }
                        }
                        if ($smsRuleTemp !== false) {
                            $response['data'] = $newRule;
                            $response['message'] = __('New Rule Added', '@@@');
                            $response['status'] = true;
                            update_option('smsRule-'.$smsRuleTemp , $newRule);
                            update_option( 'smsRuleTemp', $smsRuleTemp+1);
                            array_push($response['debug'], ['smsRuleTemp' => $smsRuleTemp]);
                            array_push($response['debug'], ['definedRules' => $definedRules]);
                            array_push($response['debug'], ['newRule' => $newRule]);
                        }
                        break;
                    case 'deleteSmsRule':
                        if (get_option( 'smsRuleTemp') === false){ 
                            $temp = false;
                            break;
                        }
                        $isDeleteted = false;
                        $smsRuleTemp = json_decode(get_option('smsRuleTemp'));
                        for ($i = 1; $i < $smsRuleTemp; $i++){
                            $definedRule = get_option('smsRule-'.$i);
                            if ($post['rule'] === $definedRule) {
                                delete_option( 'smsRule-'.$i );
                                delete_option( 'smsRule-'.$i.'-smsMessage' );
                                delete_option( 'smsRule-'.$i.'-recipients' );
                                $smsRuleTemp = json_decode(get_option('smsRuleTemp'));
                                $smsRuleTemp = $smsRuleTemp-1;
                                update_option('smsRuleTemp', $smsRuleTemp);
                                $isDeleteted = true;
                                $response['status'] = true;
                                $response['message'] = __('sms Rule deleted', '@@@');
                            }
                            if ($isDeleteted) {
                                update_option(('smsRule-'.($i)), get_option('smsRule-'.$i+1));
                                delete_option('smsRule-'.($i+1));
                                update_option(('smsRule-'.($i).'-smsMessage'), get_option('smsRule-'.($i+1).'-smsMessage'));
                                delete_option('smsRule-'.($i+1).'-smsMessage');
                                update_option(('smsRule-'.($i).'-recipients'), get_option('smsRule-'.($i+1).'-recipients'));
                                delete_option('smsRule-'.($i+1).'-recipients');
                            }
                        }
                        break;
                    case 'getSmsTemplate':
                        if (get_option( 'smsRuleTemp') === false){ 
                            $temp = false;
                        }
                        $smsRuleTemp = json_decode(get_option('smsRuleTemp'));
                        $targetTemplateIndex; // optionlarda şablonu tutacak olan index
                        for ($i = 1; $i < $smsRuleTemp; $i++){
                            $definedRule = get_option('smsRule-'.$i);
                            if ($post['rule'] === $definedRule) {
                                $targetTemplateIndex = 'smsRule-'.$i;
                            }
                        }

                        if (get_option($targetTemplateIndex.'-smsMessage') === false) {
                            update_option($targetTemplateIndex.'-smsMessage', __('Not Added Yet SMS Message', '@@@'));
                        }
                        
                        $response['message'] = __('SMS template brought', '@@@'); 
                        $response['status'] = true;
                        $response['data'] = [ 
                            'smsMessage' => get_option($targetTemplateIndex.'-smsMessage'),
                            'recipients' => get_option($targetTemplateIndex.'-recipients', '')
                        ];

                        break;
                    case 'smsMessageSave':
                        $smsRuleTemp = json_decode(get_option('smsRuleTemp'));
                        $targetTemplateIndex; 
                        for ($i = 1; $i < $smsRuleTemp; $i++){
                            $definedRule = get_option('smsRule-'.$i);
                            if ($post['target'] === $definedRule) {
                                $targetTemplateIndex = 'smsRule-'.$i;
                                break;
                            }
                        }
                        update_option(($targetTemplateIndex.'-recipients') , $post['recipients']);
                        update_option(($targetTemplateIndex.'-smsMessage'), $post['newsmsMessage']);
                        $response['message'] = __('Sms Message Saved', '@@@');
                        $response['status'] = true;
                        break;
                    case 'getGeneralData':
                        $isTelegramEnable   = get_option('isTelegramEnable', 'disable');
                        $isMailEnable       = get_option('isMailEnable', 'disable');
                        $isSmsEnable        = get_option('isSmsEnable', 'disable');
                        $response['data'] = [
                            'isTelegramEnable' => $isTelegramEnable ,
                            'isMailEnable' => $isMailEnable ,
                            'isSmsEnable' => $isSmsEnable
                        ];
                        $response['status'] = true;
                        $response['message'] = __('Option information arrived', '@@@');
                        break;
                    case 'saveOption':
                        $switchTemp = false;
                        switch ($post['optionType']){
                            case 'telegramToggle':
                                update_option('isTelegramEnable', $post['value']);
                                $switchTemp = true;
                                break;
                            case 'mailToggle':
                                update_option('isMailEnable', $post['value']);
                                $switchTemp = true;
                                break;
                            case 'smsToggle':
                                update_option('isSmsEnable', $post['value']);
                                $switchTemp = true;
                                break;
                        default:
                            $temp = false;
                            break;
                        }

                        if ($switchTemp) {
                            $response['status'] = true;
                            $response['message'] = __('Option Settings Saved', '@@@');
                        }
                        else{
                            $temp = false;
                        }

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