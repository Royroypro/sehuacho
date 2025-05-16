<?php
session_start();
require '../../app/config.php';

// Verificar si se ha recibido el token a través de la URL y el código de verificación a través del formulario
if (isset($_GET['token']) && isset($_POST['codigo'])) {
    $token = $_GET['token']; // Token recibido desde la URL
    $codigo_ingresado = $_POST['codigo']; // Código ingresado por el usuario

    try {
        // Buscar al usuario con el token proporcionado en la base de datos
        $sentencia = $pdo->prepare("SELECT * FROM usuario WHERE token = :token");
        $sentencia->bindParam('token', $token);
        $sentencia->execute();

        // Verificar si el usuario con ese token existe
        if ($sentencia->rowCount() > 0) {
            $usuario = $sentencia->fetch(PDO::FETCH_ASSOC);

            // Verificar la hora en la que se generó el código y el token
            $hora_actual = new DateTime();
            $hora_codigo = new DateTime($usuario['Fecha_Actualizacion']);
            $diferencia = $hora_actual->diff($hora_codigo);

            // Comparar el código ingresado y verificar si el token ha caducado
            if ($usuario['codigo'] == $codigo_ingresado && $diferencia->h < 24) {
                // Actualizar el estado del usuario a "Confirmado" (estado = 2) y confirmado = 'si'
                $actualizar = $pdo->prepare("UPDATE usuario SET estado = 1, confirmado = 'si' WHERE token = :token");
                $actualizar->bindParam('token', $token);
                $actualizar->execute();
                echo "Tu cuenta ha sido confirmada exitosamente.";
                // Mostrar cuenta regresiva de 5 segundos
                echo "<p class='text-center'>Redirigiendo en <span id='contador'>5</span> segundos...</p>";
                echo "<script>var contador = 5;
                setInterval(function(){
                    contador--;
                    document.getElementById('contador').innerHTML = contador;
                    if (contador == 0) {
                        window.location.href = '".$URL."/login/vistas';
                    }
                }, 1000);</script>";
                
            } else {
                // Error: el token ha caducado o el código es incorrecto
                echo "El token ha caducado o el código de verificación es incorrecto.";
            }
        } else {
            // Error: no se encontró un usuario con ese token
            echo "No se encontró el usuario con ese token.";
        }
    } catch (PDOException $e) {
        // Manejo de errores en caso de problemas con la base de datos
        echo "Error al verificar el código: " . $e->getMessage();
        // Loggear el error en un archivo
        $file = fopen("error.log", "a");
        fwrite($file, date("Y-m-d H:i:s") . " - " . $e->getMessage() . "\n");
        fclose($file);
    }
} else {
    // Si no se reciben el token o el código, mostrar un mensaje de error
    echo "Faltan datos para la verificación.";
    // Loggear el error en un archivo
    $file = fopen("error.log", "a");
    fwrite($file, date("Y-m-d H:i:s") . " - Faltan datos para la verificación\n");
    fclose($file);
}

