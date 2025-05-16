<?php
require '../../app/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $token = $_POST['token'];
    $nueva_contraseña = $_POST['nueva_contraseña'];
    $repetir_contraseña = $_POST['repetir_contraseña'];

    // Verificar si las contraseñas coinciden
    if ($nueva_contraseña === $repetir_contraseña) {
        // Buscar al usuario con el token proporcionado en la base de datos
        $sentencia = $pdo->prepare("SELECT * FROM usuario WHERE token = :token");
        $sentencia->bindParam(':token', $token);
        $sentencia->execute();

        if ($sentencia->rowCount() > 0) {
            // Generar un hash para la nueva contraseña
            $password = password_hash($nueva_contraseña, PASSWORD_DEFAULT);

            // Actualizar el password en la base de datos
            $actualizar = $pdo->prepare("UPDATE usuario SET Contraseña = :password WHERE token = :token");
            $actualizar->bindParam(':password', $password);
            $actualizar->bindParam(':token', $token);

            if ($actualizar->execute()) {
                echo 'success';
            } else {
                echo "Error al actualizar la contraseña";
            }
        } else {
            echo "No se encontró un usuario con ese token";
        }
    } else {
        echo "Las contraseñas no coinciden";
    }
} else {
    echo "No se recibieron los datos del formulario";
}


