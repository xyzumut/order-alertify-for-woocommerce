<?php 
    namespace OrderAlertify\Tools;
    
    
    
    use PHPMailer\PHPMailer_\PHPMailer;
    use PHPMailer\PHPMailer_\Exception;
    require (__DIR__.'/').'phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
    require (__DIR__.'/').'phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require (__DIR__.'/').'phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';
    
    final class MailManager{
        
        public $enableMailOption;
        public $mail;
        public $password;
        public $targets;
        public $mailContent;
        public $mailSubject;
        public $senderName;

        CONST outlookPort = 587;
        CONST outlookSecure = 'STARTTLS';
        CONST outlookHost = 'smtp.office365.com';  

        CONST yandexPort = 465;
        CONST yandexSecure = 'ssl';
        CONST yandexHost = 'smtp.yandex.com.tr'; 
        


        public function __construct($enableMailOption, $targets, $mailSubject, $mailContent, $mail, $password, $senderName){
            $this->enableMailOption = $enableMailOption ;            
            $this->mail             = $mail ;            
            $this->password         = $password ;            
            $this->targets          = $targets ;            
            $this->mailContent      = $mailContent ;            
            $this->mailSubject      = $mailSubject ;            
            $this->senderName       = $senderName ;            
        }
       

        public function sendMail(){

            $mail = new PHPMailer();
            $mail->isSMTP();                                            
            $mail->Host       = $this->enableMailOption === 'useOutlook' ? MailManager::outlookHost : MailManager::yandexHost;                     
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = $this->mail;                     
            $mail->Password   = $this->password;                            
            $mail->SMTPSecure = $this->enableMailOption === 'useOutlook' ? MailManager::outlookSecure : MailManager::yandexSecure;            
            $mail->Port       = $this->enableMailOption === 'useOutlook' ? MailManager::outlookPort : MailManager::yandexPort;                                  
        
            $mail->setFrom($this->mail, $this->senderName);

            foreach ($this->targets as $target) {
               $mail->addAddress($target);     
            }
        
            $mail->isHTML(true); 
        
            $mail->Subject = $this->mailSubject;
            $mail->Body    = $this->mailContent;
        
            $mail->CharSet = "utf-8";

            $mail->send();
        }  
    }
?>