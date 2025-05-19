<?php

namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP(); // Usar SMTP
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'c021c0cfe3514e';
        $mail->Password = '2e65886df67963';
        $mail->setFrom('cuentas@luminous.com');
        $mail->addAddress('cuentas@luminous.com', 'Luminous_Spa.com');
        $mail->Subject = 'Confirma tu cuenta';

        // Configurar el contenido del correo
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->email . "</strong> Has creado tu cuenta en
        Luminous Spa, solo debes confirmarla presionando en el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/confirmar-cuenta?token="
            . $this->token . "'>Confirmar cuenta</a></p>";
        $contenido .= "<p>Si no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        // Enviar el correo
        $mail ->send();
    }

    // Enviar instrucciones para restablecer contraseña
    public function enviarInstrucciones() {
        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP(); // Usar SMTP
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'c021c0cfe3514e';
        $mail->Password = '2e65886df67963';
        $mail->setFrom('cuentas@luminous.com');
        $mail->addAddress('cuentas@luminous.com', 'Luminous_Spa.com');
        $mail->Subject = 'Restablece tu contraseña';

        // Configurar el contenido del correo
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado restablecer tu
        contraseña, sigue el siguiente enlace para hacerlo</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost:3000/recuperar?token="
            . $this->token . "'>Restablecer password</a></p>";
        $contenido .= "<p>Si no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        // Enviar el correo
        $mail->send();
    }
}