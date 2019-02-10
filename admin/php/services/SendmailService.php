<?php
$fullpath = str_replace("\\","/",$_SERVER['REQUEST_URI']);
$fisical = str_replace(($fullpath == "/" ? "" : $fullpath),"",str_replace("\\","/",getcwd()));
$path = str_replace((dirname($_SERVER['PHP_SELF']) == "/" ? "" : dirname($_SERVER['PHP_SELF'])),"",$fisical);

include_once $path."/admin/php/mailer/PHPMailer.php";
include_once $path."/admin/php/mailer/Exception.php";
include_once $path."/admin/php/mailer/SMTP.php";
include_once $path."/admin/php/utils/GeneralUtils.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class SendmailService {
    
    private $mail;
    private $dpService;
    private $config;
    
    public function __construct($exceptions = true) {
        $this->config          = GeneralUtils::getConfigIni();

        
        $this->mail             = new PHPMailer($exceptions);                              // Passing `true` enables exceptions
        $this->mail->CharSet    = 'UTF-8';
        //Server settings
        //$mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $this->mail->isSMTP();                                      // Set mailer to use SMTP
        $this->mail->Host = "localhost";  // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;                               // Enable SMTP authentication
        $this->mail->Username = $this->config['email_username'];                 // SMTP username
        $this->mail->Password = $this->config['email_password'];                           // SMTP password
        //$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = 25;                                    // TCP port to connect to
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ),
        );
        $this->mail->setFrom($this->config['email_username'],$this->config['nombre_sitio']);
    }
    
    public function sendMail($emailTo,$subject,$msj) {
        
        try {
            $this->mail->addAddress($emailTo);
            
            //Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->Subject = $subject;
            $this->mail->Body    = $msj;
            $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $this->mail->send();
        } catch (Exception $ex) {
            return $ex->getTraceAsString();
        }
    }
        
    
    public function sendMailRecuperoClave($emailTo,int $codigo) {
        
        $url = $this->config['url_sitio'];
        $skin = new SkinManager("/admin/html/emails/email_generico.html");
        
        $skin->addVariable("TXT_URL_SITIO"          , $url);
        $skin->addVariable("TXT_MENSAJE"            , "Recuperar Su Contraseña");
        $skin->addVariable("TXT_MENSAJE_SECUNDARIO" , "Ingrese en el Link para recuperar su contraseña");
        $skin->addVariable("TXT_LINK"               , $url."/modulos/usuarios/php/recuperarpassword.php?codigo=".$codigo);
        
        try {
            $this->mail->addAddress($emailTo);
            
            //Content
            $this->mail->isHTML(true);                                  // Set email format to HTML
            $this->mail->Subject = "Recuperar su Contraseña";
            $this->mail->Body    = $skin->getSkin();
            $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $this->mail->send();
        } catch (Exception $ex) {
            return $ex->getTraceAsString();
        }
        
    }
}



?>