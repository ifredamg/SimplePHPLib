<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require __DIR__.'/../vendor/phpmailer/phpmailer/src/Exception.php';
    require __DIR__.'/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require __DIR__.'/../vendor/phpmailer/phpmailer/src/SMTP.php';

    // $mail = new PHPMailer();
    // $mail->isSMTP();
    // $mail->Host = 'smtp.gmail.com';
    // $mail->SMTPAuth = true;
    // $mail->SMTPSecure = 'tls';
    // $mail->Username = 'exemplo@gmail.com';
    // $mail->Password = 'senha';
    // $mail->Port = 587;

    // $mail->setFrom('remetente@email.com.br');
    // $mail->addReplyTo('no-reply@email.com.br');
    // $mail->addAddress('email@email.com.br', 'Nome');
    // $mail->addAddress('email@email.com.br', 'Contato');
    // $mail->addCC('email@email.com.br', 'Cópia');
    // $mail->addBCC('email@email.com.br', 'Cópia Oculta');

    // $mail->isHTML(true);
    // $mail->Subject = 'Assunto do email';
    // $mail->Body    = 'Este é o conteúdo da mensagem em <b>HTML!</b>';
    // $mail->AltBody = 'Para visualizar essa mensagem acesse http://site.com.br/mail';
    // $mail->addAttachment('/tmp/image.jpg', 'nome.jpg');

    // if(!$mail->send()) {
    //     echo 'Não foi possível enviar a mensagem.<br>';
    //     echo 'Erro: ' . $mail->ErrorInfo;
    // } else {
    //     echo 'Mensagem enviada.';
    // }

    class SMTPMailer extends PHPMailer
    {
        public function __construct($host, $loginUsername, $loginPassword, $port = 587, $isHTML = true)
        {
            $this->isSMTP();
            $this->SMTPAuth = true;
            $this->SMTPSecure = 'tls';
            $this->Host = $host;
            $this->Username = $loginUsername;
            $this->Password = $loginPassword;
            $this->Port = $port;
            $this->isHTML($isHTML);
        }

        public function EnderecoOrigem($from)
        {
            $this->setFrom($from);
        }

        public function EnderecoResposta($reply)
        {
            $this->addReplyTo($reply);
        }

        public function Endereco($to, $name = '')
        {
            $this->addAddress($to, $name);
        }

        public function EnderecoCC($cc, $name = '')
        {
            $this->addCC($cc, $name);
        }

        public function EnderecoBCC($bcc, $name = '')
        {
            $this->addBCC($bcc, $name);
        }

        public function Assunto($assunto)
        {
            $this->Subject($assunto);
        }

        public function CorpoEmail($body)
        {
            $this->Body($body);
        }

        public function AltCorpoEmail($altbody)
        {
            $this->AltBody($altbody);
        }

        public function Anexo($fileDir, $fileName = '')
        {
            $this->addAttachment($fileDir, $fileName);
        }

        public function Enviar()
        {
            return $this->send();
        }

        public function ObterErro()
        {
            return $this->ErrorInfo;
        }
    }
?>