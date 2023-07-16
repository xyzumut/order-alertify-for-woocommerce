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
        public $host; 
        public $port;
        public $secure;


        public function __construct($enableMailOption, $targets, $mailSubject, $mailContent, $mail, $password, $senderName, $host, $port, $secure){
            $this->enableMailOption = $enableMailOption ;            
            $this->mail             = $mail ;            
            $this->password         = $password ;            
            $this->targets          = $targets ;            
            $this->mailContent      = $mailContent ;            
            $this->mailSubject      = $mailSubject ;            
            $this->senderName       = $senderName ;
            $this->host             = $host;
            $this->port             = $port;
            $this->secure           = $secure;            
        }
       

        public function sendMail(){

            $mail = new PHPMailer();
            $mail->isSMTP();                                            
            $mail->Host       = $this->host;                     
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = $this->mail;                     
            $mail->Password   = $this->password;                            
            $mail->SMTPSecure = $this->secure;            
            $mail->Port       = $this->port;                                  
        
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