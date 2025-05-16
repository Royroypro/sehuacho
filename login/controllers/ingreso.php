<?php

include('../../app/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Se obtienen los datos del formulario
        $email = $_POST['correo'];
        $password_user = $_POST['password'];

        // Se verifica si el usuario existe en la base de datos
        $sql = "SELECT * FROM usuario WHERE Correo = :Correo LIMIT 1";
        $query = $pdo->prepare($sql);
        $query->execute([
            ':Correo' => $email
        ]);

        $usuario = $query->fetch(PDO::FETCH_ASSOC);

        // Se verifica si los datos ingresados son correctos
        if ($usuario && password_verify($password_user, $usuario['Contraseña'])) {
            // Si los datos son correctos, se inicia la sesión y se redirige al index
            session_start();
            $_SESSION['sesion_email'] = $email;
            header('Location: ../controllers/comprobaciones.php?id_usuario_sesion='.$usuario['id']);
            exit; // Asegúrate de terminar el script después de redirigir
        } else {
            // Si los datos son incorrectos, se muestra un mensaje de error
            echo json_encode(['success' => false, 'message' => 'Error: datos incorrectos']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
