<?php

namespace saes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

use Rain\Tpl;

class Mailer
{

    public const USERNAME = 'EMAIL';
    public const PASSWORD = 'EMAIL_PASSWORD';
    public const NAME_FROM = "NAME";

    public function __construct($toAddress, $toName = "NAME", $subject, $tplName, $data = array())
    {
        $config = array(
        "tpl_dir"=>$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'processos'.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'email'.DIRECTORY_SEPARATOR,
        "cache_dir"=>$_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'processos'.DIRECTORY_SEPARATOR.'views-cache'.DIRECTORY_SEPARATOR,
        "debug"=>false
      );
        Tpl::configure($config);
        $tpl = new Tpl();
        foreach ($data as $key=>$value) {
            $tpl->assign($key, $value);
        }

        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->SMTPKeepAlive = true;
            $mail->Username = Mailer::USERNAME;
            $mail->Password = Mailer::PASSWORD;
            $mail->SMTPSecure = 'tls';
            $mail->setFrom('EMAIL', 'NAME');
            $mail->addAddress($toAddress);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $html = $tpl->draw($tplName, true);
            $mail->msgHTML($html);
            $mail->send();
        } catch (\Exception $e) {
          echo "erro email não enviado;";
        }
    }

}
