<?php

namespace Classes;
use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;
    public $fecha;
    public $hora;


    public function __construct($email, $nombre, $token, $fecha, $hora) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
        $this->fecha = $fecha;
        $this->hora = $hora;
    }

    public function enviarConfirmacion() {
        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP(); // Usar SMTP
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];
        $mail->setFrom('cuentas@luminous.com');
        $mail->addAddress('cuentas@luminous.com', 'Luminous_Spa.com');
        $mail->Subject = 'Confirma tu cuenta';

        // Configurar el contenido del correo
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->email . "</strong> Has creado tu cuenta en
        Luminous Spa, solo debes confirmarla presionando en el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['APP_URL'] . "/confirmar-cuenta?token="
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
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];
        $mail->setFrom('cuentas@luminous.com');
        $mail->addAddress('cuentas@luminous.com', 'Luminous_Spa.com');
        $mail->Subject = 'Restablece tu contraseña';

        // Configurar el contenido del correo
        $mail->isHTML(TRUE);
        $mail->CharSet = 'UTF-8';
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado restablecer tu
        contraseña, sigue el siguiente enlace para hacerlo</p>";
        $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['APP_URL'] . "/recuperar?token="
            . $this->token . "'>Restablecer password</a></p>";
        $contenido .= "<p>Si no solicitaste este cambio, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        // Enviar el correo
        $mail->send();
    }

    public function enviarRecordatorio() {
        $mail = new PHPMailer();

        try {
            $mail->isSMTP(); // Usar SMTP
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['EMAIL_PORT'];
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASS'];
            $mail->setFrom('cuentas@luminous.com');
            $mail->addAddress($this->email, $this->nombre); // Usa el email del destinatario
            $mail->Subject = 'Recordatorio de tu Cita en Luminous Spa';

            // Habilitar depuración durante desarrollo
            $mail->SMTPDebug = 0; // Cambiar a 2 para ver detalles

            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';
            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>, te recordamos que tienes una cita programada en Luminous Spa.</p>";
            $contenido .= "<p>Detalles de la Cita:</p>";
            $contenido .= "<p>ID de Cita: " . $this->token . "</p>";

            // Añadir fecha y hora si están disponibles
            if(!empty($this->fecha)) {
                $fechaFormateada = date('d/m/Y', strtotime($this->fecha));
                $contenido .= "<p>Fecha: <strong>" . $fechaFormateada . "</strong></p>";
            }

            if(!empty($this->hora)) {
                $contenido .= "<p>Hora: <strong>" . $this->hora . "</strong></p>";
            }

            $contenido .= "<p>Confirma tu asistencia aquí: <a href='" . $_ENV['APP_URL'] . "/confirmar-cita?token=" . $this->token . "'>Confirmar Cita</a></p>";
            $contenido .= "<p>Si no puedes asistir, por favor cancela tu cita con anticipación.</p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;
            $enviado = $mail->send();

            if (!$enviado) {
                //error_log("Error al enviar recordatorio: " . $mail->ErrorInfo);
                return false;
            }

            return true; // Importante: retorna true si el envío fue exitoso

        } catch (\Exception $e) {
            //error_log("Excepción al enviar recordatorio: " . $e->getMessage());
            return false;
        }
    }
}