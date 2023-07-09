<?php 
    namespace OrderAlertify\Tools;
    
    
    
    use PHPMailer\PHPMailer_\PHPMailer;
    use PHPMailer\PHPMailer_\Exception;
    require (__DIR__.'/').'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
    require (__DIR__.'/').'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require (__DIR__.'/').'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';
    
    final class MailManager{

        CONST outlookMailOptionName = 'woocommerceOrderNotificationOutlookAddress';
        CONST outlookPasswordOptionName = 'woocommerceOrderNotificationOutlookPassword';
        CONST yandexMailOptionName = 'woocommerceOrderNotificationYandexMailAddress';
        CONST yandexAppPasswordOptionName = 'woocommerceOrderNotificationYandexAppPassword';
        CONST brevoTokenOptionName = 'woocommerceOrderNotificationBrevoToken';
        CONST statuesSlugInitOptionName = 'statues_slug_init';
        
        public $activeMailOption;
        public $outlookMail;
        public $outlookPassword;
        public $yandexMail;
        public $yandexAppPassword;
        public $brevoToken;

        CONST outlookPort = 587;
        CONST outlookSecure = 'STARTTLS';
        CONST outlookHost = 'smtp.office365.com';  

        CONST yandexPort = 465;
        CONST yandexSecure = 'ssl';
        CONST yandexHost = 'smtp.yandex.com.tr'; 
        
        public $targetMail;
        public $targetMailContent;
        public $targetMailSubject;

        public function __construct(){
            add_action('wp_ajax_mailSettingsListener', [$this, 'loadSettingsListener']);
            add_action('wp_ajax_nopriv_mailSettingsListener', [$this, 'loadSettingsListener']);
            $this->activeMailOption = get_option('useMailOption') === false ? 'dontUseMail' : get_option('useMailOption');
            // $this->mailFormatter();
            $this->statues_slug_init();

            switch ($this->activeMailOption){
                case 'useOutlook';
                    $this->outlookMail = get_option(MailManager::outlookMailOptionName) ;
                    $this->outlookPassword = get_option(MailManager::outlookPasswordOptionName) ;
                break;
                case 'useYandex';
                    $this->yandexMail = get_option(MailManager::yandexMailOptionName) ;
                    $this->yandexAppPassword = get_option(MailManager::yandexAppPasswordOptionName) ;
                break;
                case 'useBrevo';
                    $this->brevoToken = get_option(MailManager::brevoTokenOptionName) ;
                break;
                default:
                
                break;
            }
        }

        public function localizeScript(){
            return [
                'admin_url' => get_admin_url(),
                'order_statuses' => wc_get_order_statuses(),
                'options' => array(
                    'mail_options' => array(
                        'outlook' => ($this->checkMailValue(get_option(MailManager::outlookMailOptionName)) && $this->checkMailValue(get_option(MailManager::outlookPasswordOptionName))) ? true : false,
                        'yandex' => ($this->checkMailValue(get_option(MailManager::yandexMailOptionName)) && $this->checkMailValue(get_option(MailManager::yandexAppPasswordOptionName))) ? true : false,
                        'brevo' => ($this->checkMailValue(get_option(MailManager::brevoTokenOptionName))) ? true : false,
                        'activeMailOption' => $this->activeMailOption   
                    )
                ),
                'mailTemplates' => $this->prepareMailTemplateForLocalize()
            ];
        }

        public function checkMailValue($value){
            if ($value === false)
                return false;
            $value = trim($value);
            if ($value === '')
                return false;
            return true;
        }

        public function loadSettingsListener(){
            
            $post = $_POST;
            $status = false;
           
            if (isset($post['operation_'])) {
                $status = true;
                switch ($post['operation_']) {
                    case 'outlookFormSubmit_': # Outlook Submit Edilmiş Demektir
                        update_option('woocommerceOrderNotificationOutlookAddress', $post['woocommerceOrderNotificationOutlookAddress']);
                        update_option('woocommerceOrderNotificationOutlookPassword', $post['woocommerceOrderNotificationOutlookPassword']);
                        /* 
                            Uyarı çıkart
                        */
                        break;
                    case 'yandexFormSubmit_': # Yandex Submit Edilmiş Demektir
                        update_option('woocommerceOrderNotificationYandexMailAddress', $post['woocommerceOrderNotificationYandexAddress']);
                        update_option('woocommerceOrderNotificationYandexAppPassword', $post['woocommerceOrderNotificationYandexPassword']);
                        /* 
                            Uyarı Çıkart
                        */
                        break;
                    case 'brevoFormSubmit_': # Brevo Submit Edilmiş Demektir
                        update_option('woocommerceOrderNotificationBrevoToken', $post['brevoToken']);
                        /*
                            Uyarı Çıkart 
                        */
                        break;
                    case 'useMailSettings_':
                        update_option('useMailOption', $post['useMailOption']);
                        break;
                    case 'saveMailTemplate':
                        $content = $post['mailTemplateContent'];
                        $subject = $post['mailTemplateSubject'];
                        $slug = $post['mailTemplateSlug'];
                        update_option( $slug . '-mailContent' , $content );
                        update_option( $slug . '-mailHeader' , $subject );
                        break;
                    default: # Yanlış Bir Şeyler Oldu Demektir
                        $status = false;
                        /* 
                            Uyarı Çıkart
                        */
                        break;
                }
            }
            if ($status) 
                wp_send_json(['status' => 'true', ]);
            wp_send_json(['status' => 'false']);
        }

        public function prepareMailTemplateForLocalize(){

            if (!(get_option( 'statues_slug_init' ) == 'true')) 
                return 'null';

            $slugs = array_keys(wc_get_order_statuses());

            $data = [];
            for ($i = 0; $i < count($slugs); $i++){
                $slugs[$i] = str_replace('wc-','',$slugs[$i]);
                $data[$i]['mailHeader'] = get_option( $slugs[$i].'-mailHeader' );                
                $data[$i]['mailContent'] = get_option( $slugs[$i].'-mailContent' );  
                $data[$i]['slug'] = $slugs[$i];  
            }
            return $data;
        }

        // public function mailFormatter(){

        //     remove_action('media_buttons', 'media_buttons');
            
        //     add_filter('mce_buttons', function ($buttons) {
        //         $buttons = array_diff($buttons, array('formatselect', 'blockquote', 'wp_more', 'fullscreen', 'link', 'wp_adv'));
        //         return $buttons;
        //     });
        
        //     add_filter('tiny_mce_before_init', function ($init) {
        //         $init['width'] = '500';
        //         $init['height'] = '300';
        //         return $init;
        //     });
        
        // }

        public function statues_slug_init(){

            $mailSubject = __('Mail Subject', '@@@');
            // $mailContent = __('Mail Content.'.PHP_EOL.'', '@@@')
            if (get_option( MailManager::statuesSlugInitOptionName ) == 'true') 
                return;
            
            $slugs = array_keys(wc_get_order_statuses());

            for ($i = 0; $i < count($slugs); $i++){
                $slugs[$i] = str_replace('wc-','',$slugs[$i]);
                update_option( $slugs[$i].'-mailHeader' , 'Mail Başlığı' );                
                update_option( $slugs[$i].'-mailContent' , 'Mail İçeriği' );                
            }
            update_option( MailManager::statuesSlugInitOptionName, 'true' );
        }

        public function sendMail(){

            update_option('[UMUT]mailAdresi', $this->activeMailOption);

            if ($this->activeMailOption === 'useOutlook' || $this->activeMailOption === 'useYandex') {
                
                $mail = new PHPMailer();
                $mail->isSMTP();                                            
                $mail->Host       = $this->activeMailOption === 'useOutlook' ? MailManager::outlookHost : MailManager::yandexHost;                     
                $mail->SMTPAuth   = true;                                   
                $mail->Username   = $this->activeMailOption === 'useOutlook' ? $this->outlookMail : $this->yandexMail;                     
                $mail->Password   = $this->activeMailOption === 'useOutlook' ? $this->outlookPassword : $this->yandexAppPassword;                            
                $mail->SMTPSecure = $this->activeMailOption === 'useOutlook' ? MailManager::outlookSecure : MailManager::yandexSecure;            
                $mail->Port       = $this->activeMailOption === 'useOutlook' ? MailManager::outlookPort : MailManager::yandexPort;                                  
            
                $mail->setFrom($this->activeMailOption === 'useOutlook' ? $this->outlookMail : $this->yandexMail, 'Umut Gedik');

               
                $mail->addAddress('xyzumut06@gmail.com', 'Umut gedik');     
            
                $mail->isHTML(true); 
            
                $mail->Subject = 'Mail Başlığı - Woocommerce deneme';
                $mail->Body    = '<i>Mail İçeriği bodysi - woocommerce</i>';
            
                $mail->CharSet = "utf-8";

                $mail->send();
            }
            else if($this->activeMailOption === 'useBrevo'){

            }
            else{

            }
        }  
    }
?>