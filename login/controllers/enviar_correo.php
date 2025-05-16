<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Destruir sesion y borrar cache de la pagina
if (isset($_SESSION)) {
    session_destroy();
}
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Incluir el autoload de Composer
require '../../app/config.php';
require '../../vendor/autoload.php';
// Obtener datos del formulario
$id_usuario_sesion = $_POST['id_usuario_sesion'];
$correo = $_POST['email'];

try {
 

    // Si el correo no existe o existe uno diferente, reemplazarlo por el nuevo
    $sentencia = $pdo->prepare("SELECT * FROM usuario WHERE id = :id_usuario_sesion AND Correo = :correo");
    $sentencia->bindParam('id_usuario_sesion', $id_usuario_sesion);
    $sentencia->bindParam('correo', $correo);
    $sentencia->execute();

    if ($sentencia->rowCount() == 0) {
        $sentencia = $pdo->prepare("UPDATE usuario SET Correo = :correo WHERE id = :id_usuario_sesion");
        $sentencia->bindParam('correo', $correo);
        $sentencia->bindParam('id_usuario_sesion', $id_usuario_sesion);
        $sentencia->execute();
    }
   





    if ($sentencia->rowCount() > 0) {
        $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);
    
        // Verificar si el usuario tiene el campo llamado confirmado "no"
        if (isset($usuario['confirmado']) && $usuario['confirmado'] === 'no') {
            // Update the query to use $id_usuario_sesion for checking
            $stmt = $pdo->prepare("SELECT confirmado FROM usuario WHERE id = :id_usuario_sesion");
            $stmt->bindParam('id_usuario_sesion', $id_usuario_sesion);
            $stmt->execute();
            $resultado = $stmt->fetch();

        if ($resultado && $resultado['confirmado'] === 'no') {
                // Proceed with further actions if not confirmed
            
     
            // Generar un token único para la verificación
            $token = bin2hex(random_bytes(32));

            // Actualizar el token en la base de datos
            $actualizar = $pdo->prepare("UPDATE usuario SET token = :token, codigo = :codigo WHERE correo = :correo");
            $actualizar->bindParam('token', $token);
            $codigo = rand(1000, 9999);
            $actualizar->bindParam('codigo', $codigo);
            $actualizar->bindParam('correo', $correo);
            $actualizar->execute();
            // Configuración de PHPMailer
            $mail = new PHPMailer(true);
     
        }

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
                $mail->Subject = 'Verificar correo electrónico de tu cuenta de Sistema de Ventas';

                // Generar los enlaces de verificación
                $verification_link = $URL . "login/vistas/verificar_codigo.php?token=" . $token;

                $mail->Body = "Hola, <br><br>Para verificar tu correo electrónico, haz clic en el siguiente enlace: <br><br>
                               <a href='" . $verification_link . "'>Verificar correo electrónico</a><br><br>
                               Tu código de verificación es: " . $codigo . "<br><br>
                               Si no has solicitado verificar tu correo electrónico, ignora este correo electrónico.<br><br>
                               NO COMPARTA ESTE CORREO O ENLACE POR NINGÚN MOTIVO.";
               

                // Enviar el correo
                $mail->send();

                // Mensaje de éxito
                echo "Se ha enviado un correo para verificar tu correo electrónico si no encuentra el correo buscar en la bandeja de spam.";

            } catch (Exception $e) {
                echo "Error al enviar el correo: " . $mail->ErrorInfo;
            }
        } else {
            echo "El usuario con ese correo electrónico ya ha confirmado su cuenta.";
            echo "<meta http-equiv='refresh' content='3'>";
        }

    } else {
        echo "El correo electrónico no existe en nuestra base de datos.";
    }

} catch (PDOException $e) {
    echo "Error al buscar el usuario en la base de datos: " . $e->getMessage();
}


