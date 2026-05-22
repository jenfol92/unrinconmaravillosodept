<?php

/**
 * Servicio MailService
 * ---------------------------------------------------------
 * Servicio encargado de enviar correos electrónicos desde la aplicación.
 *
 * Actualmente se utiliza para enviar el enlace de recuperación de contraseña.
 *
 * Usa PHPMailer con SMTP.
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/mail.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    /**
     * Crea y configura una instancia de PHPMailer.
     *
     * @return PHPMailer
     */
    /*private function crearMailer()
    {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';

        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USERNAME;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->CharSet = 'UTF-8';
        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);

        return $mail;
    }
*/
private function crearMailer()
{
    $mail = new PHPMailer(true);



    $mail->isSMTP();
    $mail->Host = MAIL_HOST;
    $mail->SMTPAuth = true;

    // Forzamos LOGIN para que no intente CRAM-MD5.
    $mail->AuthType = 'LOGIN';

    // trim() evita errores si has copiado espacios accidentalmente.
    $mail->Username = trim(MAIL_USERNAME);
    $mail->Password = trim(MAIL_PASSWORD);

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = MAIL_PORT;

    $mail->CharSet = 'UTF-8';
    $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);

    return $mail;
}
    /**
     * Envía el email de recuperación de contraseña.
     *
     * @param string $email Email del usuario.
     * @param string $nombre Nombre del usuario.
     * @param string $enlace Enlace temporal para restablecer contraseña.
     *
     * @return bool
     *
     * @throws Exception
     */
    public function enviarRecuperacionPassword($email, $nombre, $enlace)
    {
        $mail = $this->crearMailer();

        $mail->addAddress($email, $nombre);

        $mail->isHTML(true);
        $mail->Subject = 'Restablecer contraseña - Un Rincón Maravilloso de PT';

        $mail->Body = '
            <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">
                <h2 style="color:#56B3AD;">Restablecer contraseña</h2>

                <p>Hola ' . htmlspecialchars($nombre) . ',</p>

                <p>
                    Hemos recibido una solicitud para restablecer la contraseña
                    de tu cuenta en <strong>Un Rincón Maravilloso de PT</strong>.
                </p>

                <p>
                    Puedes crear una nueva contraseña desde el siguiente enlace:
                </p>

                <p>
                    <a href="' . htmlspecialchars($enlace) . '"
                       style="display:inline-block; padding:12px 18px; background:#56B3AD; color:#fff; text-decoration:none; border-radius:8px;">
                        Restablecer contraseña
                    </a>
                </p>

                <p>
                    Este enlace caduca en 1 hora.
                </p>

                <p>
                    Si no has solicitado este cambio, puedes ignorar este mensaje.
                </p>

                <hr>

                <p style="font-size:12px; color:#777;">
                    Un Rincón Maravilloso de PT
                </p>
            </div>
        ';

        $mail->AltBody =
            "Hola $nombre,\n\n" .
            "Hemos recibido una solicitud para restablecer tu contraseña.\n\n" .
            "Puedes crear una nueva contraseña desde este enlace:\n" .
            "$enlace\n\n" .
            "Este enlace caduca en 1 hora.\n\n" .
            "Si no has solicitado este cambio, puedes ignorar este mensaje.";

        return $mail->send();
    }
}
