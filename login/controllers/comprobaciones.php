<?php
include('../../app/config.php');


if (isset($_GET['id_usuario_sesion'])) {
    $id_usuario_sesion = $_GET['id_usuario_sesion'];
} else {
  
    exit;
}

$stmt = $pdo->prepare("SELECT confirmado FROM usuario WHERE id = :id_usuario_sesion");
$stmt->execute(['id_usuario_sesion' => $id_usuario_sesion]);
$resultado = $stmt->fetch();
if ($resultado['confirmado'] == 'si') {
    header("Location: ../../home.php?id_usuario_sesion=$id_usuario_sesion");
    exit;
} else {
    header("Location: ../vistas/verificar_correo.php?id_usuario_sesion=$id_usuario_sesion");
    exit;
}
?>

