<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Incluir el autoload de Composer
require '../../app/config.php';
require '../../vendor/autoload.php';

// Obtener datos del formulario
$correo = $_POST['email'];

try {
    // Buscar al usuario con el correo proporcionado en la base de datos
    $sentencia = $pdo->prepare("SELECT * FROM usuario WHERE correo = :correo");
    $sentencia->bindParam('correo', $correo);
    $sentencia->execute();

    // Verificar si el usuario con ese correo existe
    if ($sentencia->rowCount() > 0) {
        $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario tiene el campo llamado confirmado "si"
        if ($usuario['confirmado'] == 'si') {
            // Generar un token único para la verificación
            $token = bin2hex(random_bytes(32));

            // Actualizar el token en la base de datos
            $actualizar = $pdo->prepare("UPDATE usuario SET token = :token WHERE correo = :correo");
            $actualizar->bindParam('token', $token);
            $actualizar->bindParam('correo', $correo);
            $actualizar->execute();

            // Configuración de PHPMailer
            $mail = new PHPMailer(true);

            try {
                // Configuración del servidor SMTP de Gmail
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'seguridadelectronicahuacho@gmail.com'; // Tu correo
                $mail->Password   = 'baugzazpvrkxjvju'; // Contraseña de la aplicación
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Remitente y destinatario
                $mail->setFrom('seguridadelectronicahuacho@gmail.com', 'Sistema de Ventas');
                $mail->addAddress($correo); // Destinatario

                // Contenido del correo
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Recuperar contraseña de tu cuenta de Sistema de Ventas';

                // Generar los enlaces de verificación
                $verification_link = $URL . "login/vistas/formulario_recuperar.php?token=" . $token;

                $mail->Body = "Hola, <br><br>Para recuperar tu contraseña, haz clic en el siguiente enlace: <br><br>
                               <a href='" . $verification_link . "'>Recuperar contraseña</a><br><br>
                               Si no has solicitado recuperar tu contraseña, ignora este correo electrónico.<br><br>
                               NO COMPARTA ESTE CORREO O ENLACE POR NINGÚN MOTIVO.";

                // Enviar el correo
                $mail->send();

                // Mensaje de éxito
                echo "Se ha enviado un correo para recuperar tu contraseña si no encuentra el correo buscar en la bandeja de spam.";

            } catch (Exception $e) {
                echo "Error al enviar el correo: " . $mail->ErrorInfo;
            }
        } else {
            echo "El usuario con ese correo electrónico no ha confirmado su cuenta.";
        }

    } else {
        echo "El correo electrónico no existe en nuestra base de datos.";
    }

} catch (PDOException $e) {
    echo "Error al buscar el usuario en la base de datos: " . $e->getMessage();
}
?>

