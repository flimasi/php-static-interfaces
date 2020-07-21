<?

include(PATH . "/library/PHPMailer-master/PHPMailerAutoload.php");

/**
 * Class Smtp
 * #author Felipe Lima <felipe@felipelima.eti.br>
 */
class Smtp
{
    public function sendMail()
    {
        //include(PATH."/library/PHPMailer-master/PHPMailerAutoload.php");

        //Create a new PHPMailer instance
        $mail = new PHPMailer;
        //Tell PHPMailer to use SMTP
        $mail->isSMTP();
        //Enable SMTP debugging
        // 0 = off (for production use)
        // 1 = client messages
        // 2 = client and server messages
        $mail->SMTPDebug = 2;
        //Ask for HTML-friendly debug output
        $mail->Debugoutput = 'html';
        //Set the hostname of the mail server
        $mail->Host = "smtp.encontreipousadas.com.br";
        //Set the SMTP port number - likely to be 25, 465 or 587
        $mail->Port = 587;
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Username to use for SMTP authentication
        $mail->Username = "contato@encontreipousadas.com.br";
        //Password to use for SMTP authentication
        $mail->Password = APP_MAIL_WEBMASTER_PASS;
        //Set who the message is to be sent from
        $mail->setFrom('contato@encontreipousadas.com.br', 'First Last');
        //Set an alternative reply-to address
        $mail->addReplyTo('contato@encontreipousadas.com.br', 'First Last');
        //Set who the message is to be sent to
        $mail->addAddress('felipelimaesilva@gmail.com', 'Felipe');
        //Set the subject line
        $mail->Subject = 'PHPMailer SMTP test';

        $mail->msgHTML('teste');

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        //$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
        //Replace the plain text body with one created manually
        $mail->AltBody = 'This is a plain-text message body';
        //Attach an image file
        //$mail->addAttachment('images/phpmailer_mini.png');

        //send the message, check for errors
        if (!$mail->send()) {
            echo $mail->ErrorInfo;
        }
    }

    public function smtp_mail($to, $subject, $message, $headers = '')
    {
        mb_internal_encoding('UTF-8');

        if ($_SERVER["SERVER_ADDR"] == "191.252.51.21") {
            //Create a new PHPMailer instance
            $mail = new PHPMailer;

            // Set PHPMailer to use the sendmail transport
            $mail->isSendmail();
            //Set who the message is to be sent from
            $mail->setFrom(APP_MAIL_WEBMASTER, APP_MAIL_WEBMASTER_NAME);
            //Set an alternative reply-to address
            $mail->addReplyTo(APP_MAIL_WEBMASTER, APP_MAIL_WEBMASTER_NAME);
            //Set who the message is to be sent to
            $mail->addAddress($to, '');
            //Set the subject line
            $mail->Subject = mb_encode_mimeheader($subject, 'UTF-8', 'Q');
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $mail->msgHTML(htmlspecialchars_decode(htmlentities($message, ENT_NOQUOTES, 'UTF-8'), ENT_NOQUOTES));
            //Replace the plain text body with one created manually
            $mail->AltBody = $message;
            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');

            //send the message, check for errors
            if (!$mail->send()) {
                echo $mail->ErrorInfo;
            }
        } else {
            Smtp::smtp_mailDesenv($to, $subject, $message, $headers = '');
        }
    }

    public function smtp_mailDesenv($to, $subject, $message, $headers = '')
    {
        $headers = 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL .
            'From: ' . APP_MAIL_WEBMASTER_NAME . ' <' . APP_MAIL_WEBMASTER . '>' . PHP_EOL .
            'Reply-To: ' . APP_MAIL_WEBMASTER_NAME . ' <' . APP_MAIL_WEBMASTER . '>' . PHP_EOL .
            'X-Mailer: PHP/' . phpversion();

        $recipients = explode(',', $to);
        $user       = APP_MAIL_WEBMASTER;
        $pass       = APP_MAIL_WEBMASTER_PASS;
        // The server details that worked for you in the above step
        $smtp_host = 'smtp.encontreipousadas.com.br';
        //The port that worked for you in the above step
        $smtp_port = 587;

        if (!($socket = fsockopen($smtp_host, $smtp_port, $errno, $errstr, 10))) {
            echo "Error connecting to '$smtp_host' ($errno) ($errstr)";
        }

        Smtp::server_parse($socket, '220');

        fwrite($socket, 'EHLO ' . $smtp_host . "\r\n");
        Smtp::server_parse($socket, '250');

        fwrite($socket, 'AUTH LOGIN' . "\r\n");
        Smtp::server_parse($socket, '334');

        fwrite($socket, base64_encode($user) . "\r\n");
        Smtp::server_parse($socket, '334');

        fwrite($socket, base64_encode($pass) . "\r\n");
        Smtp::server_parse($socket, '235');

        fwrite($socket, 'MAIL FROM: <' . $user . '>' . "\r\n");
        Smtp::server_parse($socket, '250');

        foreach ($recipients as $email) {
            fwrite($socket, 'RCPT TO: <' . $email . '>' . "\r\n");
            Smtp::server_parse($socket, '250');
        }

        fwrite($socket, 'DATA' . "\r\n");
        Smtp::server_parse($socket, '354');

        fwrite($socket, 'Subject: '
            . $subject . "\r\n" . 'To: <' . implode('>, <', $recipients) . '>'
            . "\r\n" . $headers . "\r\n\r\n" . $message . "\r\n");

        fwrite($socket, '.' . "\r\n");
        Smtp::server_parse($socket, '250');

        fwrite($socket, 'QUIT' . "\r\n");
        fclose($socket);

        return true;
    }

    //Functin to Processes Server Response Codes
    function server_parse($socket, $expected_response)
    {
        $server_response = '';
        while (substr($server_response, 3, 1) != ' ') {
            if (!($server_response = fgets($socket, 256))) {
                echo 'Error while fetching server response codes.', __FILE__, __LINE__;
            }
        }

        if (!(substr($server_response, 0, 3) == $expected_response)) {
            echo 'Unable to send e-mail."' . $server_response . '"', __FILE__, __LINE__;
        }
    }
}

?>